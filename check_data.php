<?php
require 'vendor/autoload.php';
\ = require_once 'bootstrap/app.php';
\->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
echo 'Users: ' . DB::table('users')->count() . PHP_EOL;
echo 'Roles: ' . DB::table('roles')->count() . PHP_EOL;
echo 'Subjects: ' . DB::table('subjects')->count() . PHP_EOL;
echo 'School Classes: ' . DB::table('school_classes')->count() . PHP_EOL;

