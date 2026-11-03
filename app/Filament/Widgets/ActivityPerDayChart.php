<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ActivityPerDayChart extends ChartWidget
{
    protected static ?string $heading = 'Tổng truy cập mỗi ngày trong tháng này';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        // Khởi tạo mảng ngày trong tháng với giá trị mặc định là 0
        $days = [];
        $labels = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $key = $cursor->toDateString();
            $days[$key] = 0;
            $labels[] = $cursor->format('d/m');
            $cursor->addDay();
        }

        // Lấy dữ liệu từ DB
        $viewData = Activity::selectRaw('date, SUM(view_count) as views')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('date')
            ->pluck('views', 'date');

        foreach ($viewData as $date => $views) {
            if (isset($days[$date])) {
                $days[$date] = $views;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Lượt truy cập',
                    'data' => array_values($days),
                    'borderColor' => '#8b5cf6', // tím
                    'backgroundColor' => 'rgba(139, 92, 246, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
