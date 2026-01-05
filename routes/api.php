<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\TasksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TasksController::class, 'index']);
        Route::post('/', [TasksController::class, 'store']);
        Route::put('/{task}', [TasksController::class, 'update']);
        Route::delete('/{task}', [TasksController::class, 'destroy']);
        Route::delete('/completed/clear', [TasksController::class, 'clearCompleted']);
    });
});
