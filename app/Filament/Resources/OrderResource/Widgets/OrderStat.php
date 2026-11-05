<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class OrderStat extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $today = Carbon::today();
        $ordersToday = Order::query()->whereDate('created_at', $today)->count();
        $revenueToday = (float) Order::query()->whereDate('created_at', $today)->sum('grand_total');
        $pendingOrders = Order::query()->whereIn('status', ['new', 'pending'])->count();
        $totalRevenue = (float) Order::query()
            ->whereNotIn('status', ['cancelled', 'canceled'])
            ->sum('grand_total');

        return [
            Stat::make('Đơn hàng hôm nay', $ordersToday)
                ->description(Number::currency($revenueToday, 'VND') . ' doanh thu')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->icon('heroicon-o-shopping-bag'),
            Stat::make('Lượt truy cập hôm nay', '—')
                ->description('Sẵn sàng kết nối Analytics')
                ->descriptionIcon('heroicon-m-eye')
                ->icon('heroicon-o-eye'),
            Stat::make('Tổng lượt truy cập', '—')
                ->description('Sẵn sàng kết nối Analytics')
                ->descriptionIcon('heroicon-m-users')
                ->icon('heroicon-o-globe-alt'),
            Stat::make('Đơn chờ xác nhận', $pendingOrders)
                ->description('Ưu tiên xác nhận & đóng gói')
                ->descriptionIcon('heroicon-m-clock')
                ->icon('heroicon-o-clock'),
            Stat::make('Tổng doanh thu ghi nhận', Number::currency($totalRevenue, 'VND'))
                ->description('Từ tất cả đơn hàng hợp lệ')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->icon('heroicon-o-banknotes'),
            Stat::make('Tổng số đơn hàng', Order::query()->count())
                ->description('Đơn hàng lưu trữ trong DB')
                ->descriptionIcon('heroicon-m-circle-stack')
                ->icon('heroicon-o-archive-box'),
            Stat::make('Khách hàng (Store)', User::query()->where('is_admin', false)->count())
                ->description(User::query()->where('is_admin', true)->count() . ' quản trị viên CMS')
                ->descriptionIcon('heroicon-m-user-group')
                ->icon('heroicon-o-users'),
            Stat::make('Kho sản phẩm', Product::query()->count())
                ->description(Category::query()->count() . ' danh mục')
                ->descriptionIcon('heroicon-m-tag')
                ->icon('heroicon-o-cube'),
        ];
    }
}
