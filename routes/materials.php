<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Siswa\MaterialController;

// Use the proper routes defined in web.php, this route is no longer needed
// Route::prefix('siswa')->name('siswa.')->middleware(['web', 'auth'])->group(function () {
//     // Direct materials routes
//     Route::get('/materials', function () {
//         // If you have a MaterialController, use it directly
//         if (class_exists('App\Http\Controllers\Siswa\MaterialController')) {
//             $controller = app()->make('App\Http\Controllers\Siswa\MaterialController');
//             return $controller->index();
//         }
//         
//         // Otherwise, return a view directly
//         return view('siswa.materials.index');
//     })->name('materials.direct');
// });
    
// Add other direct routes if needed
