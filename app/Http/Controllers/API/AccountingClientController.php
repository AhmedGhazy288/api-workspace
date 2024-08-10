<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\AccountingClientCollection;
use App\Http\Resources\AccountingClientResourse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\AccountingClient;
use Illuminate\Support\Facades\Validator;


class AccountingClientController extends BaseController
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

        $accountingClientsQuery = AccountingClient::orderBy($field, $dir);

        $accountingClients = AccountingClientResourse::collection($accountingClientsQuery->paginate(20));

        $response = [
            "total" => $accountingClientsQuery->count(),
            "data" => $accountingClients,
        ];

        return $this->sendResponse($response, __('messages.found'));
    }

    public function show($accountingClient)
    {
        $accountClient = AccountingClient::where("client_id", $accountingClient)->first();

        if (!$accountClient) {
            return $this->sendError(__('messages.not_found'));
        }

        return $this->sendResponse($accountClient, __('messages.found'));
    }

    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "q" => "required|max:255",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $accounting = AccountingClient::whereHas('client', static function ($k) use ($request) {
            $k->where(
                "phone", "like", '%' . $request->q . '%'
            )->orWhere("name", "like", '%' . $request->q . '%');
        })->get();

        $accounting = AccountingClientResourse::collection($accounting);

        return $this->sendResponse($accounting, __('messages.found'));
    }
}
