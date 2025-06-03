<?php
// File untuk memeriksa struktur database dan isi data

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Fungsi untuk memeriksa struktur kolom tabel
function getTableColumns($tableName) {
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing($tableName);
    echo "Kolom tabel {$tableName}: " . implode(", ", $columns) . PHP_EOL;
    echo PHP_EOL;
}

// Fungsi untuk menampilkan jumlah data
function countRecords($tableName) {
    $count = \Illuminate\Support\Facades\DB::table($tableName)->count();
    echo "Jumlah data di {$tableName}: " . $count . PHP_EOL;
    echo PHP_EOL;
}

// Fungsi untuk menampilkan sampel data
function sampleData($tableName, $limit = 3) {
    $data = \Illuminate\Support\Facades\DB::table($tableName)->limit($limit)->get();
    echo "Sampel data dari {$tableName} ({$limit} baris):" . PHP_EOL;
    foreach ($data as $row) {
        echo json_encode($row, JSON_PRETTY_PRINT) . PHP_EOL;
    }
    echo PHP_EOL;
}

// Memeriksa tabel utama
$tables = ['users', 'roles', 'subjects', 'school_classes'];

foreach ($tables as $table) {
    echo "===========================================\n";
    echo "Tabel: {$table}\n";
    echo "===========================================\n";
    
    try {
        getTableColumns($table);
        countRecords($table);
        sampleData($table);
    } catch (\Exception $e) {
        echo "Error pada tabel {$table}: " . $e->getMessage() . PHP_EOL;
    }
    
    echo PHP_EOL;
}
