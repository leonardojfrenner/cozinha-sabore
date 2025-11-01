<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PedidoService;
use Illuminate\Support\Facades\Log;

class PedidoController extends Controller
{
    protected $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    public function index()
    {
        try {
            $restaurante = session('restaurante_logado');
            
            if (!$restaurante) {
                return redirect()->route('restaurante.login')
                    ->with('error', 'É necessário fazer login para acessar os pedidos.');
            }

            $pedidos = $this->pedidoService->getPedidosByRestaurante($restaurante['email'] ?? '');

            return view('pedidos.index', compact('pedidos'));
        } catch (\Exception $e) {
            Log::error('Erro ao listar pedidos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('pedidos.index', ['pedidos' => collect([])])
                ->with('error', 'Erro ao carregar pedidos. Verifique a conexão com o servidor.');
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:NOVO,EM_PREPARO,CONCLUIDO,CANCELADO'
        ]);

        try {
            $pedido = $this->pedidoService->updatePedidoStatus($id, $request->status);

            if ($pedido) {
                return redirect()->route('pedidos.index')
                    ->with('success', 'Status do pedido atualizado com sucesso!');
            }

            return redirect()->route('pedidos.index')
                ->with('error', 'Erro ao atualizar status do pedido. Pedido não encontrado.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('pedidos.index')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar status do pedido', [
                'id' => $id,
                'status' => $request->status,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('pedidos.index')
                ->with('error', 'Erro ao atualizar status do pedido: ' . $e->getMessage());
        }
    }

    public function historico()
    {
        try {
            $restaurante = session('restaurante_logado');
            
            if (!$restaurante) {
                return redirect()->route('restaurante.login')
                    ->with('error', 'É necessário fazer login para acessar o histórico.');
            }

            $pedidos = $this->pedidoService->getPedidosByRestaurante($restaurante['email'] ?? '');

            return view('pedidos.historico', compact('pedidos'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar histórico', [
                'error' => $e->getMessage()
            ]);

            return view('pedidos.historico', ['pedidos' => collect([])])
                ->with('error', 'Erro ao carregar histórico. Verifique a conexão com o servidor.');
        }
    }
}
