<?php

use App\Http\Controllers\LoginSystem\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskListController;
use App\Http\Controllers\TodoListController;

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
Route::group(['middleware' =>['guest']],function(){
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verifyOtp', [AuthController::class, 'verifyOtp']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::group(['middleware' => ['auth']], function(){
    Route::post('createTodoList', [TodoListController::class, 'createTodolist']);
    Route::post('showTodoList', [TodoListController::class, 'showTodoList']);
    Route::post('updateTodoList/{id}', [TodoListController::class, 'updateTodolist']);
    Route::post('deleteTodoList/{id}', [TodoListController::class, 'deleteTodoList']);
    
    Route::post('createTask/{id}', [TaskListController::class, 'createTask']);
    Route::post('showTask/{id}', [TaskListController::class, 'showTask']);
    Route::post('doneTask/{id}', [TaskListController::class, 'doneTask']);
    Route::post('updateTask/{id}', [TaskListController::class, 'updateTask']);
    Route::post('deleteTask/{id}', [TaskListController::class, 'deleteTask']);
});