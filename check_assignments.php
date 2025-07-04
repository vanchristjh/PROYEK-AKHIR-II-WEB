<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$columns = Schema::getColumnListing("assignments");
echo "Assignments table columns:\n";
print_r($columns);

if (Schema::hasColumn("assignments", "status")) {
    $type = Schema::getColumnType("assignments", "status");
    echo "\nStatus column type: " . $type . "\n";
}

