<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\PendingreservationResource;
use App\Http\Resources\ReservationResource;
use App\Models\PendingReservation;
use App\Models\Reservation;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PendingReservationsController extends BaseController
{
    public function active(): JsonResponse
    {
        $reservations = PendingReservation::all();
        $reservations = PendingreservationResource::collection($reservations);
        return $this->sendResponse($reservations, __('messages.found'));
    }

    public function convert(PendingReservation $reservation): JsonResponse
    {

        $lastActiveShift = Shift::where("end_time")->first();

        if (is_null($lastActiveShift)) {
            return $this->sendError(__('messages.not_found_active_shift'));
        }

        $realReservation = Reservation::create([
            'card_id' => $reservation->card_id,
            'client_id' => $reservation->client_id,
            'starts_at' => Carbon::now(),
            'shift_id' => $lastActiveShift->id,
            'promo_code_id' => $reservation->promo_code_id,
            'promo' => $reservation->promo,
        ]);

        $reservation->delete();
        $resource = new ReservationResource($realReservation);
        return $this->sendResponse(
            $resource,
            __("messages.reservation_created")
        );
    }

    public function update(Request $request, PendingReservation $pendingReservation): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'card_id' => 'exists:cards,id',
            "client_id" => 'exists:clients,id',
            'start_time' => 'date',
            'end_time' => 'date',
            'percent' => [
                'nullable',
                'integer',
                'between:1,100',
            ]
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        if (count($request->all())) {

            if ($request->has('percent')) {
                $promo = $pendingReservation->promo;
                $promo['percent'] = $request->get('percent');
                $pendingReservation->promo = $promo;
            }

            $pendingReservation->update($request->all());
        }

        return $this->sendResponse(
            PendingreservationResource::make($pendingReservation),
            __('messages.reservation_updated')
        );
    }

    public function destroy(PendingReservation $pendingReservation): JsonResponse
    {
        $pendingReservation->delete();
        return $this->sendResponse([], __("messages.reservation_deleted"));
    }
}
