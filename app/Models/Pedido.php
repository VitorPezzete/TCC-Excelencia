<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $fillable = [
        'user_id',
        'endereco_id',
        'cupom_id',
        'status',
        'subtotal',
        'desconto',
        'taxa_entrega',
        'total',
        'observacoes'
    ];
    
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function endereco() {
        return $this->belongsTo(Endereco::class);
    }

    public function itens() {
        return $this->hasMany(ItemPedido::class);
    }

    public function pagamento() {
        return $this->hasOne(Pagamento::class);
    }

    public function avaliacao() {
        return $this->hasOne(Avaliacao::class);
    }
}
