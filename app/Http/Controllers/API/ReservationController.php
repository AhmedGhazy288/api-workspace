<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Http\Requests\EndDateRequest;
use App\Http\Requests\ReservationUpdateRequest;
use App\Http\Resources\PendingreservationResource;
use App\Http\Resources\ReceiptResource;
use App\Models\PendingReservation;
use App\Models\PromoCode;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ReservationCollection;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Models\Card;
use App\Models\CardType;
use App\Models\ReservationProduct;
use Carbon\Carbon;
use App\Models\AccountingClient;
use App\Models\AccountingDay;
use App\Models\Subscription;



class ReservationController extends BaseController
{

    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "page" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $reservationsQuery = Reservation::orderBy('created_at', 'desc');

        $reservations = new ReservationCollection($reservationsQuery->paginate(20));
        $total = $reservationsQuery->count();

        $response = [
            "total" => $total,
            "data" => $reservations
        ];

        return $this->sendResponse($response, __('messages.found'));
    }

    public function active(): JsonResponse
    {
        $reservations = Reservation::where("ends_at")->get();
        $reservations = new ReservationCollection($reservations);

        return $this->sendResponse($reservations, __('messages.found'));
    }

    public function show(Reservation $reservation): JsonResponse
    {
        $reservationResource = new ReceiptResource($reservation);
        return $this->sendResponse($reservationResource, __('messages.found'));
    }

    public function store(Request $request): JsonResponse
    {
        $create = $request->all();

        $validator = Validator::make($create, [
            'code' => [
                'nullable',
                'exists:promo_codes,code',
            ],
            'card_id' => 'required|exists:cards,id',
            "client_id" => 'required|exists:clients,id',
            'is_pending' => 'required|boolean',
            'start_time' => 'required_if:is_pending,==,true|date',
            "end_time" => 'required_if:is_pending,==,true|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        if (isset($create['code'])) {
            $promoCode = PromoCode::whereCode($create['code'])->first();

            $endsAt = Carbon::create($promoCode['ends_at']);

            $tomorrow = Carbon::tomorrow();

            if (!($endsAt->isAfter($tomorrow))) {
                return $this->sendError(__('messages.invalid_promo_code'), 400);
            } else {
                $create['promo_code_id'] = $promoCode->id;
                $create['promo'] = $promoCode;
            }
        }

        if ($request->get('is_pending')) {
            $reservation = PendingReservation::create($create);
            $resource = new PendingReservationResource($reservation);
        } else {
            $lastActiveShift = Shift::where("end_time")->first();

            if (is_null($lastActiveShift)) {
                return $this->sendError(__('messages.not_found_active_shift'));
            }

            $create["starts_at"] = Carbon::now();
            $create['shift_id'] = $lastActiveShift->id;

            $reservation = Reservation::create($create);
            $resource = new ReservationResource($reservation);
        }

        return $this->sendResponse(
            $resource,
            __("messages.reservation_created")
        );
    }

    public function update(ReservationUpdateRequest $request, Reservation $reservation): JsonResponse
    {

        if ($reservation->ends_at) {
            return $this->sendError(__('messages.already_submitted'));
        }

        $validatedData = $request->validated();

        $reservationStarts = $reservation->starts_at;
        $reservationEnds = Carbon::parse($validatedData['end_date']);

        $reservationEnds = $reservationEnds->isAfter($reservationStarts) ?
            $reservationEnds : Carbon::now();

        $cardCostPerHour = $reservation->card->cost_per_hour;
        $hoursDiff = $reservationEnds->diffAsCarbonInterval($reservation->starts_at)->total('hours');

        $reservationTotal = $cardCostPerHour * $hoursDiff;

        if ($request->has('code')) {
            $promoCode = PromoCode::whereCode($request->get('code'))->first();

            $endsAt = Carbon::create($promoCode['ends_at']);

            $tomorrow = Carbon::tomorrow();

            if (!($endsAt->isAfter($tomorrow))) {
                return $this->sendError(__('messages.invalid_promo_code'), 400);
            } else {
                $edit['promo_code_id'] = $promoCode->id;
                $edit['promo'] = $promoCode;
            }
        }

        switch ($reservation->card->card_type_id) {

                //FOR NORMAL
            case CardType::NORMAL_ID:
                if (
                    $hoursDiff >= $reservation->card->cardType->max_hours ||
                    $reservationTotal > $reservation->card->cardType->max_cost
                ) {
                    $reservationTotal = $reservation->card->cardType->max_cost;
                }
                break;

                //FOR SUBSCRIPTION
            case CardType::SUB_ID:
                //IF ACTIVE THEN USE CARD ELSE LIKE NORMAL
                $subscription = Subscription::whereCardId($reservation["card_id"])->first();
                if ($subscription->ends_at && $reservationEnds->isAfter(Carbon::create($subscription->ends_at))) {
                    $reservationTotal = $cardCostPerHour * $hoursDiff;
                } else {
                    $reservationTotal = 0;
                }
                break;
        }

        $productsTotal = $reservation->reservationProducts->sum("total");

        $edit['reservation_total'] = number_format((float)$reservationTotal, 2, '.', '');
        $edit['ends_at'] = $reservationEnds;
        $edit['products_total'] = number_format((float)$productsTotal, 2, '.', '');
        $edit['total'] = (float)$edit['reservation_total'] + (float)$edit['products_total'];
        $edit['discount'] = $validatedData['discount'] < $edit['total'] ? $validatedData['discount'] : 0;
        $edit['total'] -= (float)$edit['discount'];
        $edit['amount_paid'] = min($validatedData['amount_paid'], $edit['total']);

        //TODO BALANCE
        //        $edit['amount_paid'] = $edit['total'] - $reservation->card->balance;
        //
        //        if ($edit['total'] > $reservation->card->balance) {
        //            $reservation->card->update(
        //                [
        //                    "balance" => 0
        //                ]
        //            );
        //        } else {
        //            $reservation->card->update(
        //                [
        //                    "balance" => $reservation->card->balance - $edit['total']
        //                ]
        //            );
        //        }

        $reservation->update($edit);

        Shift::addToActive($reservationTotal, $productsTotal);

        if ($reservation->client_id) {
            AccountingClient::add($reservation);
        }

        AccountingDay::add($reservation);
        return $this->sendResponse([], __("messages.reservation_updated"));
    }

    public function destroy(Reservation $reservation): JsonResponse
    {

        $reservation->delete();
        return $this->sendResponse([], __("messages.reservation_deleted"));
    }

    public function preview(EndDateRequest $request, Reservation $reservation): JsonResponse
    {

        if ($reservation->ends_at) {
            return $this->sendError(__('messages.already_submitted'));
        }

        $validatedData = $request->validated();

        $reservationStarts = $reservation->starts_at;
        $reservationEnds = Carbon::parse($validatedData['end_date']);

        $reservationEnds = $reservationEnds->isAfter($reservationStarts) ?
            $reservationEnds : Carbon::now();

        $cardCostPerHour = $reservation->card->cost_per_hour;
        $hoursDiff = $reservationEnds->diffAsCarbonInterval($reservation->starts_at)->total('hours');

        $reservationTotal = $cardCostPerHour * $hoursDiff;

        switch ($reservation->card->card_type_id) {
                //FOR NORMAL
            case CardType::NORMAL_ID:
                if (
                    $hoursDiff >= $reservation->card->cardType->max_hours ||
                    $reservationTotal > $reservation->card->cardType->max_cost
                ) {
                    $reservationTotal = $reservation->card->cardType->max_cost;
                }
                break;

                //FOR SUBSCRIPTION
            case CardType::SUB_ID:
                //IF ACTIVE THEN USE CARD ELSE LIKE NORMAL
               
                
                $subscription = Subscription::whereCardId($reservation["card_id"])->first();
                //  dd($reservationEnds->isAfter(Carbon::create($subscription->ends_at)));
                if ($subscription->ends_at && $reservationEnds->isAfter(Carbon::create($subscription->ends_at))) {
                    $reservationTotal = $cardCostPerHour * $hoursDiff;
                } else {
                    $reservationTotal = 0;
                }
                // dd($reservationTotal);
                break;
        }

        $productsTotal = $reservation->reservationProducts->sum("total");

        $promoTotal = $reservationTotal;

        if ($request->has('code')) {
            $promoCode = PromoCode::whereCode($request->get('code'))->first();

            $endsAt = Carbon::create($promoCode['ends_at']);

            $tomorrow = Carbon::tomorrow();

            if (!($endsAt->isAfter($tomorrow))) {
                return $this->sendError(__('messages.invalid_promo_code'), 400);
            } else {
                $edit['promo_code_id'] = $promoCode->id;
                $edit['promo'] = $promoCode;
            }
        }

        // $endsAt = Carbon::create($reservation->promo['ends_at']);

        // $tomorrow = Carbon::tomorrow();

        // if (!($endsAt->isAfter($tomorrow))) {
        //     return $this->sendError(__('messages.invalid_promo_code'), 400);
        // } else 


        // if (isset($reservation->promo)) {
        //     $promoTotal = $reservationTotal * (100 - $reservation->promo['percent']) / 100;
        // }

        return $this->sendResponse([
            'before_promo' => (float)number_format((float)$reservationTotal + $productsTotal, 2),
            'total' => (float)number_format((float)$promoTotal + $productsTotal, 2),
            'totalReservation' => (float)number_format((float)$promoTotal, 2),
            'totalProducts' => (float)number_format((float)$productsTotal, 2),
        ], '');
    }
}
