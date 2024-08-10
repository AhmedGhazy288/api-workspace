<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\ReservationProduct;
use App\Models\Product;
use App\Http\Resources\ReservationProductCollection;
use App\Http\Resources\ReservationProductResource;

class ReservationProductController extends BaseController
{

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "page" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $reservationProducts = ReservationProduct::paginate(20);

        $reservationProducts = new ReservationProductCollection($reservationProducts);

        return $this->sendResponse($reservationProducts, __('messages.found'));
    }

    public function getByReservation(Request $request, $id)
    {
        $validator = Validator::make(['id' => $id], [
            "id" => "required|exists:reservations,id",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $productsQuery = ReservationProduct::whereReservationId($id)->orderBy('created_at', 'desc');

        $products = ReservationProductResource::collection($productsQuery->paginate(20));
        $total = $productsQuery->count();

        $response = [
            "total" => $total,
            "data" => $products
        ];

        return $this->sendResponse($response, __('messages.found'));
    }


    public function store(Request $request)
    {
        $createOrUpdate = $request->all();
        $product = Product::find($createOrUpdate["product_id"]);
        $validator = Validator::make($createOrUpdate, [
            'reservation_id' => 'required|exists:reservations,id',
            "product_id" => 'required|exists:products,id',
            "quantity" => 'required|integer|min:1|max:' . $product->stock,
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $reservationProductUpdate = ReservationProduct::where("reservation_id", $createOrUpdate["reservation_id"])
            ->where("product_id", $createOrUpdate["product_id"])
            ->first();

        if ($reservationProductUpdate) {

            $reservationProductUpdate->quantity += $createOrUpdate["quantity"];
            $reservationProductUpdate->total = $reservationProductUpdate->item_price * $reservationProductUpdate->quantity;

            $reservationProductUpdate->reservation->products_total += $reservationProductUpdate->item_price * $createOrUpdate["quantity"];
            $reservationProductUpdate->reservation->total += $reservationProductUpdate->item_price * $createOrUpdate["quantity"];
            $reservationProductUpdate->reservation->save();

            $reservationProductUpdate->update([
                "quantity" => $reservationProductUpdate->quantity,
                "total" => $reservationProductUpdate->total,
            ]);

            $reservationProduct = $reservationProductUpdate;

            $message = __("messages.reservationProduct_updated");
        } else {

            $createOrUpdate["item_price"] = $product->retail_price;
            $createOrUpdate["total"] = $createOrUpdate["item_price"] * $createOrUpdate["quantity"];
            $reservationProduct = ReservationProduct::Create($createOrUpdate);
            $reservationProduct->reservation->products_total += $createOrUpdate["item_price"] * $createOrUpdate["quantity"];
            $reservationProduct->reservation->total +=
                $createOrUpdate["item_price"] * $createOrUpdate["quantity"];
            $reservationProduct->reservation->save();
            $message = __("messages.reservationProduct_created");
        }

        $product->update(["stock" => $product->stock - $createOrUpdate["quantity"]]);  //DECREASE FROM PRODUCT STOCK

        return $this->sendResponse(new ReservationProductResource($reservationProduct), $message);
    }


    public function destroy(ReservationProduct $reservationProduct)
    {
        $reservationProduct->product->stock += $reservationProduct->quantity;
        $reservationProduct->product->save();

        $reservationProduct->reservation->products_total -=
            $reservationProduct->item_price * $reservationProduct->quantity;

        $reservationProduct->reservation->total -=
            $reservationProduct->item_price * $reservationProduct->quantity;
        $reservationProduct->reservation->save();

        $reservationProduct->delete();
        return $this->sendResponse([], __("messages.reservationProduct_deleted"));
    }
}
