<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AccessStatistics extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->format('Y-m-d');
        $weekStart = now()->startOfWeek()->format('Y-m-d');
        $monthStart = now()->startOfMonth()->format('Y-m-d');
        $yearStart = now()->startOfYear()->format('Y-m-d');

        return [
            Stat::make('Hôm nay', Activity::where('date', $today)->sum('view_count'))
                ->description('Lượt xem hôm nay')
                ->color('success'),

            Stat::make('Tuần này', Activity::where('date', '>=', $weekStart)->sum('view_count'))
                ->description('Lượt xem tuần này')
                ->color('info'),

            Stat::make('Tháng này', Activity::where('date', '>=', $monthStart)->sum('view_count'))
                ->description('Lượt xem tháng này')
                ->color('warning'),

            Stat::make('Năm nay', Activity::where('date', '>=', $yearStart)->sum('view_count'))
                ->description('Lượt xem năm nay')
                ->color('danger'),
        ];
    }
}