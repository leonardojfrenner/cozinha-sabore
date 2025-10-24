<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRestaurante extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'imagem_url'
    ];

    protected $casts = [
        'preco' => 'decimal:2',
    ];

    public function pedidoItens()
    {
        return $this->hasMany(PedidoItem::class);
    }
}
