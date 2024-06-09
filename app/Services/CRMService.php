<?php

namespace App\Services;

use App\Models\QrUser;
use Illuminate\Support\Carbon;

class CRMService
{
    public function getQrActivationsInterval($activated = false)
    {
        $qrActivations = QrUser::whereDate('created_at', Carbon::now())
            ->when($activated, function ($query) {
                $query->where('active', 0);
            })
            ->get()->groupBy(function($item) {
            return Carbon::parse($item->created_at)->format('H');
        })->map->count()->toArray();

        $startDay = Carbon::today()->startOfDay(); // Начало текущего дня (00:00:00)
        $intervals = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $start = $startDay->copy()->addHours($hour)->format('H');
            $end = $startDay->copy()->addHours($hour + 1)->format('H');
            $intervals[$start . ':00 - ' . $end . ':00' ] = $qrActivations[$start] ?? 0;
        }

        return $intervals;
    }
}


