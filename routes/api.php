<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{CategoryController, BookController, BookReturnController, LoanController};
use App\Http\Controllers\Api\Auth\{LoginController, LogoutController, RegisterController};

Route::middleware('guest')->group(function(){
    Route::post('login', LoginController::class);
    Route::post('register', RegisterController::class);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::get('user', function(){
        return request()->user();
    });

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('books', BookController::class);
    Route::apiResource('loans', LoanController::class);
    Route::get('my-borrowed-books', [BookReturnController::class, 'listOfBooks']);
    Route::post('return-books', [BookReturnController::class, 'returnBooks']);

    Route::post('logout', LogoutController::class);
});
