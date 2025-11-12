<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CardapioService
{
    public function __construct(private ApiPedidoService $apiPedidoService)
    {
    }

    public function listarTodos(): Collection
    {
        $resultado = $this->apiPedidoService->listarItensCardapio();
        $dados = $this->garantirSucesso($resultado, 'Erro ao listar itens do cardápio.');

        return collect($dados);
    }

    public function listarPorRestaurante(int $restauranteId): Collection
    {
        $resultado = $this->apiPedidoService->listarItensCardapioPorRestaurante($restauranteId);
        $dados = $this->garantirSucesso($resultado, 'Erro ao listar itens do cardápio do restaurante.');

        return collect($dados)->map(fn ($item) => $this->normalizarItem($item));
    }

    public function buscar(int $id): array
    {
        $resultado = $this->apiPedidoService->buscarItemCardapio($id);
        $dados = $this->garantirSucesso($resultado, 'Item do cardápio não encontrado.');

        return $this->normalizarItem($dados ?? []);
    }

    public function criar(array $dados): array
    {
        $resultado = $this->apiPedidoService->criarItemCardapio($dados);

        $data = $this->garantirSucesso($resultado, 'Erro ao criar item do cardápio.');

        return $this->normalizarItem($data ?? []);
    }

    public function atualizar(int $id, array $dados): array
    {
        $resultado = $this->apiPedidoService->atualizarItemCardapio($id, $dados);

        $data = $this->garantirSucesso($resultado, 'Erro ao atualizar item do cardápio.');

        return $this->normalizarItem($data ?? []);
    }

    public function deletar(int $id, ?int $restauranteId = null): void
    {
        $resultado = $this->apiPedidoService->deletarItemCardapio($id, $restauranteId);

        $this->garantirSucesso($resultado, 'Erro ao remover item do cardápio.');
    }

    public function uploadImagem(UploadedFile $arquivo): array
    {
        $resultado = $this->apiPedidoService->uploadImagemItem($arquivo);

        return $this->garantirSucesso($resultado, 'Erro no upload da imagem.');
    }

    private function garantirSucesso(array $resultado, string $mensagemPadrao): mixed
    {
        if (($resultado['success'] ?? false) === true) {
            return $resultado['data'] ?? null;
        }

        $erro = $resultado['error'] ?? $mensagemPadrao;
        $status = $resultado['status'] ?? null;

        Log::warning('Falha ao comunicar com backend de cardápio', [
            'mensagem' => $erro,
            'status' => $status
        ]);

        throw new RuntimeException($erro, $status ?? 0);
    }

    private function normalizarItem(array $item): array
    {
        if (!is_array($item)) {
            return $item;
        }

        $item['imagemUrlCompleta'] = $this->resolverImagemUrl($item['imagemUrl'] ?? null);

        if (isset($item['itens']) && is_array($item['itens'])) {
            $item['itens'] = array_map(function ($subItem) {
                if (!is_array($subItem)) {
                    return $subItem;
                }
                $subItem['imagemUrlCompleta'] = $this->resolverImagemUrl($subItem['imagemUrl'] ?? null);
                return $subItem;
            }, $item['itens']);
        }

        return $item;
    }

    private function resolverImagemUrl(?string $caminho): ?string
    {
        if (empty($caminho)) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $caminho)) {
            return $caminho;
        }

        $baseUrl = rtrim(config('services.backend.url', ''), '/');
        if (empty($baseUrl)) {
            return $caminho;
        }

        return $baseUrl . '/' . ltrim($caminho, '/');
    }
}


