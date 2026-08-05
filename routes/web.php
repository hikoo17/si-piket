<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PiketController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/piket/upload', [PiketController::class, 'create'])->name('piket.upload.form');
    Route::post('/piket/upload', [PiketController::class, 'storeUpload'])->middleware('throttle:5,1')->name('piket.upload');
    Route::get('/schools', [SchoolController::class, 'index'])->middleware('role:admin')->name('schools.index');
    Route::get('/schools/{school}/edit', [SchoolController::class, 'edit'])->middleware('role:admin')->name('schools.edit');
    Route::put('/schools/{school}', [SchoolController::class, 'update'])->middleware('role:admin')->name('schools.update');
    Route::post('/schools/{school}/test-whatsapp', [SchoolController::class, 'testWhatsapp'])->middleware('role:admin')->name('schools.test-whatsapp');
    Route::resource('classes', SchoolClassController::class)->middleware('role:admin');
    Route::resource('students', StudentController::class)->except('show')->middleware('role:admin');
    Route::resource('users', UserController::class)->except('show')->middleware('role:admin');
    Route::resource('schedules', ScheduleController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])->middleware('role:admin,km');
    Route::get('/verification', [VerificationController::class, 'index'])->middleware('role:admin,guru,km')->name('verification.index');
    Route::patch('/verification/{log}/approve', [VerificationController::class, 'approve'])->middleware('role:admin,guru,km')->name('verification.approve');
    Route::patch('/verification/{log}/reject', [VerificationController::class, 'reject'])->middleware('role:admin,guru,km')->name('verification.reject');
    Route::get('/reports', [ReportController::class, 'index'])->middleware('role:admin,guru,km')->name('reports.index');
    Route::get('/reports/{log}', [ReportController::class, 'show'])->middleware('role:admin,guru,km')->name('reports.show');
    Route::get('/reports.csv', [ReportController::class, 'csv'])->middleware('role:admin,guru,km')->name('reports.csv');
    Route::get('/reports.pdf', [ReportController::class, 'pdf'])->middleware('role:admin,guru,km')->name('reports.pdf');
});
