<?php

use App\Http\Controllers\auth\user\usercontroller;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return ['Laravel' => app()->version()];
// });

// require __DIR__.'/auth.php';


Route::get('/register', [usercontroller::class, 'showregistrationform'])->name('register');
Route::post('/register', [usercontroller::class, 'store']);
Route::get('/verification', [usercontroller::class, 'showVerificationForm'])->name('verification.show');
Route::post('/verification', [usercontroller::class, 'verifyCode'])->name('verification.verify');