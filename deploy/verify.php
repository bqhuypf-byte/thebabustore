<?php

require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$result = [
    'framework' => app()->version(),
    'environment' => app()->environment(),
    'debug' => config('app.debug'),
    'url' => config('app.url'),
    'mail_transport' => config('mail.default'),
    'smtp_configured' => (bool) config('mail.mailers.smtp.host') && (bool) config('mail.mailers.smtp.username') && (bool) config('mail.mailers.smtp.password'),
    'google_configured' => (bool) config('services.google.client_id') && (bool) config('services.google.client_secret'),
    'hot_file_present' => file_exists(public_path('hot')),
    'storage_link' => is_link(public_path('storage')),
];
foreach (['products', 'product_variants', 'promotion_campaigns', 'promotion_campaign_items', 'users', 'orders', 'jobs'] as $table) {
    if (Illuminate\Support\Facades\Schema::hasTable($table)) {
        $result['row_counts'][$table] = Illuminate\Support\Facades\DB::table($table)->count();
    }
}
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;

