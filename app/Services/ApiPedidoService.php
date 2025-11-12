<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ApiPedidoService
{
    private $baseUrl;
    private $token;
    private $basicAuthCredentials;
    private $cookies; // Armazena cookies da sessão

    public function __construct()
    {
        $this->baseUrl = config('services.backend.url', 'http://3.90.155.156:8080');
        $this->token = session('backend_token');
        $this->basicAuthCredentials = session('backend_basic_auth');
        $this->cookies = session('backend_cookies', []);
    }

    /**
     * Define o token de autenticação (JWT)
     */
    public function setToken($token)
    {
        $this->token = $token;
        session(['backend_token' => $token]);
    }

    /**
     * Define credenciais para Basic Auth
     */
    public function setBasicAuth($email, $password)
    {
        $this->basicAuthCredentials = ['email' => $email, 'password' => $password];
        session(['backend_basic_auth' => $this->basicAuthCredentials]);
    }

    /**
     * Armazena cookies da sessão do backend
     */
    public function setCookies($cookies)
    {
        $this->cookies = $cookies;
        session(['backend_cookies' => $cookies]);
    }

    /**
     * Remove o token de autenticação e credenciais
     */
    public function clearToken()
    {
        $this->token = null;
        $this->basicAuthCredentials = null;
        $this->cookies = [];
        session()->forget('backend_token');
        session()->forget('backend_basic_auth');
        session()->forget('backend_cookies');
    }

    /**
     * Faz uma requisição autenticada ao backend
     */
    private function makeRequest($method, $endpoint, $data = null, $useQueryString = false)
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        
        $request = Http::timeout(30)
            ->acceptJson();

        if (config('services.backend.skip_tls_verify', false)) {
            $request->withoutVerifying();
        }

        // Adiciona cookies da sessão (JSESSIONID) se existirem
        // Isso é necessário para manter a sessão do Spring Security
        if (!empty($this->cookies)) {
            // Constrói string de cookies no formato: JSESSIONID=valor
            $cookieString = '';
            foreach ($this->cookies as $name => $value) {
                if ($cookieString) $cookieString .= '; ';
                $cookieString .= "{$name}={$value}";
            }
            
            $request->withHeaders(['Cookie' => $cookieString]);
            
            Log::debug('Cookies adicionados à requisição', [
                'cookies' => array_keys($this->cookies),
                'cookie_string' => $cookieString,
                'url' => $url
            ]);
        }

        // Adiciona autenticação: prioriza JWT token, senão usa Basic Auth
        if ($this->token) {
            $request->withToken($this->token);
        } elseif ($this->basicAuthCredentials) {
            $request->withBasicAuth(
                $this->basicAuthCredentials['email'],
                $this->basicAuthCredentials['password']
            );
        }

        try {
            if ($useQueryString && $data) {
                // Para requisições que precisam de query string ao invés de corpo
                // Constrói a URL com query string manualmente
                $queryString = http_build_query($data);
                $urlWithQuery = $url . (strpos($url, '?') !== false ? '&' : '?') . $queryString;
                
                $response = match(strtoupper($method)) {
                    'GET' => $request->get($urlWithQuery),
                    'PUT' => $request->put($urlWithQuery),
                    'PATCH' => $request->patch($urlWithQuery),
                    'POST' => $request->post($urlWithQuery),
                    'DELETE' => $request->delete($urlWithQuery),
                    default => throw new Exception("Método HTTP não suportado: {$method}")
                };
            } else {
                if ($data !== null) {
                    $request = $request->asJson();
                }

                $response = match(strtoupper($method)) {
                    'GET' => $request->get($url),
                    'POST' => $request->post($url, $data ?? []),
                    'PUT' => $request->put($url, $data ?? []),
                    'PATCH' => $request->patch($url, $data ?? []),
                    'DELETE' => $request->delete($url),
                    default => throw new Exception("Método HTTP não suportado: {$method}")
                };
            }

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'status' => $response->status()
                ];
            }

            // Se receber 401, limpa o token
            if ($response->status() === 401) {
                $this->clearToken();
                return [
                    'success' => false,
                    'error' => 'Não autorizado. Faça login novamente.',
                    'status' => 401
                ];
            }

            $errorData = $response->json();
            Log::warning('Requisição ao backend falhou', [
                'url' => $url,
                'method' => strtoupper($method),
                'status' => $response->status(),
                'response_body' => $response->body(),
                'error_json' => $errorData,
            ]);
            return [
                'success' => false,
                'error' => $errorData['message'] ?? 'Erro na requisição',
                'status' => $response->status(),
                'data' => $errorData
            ];

        } catch (Exception $e) {
            Log::error('Erro na comunicação com backend', [
                'url' => $url,
                'method' => $method,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erro de conexão com o servidor: ' . $e->getMessage(),
                'status' => 500
            ];
        }
    }

    public function listarItensCardapio()
    {
        return $this->makeRequest('GET', '/itens');
    }

    public function listarItensCardapioPorRestaurante($restauranteId)
    {
        return $this->makeRequest('GET', "/itens/restaurante/{$restauranteId}");
    }

    public function buscarItemCardapio($id)
    {
        return $this->makeRequest('GET', "/itens/{$id}");
    }

    public function criarItemCardapio(array $dados)
    {
        return $this->makeRequest('POST', '/itens', $dados);
    }

    public function atualizarItemCardapio($id, array $dados)
    {
        return $this->makeRequest('PUT', "/itens/{$id}", $dados);
    }

    public function deletarItemCardapio($id, $restauranteId = null)
    {
        $dadosQuery = null;

        if (!empty($restauranteId)) {
            $dadosQuery = ['restauranteId' => $restauranteId];
        }

        return $this->makeRequest('DELETE', "/itens/{$id}", $dadosQuery, $dadosQuery !== null);
    }

    public function uploadImagemItem(UploadedFile $arquivo)
    {
        $url = rtrim($this->baseUrl, '/') . '/itens/upload';

        $request = Http::timeout(30);

        if (config('services.backend.skip_tls_verify', false)) {
            $request->withoutVerifying();
        }

        if (!empty($this->cookies)) {
            $cookieString = '';
            foreach ($this->cookies as $name => $value) {
                if ($cookieString) {
                    $cookieString .= '; ';
                }
                $cookieString .= "{$name}={$value}";
            }

            $request->withHeaders(['Cookie' => $cookieString]);
        }

        if ($this->token) {
            $request->withToken($this->token);
        } elseif ($this->basicAuthCredentials) {
            $request->withBasicAuth(
                $this->basicAuthCredentials['email'],
                $this->basicAuthCredentials['password']
            );
        }

        try {
            $response = $request
                ->attach(
                    'file',
                    file_get_contents($arquivo->getRealPath()),
                    $arquivo->getClientOriginalName()
                )
                ->post($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'status' => $response->status()
                ];
            }

            if ($response->status() === 401) {
                $this->clearToken();
                return [
                    'success' => false,
                    'error' => 'Não autorizado. Faça login novamente.',
                    'status' => 401
                ];
            }

            $errorData = $response->json();
            return [
                'success' => false,
                'error' => $errorData['message'] ?? 'Erro no upload da imagem',
                'status' => $response->status(),
                'data' => $errorData
            ];
        } catch (Exception $e) {
            Log::error('Erro ao enviar imagem para o backend', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erro de conexão com o servidor: ' . $e->getMessage(),
                'status' => 500
            ];
        }
    }

    /**
     * Lista pedidos do cliente/restaurante autenticado
     * Quando o restaurante está logado, retorna os pedidos do restaurante
     */
    public function listarPedidos()
    {
        $result = $this->makeRequest('GET', '/pedidos');
        return $result;
    }

    /**
     * Lista pedidos do restaurante autenticado
     * Usa o endpoint específico GET /pedidos/restaurante
     */
    public function listarPedidosDoRestaurante()
    {
        $result = $this->makeRequest('GET', '/pedidos/restaurante');
        return $result;
    }

    /**
     * Cria um novo pedido
     */
    public function criarPedido($pedidoData)
    {
        $result = $this->makeRequest('POST', '/pedidos', $pedidoData);
        return $result;
    }

    /**
     * Atualiza o status de um pedido (para clientes)
     */
    public function atualizarStatus($pedidoId, $status)
    {
        // O backend espera status como query parameter
        $url = "/pedidos/{$pedidoId}/status?status=" . urlencode($status);
        $result = $this->makeRequest('PUT', $url, null, false);
        return $result;
    }

    /**
     * Atualiza o status de um pedido (para restaurantes)
     * Usa o endpoint específico PUT /pedidos/{id}/status-restaurante
     */
    public function atualizarStatusRestaurante($pedidoId, $status)
    {
        // O backend espera status como query parameter no formato: PUT /pedidos/{id}/status-restaurante?status={status}
        $url = "/pedidos/{$pedidoId}/status-restaurante";
        $queryParams = ['status' => $status];
        
        Log::info('Atualizando status do pedido via backend', [
            'pedido_id' => $pedidoId,
            'novo_status' => $status,
            'url' => $url,
            'query_params' => $queryParams,
            'endpoint_completo' => rtrim($this->baseUrl, '/') . $url . '?status=' . urlencode($status)
        ]);
        
        // Usa makeRequest com useQueryString=true para garantir que o status seja enviado como query parameter
        $result = $this->makeRequest('PUT', $url, $queryParams, true);
        
        Log::info('Resultado da atualização de status', [
            'pedido_id' => $pedidoId,
            'status' => $status,
            'success' => $result['success'] ?? false,
            'error' => $result['error'] ?? null,
            'response_status' => $result['status'] ?? null
        ]);
        
        return $result;
    }

    /**
     * Faz login no backend e retorna o restaurante autenticado
     */
    public function login($email, $password)
    {
        $url = rtrim($this->baseUrl, '/') . '/restaurantes/login';
        
        // Log da requisição
        Log::info('Tentativa de login no backend', [
            'url' => $url,
            'email' => $email,
            'base_url' => $this->baseUrl
        ]);
        
        try {
            // O backend Java espera 'senha' ao invés de 'password'
            $requestData = [
                'email' => $email,
                'senha' => $password
            ];
            
            Log::debug('Dados da requisição de login', [
                'url' => $url,
                'data' => ['email' => $email, 'senha' => '***']
            ]);
            
            $httpClient = Http::timeout(30);

            if (config('services.backend.skip_tls_verify', false)) {
                $httpClient->withoutVerifying();
            }

            $response = $httpClient
                ->acceptJson()
                ->asJson()
                ->post($url, $requestData);

            // Log da resposta completa
            $headers = $response->headers();
            Log::info('Resposta do backend (login)', [
                'status' => $response->status(),
                'headers' => $headers,
                'set_cookie' => $headers['Set-Cookie'] ?? null,
                'body' => $response->body(),
                'json' => $response->json(),
                'successful' => $response->successful()
            ]);

            if ($response->successful()) {
                $restaurante = $response->json();
                
                // IMPORTANTE: Captura cookies JSESSIONID do Spring Security
                $cookiesDaResposta = [];
                
                // Tenta diferentes variações do header (case-insensitive)
                $setCookieHeaders = $headers['Set-Cookie'] ?? $headers['set-cookie'] ?? [];
                
                // Se não for array, tenta converter
                if (!is_array($setCookieHeaders) && !empty($setCookieHeaders)) {
                    $setCookieHeaders = [$setCookieHeaders];
                }
                
                Log::debug('Processando headers Set-Cookie', [
                    'headers_encontrados' => !empty($setCookieHeaders),
                    'count' => is_array($setCookieHeaders) ? count($setCookieHeaders) : 0,
                    'primeiro_header' => is_array($setCookieHeaders) && isset($setCookieHeaders[0]) ? substr($setCookieHeaders[0], 0, 100) : null
                ]);
                
                // Processa headers Set-Cookie
                if (is_array($setCookieHeaders)) {
                    foreach ($setCookieHeaders as $cookieHeader) {
                        // Extrai JSESSIONID do header Set-Cookie
                        // Formato: JSESSIONID=ABC123; Path=/; HttpOnly
                        if (is_string($cookieHeader)) {
                            if (preg_match('/JSESSIONID=([^;]+)/i', $cookieHeader, $matches)) {
                                $cookiesDaResposta['JSESSIONID'] = trim($matches[1]);
                                Log::debug('JSESSIONID encontrado', [
                                    'valor' => substr($cookiesDaResposta['JSESSIONID'], 0, 20) . '...'
                                ]);
                            }
                        }
                    }
                }
                
                // Armazena cookies para próximas requisições
                if (!empty($cookiesDaResposta)) {
                    $this->setCookies($cookiesDaResposta);
                    Log::info('Cookies de sessão capturados do login', [
                        'cookies' => array_keys($cookiesDaResposta),
                        'jsessionid_presente' => isset($cookiesDaResposta['JSESSIONID'])
                    ]);
                } else {
                    Log::warning('Nenhum cookie JSESSIONID encontrado na resposta do login');
                }
                
                // Verifica diferentes formas de autenticação
                $token = null;
                
                // 1. Verifica se há token no header Authorization (JWT)
                $authHeader = $response->header('Authorization');
                if ($authHeader) {
                    // Remove "Bearer " se existir
                    $token = str_replace('Bearer ', '', $authHeader);
                }
                
                // 2. Verifica se há token no corpo da resposta
                if (!$token && isset($restaurante['token'])) {
                    $token = $restaurante['token'];
                }
                
                // 3. Autenticação via sessão Spring Security (JSESSIONID)
                
                Log::info('Login bem-sucedido', [
                    'email' => $email,
                    'tem_token' => !empty($token),
                    'tem_cookie' => !empty($cookiesDaResposta),
                    'restaurante_id' => $restaurante['id'] ?? null
                ]);
                
                return [
                    'success' => true,
                    'restaurante' => $restaurante,
                    'token' => $token,
                    'cookies' => $cookiesDaResposta,
                    'status' => $response->status()
                ];
            }

            if ($response->status() === 401) {
                Log::warning('Login falhou - não autorizado', [
                    'email' => $email,
                    'status' => 401,
                    'response' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Email ou senha incorretos.',
                    'status' => 401,
                    'response_body' => $response->body()
                ];
            }

            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? $response->body() ?? 'Erro ao fazer login';
            
            Log::error('Erro no login', [
                'email' => $email,
                'status' => $response->status(),
                'error_data' => $errorData,
                'response_body' => $response->body()
            ]);
            
            return [
                'success' => false,
                'error' => $errorMessage,
                'status' => $response->status(),
                'data' => $errorData,
                'response_body' => $response->body()
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Erro de conexão ao fazer login no backend', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Erro de conexão com o servidor. Verifique se o backend está acessível em: ' . $url,
                'status' => 500,
                'connection_error' => true
            ];
        } catch (Exception $e) {
            Log::error('Erro ao fazer login no backend', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Erro de conexão com o servidor: ' . $e->getMessage(),
                'status' => 500
            ];
        }
    }

    /**
     * Testa a conexão com o backend
     */
    public function testConnection()
    {
        try {
            $response = Http::timeout(5)->get(rtrim($this->baseUrl, '/'));
            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }
}

