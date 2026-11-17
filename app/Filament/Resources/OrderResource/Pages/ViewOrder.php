<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \App\Filament\Support\WarrantyOrderActions::activate(),
            \App\Filament\Support\WarrantyOrderActions::link(),
            Actions\Action::make('receipt')
                ->label('Xem phiếu xác nhận')
                ->icon('heroicon-o-printer')
                ->url(fn (): string => route('orders.receipt', $this->record))
                ->openUrlInNewTab(),
            Actions\EditAction::make(),
        ];
    }
}
