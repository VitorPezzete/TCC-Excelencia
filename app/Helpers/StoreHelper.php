<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StoreHelper
{
    const DEFAULT_SCHEDULE = [
        'days'  => [1, 2, 3, 4, 5, 6], // Seg–Sáb
        'open'  => '07:00',
        'close' => '19:00',
    ];

    const DAY_NAMES = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

    public static function getSchedule(): array
    {
        try {
            $json = DB::table('settings')->where('key', 'store_schedule')->value('value');
            if ($json) {
                $decoded = json_decode($json, true);
                if (is_array($decoded) && isset($decoded['days'], $decoded['open'], $decoded['close'])) {
                    $decoded['days'] = array_map('intval', $decoded['days']);
                    return $decoded;
                }
            }
        } catch (\Exception $e) {}

        return self::DEFAULT_SCHEDULE;
    }

    public static function isOpen(): bool
    {
        $schedule = self::getSchedule();
        $now = Carbon::now('America/Sao_Paulo');

        if (!in_array($now->dayOfWeek, $schedule['days'])) {
            return false;
        }

        $openMinutes    = self::timeToMinutes($schedule['open']);
        $closeMinutes   = self::timeToMinutes($schedule['close']);
        $currentMinutes = $now->hour * 60 + $now->minute;

        return $currentMinutes >= $openMinutes && $currentMinutes < $closeMinutes;
    }

    public static function hoursLabel(): string
    {
        $schedule = self::getSchedule();
        $openDays = array_map(fn($d) => self::DAY_NAMES[$d] ?? '?', $schedule['days']);
        $daysStr  = implode(', ', $openDays);
        return "{$daysStr}: {$schedule['open']}–{$schedule['close']}";
    }

    private static function timeToMinutes(string $time): int
    {
        [$h, $m] = explode(':', $time . ':00');
        return (int)$h * 60 + (int)$m;
    }
}
