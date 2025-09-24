<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;
use App\Models\Notification;

// Public routes
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function() {
    Route::get('/tasks', [TaskController::class, 'all']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/notifications', [NotificationController::class, 'getAllNotifications']);
    Route::post('/notifications/{id}', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});
