<?php

use App\Http\Controllers\Api\AppController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('signup', [UserController::class, 'signup']);
Route::post('signin', [UserController::class, 'signin']);
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [UserController::class, 'logout']);

    Route::get('app', [AppController::class, 'index']);
    Route::get('friendPost', [PostController::class, 'friendPost']);

    Route::get('story', [StoryController::class, 'index']);
    Route::post('story', [StoryController::class, 'create']);

    Route::get('post', [PostController::class, 'index']);
    Route::post('post', [PostController::class, 'create']);
    Route::post('post/like', [PostController::class, 'like']);

    Route::post('comment', [CommentController::class, 'index']);
    Route::post('comment/create', [CommentController::class, 'create']);
    Route::delete('comment/delete/{id}', [CommentController::class, 'delete']);
    Route::patch('comment/edit', [CommentController::class, 'edit']);
});
