<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WebController;

use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\DataUserController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\PembayaranController;

use App\Http\Controllers\LoginSystem\AuthController;
use App\Http\Controllers\LoginSystem\NewPasswordController;
use App\Http\Controllers\LoginSystem\EmailVerificationController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//view Hewan qurban 
Route::post('user/sapi', [WebController::class, 'sapi']);
Route::post('user/kambing', [WebController::class, 'kambing']);
//
Route::group(['middleware' => ['guest']], function(){
     //Login with Google
     Route::get('auth/google', [AuthController::class, 'redirectToGoogle']);
     Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
     //end Login with Google
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('registerAdmin', [AuthController::class, 'registeradmin']);
    Route::post('resendVerificationEmail', [EmailVerificationController::class, 'resendVerificationEmail']);
    Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('forgot-password', [NewPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::post('reset-password', [NewPasswordController::class, 'resetPassword'])->name('password.update');
});

Route::group(['middleware' => ['auth:sanctum', 'CekLevel:admin']], function(){
    //produk
    Route::post('admin/createProduk', [ProdukController::class, 'create']);
    Route::post('admin/showProduk', [ProdukController::class, 'show']);
    Route::post('admin/updateProduk/{id}', [ProdukController::class, 'update']);
    Route::post('admin/deleteProduk/{id}', [ProdukController::class, 'delete']);
    //

    //pesanan
    Route::post('admin/showPesanan', [OrderAdminController::class, 'show']);
    Route::post('admin/updatePesanan/{id}', [OrderAdminController::class, 'update']);
    Route::post('admin/deletePesanan/{id}', [OrderAdminController::class, 'delete']);
    //

    //data User
    Route::post('admin/showAdmin', [DataUserController::class, 'showAdmin']);
    Route::post('admin/showUser', [DataUserController::class, 'show']);
    Route::post('admin/deleteUserOrAdmin/{id}', [DataUserController::class, 'delete']);
    //

    //Pembayaran
    Route::post('admin/createPembayaran', [PembayaranController::class, 'create']);
    Route::post('admin/showPembayaran', [PembayaranController::class, 'show']);
    Route::post('admin/updatePembayaran/{id}', [PembayaranController::class, 'update']);
    Route::post('admin/deletePembayaran/{id}', [PembayaranController::class, 'delete']);
    //
});

Route::group(['middleware' => ['auth:sanctum', 'CekLevel:user']], function(){
    Route::post('user/pesanan/{id}', [OrderController::class, 'pesanan']);
    Route::post('user/batalPesanan/{id}', [OrderController::class, 'batalPesanan']);
    Route::post('user/buktiPembayaran/{id}', [ OrderController::class, 'buktiPembayaran' ]);
    Route::post('user/riwayatPesanan', [OrderController::class, 'riwayatPesanan']);
    Route::post('user/message', [WebController::class, 'message']);
    Route::post('user/profil', [AuthController::class, 'profilUser']);
    Route::post('updateProfil/{id}', [AuthController::class, 'updateProfil']);
});


Route::group(['middleware' => ['auth:sanctum', 'CekLevel:user,admin']], function(){
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('changePassword/{id}', [AuthController::class, 'changePassword']);
});

