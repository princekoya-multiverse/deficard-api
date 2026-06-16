<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Front\LoadPaymentController;
use App\Http\Controllers\Front\{CardActivationController, FrontController, ProfileController, KycVerificationController};
use App\Http\Controllers\SupportTicketController;

//Route::get('asdasd',function(){
//    return \Illuminate\Support\Facades\Hash::make('JillianWorkman@mailinator.com');
//});
Auth::routes();

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
Route::get('/login', [FrontController::class, 'home'])->name('login');
Route::get('/', [FrontController::class, 'home'])->name('front.home');
Route::get('/terms', [FrontController::class, 'terms'])->name('terms.conditions');
Route::post('/notify-usdt-payment', [LoadPaymentController::class, 'notify_usdt_payment']);
Route::post('/notify-card-purchase-usdt-payment', [LoadPaymentController::class, 'notify_card_purchase_usdt_payment']);

Route::group(['middleware' => ['auth']], function() {
    Route::get('/ne-card', [FrontController::class, 'ne_card'])->name('front.ne_card');
    Route::post('/ne-card', [FrontController::class, 'ne_card']); //->name('front.ne_card_select');
    Route::post('/profile/update', [ProfileController::class, 'profile_update'])->name('profile.update');
    Route::post('/kyc/verification/save', [KycVerificationController::class, 'kyc_verification_save'])->name('kyc.verification.save');
    Route::put('/kyc/verification/update/{id}', [KycVerificationController::class, 'kyc_verification_update'])->name('kyc.verification.update');
    Route::resource('payment', PaymentController::class);
    Route::post('/card/activate', [CardActivationController::class, 'store'])->name('card.active');
    Route::post('/card/load', [LoadPaymentController::class, 'store'])->name('card.load');
    Route::resource('support_ticket', SupportTicketController::class);
    Route::any('/profile/change-password', [ProfileController::class, 'change_password'])->name('password.change');
    //visa card
    Route::post('/ne-card/transactions', [FrontController::class, 'necard_transactions'])->name('necard.transactions');
    Route::post('/card/change_pin', [FrontController::class, 'necard_pin_change'])->name('necard.change_pin');
    Route::any('/card/deactivate', [FrontController::class, 'necard_deactivate'])->name('necard.deactivate');
    Route::any('/card/reactivate', [FrontController::class, 'necard_reactivate'])->name('necard.reactivate');
    //mastercard
    Route::post('/ne-card/transactions-mc', [FrontController::class, 'necard_transactions_mc'])->name('necard.transactions_mc');
    Route::post('/card/change-pin-mc', [FrontController::class, 'necard_pin_change_mc'])->name('necard.change_pin_mc');
    Route::any('/card/deactivate-mc', [FrontController::class, 'necard_deactivate_mc'])->name('necard.deactivate_mc');
    Route::any('/card/reactivate-mc', [FrontController::class, 'necard_reactivate_mc'])->name('necard.reactivate_mc');
    //
    Route::any('/card/purchase/{type}', [FrontController::class, 'necard_purchase'])->name('necard.purchase');
    Route::post('/update-payment-with-card/{id}', [LoadPaymentController::class, 'update_usdt_payment_with_card'])->name('update-usdt-payment-with-card');
});

Route::group(['prefix' => 'admin', 'middleware' => ['is_admin']], function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('users/profile/{id}', [AdminController::class, 'user_profile'])->name('admin.user.profile');
    Route::get('users/export', [AdminController::class, 'usersExport'])->name('admin.users.export');
    Route::post('users', [AdminController::class, 'users']);
    Route::post('users', [AdminController::class, 'update_users_ids'])->name('admin-users-update-ids');
    Route::post('users/update/email/{id}', [AdminController::class, 'update_user_email'])->name('admin-users-update-email');
    Route::post('users/kyc/update_message', [AdminController::class, 'update_kyc_message'])->name('admin-kyc-update-message');
    Route::post('users/kyc/email_message', [AdminController::class, 'email_kyc_message'])->name('admin-kyc-email-message');


    Route::get('kyc', [AdminController::class, 'admin_kyc'])->name('admin.kyc');
    Route::get('kyc/export', [AdminController::class, 'admin_kycExport'])->name('admin.kyc.export');

    Route::get('card', [AdminController::class, 'admin_card'])->name('admin.card');
    Route::get('card/export', [AdminController::class, 'admin_cardExport'])->name('admin.card.export');

    Route::get('card/activations', [AdminController::class, 'admin_card_activation'])->name('admin.card.activation');
    Route::get('card/activations/export', [AdminController::class, 'admin_card_activationExport'])->name('admin.card.activation.export');

    Route::get('card/activations/update/status/{id}/{status}', [AdminController::class, 'admin_card_activation_update'])->name('admin.card.activation.update');
    Route::get('kyc/update/status/{id}/{status}', [AdminController::class, 'admin_kyc_status'])->name('admin.kyc.update');
    Route::get('card/update/status/{id}/{status}', [AdminController::class, 'admin_card_update'])->name('admin.card.update');
    Route::post('card/update/progress/{user_id}', [AdminController::class, 'admin_card_update_progress'])->name('admin.card.updateProgress');

    Route::get('card/load', [AdminController::class, 'admin_card_load'])->name('admin.card.load');
    Route::post('card/load/done', [AdminController::class, 'admin_card_load_done'])->name('admin.card.load.done');
    Route::get('card/load/export', [AdminController::class, 'admin_card_loadExport'])->name('admin.card.load.export');

    Route::get('card/load/update/status/{id}/{status}', [AdminController::class, 'admin_card_load_update'])->name('admin.card.load.update');
    Route::get('support/ticket', [AdminController::class, 'admin_support_ticket'])->name('admin.support_ticket');
    Route::get('support/ticket/update/status/{id}/{status}', [AdminController::class, 'admin_support_ticket_update'])->name('admin.support_ticket.update');
    Route::get('kyc/payment', [AdminController::class, 'admin_kyc_payment'])->name('admin.kyc.payment');
    Route::get('kyc/payment/export', [AdminController::class, 'admin_kyc_paymentExport'])->name('admin.kyc.payment.export');

    Route::get('kyc/payment/update/status/{id}/{status}', [AdminController::class, 'admin_kyc_payment_update'])->name('admin.kyc.payment.update');
    Route::any('change-password', [AdminController::class, 'change_password'])->name('admin.password.change');

    Route::post('card', [AdminController::class, 'update_card_ids'])->name('admin-card-update-ids');
    Route::post('card/payment-holder-id', [AdminController::class, 'update_payment_card_holder_id'])->name('admin-payment-card-update-holder-id');

});

