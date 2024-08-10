<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Http\Resources\ProductOutOfStockResource;
use App\Models\AccountingDay;
use App\Models\AccountingExpense;
use App\Models\PendingReservation;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\ReservationProduct;
use App\Util\Helpers;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends BaseController
{


    public function getData(Request $request): JsonResponse
    {

        //ACTIVE RESERVATIONS COUNT
        $activeReservationsCount = Reservation::whereEndsAt(null)->count();

        //PENDING RESERVATIONS COUNT
        $pendingReservationsCount = PendingReservation::count();

        //THIS MONTH RESERVATIONS / PRODUCTS REVENUE
        $reservationsRevenue = AccountingDay::getTotalReservationsThisMonth();
        $productsRevenue = AccountingDay::getTotalProductsThisMonth();

        //THIS MONTH RESERVATIONS COUNT
        $reservationsThisMonthCount = Reservation::where('ends_at', '!=')
            ->where(Helpers::getThisMonth())->count();

        //OUT OF STOCK PRODUCTS
        $productsCloseOutOfStock = Product::where('stock', '<=', 10)->get();

        //MOST BOUGHT PRODUCT
        $mostBoughtProduct = ReservationProduct::getMostBoughtProductThisMonth();

        //SUM EXPENSES
        $expensesTotal = AccountingExpense::getTotalThisMonth();

        $response = [
            "activeReservationsCount" => $activeReservationsCount,
            "pendingReservationsCount" => $pendingReservationsCount,
            'totalReservations' => $reservationsThisMonthCount,
            'mostBought' => isset($mostBoughtProduct['product_id']) ?
                [
                    'id' => $mostBoughtProduct['product_id'],
                    'name' => Product::getNameById($mostBoughtProduct['product_id']),
                    'count' => $mostBoughtProduct['sum'],
                ] : [
                    'id' => '',
                    'name' => '',
                    'count' => 0,
                ],
            'revenue' => [
                'reservations' => $reservationsRevenue,
                'products' => $productsRevenue,
            ],
            'products' => ProductOutOfStockResource::collection($productsCloseOutOfStock),
            'expensesTotal' => $expensesTotal,
        ];

        return $this->sendResponse($response, __('messages.found'));
    }

}
