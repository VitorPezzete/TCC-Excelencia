<?php

namespace App\Helpers;

use Carbon\Carbon;

class StoreHelper
{
    /**
     * Verifica se a loja está aberta com base no horário de funcionamento.
     * Horário: Segunda a Sábado, 07:00 – 19:00 | Domingo: Fechado
     */
    public static function isOpen(): bool
    {
        $now = Carbon::now('America/Sao_Paulo');

        // Domingo (0) = sempre fechado
        if ($now->dayOfWeek === Carbon::SUNDAY) {
            return false;
        }

        // Segunda (1) a Sábado (6): aberto das 07:00 às 19:00
        $minutos = $now->hour * 60 + $now->minute;
        return $minutos >= (7 * 60) && $minutos < (19 * 60);
    }

    /**
     * Retorna uma string legível com o horário de funcionamento.
     */
    public static function hoursLabel(): string
    {
        return 'Seg – Sáb: 07:00 às 19:00 | Dom: Fechado';
    }
}
