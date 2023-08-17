<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\IncomeService;

class IncomeController extends Controller
{
    private $incomeService;

    public function __construct(IncomeService $incomeService)
    {
        $this->incomeService = $incomeService;
    }

    public function get(Request $request)
    {
        $incomes = $this->incomeService->getData($request->user()->id);

        return response()->json([
            'message' => 'success',
            'incomes' => $incomes
        ], 200);
    }

    public function store(Request $request)
    {
        $result = $this->incomeService->storeData($request->user()->id, $request);

        return response()->json($result, 200);
    }

    public function update(Request $request)
    {
        $result = $this->incomeService->updateData($request->user()->id, $request);

        return response()->json($result, 200);
    }

    public function delete(Request $request)
    {
        $result = $this->incomeService->deleteData($request->id);

        return response()->json($result, 200);
    }

    public function deleteAll(Request $request)
    {
        $result = $this->incomeService->deleteAllData($request->user(), $request->password);

        return response()->json($result['data'], $result['status']);
    }
}
