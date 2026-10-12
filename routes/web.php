<?php

use App\Http\Controllers\Clinic\AppointmentController;
use App\Http\Controllers\Clinic\DashboardController;
use App\Http\Controllers\Clinic\PatientController;
use App\Http\Controllers\Clinic\QueueController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Clinic Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'active.tenant'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Clinic Modules
        |--------------------------------------------------------------------------
        */

        Route::prefix('clinic')
            ->name('clinic.')
            ->group(function () {

                // Patients
                Route::resource('patients', PatientController::class);

                // Appointments
                Route::resource('appointments', AppointmentController::class);

                // Queue Management
                Route::get('/queues', [QueueController::class, 'index'])
                    ->name('queues.index');

                Route::post('/queues', [QueueController::class, 'store'])
                    ->name('queues.store');

                Route::patch('/queues/{queue}/status', [QueueController::class, 'updateStatus'])
                    ->name('queues.status');
            });
    });

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';