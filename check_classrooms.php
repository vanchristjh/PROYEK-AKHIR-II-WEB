<?php
require 'vendor/autoload.php';
\ = require __DIR__.'/bootstrap/app.php';
\->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Enable query logging
\Illuminate\Support\Facades\DB::enableQueryLog();

// Get classroom count
\ = \Illuminate\Support\Facades\DB::table('classrooms')->get();
echo \
Classrooms
count:
\.\->count().PHP_EOL;

// Show classroom data
if (\->count() > 0) {
    echo \First
classroom:
\.print_r(\->first(), true).PHP_EOL;
}

// Show the table structure
\ = \Illuminate\Support\Facades\Schema::getColumnListing('classrooms');
echo \Classrooms
table
has
columns:
\.implode(', ', \).PHP_EOL;

// Print queries executed
\ = \Illuminate\Support\Facades\DB::getQueryLog();
echo \Queries:\.PHP_EOL;
foreach (\ as \) {
    echo \['query'].PHP_EOL;
}

