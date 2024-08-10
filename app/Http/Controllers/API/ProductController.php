<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Http\Resources\ProductSimpleResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;

class ProductController extends BaseController
{

    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "page" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $products = Product::whereStatus("1")->orderBy('created_at', 'desc');
        $total = $products->count();

        $products = $products->paginate(20);

        $products = new ProductCollection($products);

        $response = [
            "total" => $total,
            "data" => $products
        ];

        return $this->sendResponse($response, __('messages.found'));
    }

    public function indexSimple(Request $request): JsonResponse
    {
        $products = Product::select(['id', 'name', 'scan_code', 'stock'])
            ->whereStatus("1")
            ->where('stock', '>', 0)
            ->orderBy('name')->get();


        return $this->sendResponse(ProductSimpleResource::collection($products), __('messages.found'));
    }

    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "q" => "required|max:255",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $products = Product::whereStatus("1")
            ->where("scan_code", "like", '%' . $request->q . '%')
            ->orWhere("name", "like", '%' . $request->q . '%')
            ->get();

        $products = new ProductCollection($products);

        return $this->sendResponse($products, __('messages.found'));
    }

    public function show(Product $product): JsonResponse
    {
        if ($product->status === "0")
            return $this->sendResponse([], __('messages.not_found'));

        $productResource = new ProductResource($product);

        return $this->sendResponse($productResource, __('messages.found'));
    }

    public function store(Request $request): JsonResponse
    {
        $create = $request->all();

        $validator = Validator::make($create, [
            'supplier_id' => 'required|exists:suppliers,id',
            'scan_code' => 'required|unique:products,scan_code',
            "name" => 'required|max:255',
            "photo" => 'image|mimes:png,jpg|max:5000',
            'cost_price' => 'required|numeric|min:1|max:99990',
            'retail_price' => 'required|numeric|min:1|max:99990',
            'stock' => 'required|numeric|min:1|max:99990',
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        if (isset($create["photo"])) {
            $request->file('photo')->store('products');
            $create["photo"] = $request->file('photo')->hashName();
        }

        $product = Product::create($create);

        $totalCost = $product->stock * $product->cost_price;
        $product->supplier->balance -= $totalCost;
        $product->supplier->save();

        $product->supplier->accounting()->create([
            'amount_dept' => $totalCost,
            'balance' => $product->supplier->balance,
        ]);

        $productResource = new ProductResource($product);
        return $this->sendResponse(
            $productResource,
            __("messages.product_created")
        );
    }

    public function update(Request $request, product $product): JsonResponse
    {
        $edit = $request->all();

        $validator = Validator::make($edit, [
            'supplier_id' => 'exists:suppliers,id',
            'scan_code' => 'unique:products,scan_code',
            "name" => 'max:255',
            "photo" => 'image|mimes:png,jpg|max:5000',
            'cost_price' => 'numeric|min:1|max:99990',
            'retail_price' => 'numeric|min:1|max:99990',
            'stock' => 'numeric|min:1|max:99990',
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        if (isset($edit["photo"])) {
            Storage::disk('products')->delete($product->photo);
            $request->file('photo')->store('products');
            $edit["photo"] = $request->file('photo')->hashName();
        }

        if ($request->has('stock')) {
            $stock = $request->get('stock');
            if ($stock > $product->stock) {
                $difference = $stock - $product->stock;
                $totalCost = $difference * $product->cost_price;
                $product->supplier->balance -= $totalCost;
                $product->supplier->save();

                $product->supplier->accounting()->create([
                    'amount_dept' => $totalCost,
                    'balance' => $product->supplier->balance,
                ]);
            }
        }

        $product->update($edit);

        $productResource = new ProductResource($product);
        return $this->sendResponse($productResource, __("messages.product_updated"));
    }

    public function destroy(product $product): JsonResponse
    {
        if ($product->status === "1") {
            $product->update(["status" => "0", 'scan_code' => null]);
        }

        return $this->sendResponse([], __("messages.product_deleted"));
    }


}
