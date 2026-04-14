<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    protected $table = 'enderecos';
    protected $fillable = [
        'user_id',
        'nome',
        'cep',
        'numero',
        'complemento',
        'padrao',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
