<?php

$root = dirname(__DIR__);
$files = [];
foreach (['app', 'config', 'lang', 'resources', 'routes', 'public', 'database/migrations', 'storage/app/public'] as $directory) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/'.$directory, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->isLink()) continue;
        $path = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
        if (str_starts_with($path, 'public/storage/') || str_starts_with($path, 'public/pay/') || $path === 'public/hot') continue;
        $files[$path] = hash_file('sha256', $file->getPathname());
    }
}
foreach (['artisan', 'composer.json', 'composer.lock', 'bootstrap/app.php', 'bootstrap/providers.php'] as $path) {
    if (is_file($root.'/'.$path)) $files[$path] = hash_file('sha256', $root.'/'.$path);
}
ksort($files);
echo json_encode($files, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
