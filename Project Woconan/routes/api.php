<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KomentarController;
use App\Http\Controllers\ProfilUserController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\ForgotpasswordController;
use App\Http\Controllers\RoleController;
use App\Http\Middleware\RoleMiddleware;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth User
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('kirim-verifikasi',[AuthController::class,'verifikasi_user']);
Route::post('kirim-ulang',[AuthController::class,'kirim_ulang_otp']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('reset-password',[ForgotpasswordController::class,'reset']);
Route::post('verifikasi-reset',[ForgotpasswordController::class,'verifikasi_reset']);
Route::post('password-baru',[ForgotpasswordController::class,'showResetForm']);


// USER, ADMIN & SUPER ADMIN
Route::middleware(['auth:sanctum', 'role:user,admin,super_admin'])->group(function () {
    //  BERANDA
        Route::post('beranda-user', [BerandaController::class, 'berandaUser']); // get
        Route::post('read-user/{id}', [BerandaController::class, 'readUser']);  // get
    // PROFIL
        Route::post('create-profil', [ProfilUserController::class, 'storeprofil']);
        Route::post('update-profil/{id}', [ProfilUserController::class, 'update']);
        Route::post('index-profil/{id}', [ProfilUserController::class, 'showProfil']); // get

    });

// ADMIN & USER
Route::middleware(['auth:sanctum', 'role:admin,user'])->group(function () {
    // POSTINGAN
        Route::post('update-postingan/{id}', [PostController::class, 'update']);
        Route::post('delete-postingan/{id}', [PostController::class, 'destroy']);

    // KOMENTAR
        Route::post('lihat-komentar/{id}', [KomentarController::class, 'showKomentar']);  // get
        Route::post('delete-komentar/{id}', [KomentarController::class, 'destroyKomentar']);
        Route::post('delete-balasan-komentar/{id}', [KomentarController::class, 'destroyBalasanKomentar']);

    // SHOW FOLLOWER
        Route::post('follower/{id}', [FollowerController::class, 'getFollowers']);  // get

    });


// SUPER ADMIN
Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {

    // Rute yang memerlukan akses super admin
        Route::post('index-role', [RoleController::class, 'getUserData']);  // get
        Route::post('update/{id}/role', [RoleController::class, 'updateRole']);

    //USER YG TIDAK VERIFIKASI
        Route::post('user-veri', [RoleController::class, 'showUnverifiedUsers']);  // get
        Route::post('hapus-user/{id}', [RoleController::class, 'deleteUser']);

});


//  ADMIN
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    // PROFILE USER UNTUK ADMIN
        Route::post('profilUser', [ProfilUserController::class, 'showUserAdmin']);  // get

});


// USER
Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    // Post Routes
        Route::post('create-posts', [PostController::class, 'store']);

    //  KOMENTAR
        Route::post('posts/{id}/comment-count', [KomentarController::class, 'showJumlah']);  // get
        Route::post('komentar-posts/{id}', [KomentarController::class, 'storeKomentar']);
        Route::post('komentar/{id}/balas', [KomentarController::class, 'balasKomentar']);

    // Like Unlike & Count
        Route::post('posts/{id}/like', [LikeController::class, 'like']);
        Route::post('posts/{id}', [LikeController::class, 'showLike']);  // get

    // FOLLOWER
        Route::post('users/{id}/follow', [FollowerController::class, 'follow']);
        Route::post('user/{id}/follower-count', [FollowerController::class, 'getFollowerCount']);  // get

});
















