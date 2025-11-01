<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ApiPedidoService;
use Illuminate\Support\Facades\Log;

class TestBackendConnection extends Command
{
    protected $signature = 'backend:test {email?} {password?}';
    protected $description = 'Testa a conexão e login com o backend Java';

    public function handle(ApiPedidoService $apiService)
    {
        $this->info('🔍 Testando conexão com o backend...');
        
        $baseUrl = config('services.backend.url', 'http://52.201.117.189:8080');
        $this->info("URL do backend: {$baseUrl}");
        
        // Testa conexão básica
        $this->info("\n📡 Testando conectividade...");
        $isConnected = $apiService->testConnection();
        
        if ($isConnected) {
            $this->info('✅ Backend está acessível!');
        } else {
            $this->error('❌ Não foi possível conectar ao backend!');
            $this->error('Verifique se a URL está correta e se o backend está rodando.');
            return 1;
        }
        
        // Testa login se credenciais foram fornecidas
        if ($this->argument('email') && $this->argument('password')) {
            $email = $this->argument('email');
            $password = $this->argument('password');
            
            $this->info("\n🔐 Testando login...");
            $this->info("Email: {$email}");
            
            $result = $apiService->login($email, $password);
            
            if ($result['success']) {
                $this->info('✅ Login realizado com sucesso!');
                $this->info('Restaurante ID: ' . ($result['restaurante']['id'] ?? 'N/A'));
                $this->info('Restaurante Nome: ' . ($result['restaurante']['nome'] ?? 'N/A'));
                
                if ($result['token']) {
                    $this->info('Token: ' . substr($result['token'], 0, 50) . '...');
                } else {
                    $this->info('⚠️ Nenhum token retornado (será usado sessão/cookies)');
                }
                
                if (isset($result['cookies']) && !empty($result['cookies'])) {
                    $this->info('Cookies: ' . implode(', ', array_keys($result['cookies'])));
                }
                
                // Testa listar pedidos após login
                $this->info("\n📦 Testando listagem de pedidos...");
                $pedidosResult = $apiService->listarPedidosDoRestaurante();
                
                if ($pedidosResult['success']) {
                    $pedidos = $pedidosResult['data'] ?? [];
                    $this->info('✅ Pedidos listados com sucesso!');
                    $this->info('Total de pedidos: ' . count($pedidos));
                    
                    if (count($pedidos) > 0) {
                        $this->info("\nPrimeiros pedidos:");
                        foreach (array_slice($pedidos, 0, 3) as $pedido) {
                            $this->info("  - Pedido #" . ($pedido['id'] ?? 'N/A') . 
                                      " | Status: " . ($pedido['status'] ?? 'N/A') .
                                      " | Restaurante: " . ($pedido['restaurante']['nome'] ?? 'N/A'));
                        }
                    } else {
                        $this->warn('⚠️ Nenhum pedido encontrado para este restaurante');
                    }
                } else {
                    $this->error('❌ Erro ao listar pedidos!');
                    $this->error('Erro: ' . ($pedidosResult['error'] ?? 'Erro desconhecido'));
                    $this->error('Status HTTP: ' . ($pedidosResult['status'] ?? 'N/A'));
                    
                    if (isset($pedidosResult['response_body'])) {
                        $this->error('Resposta: ' . $pedidosResult['response_body']);
                    }
                }
            } else {
                $this->error('❌ Login falhou!');
                $this->error('Erro: ' . ($result['error'] ?? 'Erro desconhecido'));
                $this->error('Status HTTP: ' . ($result['status'] ?? 'N/A'));
                
                if (isset($result['response_body'])) {
                    $this->error('Resposta do backend: ' . $result['response_body']);
                }
                
                return 1;
            }
        } else {
            $this->info("\n💡 Para testar login, use: php artisan backend:test email@exemplo.com senha123");
        }
        
        $this->info("\n✅ Teste concluído!");
        return 0;
    }
}

