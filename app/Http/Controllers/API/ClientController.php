<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\ClientSimpleResource;
use App\Models\Client;
use App\Http\Controllers\BaseController;
use App\Models\PendingReservation;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ClientCollection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "page" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $clients = Client::whereStatus("1")->orderBy('created_at', 'desc')->paginate(20);

        $clients = new ClientCollection($clients);
        $total = count(Client::select('id')->whereStatus('1')->get());

        $response = [
            "total" => $total,
            "data" => $clients
        ];

        return $this->sendResponse($response, __('messages.found'));
    }

    public function indexSimple(): JsonResponse
    {
        $activeClientsReservationsIds = Reservation::select('client_id')
            ->whereEndsAt(null)->pluck('client_id');

        $clients = Client::select(['id', 'name', 'phone'])
            ->whereStatus("1")
            ->whereNotIn('id', $activeClientsReservationsIds)
            ->orderBy('name', 'ASC')
            ->paginate(20);

        return $this->sendResponse(
            new   ClientCollection($clients),
            __('messages.found')
        );
    }

    public function indexSimpleForPending(): JsonResponse
    {

        $pendingClientsReservationsIds = PendingReservation::select('client_id')
            ->pluck('client_id');

        $clients = Client::select(['id', 'name', 'phone'])
            ->whereStatus("1")
            ->whereNotIn('id', $pendingClientsReservationsIds)
            ->orderBy('name', 'ASC')
            ->paginate(20);

        return $this->sendResponse(
            new ClientCollection($clients),
            __('messages.found')
        );
    }

    public function indexSimpleList(): JsonResponse
    {


        $clients = Client::select(['id', 'name', 'phone'])
            ->whereStatus("1")
            ->orderBy('name', 'ASC')
            ->get();

        return $this->sendResponse(
            ClientSimpleResource::collection($clients),
            __('messages.found')
        );
    }

    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "q" => "required|max:255",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $client = Client::whereStatus("1")
            ->where("name", "like", '%' . $request->q . '%')
            ->orWhere("phone", "like", '%' . $request->q . '%')
            ->get();

        $client = new ClientCollection($client);

        return $this->sendResponse($client, __('messages.found'));
    }

    public function searchForCashier(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "q" => "required|max:255",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }
        $activeClientsReservationsIds = Reservation::select('client_id')
            ->whereEndsAt(null)->pluck('client_id');

        $client = Client::whereStatus("1")->whereNotIn('id', $activeClientsReservationsIds)
            ->where("name", "like", '%' . $request->q . '%')
            ->orWhere("phone", "like", '%' . $request->q . '%')

            ->get();

        $client = new ClientCollection($client);

        return $this->sendResponse($client, __('messages.found'));
    }

    public function searchForPending(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "q" => "required|max:255",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }
        $pendingClientsReservationsIds = PendingReservation::select('client_id')
            ->pluck('client_id');

        $client = Client::whereStatus("1")->whereNotIn('id', $pendingClientsReservationsIds)
            ->where("name", "like", '%' . $request->q . '%')
            ->orWhere("phone", "like", '%' . $request->q . '%')

            ->get();

        $client = new ClientCollection($client);

        return $this->sendResponse($client, __('messages.found'));
    }

    public function export(Request $request): StreamedResponse
    {
        $fileName = 'clients.csv';
        $clients = Client::select(['id', 'name', 'phone'])
            ->whereStatus('1')
            ->orderBy('id')
            ->get();

        $headers = [
            'Set-Cookie' => 'fileDownload=true; path=/',
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Content-Transfer-Encoding' => 'binary',
        ];

        $callback = function () use ($clients) {
            $columns = [
                'Number',
                'Name',
                'Phone',
            ];

            //Use tab as field separator
            $newTab = "\t";
            $newLine = "\n";

            $fputcsv = count($columns) ?
                implode(',', $columns) . $newLine :
                '';

            foreach ($clients as $client) {
                $row['Number'] = $client->id;
                $row['Name'] = $client->name;
                $row['Phone'] = $client->phone;

                $fputcsv .=
                    implode(',', [
                        $row['Number'],
                        $row['Name'],
                        "'" . $row['Phone'],
                    ]) . $newLine;
            }

            $encoded_csv = mb_convert_encoding($fputcsv, 'UTF-16LE', 'UTF-8');
            $headers['Content-Length'] = strlen($encoded_csv);
            echo chr(255) . chr(254) . $encoded_csv;
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(Client $client): JsonResponse
    {
        if ($client->status == "0")
            return $this->sendResponse([], __('messages.not_found'));

        $clientResource = new ClientResource($client);

        return $this->sendResponse($clientResource, __('messages.found'));
    }

    public function store(Request $request): JsonResponse
    {
        $create = $request->all();

        $validator = Validator::make($create, [
            "name" => 'required|max:255',
            "phone" => 'required|string|min:11|unique:clients,phone',
            'real_password' => 'required|min:6|max:12|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $client = Client::createWithCredentials($create);
        return $this->sendResponse(new ClientResource($client), __('messages.client_created'));
    }

    public function update(Request $request, Client $client): JsonResponse
    {
        $edit = $request->all();

        $validator = Validator::make($edit, [
            "name" => 'max:255',
            "phone" => 'min:11|unique:clients,phone,' . $client->id,
            "username" => 'min:4|max:10|unique:username,' . $client->id,
            "password" => 'min:6|max:24',
            "real_password" => 'min:6|max:24',
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $client->update($edit);

        return $this->sendResponse(new ClientResource($client), __('messages.client_updated'));
    }

    public function destroy(Client $client): JsonResponse
    {
        $client->status = "0";
        $client->save();
        return $this->sendResponse([], __('messages.client_deleted'));
    }
}
