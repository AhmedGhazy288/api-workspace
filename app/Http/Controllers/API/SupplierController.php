<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\SupplierResource;
use App\Http\Resources\SupplierCollection;


class SupplierController extends BaseController
{

    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "page" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $suppliers = Supplier::whereStatus("1")->orderBy('created_at', 'desc')->paginate(20);

        $suppliers = new SupplierCollection($suppliers);
        $total = count(Supplier::select('id')->get());

        $response = [
            "total" => $total,
            "data" => $suppliers
        ];

        return $this->sendResponse($response, __('messages.found'));
    }

    public function indexSimple()
    {
        $suppliers = Supplier::select('id', 'company')->whereStatus("1")->orderBy('company', 'ASC')->get();

        return $this->sendResponse($suppliers, __('messages.found'));
    }

    public function show(Supplier $supplier)
    {
        if ($supplier->status == "0")
            return $this->sendResponse([], __('messages.not_found'));

        $supplier = new SupplierResource($supplier);
        return $this->sendResponse($supplier, __('messages.found'));
    }

    public function store(Request $request)
    {
        $create = $request->all();
        $validator = Validator::make($create, [
            'company' => 'required|max:255|unique:suppliers,company',
            "photo" => 'image|mimes:png,jpg|max:5000',
            'phone' => 'required|min:11',
        ]);


        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        if (isset($create["photo"])) {
            $request->file('photo')->store('suppliers');
            $create["photo"] = $request->file('photo')->hashName();
        }

        $supplier = Supplier::create($create);


        return $this->sendResponse(new SupplierResource($supplier), __('messages.supplier_created'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $edit = $request->all();

        $validator = Validator::make($edit, [
            'company' => 'max:255|unique:suppliers,company,' . $supplier->id,
            "photo" => 'image|mimes:png,jpg|max:5000',
            'phone' => 'min:11',
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        if (isset($edit["photo"])) {
            Storage::disk('suppliers')->delete($supplier->photo);
            $request->file('photo')->store('suppliers');
            $edit["photo"] = $request->file('photo')->hashName();
        }

        $supplier->update($edit);

        return $this->sendResponse(new SupplierResource($supplier), __('messages.supplier_updated'));
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplier->status = '0';
        $supplier->save();
        return $this->sendResponse([], __('messages.supplier_deleted'));
    }
}
