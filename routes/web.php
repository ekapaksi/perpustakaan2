<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Member\MemberController;
use App\Http\Controllers\Member\TempController;
use App\Http\Controllers\Member\BookingController;
use App\Http\Controllers\PinjamController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [MemberController::class, 'index'])->name('member.index')->middleware('isMember');
Route::get('detail-buku/{buku}', [MemberController::class, 'detailBuku'])->name('member.detailBuku')->middleware('isMember');


Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register-member', [AuthController::class, 'registerMember'])->name('registerMember');
    Route::post('/login-member', [AuthController::class, 'loginMember'])->name('loginMember');
});

Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profil', [DashboardController::class, 'tampilProfil'])->name('profil');
        Route::put('/profil', [DashboardController::class, 'updateProfil']);
        Route::get('/ganti-password', [DashboardController::class, 'tampilGantiPassword'])->name('ganti-password');
        Route::post('/ganti-password', [DashboardController::class, 'updateGantiPassword'])->name('ganti-password');
    });
    Route::prefix('admin/master')->name('admin.master.')->middleware(['auth', 'isAdmin'])->group(function () {
        Route::resource('kategori', KategoriController::class);
        Route::resource('buku', BukuController::class);
        Route::resource('user', UserController::class);
        Route::put('user/reset-password/{user}', [UserController::class, 'resetPassword'])->name('user.resetPassword');
    });
});
Route::middleware(['auth', 'isMember'])->group(function () {
    Route::prefix('member')->name('member.')->group(function () {
        Route::get('/profil', [MemberController::class, 'tampilProfil'])->name('profil');
        Route::put('/profil', [MemberController::class, 'updateProfil']);
        Route::get('/ganti-password', [MemberController::class, 'tampilGantiPassword'])->name('ganti-password');
        Route::put('/ganti-password', [MemberController::class, 'updateGantiPassword'])->name('ganti-password');
        Route::post('tambah-ke-keranjang', [TempController::class, 'tambahKeranjang'])->name('tambahKeranjang');
        Route::get('data-keranjang/{user}', [TempController::class, 'dataKeranjang'])->name('dataKeranjang');
        Route::get('data-keranjang/{user}', [TempController::class, 'dataKeranjang'])->name('dataKeranjang');
        Route::get('data-booking/{user}', [BookingController::class, 'dataBooking'])->name('dataBooking');
        Route::get('sedang-pinjam/{user}', [PinjamController::class, 'sedangPinjam'])->name('sedangPinjam');
        Route::get('riwayat-pinjam/{user}', [PinjamController::class, 'riwayatPinjam'])->name('riwayatPinjam');
        Route::delete('hapus-keranjang/{buku}/{user}', [TempController::class, 'hapusKeranjang'])->name('hapusKeranjang');
        Route::post('simpan-booking', [TempController::class, 'simpanBooking'])->name('simpanBooking');
        Route::get('booking-pdf/{user}', [BookingController::class, 'bookingPdf'])->name('bookingPdf');
    });
});

Route::prefix('admin/transaksi')->name('admin.transaksi.')->middleware(['auth', 'isAdmin'])->group(function () {
    Route::resource('booking', BookingController::class);
    Route::resource('peminjaman', PinjamController::class);
    Route::get('pengembalian', [PinjamController::class, 'pengembalian_index'])->name('peminjaman.pengembalian');

    Route::get('transaksi/peminjaman/data', [PinjamController::class, 'getData'])->name('peminjaman.data');
    Route::get('export-pdf-pinjam', [PinjamController::class, 'exportPdfPinjam'])->name('pinjam.exportPdfPinjam');
    Route::get('export-excel-pinjam', [PinjamController::class, 'exportExcelPinjam'])->name('pinjam.exportExcelPinjam');
    Route::put(
        'pinjam/kembalikanBuku/{no_pinjam}/{id_buku}',
        [PinjamController::class, 'kembalikanBuku']
    )->name('pinjam.kembalikanBuku');
});
