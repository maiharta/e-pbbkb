<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Operator\SspdController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\Operator\SptpdController;
use App\Http\Controllers\Operator\PelaporanController;
use App\Http\Controllers\Operator\PembelianController;
use App\Http\Controllers\Operator\PenjualanController;
use App\Http\Controllers\Operator\TransactionController;
use App\Http\Controllers\Admin\MasterData\CutiController;
use App\Http\Controllers\Admin\Verifikasi\UserController;
use App\Http\Controllers\Admin\PengaturanSistemController;
use App\Http\Controllers\Admin\MasterData\SektorController;
use App\Http\Controllers\Admin\MasterData\JenisBbmController;
use App\Http\Controllers\Admin\Verifikasi\PelaporanController as AdminPelaporanController;

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
    Route::get('/api/dashboard/stats', [App\Http\Controllers\DashboardController::class, 'getStats']);

    // ADMINISTRATOR
    Route::middleware(['role:administrator'])->group(function () {
        // Verifikasi
        Route::prefix('verifikasi')->name('verifikasi.')->group(function () {
            // Pelaporan
            Route::prefix('pelaporan')->name('pelaporan.')->group(function () {
                Route::get('/', [AdminPelaporanController::class, 'index'])->name('index');
                Route::get('/{ulid}/show', [AdminPelaporanController::class, 'show'])->name('show');

                // ajax
                Route::post('/approve', [AdminPelaporanController::class, 'approve'])->name('approve');
                Route::post('/revisi', [AdminPelaporanController::class, 'revisi'])->name('revisi');

                // table
                Route::get('/{ulid}/penjualan/table', [AdminPelaporanController::class, 'penjualanTable'])->name('penjualan.table');
                Route::get('/{ulid}/pembelian/table', [AdminPelaporanController::class, 'pembelianTable'])->name('pembelian.table');
            });
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
            // Cuti
            Route::prefix('cuti')->name('cuti.')->group(function () {
                Route::get('/', [CutiController::class, 'index'])->name('index');
                Route::get('/create', [CutiController::class, 'create'])->name('create');
                Route::post('/', [CutiController::class, 'store'])->name('store');
                Route::get('/{ulid}/edit', [CutiController::class, 'edit'])->name('edit');
                Route::put('/{ulid}', [CutiController::class, 'update'])->name('update');
                Route::delete('/{ulid}', [CutiController::class, 'destroy'])->name('destroy');
            });
        });
        // Laporan
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\LaporanController::class, 'index'])->name('index');
            Route::get('/export-excel', [App\Http\Controllers\Admin\LaporanController::class, 'exportExcel'])->name('export-excel');
        });
        // Pengaturan Sistem
        Route::prefix('pengaturan-sistem')->name('pengaturan-sistem.')->group(function () {
            Route::get('/', [PengaturanSistemController::class, 'index'])->name('index');
            Route::put('/', [PengaturanSistemController::class, 'update'])->name('update');
        });
    });

    // OPERATOR
    Route::middleware(['role:operator', 'is_berkas_persyaratan_verified'])->group(function () {
        // Pelaporan
        Route::prefix('pelaporan')->name('pelaporan.')->group(function () {
            Route::get('/', [PelaporanController::class, 'index'])->name('index');
            Route::post('/{ulid?}', [PelaporanController::class, 'send'])->name('send');


            // Pembelian
            Route::prefix('pembelian')->name('pembelian.')->group(function () {
                Route::get('/{ulid}', [PembelianController::class, 'index'])->name('index');
                Route::get('/{ulid}/show', [PembelianController::class, 'show'])->name('show');
                Route::middleware('ensure_pelaporan_is_not_send_to_admin')->group(function () {
                    Route::get('/{ulid}/create', [PembelianController::class, 'create'])->name('create');
                    Route::post('/{ulid}/store', [PembelianController::class, 'store'])->name('store');
                    Route::get('/{ulid}/edit/{pembelian}', [PembelianController::class, 'edit'])->name('edit');
                    Route::put('/{ulid}/{pembelian}', [PembelianController::class, 'update'])->name('update');
                    Route::delete('/{ulid}/{pembelian}', [PembelianController::class, 'destroy'])->name('destroy');
                    Route::post('/{ulid}/import', [PembelianController::class, 'import'])->name('import');
                    Route::get('/download/template-import', [PembelianController::class, 'downloadTemplateImport'])->name('download-template-import');
                });
            });

            // Penjualan
            Route::prefix('penjualan')->name('penjualan.')->group(function () {
                Route::get('/{ulid}', [PenjualanController::class, 'index'])->name('index');
                Route::get('/{ulid}/show', [PenjualanController::class, 'show'])->name('show');
                Route::middleware(['ensure_pelaporan_is_not_send_to_admin'])->group(function () {
                    Route::get('/{ulid}/create', [PenjualanController::class, 'create'])->name('create');
                    Route::post('/{ulid}/store', [PenjualanController::class, 'store'])->name('store');
                    Route::get('/{ulid}/edit/{penjualan}', [PenjualanController::class, 'edit'])->name('edit');
                    Route::put('/{ulid}/{penjualan}', [PenjualanController::class, 'update'])->name('update');
                    Route::delete('/{ulid}/{penjualan}', [PenjualanController::class, 'destroy'])->name('destroy');
                    Route::post('/{ulid}/import', [PenjualanController::class, 'import'])->name('import');
                    Route::get('/download/template-import', [PenjualanController::class, 'downloadTemplateImport'])->name('download-template-import');
                });
            });

            Route::middleware(['ensure_pelaporan_is_verified'])->group(function () {
                // SPTPD
                Route::prefix('sptpd')->name('sptpd.')->group(function () {
                    Route::get('/{ulid}', [SptpdController::class, 'index'])->name('index');
                    Route::get('/pelaporan/sptpd/download/{ulid}', [App\Http\Controllers\Operator\SptpdController::class, 'downloadSptpd'])
                        ->name('download');
                    // ajax
                    Route::post('/cancel/{ulid?}', [SptpdController::class, 'cancel'])->name('cancel');
                    Route::post('/approve/{ulid?}', [SptpdController::class, 'approve'])->name('approve');
                });
                // SSPD
                Route::prefix('sspd')->name('sspd.')->group(function () {
                    Route::get('/{ulid}', [SspdController::class, 'index'])->name('index');
                    Route::get('/download/bukti-bayar/{ulid}', [SspdController::class, 'downloadBuktiBayar'])->name('download-bukti-bayar');
                    Route::get('/download/sspd/{ulid}', [SspdController::class, 'downloadSspd'])->name('download-sspd');
                });
            });
        });

        // Data Transaksi
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [TransactionController::class, 'index'])->name('index');
            Route::get('/detail-invoice/{ulid?}', [TransactionController::class, 'showInvoice'])->name('show-invoice');
            Route::get('/{ulid}', [TransactionController::class, 'show'])->name('show');
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
