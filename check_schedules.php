<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

\ = new Illuminate\Foundation\Application(
    realpath(__DIR__)
);
\->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

\ = Schema::getColumnListing('schedules');
echo \
Table
schedules
has
the
following
columns:\\n\;
foreach (\ as \) {
    \ = DB::getSchemaBuilder()->getColumnType('schedules', \);
    echo \-
\$column
\$type
\\n\;
}

