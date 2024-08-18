<?php

use App\Http\Controllers\Api\AppController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FriendController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
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
Route::post('signin/google', [UserController::class, 'handleGoogleCallback']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [UserController::class, 'logout']);

    Route::get('app', [AppController::class, 'index']);
    Route::get('friendPost', [PostController::class, 'friendPost']);

    Route::get('story', [StoryController::class, 'index']);
    Route::post('story', [StoryController::class, 'create']);

    Route::get('post', [PostController::class, 'index']);
    Route::get('post/{id}', [PostController::class, 'post']);
    Route::patch('post', [PostController::class, 'updatePost']);
    Route::post('post', [PostController::class, 'create']);
    Route::post('post/like', [PostController::class, 'like']);
    Route::patch('post/media/delete', [PostController::class, 'deleteMedia']);
    Route::delete('post/delete/{id}', [PostController::class, 'postDelete']);

    Route::post('comment', [CommentController::class, 'index']);
    Route::post('comment/create', [CommentController::class, 'create']);
    Route::delete('comment/delete/{id}', [CommentController::class, 'delete']);
    Route::patch('comment/edit', [CommentController::class, 'edit']);

    Route::get('profile/post/{id}', [ProfileController::class, 'getPost']);
    Route::get('profile/data/{id}', [ProfileController::class, 'profileData']);
    Route::post('profile/aboutus', [ProfileController::class, 'createUserAbout']);
    Route::delete('profile/aboutus/{id}', [ProfileController::class, 'deleteUserAbout']);
    Route::patch('profile/bio', [ProfileController::class, 'addUserBio']);
    Route::patch('profile/update', [ProfileController::class, 'updateUserProfile']);

    Route::get('friend/requested', [FriendController::class, 'index']);
    Route::get('friend/waiting', [FriendController::class, 'waitingFriend']);
    Route::get('friend/request/{id}', [FriendController::class, 'friendRequest']);
    Route::patch('friend/accept', [FriendController::class, 'friendRequestAccept']);
    Route::patch('friend/decline', [FriendController::class, 'friendRequestDecline']);
    Route::get('unfriend/{id}', [FriendController::class, 'unfriend']);

    Route::get('chat/{id}', [ChatController::class, 'index']);
    Route::post('chat', [ChatController::class, 'store']);
    Route::patch('chat/read', [ChatController::class, 'markAsRead']);
    Route::get('last/chat', [ChatController::class, 'lastmessage']);
    Route::get('search/chat', [ChatController::class, 'search']);

    Route::get('notification', [NotificationController::class, 'getNoti']);
    Route::patch('notification', [NotificationController::class, 'readNoti']);
    Route::patch('notification/markAsReadAll', [NotificationController::class, 'markAsReadAll']);

    Route::get('photos', [AppController::class, 'photos']);
    Route::get('search', [AppController::class, 'search']);
    Route::patch('user/updateLastOnline', [AppController::class, 'updateLastOnline']);
});
