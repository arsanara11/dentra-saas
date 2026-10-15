<?php

use App\Http\Controllers\Clinic\AppointmentController;
use App\Http\Controllers\Clinic\DashboardController;
use App\Http\Controllers\Clinic\DentalRecordController;
use App\Http\Controllers\Clinic\DoctorController;
use App\Http\Controllers\Clinic\PatientController;
use App\Http\Controllers\Clinic\QueueController;
use App\Http\Controllers\ProfileController;
use App\Services\Queue\QueueService;
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

                // Doctors
                Route::resource('doctors', DoctorController::class);

                // Dental Records
                Route::resource('dental-records', DentalRecordController::class);

                // Queue Management
                Route::controller(QueueController::class)
                    ->prefix('queues')
                    ->name('queues.')
                    ->group(function () {

                        Route::get('/', 'index')
                            ->name('index');

                        Route::post('/', 'store')
                            ->middleware('throttle:30,1')
                            ->name('store');

                        // Must stay above the "{queue}" routes.
                        Route::post('/call-next', 'callNext')
                            ->middleware('throttle:60,1')
                            ->name('call-next');

                        Route::get('/{queue}', 'show')
                            ->whereNumber('queue')
                            ->name('show');

                        // call, recall, serve, complete, skip, no_show, cancel
                        Route::post('/{queue}/{action}', 'perform')
                            ->whereNumber('queue')
                            ->whereIn('action', array_keys(QueueService::ACTIONS))
                            ->middleware('throttle:60,1')
                            ->name('action');
                    });
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