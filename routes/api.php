<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/posts', [PostController::class, 'getPosts']);
Route::get('/post/{id}', [PostController::class, 'getPostById']);

Route::middleware('auth:sanctum')->group(function () {

    Route::controller(AuthController::class)->group(function () {

        Route::post('/logout', 'logout');
        Route::get('/users', 'getUsers');
    });

    Route::controller(PostController::class)->group(function () {

        Route::post('/add/posts', 'createPost');
        Route::put('/edit/post/{id}', 'editPost');
        Route::delete('/delete/post/{id}', 'deletePost');
    });

});
