<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\Operator\PenjualanController;
use App\Http\Controllers\Admin\Verifikasi\UserController;
use App\Http\Controllers\Admin\MasterData\SektorController;
use App\Http\Controllers\Admin\MasterData\JenisBbmController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login.index');
});

Route::middleware(['guest'])->group(function () {
    // login
    Route::prefix('login')->name('login.')->group(function () {
        Route::get('/', [AuthenticationController::class, 'login'])
            ->name('index');
        Route::post('/', [AuthenticationController::class, 'authenticate'])
            ->name('store');
    });

    // register
    Route::prefix('register')->name('register.')->group(function () {
        Route::get('/', [AuthenticationController::class, 'register'])
            ->name('index');
        Route::post('/', [AuthenticationController::class, 'store'])
            ->name('store');

        // otp
        Route::prefix('otp')->name('otp.')->group(function () {
            Route::post('/generate', [AuthenticationController::class, 'generateOtp'])
                ->name('generate');
        });
    });

    // reset password
    Route::name('password.')->group(function () {
        Route::get('/forgot-password', [ResetPasswordController::class, 'index'])->name('request');
        Route::post('/forgot-password', [ResetPasswordController::class, 'request'])->name('email');
        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('reset');
        Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('update');
    });
});


// auth
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ADMINISTRATOR
    Route::middleware(['role:administrator'])->group(function () {
        // Verifikasi
        Route::prefix('verifikasi')->name('verifikasi.')->group(function () {
            // User
            Route::prefix('user')->name('user.')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::get('/{ulid}/show', [UserController::class, 'show'])->name('show');

                // ajax
                Route::post('/approve', [UserController::class, 'approve'])->name('approve');
                Route::post('/revisi', [UserController::class, 'revisi'])->name('revisi');
            });
        });

        // // Master Data
        Route::prefix('master-data')->name('master-data.')->group(function () {
            // Sektor
            Route::prefix('sektor')->name('sektor.')->group(function () {
                Route::get('/', [SektorController::class, 'index'])->name('index');
                Route::get('/create', [SektorController::class, 'create'])->name('create');
                Route::post('/', [SektorController::class, 'store'])->name('store');
                Route::get('/{ulid}/edit', [SektorController::class, 'edit'])->name('edit');
                Route::put('/{ulid}', [SektorController::class, 'update'])->name('update');
                Route::delete('/{ulid}', [SektorController::class, 'destroy'])->name('destroy');
            });
            // Jenis BBM
            Route::prefix('jenis-bbm')->name('jenis-bbm.')->group(function () {
                Route::get('/', [JenisBbmController::class, 'index'])->name('index');
                Route::get('/create', [JenisBbmController::class, 'create'])->name('create');
                Route::post('/', [JenisBbmController::class, 'store'])->name('store');
                Route::get('/{ulid}/edit', [JenisBbmController::class, 'edit'])->name('edit');
                Route::put('/{ulid}', [JenisBbmController::class, 'update'])->name('update');
                Route::delete('/{ulid}', [JenisBbmController::class, 'destroy'])->name('destroy');
            });
        });
    });

    // OPERATOR
    Route::middleware(['role:operator', 'is_berkas_persyaratan_verified'])->group(function () {
        Route::prefix('penjualan')->name('penjualan.')->group(function () {
            Route::get('/', [PenjualanController::class, 'index'])->name('index');
            Route::get('/{ulid}/show', [PenjualanController::class, 'show'])->name('show');
        });
    });


    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])
            ->name('index');
        Route::post('/', [ProfileController::class, 'store'])
            ->name('store');
    });

    // Logout
    Route::post('logout', [AuthenticationController::class, 'logout'])
        ->name('logout');
});
