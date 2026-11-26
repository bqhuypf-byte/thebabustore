<?php
namespace App\Filament\Resources\PromotionCampaignResource\Pages;
use App\Filament\Resources\PromotionCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListPromotionCampaigns extends ListRecords {
    protected static string $resource=PromotionCampaignResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
