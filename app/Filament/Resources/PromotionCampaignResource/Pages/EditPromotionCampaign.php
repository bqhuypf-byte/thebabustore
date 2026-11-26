<?php
namespace App\Filament\Resources\PromotionCampaignResource\Pages;
use App\Filament\Resources\PromotionCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditPromotionCampaign extends EditRecord {
    protected static string $resource=PromotionCampaignResource::class;
    protected function getHeaderActions(): array { return [Actions\Action::make('open')->label('Mở trang chiến dịch')->url(fn()=> $this->record->publicUrl())->openUrlInNewTab(),Actions\DeleteAction::make()]; }
}
