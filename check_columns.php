<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$columns = DB::select("SHOW COLUMNS FROM schedules");
foreach ($columns as $column) {
    echo $column->Field . "\n";
}

