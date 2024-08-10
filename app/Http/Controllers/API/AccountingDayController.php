<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\AccountingDayCollection;
use App\Models\AccountingDay;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;


class AccountingDayController extends BaseController
{

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "page" => "required|numeric",
            "field" => "nullable|in:reservations_count,reservations_total,products_total,final_total,created_at",
            "dir" => "nullable|in:asc,desc",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $field = $request->field ?? "created_at";
        $dir = $request->dir ?? "desc";

        $accountingDaysQuery = AccountingDay::orderBy($field, $dir);

        $accountingDays = new AccountingDayCollection($accountingDaysQuery->paginate(20));

        $response = [
            "total" => $accountingDaysQuery->count(),
            "data" => $accountingDays,
        ];

        return $this->sendResponse($response, __('messages.found'));
    }

    public function filter(Request $request): JsonResponse
    {
        $fullFilter = $request->all();
        $validator = Validator::make($fullFilter, [
            "page" => "required|numeric",
            "from" => ['required_without:to', 'date'],
            "to" => ['required_without:from', 'date'],
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        try {

            $from =
                !empty($fullFilter['from']) ?
                    Carbon::parse($fullFilter['from'])->startOfDay()
                    : null;
            $to =
                !empty($fullFilter['to']) ?
                    Carbon::parse($fullFilter['to'])->endOfDay()
                    : null;

            $accountingDaysQuery = AccountingDay::when(
                $from, static function ($q) use ($from) {
                $q->where('created_at', '>=', $from);
            })->when(
                $to, static function ($q) use ($to) {
                $q->where('created_at', '<', $to);
            });

            /** @noinspection StaticInvocationViaThisInspection */
            $accountingDays = new AccountingDayCollection(
                $accountingDaysQuery->paginate(20)
            );

            /** @noinspection StaticInvocationViaThisInspection */
            $response = [
                "total" => $accountingDaysQuery->count(),
                "data" => $accountingDays,
            ];

            return $this->sendResponse($response, __('messages.found'));

        } catch (\Exception) {
            return $this->sendError([], __('messages.error_validation'));
        }
    }

    public function show($accountingDay)
    {
        $date = base64_decode($accountingDay);

        $accountDay = AccountingDay::where("day", $date)->first();

        if (!$accountDay) {
            return $this->sendError(__('messages.not_found'));
        }
        return $this->sendResponse($accountDay, __('messages.found'));

    }

}
