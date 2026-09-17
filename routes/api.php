<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\Api\WhatsAppApprovalController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle']);

Route::prefix('whatsapp')->group(function () {
    Route::get('/pending-approvals', [WhatsAppApprovalController::class, 'getPendingApprovals']);
    Route::post('/action', [WhatsAppApprovalController::class, 'handleAction']);
});
