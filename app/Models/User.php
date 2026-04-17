<?php

namespace App\Models;

use App\Models\Endereco;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function enderecos()
    {
        return $this->hasMany(Endereco::class);
    }

    public function itensCarrinho()
    {
        return $this->hasMany(ItemCarrinho::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}