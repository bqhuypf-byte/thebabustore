<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_storefront')
                ->label('Xem trên web')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn () => url('/product/' . $this->record->slug))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make()
                ->label('Xóa sản phẩm'),
        ];
    }
}
