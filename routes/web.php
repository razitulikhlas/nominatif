<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AfiliasiController;
use App\Http\Controllers\AnalisController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\CapemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NominatifController;
use App\Http\Controllers\PdfGenerateController;
use App\Http\Controllers\SendMessageController;
use Faker\Core\Barcode;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\UserController;

// Route::get('/', function () {
//     return redirect('/login');
// });


// Route::get('/login', [LoginController::class, 'index'])->name('login');
// Route::post('login', [LoginController::class, 'store']);
// Route::get('/login', 'LoginController@index')->name('login');

Route::get('logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/account',[AccountController::class,'index']);
    Route::get('/kredit',[SendMessageController::class,'index']);
    Route::resource('/number', AccountController::class);
    Route::resource('/barcode', BarcodeController::class);
    Route::resource('pdfgenerate', PdfGenerateController::class);
    Route::get('/nasabah/{norek}', [SendMessageController::class, 'showDetail'])->name('nasabah.detail');
    // Route::resource('/user', UserController::class);
    Route::get('/pdf/{norek}',[PdfGenerateController::class,'show'])->name('pdf.show');
    Route::resource('surats', SuratController::class);

    Route::resource('dashboard', DashboardController::class);

    Route::resource('nominatif', NominatifController::class);
    Route::resource('cabang', CabangController::class);
    Route::resource('capem', CapemController::class);


    Route::delete('/nominatif/{id}', [NominatifController::class, 'destroy'])->name('nominatif.destroy');
    Route::post('/nominatif/upload', [NominatifController::class, 'upload'])->name('nominatif.upload');
    Route::resource('afiliasi', AfiliasiController::class);
    Route::resource('analis', AnalisController::class);
    Route::post('/analis/upload', [AnalisController::class, 'upload'])->name('analis.upload');
    Route::get('/analisfilter/{id}', [AnalisController::class, 'getAnalis']);

    Route::post('/afiliasi/upload', [AfiliasiController::class, 'upload'])->name('afiliasi.upload');
    Route::get('/sendwa', [DashboardController::class, 'sendTunggakan'])->name('dasboard.sendwa');
    Route::get('/testa', [DashboardController::class, 'filter'])->name('testa');

    Route::resource('/user', UserController::class);
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});









