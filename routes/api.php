<?php

use App\Http\Controllers\Auth\API\AuthController;
use App\Http\Controllers\Backend\API\DashboardController;
use App\Http\Controllers\Backend\API\NotificationsController;
use App\Http\Controllers\Backend\API\SettingController;
use App\Http\Controllers\UpsellController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
use App\Http\Controllers\WebHookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Subscriptions\Models\Plan;

Route::get('user-detail', [AuthController::class, 'userDetails']);
Route::get('get-plans',  function (Request $request) {
    $data = Plan::with('orderBumps')->get();
    $response =
        [
            "status" => $data ? true : false,
            "data" => $data
        ];
    return $response;
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('social-login', 'socialLogin');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('check-user-exists', 'checkUserExists');
    Route::get('logout', 'logout');
});
Route::post('/store-access-token', [SettingController::class, 'storeToken']);
Route::post('/token-revoke', [SettingController::class, 'revokeToken']);

Route::get('dashboard-detail', [DashboardController::class, 'DashboardDetail']);
Route::get('dashboard-detail-data', [DashboardController::class, 'DashboardDetailData']);
Route::get('get-tranding-data', [DashboardController::class, 'getTrandingData']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('setting', SettingController::class);
    Route::apiResource('notification', NotificationsController::class);
    Route::get('notification-list', [NotificationsController::class, 'notificationList']);
    Route::get('gallery-list', [DashboardController::class, 'globalGallery']);
    Route::get('search-list', [DashboardController::class, 'searchList']);
    Route::post('update-profile', [AuthController::class, 'updateProfile']);

    Route::post('change-password', [AuthController::class, 'changePassword']);
    Route::post('delete-account', [AuthController::class, 'deleteAccount']);

    Route::get('vendor-dashboard-list', [DashboardController::class, 'VendorDashboardDetail']);
});
Route::get('app-configuration', [SettingController::class, 'appConfiguraton']);

Route::post('execute-webhook-logs/{webhook}', [WebHookController::class, 'executeWebhookLogs']);

//webhook cartpanda
Route::group(['prefix' => 'webhook'], function () {
    Route::controller(WebHookController::class)->group(function () {
        Route::post('cartpanda', 'cartpanda');
        Route::post('tribopay', 'tribopay');
        Route::post('stripepages', 'stripepages');
        Route::post('{type}', 'genericWebhookHandler');
    });
    
    // Dedicated webhook controllers
    Route::post('stripe', [\App\Http\Controllers\StripeWebhookController::class, 'handle']);
    Route::post('mercadopago', [\App\Http\Controllers\MercadoPagoWebhookController::class, 'handle']);
});

//webhook cartpanda
Route::group(['prefix' => 'snapupsell'], function () {
    Route::controller(UpsellController::class)->group(function () {
        Route::post('accept', 'accept');
        Route::post('reject', 'reject');
        Route::get('price', 'price');

    });
});

Route::get('get-tranding-data', [DashboardController::class, 'getTrandingData']);
