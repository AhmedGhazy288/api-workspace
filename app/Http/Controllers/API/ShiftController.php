<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Models\Shift;
use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ShiftCollection;
use App\Http\Resources\ShiftResource;
use Carbon\Carbon;

class ShiftController extends BaseController
{

    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "page" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $shiftsQuery = Shift::orderBy('created_at', 'desc');

        $shifts = new ShiftCollection($shiftsQuery->paginate(20));
        $total = $shiftsQuery->count();

        $response = [
            "total" => $total,
            "data" => $shifts
        ];

        return $this->sendResponse($response, __('messages.found'));
    }

    public function getReservations(Request $request, $id): JsonResponse
    {
        $validator = Validator::make([...$request->all(), 'id' => $id], [
            'id' => 'required|exists:shifts,id',
            "page" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $reservationsQuery = Reservation::whereShiftId($id)->orderBy('created_at', 'desc');

        $reservations = ReservationResource::collection($reservationsQuery->paginate(20));
        $total = $reservationsQuery->count();

        $response = [
            "total" => $total,
            "data" => $reservations
        ];

        return $this->sendResponse($response, __('messages.found'));
    }

    public function show(Shift $shift): JsonResponse
    {
        if (!$shift) {
            return $this->sendError(__('messages.not_found'));
        }

        $shiftResource = new ShiftResource($shift);
        return $this->sendResponse($shiftResource, __('messages.found'));
    }

    public function store(): JsonResponse
    {
        $count = count(Shift::where("end_time", null)->get());

        if ($count > 0) {
            return $this->sendError([], __('messages.should_close_all_shifts'));
        }

        $shift["user_id"] = auth()->user()->id;
        $shift["start_time"] = Carbon::now();

        $shiftResource = new ShiftResource(Shift::create($shift));

        return $this->sendResponse($shiftResource, __('messages.shift_started'));
    }

    public function update(): JsonResponse
    {
        $user_shift = Shift::where("user_id", auth()->user()->id)
            ->where("end_time")
            ->latest()
            ->first();

        if (!$user_shift) {
            return $this->sendError(__('messages.not_found_active_shift'));
        }

        $user_shift->update(["end_time" => Carbon::now()]);


        return $this->sendResponse($user_shift, __('messages.shift_ended'));
    }

    public function active()
    {
        $active = Shift::where("end_time", null)->whereUserId(auth()->user()->id)->get();
        $count = count($active);

        $active = new ShiftCollection($active);

        if ($count === 0) {
            return $this->sendResponse([], __('messages.not_found_active_shift'), 210);

        }

        return $this->sendResponse($active, __('messages.Found_active_shift'));
    }
}
