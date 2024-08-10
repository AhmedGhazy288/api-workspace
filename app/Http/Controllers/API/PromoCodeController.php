<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Http\Resources\PromoCodeResource;
use App\Models\PromoCode;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PromoCodeController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "page" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        return $this->sendResponse(PromoCodeResource::collection(
            PromoCode::orderBy('created_at', 'desc')->paginate(50)
        )->resource, __('messages.found'));
    }

    public function show(PromoCode $promo): JsonResponse
    {
        return $this->sendResponse(
            PromoCodeResource::make($promo),
            __('messages.found'));
    }

    /**
     * @throws \Exception
     */
    public function store(Request $request): JsonResponse
    {
        $create = $request->all();
        $validator = Validator::make($create, [
            "code" => [
                'nullable',
                'unique:promo_codes,code',
            ],
            "percent" => [
                'required',
                'integer',
                'between:1,100',
            ],
            "ends_at" => "required|date|after:tomorrow",

        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $promoCode = PromoCode::make($create);

        if (!isset($create['code'])) {
            $promoCode->code = PromoCode::generateUniqueCode();
        }

        $promoCode->save();
        return $this->sendResponse(
            PromoCodeResource::make($promoCode),
            __('messages.item-created')
        );
    }

    public function update(Request $request, PromoCode $promo): JsonResponse
    {
        $edit = $request->all();

        $validator = Validator::make($edit, [
            "code" => [
                "nullable",
                'unique:promo_codes,code,' . $promo->getKey(),
            ],
            "percent" => [
                'nullable',
                'integer',
                'between:1,100'
            ],
            "ends_at" => "date|after:tomorrow",

        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $promo->update($edit);
        return $this->sendResponse(
            PromoCodeResource::make($promo),
            __('messages.item-updated')
        );
    }

    public function checkCode(Request $request,): JsonResponse
    {
     
        $validator = Validator::make($request->all(), [
            "code" => [
                "required",
                'exists:promo_codes,code',
            ],
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $promo = PromoCode::whereCode($request->get('code'))->first();

        $endsAt = Carbon::create( $promo['ends_at']);

        $tomorrow = Carbon::tomorrow();

    

        if( !($endsAt->isAfter($tomorrow)) ){
            return $this->sendError(__('messages.invalid_promo_code'), 400);
        }


        return $this->sendResponse(
            PromoCodeResource::make($promo),
        );
    }

    public function destroy(PromoCode $promo): JsonResponse
    {
        $promo->delete();
        return $this->sendResponse(
            PromoCodeResource::make($promo),
            __('messages.item-deleted')
        );
    }
}
