<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurante_id',
        'cliente_id',
        'observacoes_gerais',
        'status',
        'criado_em'
    ];

    protected $casts = [
        'criado_em' => 'datetime',
    ];

    const STATUS_NOVO = 'NOVO';
    const STATUS_CONCLUIDO = 'CONCLUIDO';

    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function itens()
    {
        return $this->hasMany(PedidoItem::class);
    }
}
