<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\AccountingClientCollection;
use App\Http\Resources\AccountingClientResourse;
use App\Http\Resources\AccountingSupplierResource;
use App\Models\AccountingSupplier;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\AccountingClient;
use Illuminate\Support\Facades\Validator;


class AccountingSupplierController extends BaseController
{

    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "page" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $accountingQuery = AccountingSupplier::orderBy('created_at', 'desc');

        $accountingSuppliers = AccountingSupplierResource::collection($accountingQuery->paginate(25));

        $response = [
            "total" => $accountingQuery->count(),
            "data" => $accountingSuppliers,
        ];

        return $this->sendResponse($response, __('messages.found'));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'exists:suppliers,id',
            'amount_paid' => 'numeric|min:1|max:99990',
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $supplier = Supplier::find($request->get('supplier_id'));

        $supplier->balance += $request->get('amount_paid');
        $supplier->save();

        $accounting = $supplier->accounting()->create([
            'amount_paid' => $request->get('amount_paid'),
            'amount_dept' => 0,
            'balance' => $supplier->balance,
        ]);

        return $this->sendResponse(new AccountingSupplierResource($accounting), __("messages.found"));
    }

}
