<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderReceiptController extends Controller
{
    public function __invoke(Request $request, Order $order): View
    {
        $user = $request->user();
        abort_unless($user && ($order->user_id === $user->id || $user->canAccessPanel(Filament::getPanel('admin'))), 404);

        $order->load(['address', 'items.product_variant.product']);

        return view('orders.receipt', [
            'order' => $order,
            'settings' => Setting::query()->first(),
        ]);
    }
}
