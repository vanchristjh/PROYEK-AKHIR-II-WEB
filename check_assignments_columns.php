<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$columns = Schema::getColumnListing("assignments");
echo "Assignments columns: " . json_encode($columns) . PHP_EOL;

