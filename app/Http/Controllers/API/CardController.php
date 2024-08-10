<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\CardSimpleResource;
use App\Models\Card;
use App\Http\Controllers\BaseController;
use App\Models\CardType;
use App\Models\PendingReservation;
use App\Models\Reservation;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CardResource;
use App\Http\Resources\CardCollection;
use Illuminate\Http\Request;

class CardController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "page" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $cards = Card::whereStatus("1")->orderBy('created_at', 'desc')->paginate(20);

        $cards = new CardCollection($cards);
        $total = count(Card::select('id')->whereStatus('1')->get());

        $response = [
            "total" => $total,
            "data" => $cards
        ];

        return $this->sendResponse($response, __('messages.found'));
    }

    public function indexSimple(): JsonResponse
    {
        $activeCardsReservationsIds = Reservation::select('card_id')
            ->whereEndsAt(null)->pluck('card_id');

      

        $cards = Card::select(['id', 'card_type_id', 'rfid'])
            ->whereStatus("1")
            ->whereNotIn('id', $activeCardsReservationsIds)
            ->get();

        return $this->sendResponse(CardSimpleResource::collection($cards), __('messages.found'));
    }

    public function indexSimpleForPending(): JsonResponse
    {
        

        $pendingCardsReservationsIds = PendingReservation::select('card_id')
            ->pluck('card_id');

     

        $cards = Card::select(['id', 'card_type_id', 'rfid'])
            ->whereStatus("1")
            ->whereNotIn('id', $pendingCardsReservationsIds)
            ->get();

        return $this->sendResponse(CardSimpleResource::collection($cards), __('messages.found'));
    }

    public function indexSimpleList(): JsonResponse
    {


        $cards = Card::select(['id', 'card_type_id', 'rfid'])
            ->whereStatus("1")
            ->whereCardTypeId(4)
            ->get();

        return $this->sendResponse(CardSimpleResource::collection($cards), __('messages.found'));
    }

    public function show(Card $card): JsonResponse
    {
        if ($card->status === "0")
            return $this->sendResponse([], __('messages.not_found'));

        $cardResource = new CardResource($card);

        return $this->sendResponse($cardResource, __('messages.found'));

    }

    public function store(Request $request): JsonResponse
    {
        $create = $request->all();

        $validator = Validator::make($create, [
            "card_type_id" => "required|integer|exists:card_types,id",
            "rfid" => "required|unique:cards,rfid|max:255",
            "cost_per_hour" => "required|numeric|min:0|max:1000",
            "ends_at" => "exclude_unless:card_type_id," . CardType::SUB_ID . "|date|after:tomorrow",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        if (intval($create['card_type_id']) !== CardType::SUB_ID) {

            // $create['ends_at'] = '';
        }
        
        $card = Card::create($create);
        $cardResource = new CardResource($card);

        return $this->sendResponse($cardResource, __('messages.card_created'));
    }

    public function update(Request $request, Card $card): JsonResponse
    {
        $edit = $request->all();

        $validator = Validator::make($edit, [
            "rfid" => "max:255",
            "cost_per_hour" => "numeric|min:0|max:1000",
            "ends_at" => "date",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $card->update($edit);
        $cardResource = new CardResource($card);

        return $this->sendResponse($cardResource, __('messages.card_updated'));
    }

    public function destroy(Card $card): JsonResponse
    {
        $card->status = "0";
        $card->rfid = null;
        $card->save();
        return $this->sendResponse([], __('messages.card_deleted'));
    }
}
