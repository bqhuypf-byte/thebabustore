<?php
namespace App\Filament\Pages;
use App\Models\Setting;
use App\Support\RepairServicesContent;
use App\Support\RepairContentSchema;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
class RepairCatalog extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Babu Care — Dịch vụ';
    protected static ?string $navigationGroup = 'Quản lý nội dung';
    protected static ?string $title = 'Babu Care — Dịch vụ & linh kiện';
    protected static string $view = 'filament.pages.repair-catalog';
    public ?array $data = [];
    public function mount(): void
    {
        $setting = Setting::query()->firstOrFail();
        $this->form->fill(['repair_services' => $setting->repair_services ?? RepairServicesContent::defaults(), 'repair_parts' => $setting->repair_parts ?? RepairServicesContent::partDefaults()]);
    }
    public function form(Form $form): Form
    {
        return $form->schema([Tabs::make('Nội dung')->tabs([RepairContentSchema::tab()])])->statePath('data');
    }
    public function save(): void
    {
        $state = $this->form->getState();
        Setting::query()->firstOrFail()->update(collect($state)->only(['repair_services', 'repair_parts'])->all());
        Notification::make()->title('Đã lưu nội dung Babu Care')->success()->send();
    }
}
