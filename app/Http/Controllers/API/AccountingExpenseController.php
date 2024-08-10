<?php /** @noinspection StaticInvocationViaThisInspection */

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountingExpenseResource;
use App\Models\AccountingExpense;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountingExpenseController extends BaseController
{

    public function index(Request $request): JsonResponse
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

        $accountingExpensesQuery = AccountingExpense::orderBy($field, $dir);

        $accountingExpenses = AccountingExpenseResource::collection($accountingExpensesQuery
            ->paginate(20));

        $response = [
            "total" => $accountingExpensesQuery->count(),
            "data" => $accountingExpenses,
        ];

        return $this->sendResponse($response, 'created');
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

            $accountingExpensesQuery = AccountingExpense::when(
                $from, static function ($q) use ($from) {
                $q->where('created_at', '>=', $from);
            })->when(
                $to, static function ($q) use ($to) {
                $q->where('created_at', '<', $to);
            });

            $accountingExpenses = AccountingExpenseResource::collection(
                $accountingExpensesQuery->paginate(20)
            );

            $response = [
                "total" => $accountingExpensesQuery->count(),
                "data" => $accountingExpenses,
            ];

            return $this->sendResponse($response, __('messages.found'));

        } catch (Exception) {
            return $this->sendError([], __('messages.error_validation'));
        }
    }

    public function store(Request $request): JsonResponse
    {
        $create = $request->all();

        $validator = Validator::make($create, [
            "name" => "required|string|min:3",
            "cost" => "required|numeric|min:0|max:1000",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $expense = AccountingExpense::create($create);
        $expenseResource = new AccountingExpenseResource($expense);

        return $this->sendResponse($expenseResource, __('messages.card_created'));
    }

    public function destroy(AccountingExpense $accountingExpense): JsonResponse
    {
        $accountingExpense->delete();
        return $this->sendResponse([], 'deleted');
    }
}
