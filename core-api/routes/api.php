<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\TusHookController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SessionController;

use Illuminate\Support\Facades\Broadcast;


use App\Http\Middleware\SanctumTokenMiddleware;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/verify-email/{code}', [AuthController::class, 'verifyEmail']);

Route::post('tus/hooks', TusHookController::class);

Route::middleware(SanctumTokenMiddleware::class)->group(function () {
    Broadcast::routes(['middleware' => ['api', SanctumTokenMiddleware::class]]);

    Route::post('logout', [SessionController::class, 'destroyCurrent']);

    Route::prefix('attachments')->group(function () {
        Route::post('init',     [AttachmentController::class,'init']);
        Route::post('complete', [AttachmentController::class,'complete']);
    });

    Route::prefix('chats')->group(function () {
        Route::get('', [ChatController::class, 'index']);
        Route::post('', [ChatController::class, 'store']);
        Route::get('{key}', [ChatController::class, 'show']);

        Route::post('private/{recipient}', [ChatController::class,'createPrivateChatAndSend']);

        Route::prefix('{chat}/messages')->group(function () {
            Route::get('', [MessageController::class, 'index']);
            Route::post('', [MessageController::class, 'store']);
            Route::patch('{message}', [MessageController::class, 'update']);
            Route::delete('{message}',[MessageController::class, 'destroy']);
            Route::post('{message}/react', [MessageController::class, 'react']);
            Route::post('{message}/read', [MessageController::class, 'markRead']);
        });
    });

    Route::prefix('threads')->group(function () {
        Route::get('{root}/messages',[ThreadController::class, 'index']);
        Route::get('{root}/count',   [ThreadController::class, 'count']);
        Route::post('{root}/messages',[ThreadController::class, 'store']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/me', function (Request $request) {
            return $request->user();
        });
    });

    Route::prefix('search')->group(function () {
        Route::get('', [SearchController::class, 'index']);
    });

    Route::prefix('sessions')->group(function () {
        Route::get('', [SessionController::class, 'index']);
        Route::delete('others', [SessionController::class, 'destroyOthers']);
        Route::delete('{id}', [SessionController::class, 'destroy']);
    });
});
