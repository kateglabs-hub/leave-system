<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ReportController;

Route::get('/', function(){
    return redirect('/dashboard');
});

// Auth routes will be provided by Breeze (after composer install & breeze install)

Route::middleware(['auth'])->group(function(){
    Route::get('/dashboard', function(){
        return view('dashboard');
    })->name('dashboard');

    Route::get('/leaves', [LeaveController::class,'index']);
    Route::post('/leaves', [LeaveController::class,'store']);
    Route::put('/leaves/{id}/status', [LeaveController::class,'updateStatus']);

    Route::get('/reports/individual/{user?}', [ReportController::class,'individual']);
    Route::get('/reports/company', [ReportController::class,'company']);
});
