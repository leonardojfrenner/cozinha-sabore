<?php

namespace App\Http\Controllers;

use App\Services\CardapioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use RuntimeException;

class CardapioController extends Controller
{
    public function __construct(private CardapioService $cardapioService)
    {
    }

    public function index(Request $request): View|RedirectResponse
    {
        $restaurante = session('restaurante_logado');

        if (!$restaurante) {
            return redirect()->route('restaurante.login')
                ->with('error', 'É necessário estar autenticado para acessar o cardápio.');
        }

        try {
            $itens = $this->cardapioService
                ->listarPorRestaurante((int) ($restaurante['id'] ?? 0));
        } catch (RuntimeException $exception) {
            Log::error('Erro ao carregar itens do cardápio', [
                'erro' => $exception->getMessage(),
            ]);

            session()->flash('error', $exception->getMessage());
            $itens = collect();
        }

        return view('cardapio.index', [
            'itens' => $itens,
        ]);
    }

    public function create(): View|RedirectResponse
    {
        if (!session('restaurante_logado')) {
            return redirect()->route('restaurante.login')
                ->with('error', 'É necessário estar autenticado para acessar o cardápio.');
        }

        return view('cardapio.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $restaurante = session('restaurante_logado');

        if (!$restaurante) {
            return redirect()->route('restaurante.login')
                ->with('error', 'Sessão expirada. Faça login novamente.');
        }

        $dadosValidados = $this->validarFormulario($request, false);

        try {
            $payload = $this->montarPayload($dadosValidados, $request, $restaurante);

            $this->cardapioService->criar($payload);

            return redirect()->route('cardapio.index')
                ->with('success', 'Item criado com sucesso.');
        } catch (RuntimeException $exception) {
            Log::error('Erro ao criar item do cardápio', [
                'erro' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }
    }

    public function edit(int $id): View|RedirectResponse
    {
        $restaurante = session('restaurante_logado');

        if (!$restaurante) {
            return redirect()->route('restaurante.login')
                ->with('error', 'Sessão expirada. Faça login novamente.');
        }

        try {
            $item = $this->cardapioService->buscar($id);
        } catch (RuntimeException $exception) {
            Log::error('Erro ao buscar item do cardápio', [
                'id' => $id,
                'erro' => $exception->getMessage(),
            ]);

            return redirect()->route('cardapio.index')
                ->with('error', $exception->getMessage());
        }

        return view('cardapio.edit', [
            'item' => $item,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $restaurante = session('restaurante_logado');

        if (!$restaurante) {
            return redirect()->route('restaurante.login')
                ->with('error', 'Sessão expirada. Faça login novamente.');
        }

        $dadosValidados = $this->validarFormulario($request, true);

        try {
            $payload = $this->montarPayload($dadosValidados, $request, $restaurante, true);

            $this->cardapioService->atualizar($id, $payload);

            return redirect()->route('cardapio.index')
                ->with('success', 'Item atualizado com sucesso.');
        } catch (RuntimeException $exception) {
            Log::error('Erro ao atualizar item do cardápio', [
                'id' => $id,
                'erro' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $restaurante = session('restaurante_logado');

        if (!$restaurante) {
            return redirect()->route('restaurante.login')
                ->with('error', 'Sessão expirada. Faça login novamente.');
        }

        try {
            Log::info('Solicitando exclusão de item do cardápio', [
                'item_id' => $id,
                'restaurante_id' => $restaurante['id'] ?? null,
            ]);
            $this->cardapioService->deletar($id, (int) ($restaurante['id'] ?? 0));

            return redirect()->route('cardapio.index')
                ->with('success', 'Item removido com sucesso.');
        } catch (RuntimeException $exception) {
            Log::error('Erro ao remover item do cardápio', [
                'id' => $id,
                'restaurante_id' => $restaurante['id'] ?? null,
                'erro' => $exception->getMessage(),
            ]);

            return redirect()->route('cardapio.index')
                ->with('error', $exception->getMessage());
        }
    }

    private function validarFormulario(Request $request, bool $editar): array
    {
        $regras = [
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'preco' => ['required', 'numeric', 'min:0'],
            'categoria' => ['nullable', 'string', 'max:50'],
            'imagem' => [$editar ? 'nullable' : 'sometimes', 'image', 'max:2048'],
        ];

        return $request->validate($regras, [
            'nome.required' => 'Informe o nome do item.',
            'preco.required' => 'Informe o preço.',
            'preco.numeric' => 'O preço deve ser um número válido.',
            'preco.min' => 'O preço deve ser maior ou igual a zero.',
            'imagem.image' => 'O arquivo deve ser uma imagem válida.',
            'imagem.max' => 'A imagem deve ter no máximo 2MB.',
        ]);
    }

    private function montarPayload(array $dados, Request $request, array $restaurante, bool $editar = false): array
    {
        $payload = [
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'] ?? null,
            'preco' => $this->normalizarPreco($dados['preco']),
            'categoria' => $dados['categoria'] ?? null,
            'restaurante' => [
                'id' => $restaurante['id'] ?? null,
            ],
        ];

        $imagemAtual = $request->input('imagemUrlAtual');

        if ($request->hasFile('imagem')) {
            $arquivoOriginal = $request->file('imagem');
            $arquivoProcessado = $this->otimizarImagem($arquivoOriginal);

            try {
                $upload = $this->cardapioService->uploadImagem($arquivoProcessado);
            } finally {
                if ($arquivoProcessado !== $arquivoOriginal && file_exists($arquivoProcessado->getPathname())) {
                    @unlink($arquivoProcessado->getPathname());
                }
            }

            $payload['imagemUrl'] = $upload['url'] ?? $imagemAtual;
        } else {
            $payload['imagemUrl'] = $imagemAtual;
        }

        if (!$editar && empty($payload['imagemUrl'])) {
            unset($payload['imagemUrl']);
        }

        return $payload;
    }

    private function otimizarImagem(UploadedFile $arquivo): UploadedFile
    {
        $limiteBytes = 2 * 1024 * 1024;

        if ($arquivo->getSize() <= $limiteBytes || !extension_loaded('gd')) {
            return $arquivo;
        }

        $conteudo = @file_get_contents($arquivo->getPathname());
        if ($conteudo === false) {
            return $arquivo;
        }

        $imagem = @imagecreatefromstring($conteudo);
        if ($imagem === false) {
            return $arquivo;
        }

        $larguraOriginal = imagesx($imagem);
        $alturaOriginal = imagesy($imagem);

        $tempPath = tempnam(sys_get_temp_dir(), 'cardapio_img_');
        $mime = $arquivo->getMimeType();

        $salvo = $this->salvarCompactado($imagem, $mime, $tempPath, 90);

        if (!$salvo || filesize($tempPath) > $limiteBytes) {
            $fatorReducao = sqrt($limiteBytes / max($arquivo->getSize(), 1));
            $fatorReducao = min($fatorReducao, 0.85);

            $larguraNova = max(200, (int) ($larguraOriginal * $fatorReducao));
            $alturaNova = max(200, (int) ($alturaOriginal * $fatorReducao));

            $redimensionada = imagecreatetruecolor($larguraNova, $alturaNova);

            if ($mime === 'image/png') {
                imagealphablending($redimensionada, false);
                imagesavealpha($redimensionada, true);
                $transparente = imagecolorallocatealpha($redimensionada, 255, 255, 255, 127);
                imagefill($redimensionada, 0, 0, $transparente);
            }

            imagecopyresampled(
                $redimensionada,
                $imagem,
                0,
                0,
                0,
                0,
                $larguraNova,
                $alturaNova,
                $larguraOriginal,
                $alturaOriginal
            );

            $salvo = $this->salvarCompactado($redimensionada, $mime, $tempPath, 82);

            imagedestroy($redimensionada);
        }

        imagedestroy($imagem);

        if (!$salvo || !file_exists($tempPath)) {
            @unlink($tempPath);
            return $arquivo;
        }

        if (filesize($tempPath) >= $arquivo->getSize()) {
            @unlink($tempPath);
            return $arquivo;
        }

        return new UploadedFile(
            $tempPath,
            $arquivo->getClientOriginalName(),
            $arquivo->getMimeType(),
            null,
            true
        );
    }

    private function salvarCompactado($imagem, ?string $mime, string $destino, int $qualidade): bool
    {
        if ($mime === 'image/png') {
            $compressao = (int) round((100 - $qualidade) / 10);
            $compressao = max(0, min(9, $compressao));

            return imagepng($imagem, $destino, $compressao);
        }

        if ($mime === 'image/gif') {
            return imagegif($imagem, $destino);
        }

        return imagejpeg($imagem, $destino, $qualidade);
    }

    private function normalizarPreco(mixed $preco): float
    {
        if (is_string($preco)) {
            $valor = trim($preco);
            $valor = str_replace(' ', '', $valor);

            $temVirgula = str_contains($valor, ',');
            $temPonto = str_contains($valor, '.');

            if ($temVirgula && $temPonto) {
                $valor = str_replace('.', '', $valor);
                $valor = str_replace(',', '.', $valor);
            } elseif ($temVirgula) {
                $valor = str_replace(',', '.', $valor);
            }

            return (float) $valor;
        }

        return (float) $preco;
    }
}


