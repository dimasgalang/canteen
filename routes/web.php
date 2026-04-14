<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\CanteenController;
use App\Http\Controllers\DuplicateScannerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\SignaturePadController;
use App\Http\Controllers\SpeechController;
use App\Http\Controllers\SysLogController;
use App\Http\Controllers\UserController;
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

Route::get('/', [LoginController::class, 'login'])->name('/');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/canteen/canteen', [CanteenController::class, 'canteen'])->name('canteen.canteen');
Route::get('/canteen/scancanteen1', [CanteenController::class, 'scancanteen1'])->name('canteen.scancanteen1');
Route::get('/canteen/showcanteen1', [CanteenController::class, 'showcanteen1'])->name('canteen.showcanteen1');
Route::get('/canteen/scancanteen2', [CanteenController::class, 'scancanteen2'])->name('canteen.scancanteen2');
Route::get('/canteen/showcanteen2', [CanteenController::class, 'showcanteen2'])->name('canteen.showcanteen2');

Route::get('/scanner/barcodecanteen1', [ScannerController::class, 'barcodecanteen1'])->name('scanner.barcodecanteen1');
Route::get('/scanner/barcodecanteen2', [ScannerController::class, 'barcodecanteen2'])->name('scanner.barcodecanteen2');
Route::get('/scanner/barcodescanning1', [ScannerController::class, 'barcodescanning1'])->name('scanner.barcodescanning1');
Route::get('/scanner/barcodescanning2', [ScannerController::class, 'barcodescanning2'])->name('scanner.barcodescanning2');

Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register/guest', [RegisterController::class, 'store'])->name('register.guest');

    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login');
});


Route::group(['middleware' => 'auth'], function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    //Register
    Route::get('/register/create', [RegisterController::class, 'create'])->name('register.create');
    Route::post('/register', [RegisterController::class, 'storeAuth'])->name('register');

    //Role
    Route::get('/role/index', [RoleController::class, 'index'])->name('role.index');
    Route::get('/role/delete/{id}', [RoleController::class, 'delete'])->name('role.delete');
    Route::get('/role/create', [RoleController::class, 'create'])->name('role.create');
    Route::post('/role/store', [RoleController::class, 'store'])->name('role.store');
    Route::get('/role/find/{id}', [RoleController::class, 'find'])->name('role.find');
    Route::post('/role/update', [RoleController::class, 'update'])->name('role.update');

    //User
    Route::get('/user/index', [UserController::class, 'index'])->name('user.index');
    Route::post('/user/update', [UserController::class, 'update'])->name('user.update')->middleware(['auth', 'role:Admin']);
    Route::get('/user/delete/{id}', [UserController::class, 'delete'])->name('user.delete');
    Route::get('/user/detail/{id}', [UserController::class, 'detail'])->name('user.detail')->middleware(['auth', 'role:Admin']);
    Route::get('/user/assign/{id}', [UserController::class, 'assign'])->name('user.assign');
    Route::post('/user/assignrole', [UserController::class, 'assignrole'])->name('user.assignrole');

    //Canteen
    Route::get('/canteen/index', [CanteenController::class, 'index'])->name('canteen.index');
    Route::get('/canteen/showcanteen', [CanteenController::class, 'showcanteen'])->name('canteen.showcanteen');
    Route::post('/canteen/synchronize', [CanteenController::class, 'synchronize'])->name('canteen.synchronize');

    //Scanner
    Route::get('/scanner/index', [ScannerController::class, 'index'])->name('scanner.index');
    Route::get('/scanner/checkscanning', [ScannerController::class, 'checkscanning'])->name('scanner.checkscanning');
    Route::get('/scanner/create', [ScannerController::class, 'create'])->name('scanner.create');
    Route::get('/scanner/batch', [ScannerController::class, 'batch'])->name('scanner.batch');
    Route::post('/scanner/store', [ScannerController::class, 'store'])->name('scanner.store');
    Route::get('/scanner/store', [ScannerController::class, 'store'])->name('scanner.store');
    Route::get('/scanner/canteen', [ScannerController::class, 'canteen'])->name('scanner.canteen');

    //Karyawan
    Route::get('/karyawan/index', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::get('/karyawan/detail/{id}', [KaryawanController::class, 'show'])->name('karyawan.detail');
    Route::get('/karyawan/generateqr/{id}', [KaryawanController::class, 'generateqr'])->name('karyawan.generateqr');
    Route::get('/karyawan/batchQR', [KaryawanController::class, 'batchQR'])->name('karyawan.batchQR');
    Route::get('/karyawan/batchBarcode', [KaryawanController::class, 'batchBarcode'])->name('karyawan.batchBarcode');

    Route::get('/syslog/index', [SysLogController::class, 'index'])->name('syslog.index');


    //Export
    Route::get('/canteen/export', [CanteenController::class, 'export_excel'])->name('canteen.export');

    
    Route::post('/scanner/duplicate/bulk-move', [DuplicateScannerController::class, 'bulkMove'])->name('scanner.duplicate.bulkMove');
    Route::delete('/scanner/duplicate/{id}', [DuplicateScannerController::class, 'destroy'])->name('scanner.duplicate.destroy');
    
});

//Duplicate Scanner
Route::get('/scanner/duplicate', [DuplicateScannerController::class, 'index'])->name('scanner.duplicate');
Route::get('/scanner/duplicate-data', [DuplicateScannerController::class, 'data'])->name('scanner.duplicateData');

// Livewire Scanner Routes (outside auth middleware - same as existing scanner routes)
Route::get('/scanner/livewire/canteen1', \App\Http\Livewire\ScannerComponent::class)
    ->defaults('canteenNo', 1)
    ->name('scanner.livewire.canteen1');

Route::get('/scanner/livewire/canteen2', \App\Http\Livewire\ScannerComponent::class)
    ->defaults('canteenNo', 2)
    ->name('scanner.livewire.canteen2');

