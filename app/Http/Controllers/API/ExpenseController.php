<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\ExpenseService;

class ExpenseController extends Controller
{
    private $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    public function get(Request $request)
    {
        $expenses = $this->expenseService->getData($request->user()->id);

        return response()->json([
            'message' => 'success',
            'expenses' => $expenses
        ], 200);
    }

    public function store(Request $request)
    {
        $result = $this->expenseService->storeData($request->user()->id, $request);

        return response()->json($result, 200);
    }

    public function update(Request $request)
    {
        $result = $this->expenseService->updateData($request->user()->id, $request);

        return response()->json($result, 200);
    }

    public function delete(Request $request)
    {
        $result = $this->expenseService->deleteData($request->id);

        return response()->json($result, 200);
    }

    public function deleteAll(Request $request)
    {
        $result = $this->expenseService->deleteAllData($request->user(), $request->password);

        return response()->json($result['data'], $result['status']);
    }
}
