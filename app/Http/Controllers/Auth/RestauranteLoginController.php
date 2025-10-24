<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PedidoService;

class RestauranteLoginController extends Controller
{
    protected $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    public function showLoginForm()
    {
        return view('auth.restaurante-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $restaurante = $this->pedidoService->authenticateRestaurante($request->email, $request->password);

        if ($restaurante) {
            // Simular login usando sessão
            session(['restaurante_logado' => $restaurante]);
            return redirect()->route('pedidos.index');
        }

        return back()->withErrors([
            'email' => 'Email ou senha inválidos.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        session()->forget('restaurante_logado');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('restaurante.login');
    }
}
