<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\IncomeController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [UserController::class, 'register']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['controller' => UserController::class, 'prefix' => 'user'], function () {
        Route::get('/', 'get');
        Route::get('stats', 'stats');
        Route::put('update', 'update');
    });

    Route::group(['controller' => IncomeController::class, 'prefix' => 'income'], function () {
        Route::get('/', 'get');
        Route::post('store', 'store');
        Route::put('update', 'update');
        Route::delete('delete', 'delete');
        Route::post('delete-all', 'deleteAll');
    });

    Route::group(['controller' => ExpenseController::class, 'prefix' => 'expense'], function () {
        Route::get('/', 'get');
        Route::post('store', 'store');
        Route::put('update', 'update');
        Route::delete('delete', 'delete');
        Route::post('delete-all', 'deleteAll');
    });

    Route::get('logout', [AuthController::class, 'logout']);
});
