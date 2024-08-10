<?php

namespace App\Http\Controllers\API;

use App\Models\Subscription;
use App\Models\AccountingClient;
use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\SubscriptionCollection;
use App\Http\Resources\SubscriptionResource;
use Illuminate\Http\Request;
use App\Models\AccountingDay;

class SubscriptionController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "page" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $subscriptions = Subscription::orderBy('created_at', 'desc')->paginate(20);

        $subscriptions = new SubscriptionCollection($subscriptions);
        $total = count(Subscription::select('id')->get());

        $response = [
            "total" => $total,
            "data" => $subscriptions
        ];

        return $this->sendResponse($response, __('messages.found'));
    }

    public function show(Subscription $subscription): JsonResponse
    {
        $subscriptionResource = new SubscriptionResource($subscription);

        return $this->sendResponse($subscriptionResource, __('messages.found'));
    }

    public function store(Request $request): JsonResponse
    {
        $create = $request->all();

        $validator = Validator::make($create, [
            "card_id" => "required|integer|exists:cards,id|unique:subscriptions,card_id",
            "client_id" => "required|integer|exists:clients,id",
            "amount_payed" => "required|numeric|min:0|max:10000",
            "ends_at" => "required|date|after:tomorrow",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $subscription = Subscription::create($create);
        AccountingClient::addSubscription($subscription);
        $subscriptionResource = new SubscriptionResource($subscription);

        AccountingDay::addSubscription($subscription->amount_payed);

        return $this->sendResponse($subscriptionResource, __('messages.subscription_created'));
    }

    public function update(Request $request, Subscription $subscription): JsonResponse
    {
        $edit = $request->all();

        $validator = Validator::make($edit, [
            'card_id' => 'integer|exists:cards,id|unique:subscriptions,card_id,' . $subscription->id,
            "client_id" => "integer|exists:clients,id",
            "amount_payed" => "numeric|min:0|max:10000",
            "ends_at" => "date|after:tomorrow",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        // dd($subscription,$edit,$edit['amount_payed']-$subscription['amount_payed']);

        
        $diffAmount =$edit['amount_payed']-$subscription['amount_payed'];


        $subscription->update($edit);

        AccountingClient::editSubscription($subscription->client_id, $diffAmount);

        AccountingDay::editSubscription($subscription->created_at, $diffAmount);

        return $this->sendResponse(new SubscriptionResource($subscription), __('messages.subscription_updated'));
    }

    public function destroy(Subscription $subscription): JsonResponse
    {
        $subscription->delete();
        AccountingDay::deleteSubscription($subscription->created_at, $subscription->amount_payed);
        AccountingClient::deleteSubscription($subscription->client_id, $subscription->amount_payed);


        return $this->sendResponse([], __('messages.subscription_deleted'));
    }
}
