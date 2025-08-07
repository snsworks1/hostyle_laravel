<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SecurityEventController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 보안 이벤트 관련 라우트
Route::prefix('security')->group(function () {
    Route::post('/events', [SecurityEventController::class, 'receiveEvent']);
    Route::get('/events', [SecurityEventController::class, 'index']);
    Route::get('/events/{event}', [SecurityEventController::class, 'show']);
    Route::patch('/events/{event}/process', [SecurityEventController::class, 'markAsProcessed']);
    Route::get('/statistics', [SecurityEventController::class, 'statistics']);
});
