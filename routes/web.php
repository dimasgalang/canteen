<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\CanteenController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\SignaturePadController;
use App\Http\Controllers\SpeechController;
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

Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register/guest', [RegisterController::class, 'store'])->name('register.guest');

    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login');
});


Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
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
    Route::get('/canteen/showcanteen1', [CanteenController::class, 'showcanteen1'])->name('canteen.showcanteen1');
    Route::get('/canteen/showcanteen2', [CanteenController::class, 'showcanteen2'])->name('canteen.showcanteen2');
    Route::get('/canteen/canteen', [CanteenController::class, 'canteen'])->name('canteen.canteen');
    Route::get('/canteen/canteen1', [CanteenController::class, 'canteen1'])->name('canteen.canteen1');
    Route::get('/canteen/canteen2', [CanteenController::class, 'canteen2'])->name('canteen.canteen2');
    Route::get('/canteen/scancanteen1', [CanteenController::class, 'scancanteen1'])->name('canteen.scancanteen1');
    Route::get('/canteen/scancanteen2', [CanteenController::class, 'scancanteen2'])->name('canteen.scancanteen2');

    //Scanner
    Route::get('/scanner/index', [ScannerController::class, 'index'])->name('scanner.index');
    Route::get('/scanner/qrscanning', [ScannerController::class, 'qrscanning'])->name('scanner.qrscanning');
    Route::get('/scanner/scanning', [ScannerController::class, 'scanning'])->name('scanner.scanning');
    Route::get('/scanner/create', [ScannerController::class, 'create'])->name('scanner.create');
    Route::get('/scanner/batch', [ScannerController::class, 'batch'])->name('scanner.batch');
    Route::post('/scanner/store', [ScannerController::class, 'store'])->name('scanner.store');
    Route::get('/scanner/store', [ScannerController::class, 'store'])->name('scanner.store');
    Route::get('/scanner/canteen', [ScannerController::class, 'canteen'])->name('scanner.canteen');
    Route::get('/scanner/barcode', [ScannerController::class, 'barcode'])->name('scanner.barcode');

    //Karyawan
    Route::get('/karyawan/index', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::get('/karyawan/detail/{id}', [KaryawanController::class, 'show'])->name('karyawan.detail');
    Route::get('/karyawan/generateqr/{id}', [KaryawanController::class, 'generateqr'])->name('karyawan.generateqr');
    Route::get('/karyawan/batch', [KaryawanController::class, 'batch'])->name('karyawan.batch');

    //Export
    Route::get('/canteen/export', [CanteenController::class, 'export_excel'])->name('canteen.export');
});
