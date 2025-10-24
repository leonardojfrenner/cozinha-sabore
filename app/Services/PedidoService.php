<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class PedidoService
{
    private $pedidosData;

    public function __construct()
    {
        $this->loadPedidosData();
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
        return collect($this->pedidosData);
    }

    public function getPedidosByRestaurante($email)
    {
        return collect($this->pedidosData)->filter(function ($pedido) use ($email) {
            return $pedido['restaurante']['email'] === $email;
        });
    }

    public function getPedidoById($id)
    {
        return collect($this->pedidosData)->firstWhere('id', $id);
    }

    public function updatePedidoStatus($id, $status)
    {
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
        $restaurantes = $this->getRestaurantes();
        
        $restaurante = $restaurantes->firstWhere('email', $email);
        
        if ($restaurante && $password === '123456') {
            return $restaurante;
        }
        
        return null;
    }
}
