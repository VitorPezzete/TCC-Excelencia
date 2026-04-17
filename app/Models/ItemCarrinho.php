<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCarrinho extends Model
{
    use HasFactory;

    protected $table = 'itens_carrinho';

    protected $fillable = [
        'user_id',
        'produto_id',
        'quantidade',
        'observacoes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
