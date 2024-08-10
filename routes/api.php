<?php

use App\Http\Controllers\API\AccountingExpenseController;
use App\Http\Controllers\API\AccountingSupplierController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\PendingReservationsController;
use App\Http\Controllers\API\PermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SupplierController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CardController;
use App\Http\Controllers\API\CardTypesController;
use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\ReservationProductController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\API\AccountingClientController;
use App\Http\Controllers\API\AccountingDayController;
use App\Http\Controllers\API\ShiftController;
use App\Http\Controllers\API\PromoCodeController;
use App\Http\Controllers\API\SubscriptionController;

/*
|----------------------------------------------------------------f----------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the 'api' middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'localization'])->group(function () {

    Route::middleware(['admin.check'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('home', [HomeController::class, 'getData']);

        Route::apiResource('user', UserController::class)->except(['update']);
        Route::post('user/{user}', [UserController::class, 'update']);

        Route::apiResource('permissions', PermissionController::class)->only(['index']);

        Route::apiResource('supplier', SupplierController::class)->except(['update']);
        Route::post('supplier/{supplier}', [SupplierController::class, 'update']);
        Route::get('supplier-simple', [SupplierController::class, 'indexSimple']);

        Route::get('products/search', [ProductController::class, 'search']);
        Route::get('products-simple', [ProductController::class, 'indexSimple']);
        Route::apiResource('product', ProductController::class)->except(['update']);
        Route::post('product/{product}', [ProductController::class, 'update']);

        Route::apiResource('card-type', CardTypesController::class)->except(['update']);
        Route::post('card-type/{cardType}', [CardTypesController::class, 'update']);
        Route::get('card-type-simple', [CardTypesController::class, 'indexSimple']);

        Route::apiResource('card', CardController::class)->except(['update']);
        Route::get('cards-simple', [CardController::class, 'indexSimple']);
        Route::get('cards-simple-pending', [CardController::class, 'indexSimpleForPending']);
        Route::get('cards-simple-list', [CardController::class, 'indexSimpleList']);
        Route::post('card/{card}', [CardController::class, 'update']);

        Route::apiResource('promo', PromoCodeController::class);
        Route::post('check-promo-code', [PromoCodeController::class, 'checkCode']);

        Route::get('client/search', [ClientController::class, 'search']);
        Route::get('client/search-cashier', [ClientController::class, 'searchForCashier']);
        Route::get('client/search-pending', [ClientController::class, 'searchForPending']);
        Route::get('client-export', [ClientController::class, 'export']);
        Route::apiResource('client', ClientController::class)->except(['update']);
        Route::post('client/{client}', [ClientController::class, 'update']);
        Route::get('client-simple', [ClientController::class, 'indexSimple']);
        Route::get('client-simple-pending', [ClientController::class, 'indexSimpleForPending']);
        Route::get('client-simple-list', [ClientController::class, 'indexSimpleList']);

        Route::post('reservation-preview/{reservation}', [ReservationController::class, 'preview']);
        Route::get('reservation/{id}/products', [ReservationProductController::class, 'getByReservation']);
        Route::apiResource('reservation-product', ReservationProductController::class)->except(['update', 'show']);
        Route::apiResource('reservation', ReservationController::class)->except(['update']);
        Route::get('reservation-active', [ReservationController::class, 'active']);
        Route::post('reservation/{reservation}', [ReservationController::class, 'update']);


        Route::apiResource('accounting-day', AccountingDayController::class)->only(['index', 'show']);
        Route::get('accounting-day-filter', [AccountingDayController::class, 'filter']);

        Route::get('accounting-client/search', [AccountingClientController::class, 'search']);
        Route::apiResource('accounting-client', AccountingClientController::class)->only(['index', 'show']);

        Route::apiResource('accounting-suppliers', AccountingSupplierController::class)->only(['index', 'store']);
        Route::apiResource('accounting-expenses', AccountingExpenseController::class)->only(['index', 'store']);
        Route::get('accounting-expenses-filter', [AccountingExpenseController::class, 'filter']);


        Route::apiResource('shift', ShiftController::class)->only(["index", "show"]);
        Route::get('shift-active', [ShiftController::class, 'active']);
        Route::post('shift-start', [ShiftController::class, 'store']);
        Route::post('shift-end', [ShiftController::class, 'update']);

        Route::get('shift/{id}/reservations', [ShiftController::class, 'getReservations']);

        Route::apiResource('pending-reservations', PendingReservationsController::class)->only(['update', 'destroy']);
        Route::get('pending-reservations/active', [PendingReservationsController::class, 'active']);
        Route::post('pending-reservations/convert/{reservation}', [PendingReservationsController::class, 'convert']);

        Route::apiResource('subscriptions', SubscriptionController::class)->except(['update']);
        Route::post('subscriptions/{subscription}', [SubscriptionController::class, 'update']);
    });

    Route::middleware(['user.check'])->prefix('user')->group(function () {

        Route::get('home', [HomeController::class, 'getData']);

        //CASHIER
        Route::middleware(['permission.check:reservations'])->group(function () {
            Route::get('shift-active', [ShiftController::class, 'active']);
            Route::get('client-simple', [ClientController::class, 'indexSimple']);
            Route::get('client-simple-pending', [ClientController::class, 'indexSimpleForPending']);
            Route::get('cards-simple', [CardController::class, 'indexSimple']);
            Route::get('cards-simple-pending', [CardController::class, 'indexSimpleForPending']);
            Route::get('products-simple', [ProductController::class, 'indexSimple']);
            Route::apiResource('pending-reservations', PendingReservationsController::class)->only(['update', 'destroy']);
            Route::get('pending-reservations/active', [PendingReservationsController::class, 'active']);
            Route::post('pending-reservations/convert/{reservation}', [PendingReservationsController::class, 'convert']);
            Route::get('reservation/{id}/products', [ReservationProductController::class, 'getByReservation']);
            Route::post('shift-start', [ShiftController::class, 'store']);
            Route::post('shift-end', [ShiftController::class, 'update']);

            Route::get('shift/{id}/reservations', [ShiftController::class, 'getReservations']);
        });

        Route::middleware(['permission.check:suppliers'])->apiResource('supplier', SupplierController::class)->except(['update']);
        Route::middleware(['permission.check:suppliers'])->post('supplier/{supplier}', [SupplierController::class, 'update']);
        Route::middleware(['permission.check:suppliers'])->get('supplier-simple', [SupplierController::class, 'indexSimple']);


        Route::middleware(['permission.check:products'])->get('products/search', [ProductController::class, 'search']);
        Route::middleware(['permission.check:products'])->apiResource('product', ProductController::class)->except(['update']);
        Route::middleware(['permission.check:products'])->post('product/{product}', [ProductController::class, 'update']);

        Route::middleware(['permission.check:cards'])->apiResource('card-type', CardTypesController::class)->except(['update']);
        Route::middleware(['permission.check:cards'])->post('card-type/{cardType}', [CardTypesController::class, 'update']);
        Route::middleware(['permission.check:cards'])->get('card-type-simple', [CardTypesController::class, 'indexSimple']);

        Route::middleware(['permission.check:cards'])->apiResource('card', CardController::class)->except(['update']);
        Route::middleware(['permission.check:cards'])->post('card/{card}', [CardController::class, 'update']);


        Route::middleware(['permission.check:cards'])->get('cards-simple-list', [CardController::class, 'indexSimpleList']);
        Route::middleware(['permission.check:cards'])->get('client-simple-list', [ClientController::class, 'indexSimpleList']);

        Route::middleware(['permission.check:cards'])->apiResource('subscriptions', SubscriptionController::class)->except(['update']);
        Route::middleware(['permission.check:cards'])->post('subscriptions/{subscription}', [SubscriptionController::class, 'update']);

        Route::middleware(['permission.check:promo'])->apiResource('promo', PromoCodeController::class);


        Route::middleware(['permission.check:clients'])->get('client/search-cashier', [ClientController::class, 'searchForCashier']);
        Route::middleware(['permission.check:clients'])->get('client/search-pending', [ClientController::class, 'searchForPending']);
        Route::middleware(['permission.check:clients'])->get('client-export', [ClientController::class, 'export']);
        Route::middleware(['permission.check:clients'])->get('client/search', [ClientController::class, 'search']);
        Route::middleware(['permission.check:clients'])->apiResource('client', ClientController::class)->except(['update']);
        Route::middleware(['permission.check:clients'])->post('client/{client}', [ClientController::class, 'update']);

        Route::middleware(['permission.check:reservations'])->post('reservation-preview/{reservation}', [ReservationController::class, 'preview']);
        Route::middleware(['permission.check:reservations'])->apiResource('reservation-product', ReservationProductController::class)->except(['update', 'show']);
        Route::middleware(['permission.check:reservations'])->apiResource('reservation', ReservationController::class)->except(['update']);
        Route::middleware(['permission.check:reservations'])->post('reservation/{reservation}', [ReservationController::class, 'update']);
        Route::middleware(['permission.check:reservations'])->get('reservation-active', [ReservationController::class, 'active']);
        Route::middleware(['permission.check:reservations'])->post('check-promo-code', [PromoCodeController::class, 'checkCode']);

        Route::middleware(['permission.check:reports'])->get('accounting-client/search', [AccountingClientController::class, 'search']);
        Route::middleware(['permission.check:reports'])->apiResource('accounting-day', AccountingDayController::class)->only(['index', 'show']);
        Route::middleware(['permission.check:reports'])->get('accounting-day-filter', [AccountingDayController::class, 'filter']);
        Route::middleware(['permission.check:reports'])->apiResource('accounting-client', AccountingClientController::class)->only(['index', 'show']);
        Route::middleware(['permission.check:reports'])->apiResource('accounting-suppliers', AccountingSupplierController::class)->only(['index', 'store']);
        Route::middleware(['permission.check:reports'])->apiResource('accounting-expenses', AccountingExpenseController::class)->only(['index', 'store']);
        Route::middleware(['permission.check:reports'])->get('accounting-expenses-filter', [AccountingExpenseController::class, 'filter']);


        Route::middleware(['permission.check:shifts'])->apiResource('shift', ShiftController::class)->only(["index", "show"]);

        Route::middleware(['permission.check:users'])->apiResource('user', UserController::class)->except(['update']);
        Route::middleware(['permission.check:users'])->post('user/{user}', [UserController::class, 'update']);
        Route::middleware(['permission.check:users'])->apiResource('permissions', PermissionController::class)->only(['index']);
    });
});
