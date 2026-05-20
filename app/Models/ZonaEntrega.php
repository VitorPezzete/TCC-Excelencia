<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZonaEntrega extends Model
{
    protected $table = 'zonas_entrega';

    protected $fillable = [
        'nome',
        'taxa',
        'bairros',
        'frete_gratis_acima',
        'ativo',
    ];

    protected $casts = [
        'bairros' => 'array',
        'taxa' => 'decimal:2',
        'frete_gratis_acima' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    /**
     * Scope para buscar apenas zonas ativas.
     */
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Encontra a zona de entrega para um bairro específico (case-insensitive).
     */
    public static function encontrarPorBairro(string $bairro): ?self
    {
        $bairroNormalizado = mb_strtolower(trim($bairro));

        return static::ativo()->get()->first(function ($zona) use ($bairroNormalizado) {
            $bairros = array_map(fn($b) => mb_strtolower(trim($b)), $zona->bairros ?? []);
            return in_array($bairroNormalizado, $bairros);
        });
    }
}
