<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PedidoService;
use Illuminate\Support\Facades\Log;

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

        try {
            Log::info('Iniciando processo de login', [
                'email' => $request->email,
                'use_backend_api' => env('USE_BACKEND_API', true)
            ]);
            
            $restaurante = $this->pedidoService->authenticateRestaurante($request->email, $request->password);

            if ($restaurante) {
                // Login bem-sucedido - armazena na sessão
                session(['restaurante_logado' => $restaurante]);
                
                Log::info('Login realizado com sucesso', [
                    'email' => $request->email,
                    'restaurante_id' => $restaurante['id'] ?? null
                ]);
                
                return redirect()->route('pedidos.index')
                    ->with('success', 'Login realizado com sucesso!');
            }

            Log::warning('Login falhou - credenciais inválidas ou erro no backend', [
                'email' => $request->email
            ]);

            return back()->withErrors([
                'email' => 'Email ou senha inválidos. Verifique os logs do Laravel para mais detalhes.',
            ])->withInput($request->only('email'));
            
        } catch (\Exception $e) {
            Log::error('Erro ao fazer login', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'email' => 'Erro ao fazer login: ' . $e->getMessage() . '. Verifique os logs para mais detalhes.',
            ])->withInput($request->only('email'));
        }
    }

    public function logout(Request $request)
    {
        // Limpa token do backend se existir
        $this->pedidoService->setBackendToken(null);
        
        // Remove dados da sessão
        session()->forget('restaurante_logado');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('restaurante.login')
            ->with('success', 'Logout realizado com sucesso!');
    }
}
