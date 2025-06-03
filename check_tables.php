<?php
// File untuk memeriksa struktur tabel materials

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Periksa apakah tabel materials ada
if (\Illuminate\Support\Facades\Schema::hasTable('materials')) {
    // Dapatkan daftar kolom
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('materials');
    echo "Kolom-kolom pada tabel materials:\n";
    echo implode(", ", $columns) . "\n";
} else {
    echo "Tabel materials tidak ditemukan.\n";
}

// Periksa tabel assignments
if (\Illuminate\Support\Facades\Schema::hasTable('assignments')) {
    // Dapatkan daftar kolom
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('assignments');
    echo "\nKolom-kolom pada tabel assignments:\n";
    echo implode(", ", $columns) . "\n";
} else {
    echo "\nTabel assignments tidak ditemukan.\n";
}

// Periksa tabel quizzes
if (\Illuminate\Support\Facades\Schema::hasTable('quizzes')) {
    // Dapatkan daftar kolom
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('quizzes');
    echo "\nKolom-kolom pada tabel quizzes:\n";
    echo implode(", ", $columns) . "\n";
} else {
    echo "\nTabel quizzes tidak ditemukan.\n";
}
