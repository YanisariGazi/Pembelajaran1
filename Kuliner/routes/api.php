<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\LoginSystem\AuthController;
use App\Http\Controllers\LoginSystem\PasswordController;
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
Route::group(['middleware' => ['guest']], function(){
   //Login with Google
//    Route::get('auth/google', [AuthController::class, 'redirectToGoogle']);
//    Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
   //end Login with Google
   Route::post('register', [AuthController::class, 'register']);
   Route::post('registerAdmin', [AuthController::class, 'registeradmin']);
   Route::post('login', [AuthController::class, 'login']);
   Route::post('resendVerificationEmail', [EmailVerificationController::class, 'resendVerificationEmail']);
   Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
   Route::post('forgot-password', [PasswordController::class, 'sendResetLink'])->name('password.email');
   Route::post('reset-password', [PasswordController::class, 'resetPassword'])->name('password.update');
});

Route::group(['middleware' => ['auth:api', 'role:user']], function(){
   Route::post('user/profilUser', [AuthController::class, 'profilUser']);
   Route::post('user/updateProfil/{id}', [AuthController::class, 'updateProfil']);
   Route::post('user/createArtikel', [ArtikelController::class, 'createArtikel']);
   Route::post('user/showArtikel', [ArtikelController::class, 'showArtikel']);
   Route::post('user/showOneArtikel/{id}', [ArtikelController::class, 'showOneArtikel']);
   Route::post('user/showAllArtikel', [ArtikelController::class, 'showAllArtikel']);
   Route::post('user/updateArtikel/{id}', [ArtikelController::class, 'updateArtikel']);
   Route::post('user/deleteArtikel/{id}', [ArtikelController::class, 'deleteArtikel']);
   Route::post('user/likeUnlikeArtikel/{id}', [ArtikelController::class, 'likeUnlikeArtikel']);
   Route::post('user/dislikeUndislikeArtikel/{id}', [ArtikelController::class, 'dislikeUndisArtikel']);
   Route::post('user/reportArtikel/{id}', [ArtikelController::class, 'reportArtikel']);
});

Route::group(['middleware' => ['auth:api', 'role:user,admin']], function(){
   Route::post('logout', [AuthController::class, 'logout']);
   Route::post('changePassword/{id}', [PasswordController::class, 'changePassword']);

});
