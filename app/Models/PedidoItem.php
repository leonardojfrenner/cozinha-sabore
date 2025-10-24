<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'item_restaurante_id',
        'quantidade',
        'observacoes',
        'ingredientes_removidos',
        'ingredientes_adicionados'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function itemRestaurante()
    {
        return $this->belongsTo(ItemRestaurante::class);
    }
}
