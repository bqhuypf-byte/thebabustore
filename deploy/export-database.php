<?php

// Run locally; creates private deployment inputs without printing secrets.
require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$config = config('database.connections.mysql');
$dump = new Symfony\Component\Process\Process([
    'C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysqldump.exe',
    '--host='.$config['host'], '--port='.$config['port'], '--user='.$config['username'],
    '--single-transaction', '--no-tablespaces', '--set-gtid-purged=OFF',
    '--default-character-set=utf8mb4', '--result-file='.__DIR__.'/database.private.sql',
    $config['database'],
], null, ['MYSQL_PWD' => $config['password']]);
$dump->setTimeout(300);
$dump->mustRun();
$dbPassword = bin2hex(random_bytes(24));
$rootPassword = bin2hex(random_bytes(24));
file_put_contents(__DIR__.'/.database.env', "MYSQL_DATABASE=thebabustore\nMYSQL_USER=babu\nMYSQL_PASSWORD=$dbPassword\nMYSQL_ROOT_PASSWORD=$rootPassword\n");
$env = file_get_contents(dirname(__DIR__).'/.env');
$overrides = [
    'APP_ENV'=>'production', 'APP_DEBUG'=>'false', 'APP_URL'=>'https://thebabustore.vn',
    'DB_HOST'=>'db', 'DB_PORT'=>'3306', 'DB_DATABASE'=>'thebabustore',
    'DB_USERNAME'=>'babu', 'DB_PASSWORD'=>$dbPassword,
    'SESSION_SECURE_COOKIE'=>'true', 'SESSION_DOMAIN'=>'null',
    'GOOGLE_REDIRECT'=>'https://thebabustore.vn/auth/google/callback',
    // Keep staging from sending mail; enable verified SMTP at cutover.
    'MAIL_MAILER'=>'log',
];
foreach ($overrides as $key=>$value) {
    $env = preg_replace('/^'.preg_quote($key, '/').'=.*\R?/m', '', $env);
    $env .= "\n$key=$value\n";
}
file_put_contents(__DIR__.'/production.private.env', $env);
echo "Database snapshot and production environment prepared.\n";

