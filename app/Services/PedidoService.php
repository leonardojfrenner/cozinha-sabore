<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PedidoService
{
    private $pedidosData;
    private $apiPedidoService;
    private $useBackendApi;

    public function __construct(ApiPedidoService $apiPedidoService)
    {
        $this->apiPedidoService = $apiPedidoService;
        // Define se deve usar backend API ou arquivo local
        $this->useBackendApi = env('USE_BACKEND_API', true);
        
        if (!$this->useBackendApi) {
            $this->loadPedidosData();
        }
    }

    private function loadPedidosData()
    {
        $jsonPath = base_path('pedidos.json');
        
        if (file_exists($jsonPath)) {
            $jsonContent = file_get_contents($jsonPath);
            $this->pedidosData = json_decode($jsonContent, true);
        } else {
            $this->pedidosData = [];
        }
    }

    public function getAllPedidos()
    {
        if ($this->useBackendApi) {
            $result = $this->apiPedidoService->listarPedidos();
            if ($result['success']) {
                return collect($result['data'] ?? []);
            }
            return collect([]);
        }
        return collect($this->pedidosData);
    }

    public function getPedidosByRestaurante($email)
    {
        if ($this->useBackendApi) {
            // Busca o restaurante completo da sessão para ter o ID também
            $restauranteLogado = session('restaurante_logado');
            $restauranteId = $restauranteLogado['id'] ?? null;
            $restauranteCompleto = $restauranteLogado['restaurante_completo'] ?? null;
            
            Log::info('Buscando pedidos do restaurante', [
                'email' => $email,
                'restaurante_id' => $restauranteId,
                'use_backend_api' => true
            ]);
            
            // Usa o endpoint específico GET /pedidos/restaurante
            // O backend já filtra pelos pedidos do restaurante autenticado
            $result = $this->apiPedidoService->listarPedidosDoRestaurante();
            
            Log::info('Resultado da busca de pedidos', [
                'success' => $result['success'] ?? false,
                'status' => $result['status'] ?? null,
                'count' => isset($result['data']) ? count($result['data']) : 0,
                'error' => $result['error'] ?? null
            ]);
            
            if ($result['success']) {
                $pedidos = $result['data'] ?? [];
                
                // O endpoint /pedidos/restaurante já retorna apenas os pedidos do restaurante autenticado
                // Mas mantemos um filtro de segurança adicional por garantia
                $pedidosFiltrados = collect($pedidos)->filter(function ($pedido) use ($email, $restauranteId) {
                    // Se não houver informação do restaurante no pedido, rejeita por segurança
                    if (!isset($pedido['restaurante'])) {
                        Log::warning('Pedido sem informação de restaurante', [
                            'pedido_id' => $pedido['id'] ?? null
                        ]);
                        return false;
                    }
                    
                    // Verifica se o restaurante corresponde (segurança adicional)
                    // O backend já filtra, mas garantimos aqui também
                    if (isset($pedido['restaurante']['email'])) {
                        return $pedido['restaurante']['email'] === $email;
                    }
                    
                    if ($restauranteId && isset($pedido['restaurante']['id'])) {
                        return $pedido['restaurante']['id'] == $restauranteId;
                    }
                    
                    // Se não conseguir verificar, aceita (backend já filtrou)
                    return true;
                });
                
                Log::info('Pedidos recebidos do restaurante', [
                    'total_recebido' => count($pedidos),
                    'total_filtrado' => $pedidosFiltrados->count(),
                    'email' => $email,
                    'restaurante_id' => $restauranteId
                ]);
                
                return $pedidosFiltrados;
            }
            
            Log::warning('Falha ao buscar pedidos do backend', [
                'error' => $result['error'] ?? 'Erro desconhecido',
                'status' => $result['status'] ?? null,
                'response_body' => $result['response_body'] ?? null
            ]);
            
            return collect([]);
        }
        
        return collect($this->pedidosData)->filter(function ($pedido) use ($email) {
            return $pedido['restaurante']['email'] === $email;
        });
    }

    public function getPedidoById($id)
    {
        if ($this->useBackendApi) {
            $result = $this->apiPedidoService->listarPedidos();
            if ($result['success']) {
                $pedidos = $result['data'] ?? [];
                return collect($pedidos)->firstWhere('id', $id);
            }
            return null;
        }
        
        return collect($this->pedidosData)->firstWhere('id', $id);
    }

    public function updatePedidoStatus($id, $status)
    {
        // Valida status permitido
        $statusPermitidos = ['NOVO', 'EM_PREPARO', 'CONCLUIDO', 'CANCELADO'];
        if (!in_array($status, $statusPermitidos)) {
            throw new \InvalidArgumentException("Status inválido: {$status}");
        }

        if ($this->useBackendApi) {
            // Usa o endpoint específico para restaurantes PUT /pedidos/{id}/status-restaurante
            $result = $this->apiPedidoService->atualizarStatusRestaurante($id, $status);
            if ($result['success']) {
                return $result['data'];
            }
            throw new \Exception($result['error'] ?? 'Erro ao atualizar status do pedido');
        }

        $pedidos = collect($this->pedidosData);
        $pedidoIndex = $pedidos->search(function ($pedido) use ($id) {
            return $pedido['id'] == $id;
        });

        if ($pedidoIndex !== false) {
            $this->pedidosData[$pedidoIndex]['status'] = $status;
            $this->savePedidosData();
            return $this->pedidosData[$pedidoIndex];
        }

        return null;
    }

    private function savePedidosData()
    {
        $jsonPath = base_path('pedidos.json');
        file_put_contents($jsonPath, json_encode($this->pedidosData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function getRestaurantes()
    {
        if ($this->useBackendApi) {
            // Para o backend, precisaríamos de um endpoint específico de restaurantes
            // Por enquanto, mantemos compatibilidade com o sistema antigo
            // Você pode implementar isso conforme necessário
            return collect([]);
        }
        
        $restaurantes = collect($this->pedidosData)->pluck('restaurante')->unique('id');
        
        return $restaurantes->map(function ($restaurante) {
            return [
                'id' => $restaurante['id'],
                'nome' => $restaurante['nome'],
                'cnpj' => $restaurante['cnpj'],
                'email' => $restaurante['email'],
                'telefone' => $restaurante['telefone'],
                'password' => '123456' // Senha padrão para teste
            ];
        });
    }

    public function authenticateRestaurante($email, $password)
    {
        if ($this->useBackendApi) {
            Log::info('Tentando autenticar no backend', [
                'email' => $email,
                'backend_url' => config('services.backend.url')
            ]);
            
            // Faz login no backend Java
            $result = $this->apiPedidoService->login($email, $password);
            
            Log::info('Resultado do login no backend', [
                'success' => $result['success'] ?? false,
                'status' => $result['status'] ?? null,
                'error' => $result['error'] ?? null,
                'has_restaurante' => isset($result['restaurante'])
            ]);
            
            if ($result['success']) {
                $restaurante = $result['restaurante'];
                
                // Se houver token JWT, armazena
                if (isset($result['token']) && $result['token']) {
                    $this->apiPedidoService->setToken($result['token']);
                    Log::info('Token JWT configurado');
                } else {
                    // Se não houver token, usa Basic Auth para próximas requisições
                    $this->apiPedidoService->setBasicAuth($email, $password);
                    Log::info('Basic Auth configurado');
                }
                
                // Retorna o restaurante no formato esperado pela sessão
                // NÃO armazena senha na sessão por segurança
                return [
                    'id' => $restaurante['id'] ?? null,
                    'nome' => $restaurante['nome'] ?? '',
                    'cnpj' => $restaurante['cnpj'] ?? '',
                    'email' => $restaurante['email'] ?? $email,
                    'telefone' => $restaurante['telefone'] ?? '',
                    'restaurante_completo' => $restaurante // Armazena o objeto completo também
                ];
            }
            
            // Se o login falhou, loga o erro e retorna null
            Log::warning('Login falhou no backend', [
                'error' => $result['error'] ?? 'Erro desconhecido',
                'status' => $result['status'] ?? null,
                'response_body' => $result['response_body'] ?? null
            ]);
            
            return null;
        }
        
        // Fallback para autenticação local (arquivo JSON)
        $restaurantes = $this->getRestaurantes();
        
        $restaurante = $restaurantes->firstWhere('email', $email);
        
        if ($restaurante && $password === '123456') {
            return $restaurante;
        }
        
        return null;
    }

    /**
     * Define o token de autenticação para o backend
     */
    public function setBackendToken($token)
    {
        $this->apiPedidoService->setToken($token);
    }
}
