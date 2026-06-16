<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiUserController;
use App\Http\Controllers\Api\ApiKycController;
use App\Http\Controllers\Api\ApiCardController;
use App\Http\Controllers\Api\ApiTicketController;
use App\Http\Controllers\Api\ApiAdminController;

/*
|--------------------------------------------------------------------------
| API Routes — DeFiCard v1
|--------------------------------------------------------------------------
|
| All endpoints return JSON. Public routes for auth. Protected routes
| use Sanctum tokens. Admin routes check is_admin flag.
|
*/

// ─── Public Auth Routes ───────────────────────────────────────────────
Route::post('/auth/register', [ApiAuthController::class, 'register']);
Route::post('/auth/login', [ApiAuthController::class, 'login']);
Route::post('/auth/wallet', [ApiAuthController::class, 'wallet']);

// ─── Heleket Payment Webhook (no auth — signed by Heleket) ────────────
Route::post('/webhooks/heleket/payment', function (Request $request) {
    // Log incoming webhook
    \Illuminate\Support\Facades\Log::channel('api')->info('Heleket webhook received', $request->all());
    
    $trans_address = $request->input('trans_address');
    $amount = $request->input('amount');
    $tx_id = $request->input('tx_id');
    
    if (!$trans_address || !$amount) {
        return response()->json(['error' => 'Missing required fields'], 400);
    }
    
    // Find payment by trans_address
    $payment = \App\Models\Payment::where('trans_address', $trans_address)->first();
    
    if (!$payment) {
        \Illuminate\Support\Facades\Log::channel('api')->warning('No payment found for address', ['address' => $trans_address]);
        return response()->json(['received' => true, 'note' => 'No matching payment found']);
    }
    
    // Update payment with transaction data
    $payment->trans_amount = $amount;
    $payment->tx_id = $tx_id;
    $payment->status = 'Approved';
    $payment->save();
    
    \Illuminate\Support\Facades\Log::channel('api')->info('Payment auto-approved via webhook', ['payment_id' => $payment->id]);
    
    return response()->json(['received' => true]);
});

// ─── Authenticated Routes ─────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    
    // Users
    Route::get('/users/me', [ApiUserController::class, 'me']);
    Route::put('/users/me/password', [ApiUserController::class, 'updatePassword']);
    
    // KYC
    Route::post('/kyc', [ApiKycController::class, 'store']);
    Route::get('/kyc', [ApiKycController::class, 'index']);
    Route::get('/kyc/all', [ApiKycController::class, 'all'])->middleware('is_admin');
    Route::post('/kyc/{id}/approve', [ApiKycController::class, 'approve'])->middleware('is_admin');
    Route::post('/kyc/{id}/reject', [ApiKycController::class, 'reject'])->middleware('is_admin');
    Route::put('/kyc/{id}', [ApiKycController::class, 'updateMessage'])->middleware('is_admin');
    
    // Cards — Purchase
    Route::post('/cards/purchase', [ApiCardController::class, 'purchase']);
    Route::get('/cards/purchases', [ApiCardController::class, 'purchases'])->middleware('is_admin');
    Route::post('/cards/purchases/{id}/approve', [ApiCardController::class, 'approvePurchase'])->middleware('is_admin');
    Route::post('/cards/purchases/{id}/reject', [ApiCardController::class, 'rejectPurchase'])->middleware('is_admin');
    
    // Cards — Management
    Route::get('/cards', [ApiCardController::class, 'cards']);
    Route::get('/cards/{id}/balance', [ApiCardController::class, 'balance']);
    Route::get('/cards/{id}/transactions', [ApiCardController::class, 'transactions']);
    Route::post('/cards/{id}/pin', [ApiCardController::class, 'changePin']);
    Route::post('/cards/{id}/toggle', [ApiCardController::class, 'toggle']);
    
    // Cards — Loading
    Route::post('/cards/load', [ApiCardController::class, 'load']);
    Route::get('/cards/loads', [ApiCardController::class, 'loads'])->middleware('is_admin');
    Route::post('/cards/loads/{id}/confirm', [ApiCardController::class, 'confirmLoad'])->middleware('is_admin');
    
    // Support Tickets
    Route::post('/tickets', [ApiTicketController::class, 'store']);
    Route::get('/tickets', [ApiTicketController::class, 'index']);
    Route::get('/tickets/all', [ApiTicketController::class, 'all'])->middleware('is_admin');
    Route::post('/tickets/{id}/reply', [ApiTicketController::class, 'reply'])->middleware('is_admin');
    Route::post('/tickets/{id}/close', [ApiTicketController::class, 'close'])->middleware('is_admin');
    
    // Admin / System
    Route::get('/admin/dashboard', [ApiAdminController::class, 'dashboard'])->middleware('is_admin');
    Route::get('/admin/reports/kyc', [ApiAdminController::class, 'kycReport'])->middleware('is_admin');
    Route::get('/admin/reports/transactions', [ApiAdminController::class, 'transactionReport'])->middleware('is_admin');
    Route::get('/admin/health', [ApiAdminController::class, 'health']);
    Route::post('/admin/backup', [ApiAdminController::class, 'backup'])->middleware('is_admin');
    
    // Notifications
    Route::post('/notifications/email', [ApiAdminController::class, 'sendEmail'])->middleware('is_admin');
});
