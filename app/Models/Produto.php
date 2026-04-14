<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $table = 'produtos';
    protected $fillable = [
        'categoria_id',
        'nome',
        'descricao',
        'preco',
        'imagem',
        'destaque',
        'ativo'
    ];
    
    public function categoria() {
        return $this->belongsTo(Categoria::class);
    }
}
