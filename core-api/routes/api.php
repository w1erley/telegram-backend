<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\TusHookController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ThreadController;


use App\Http\Middleware\SanctumTokenMiddleware;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/verify-email/{code}', [AuthController::class, 'verifyEmail']);

Route::post('tus/hooks', TusHookController::class);

Route::middleware(SanctumTokenMiddleware::class)->group(function () {
    Route::prefix('attachments')->group(function () {
        Route::post('init',     [AttachmentController::class,'init']);
        Route::post('complete', [AttachmentController::class,'complete']);
    });

    Route::prefix('chats')->group(function () {
        Route::post('', [ChatController::class,'store']);
        Route::post('private/{user}', [ChatController::class,'createPrivate']);
        Route::get('{chat}/messages', [MessageController::class,'index']);
    });

    Route::prefix('messages')->group(function () {
        Route::post('', [MessageController::class,'store']);
        Route::patch('{message}', [MessageController::class,'update']);
        Route::delete('{message}',[MessageController::class,'destroy']);
        Route::post('{message}/react', [MessageController::class,'react']);
    });

    Route::prefix('threads')->group(function () {
        Route::get('{root}/messages',[ThreadController::class,'index']);
        Route::get('{root}/count',   [ThreadController::class,'count']);
        Route::post('{root}/messages',[ThreadController::class,'store']);
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
});
