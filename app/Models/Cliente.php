<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'telefone',
        'cpf',
        'email',
        'cep',
        'rua',
        'bairro',
        'cidade',
        'estado',
        'numero',
        'aceita_protecao_dados',
        'aceita_marketing',
        'aceita_atendimento'
    ];

    protected $casts = [
        'aceita_protecao_dados' => 'boolean',
        'aceita_marketing' => 'boolean',
        'aceita_atendimento' => 'boolean',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
