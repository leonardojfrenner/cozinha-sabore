<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PedidoService;

class PedidoController extends Controller
{
    protected $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    public function index()
    {
        $restaurante = session('restaurante_logado');
        $pedidos = $this->pedidoService->getPedidosByRestaurante($restaurante['email']);

        return view('pedidos.index', compact('pedidos'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:NOVO,CONCLUIDO'
        ]);

        $pedido = $this->pedidoService->updatePedidoStatus($id, $request->status);

        if ($pedido) {
            return redirect()->route('pedidos.index')
                ->with('success', 'Status do pedido atualizado com sucesso!');
        }

        return redirect()->route('pedidos.index')
            ->with('error', 'Erro ao atualizar status do pedido!');
    }

    public function historico()
    {
        $restaurante = session('restaurante_logado');
        $pedidos = $this->pedidoService->getPedidosByRestaurante($restaurante['email']);

        return view('pedidos.historico', compact('pedidos'));
    }
}
