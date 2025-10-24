<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Restaurante extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nome',
        'cnpj',
        'telefone',
        'email',
        'rua',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'descricao',
        'horario',
        'lotacao',
        'site',
        'facebook',
        'instagram',
        'whatsapp',
        'cardapio_url',
        'logo_url',
        'banner_url',
        'aceita_comunicacao',
        'aceita_marketing',
        'aceita_protecao_dados',
        'avaliacao_media',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'aceita_comunicacao' => 'boolean',
        'aceita_marketing' => 'boolean',
        'aceita_protecao_dados' => 'boolean',
        'avaliacao_media' => 'decimal:1',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
