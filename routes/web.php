<?php

use App\Http\Controllers\CertificateHealthMonitorController;
use App\Http\Controllers\CrawlerController;
use App\Http\Controllers\MonitorController;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');
    Route::get('/monitors', [ MonitorController::class, 'index' ])->name('monitors');
    Route::get('/monitors/new', [ MonitorController::class, 'create' ])->name('monitors.new');
    Route::post('/monitors/new', [ MonitorController::class, 'store' ])->name('monitors.new');
    Route::get('/monitors/show/{ID}', [ MonitorController::class, 'show' ])->name('monitors.show');
    Route::delete('/monitors/delete/{id}', [MonitorController::class, 'delete'])->name('monitors.delete');
});
