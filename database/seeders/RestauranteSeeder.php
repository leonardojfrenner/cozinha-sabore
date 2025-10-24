<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurante;

class RestauranteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dados de teste baseados no JSON
        $restaurantes = [
            [
                'nome' => 'Cozinha da Mãe',
                'cnpj' => '78965896589745',
                'telefone' => '13996083999',
                'email' => 'cozinha@email.com',
                'rua' => 'Rua Tupi',
                'numero' => 633,
                'bairro' => 'Tupi',
                'cidade' => 'Praia Grande',
                'estado' => 'SP',
                'cep' => '11703260',
                'descricao' => 'O Melhor Acarajé da Cidade',
                'horario' => 'Seg-Sex: 12h-20h',
                'lotacao' => 12,
                'password' => bcrypt('123456'),
                'aceita_comunicacao' => true,
                'aceita_marketing' => true,
                'aceita_protecao_dados' => true,
                'avaliacao_media' => 2.6
            ],
            [
                'nome' => 'Temakeria',
                'cnpj' => '47896523658974',
                'telefone' => '13996083999',
                'email' => 'temaki@email.com',
                'rua' => 'Avenida Casemiro Domcev',
                'numero' => 101,
                'bairro' => 'Nova Mirim',
                'cidade' => 'Praia Grande',
                'estado' => 'SP',
                'cep' => '11717037',
                'descricao' => 'Melhor Temaki da Cidade',
                'horario' => 'Seg - Qui: 18h-24h',
                'lotacao' => 8,
                'password' => bcrypt('123456'),
                'aceita_comunicacao' => true,
                'aceita_marketing' => true,
                'aceita_protecao_dados' => true,
                'avaliacao_media' => 0
            ]
        ];

        foreach ($restaurantes as $restaurante) {
            Restaurante::create($restaurante);
        }
    }
}
