<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CardTypeResource;
use App\Http\Resources\CardTypeCollection;
use Illuminate\Http\Request;
use App\Models\CardType;

class CardTypesController extends BaseController
{
    public function index(): JsonResponse
    {

        $cardTypes = CardType::whereStatus("1")->get();

        $cardTypes = new CardTypeCollection($cardTypes);

        return $this->sendResponse($cardTypes, __('messages.found'));

    }

    public function show(CardType $cardType): JsonResponse
    {
        $cardTypeResource = new CardTypeResource($cardType);

        return $this->sendResponse(
            $cardTypeResource,
            __('messages.found')
        );
    }

    public function indexSimple(): JsonResponse
    {
        $cardTypes = CardType::select(['id', 'name', 'cost_per_hour'])
            ->whereStatus("1")
            ->orderBy('name', 'ASC')
            ->get();

        return $this->sendResponse($cardTypes, __('messages.found'));
    }

    public function store(Request $request)
    {
        $create = $request->all();

        $validator = Validator::make($create, [
            "name" => "required|max:255|unique:card_types,name",
            "cost_per_hour" => "required|numeric|min:1|max:1000",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $cardType = CardType::create($create);

        $success['id'] = $cardType->id;
        $success['name'] = $cardType->name;

        return $this->sendResponse($success, __('messages.card_Type_created'));
    }

    public function update(Request $request, CardType $cardType)
    {
     
        $edit = $request->all();

        $validator = Validator::make($edit, [
            "name" => "max:255|unique:card_types,name," . $cardType->id,
            "cost_per_hour" => "numeric|min:0|max:1000",
            "max_hours" => "numeric|min:0|max:24",
            "max_cost" => "numeric|min:0|max:1000",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $cardType->update($edit);

        $cardTypeResource = new CardTypeResource($cardType);


        return $this->sendResponse( $cardTypeResource, __('messages.card_Type_updated'));
    }

    public function destroy(CardType $cardType)
    {
        if ($cardType->status === "1") {
            $cardType->update(["status" => "0"]);
        }

        return $this->sendResponse([], __('messages.card_Type_created'));
    }
}
