<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Services\DailyStatService;
use App\Http\Services\MonthlyStatService;
use App\Http\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;
    private $dailyStatService;
    private $monthlyStatService;

    public function __construct(UserService $userService, DailyStatService $dailyStatService, MonthlyStatService $monthlyStatService)
    {
        $this->userService = $userService;
        $this->dailyStatService = $dailyStatService;
        $this->monthlyStatService = $monthlyStatService;
    }

    public function register(Request $request)
    {
        $result = $this->userService->registerUser($request);

        return response()->json($result['data'], $result['status']);
    }

    public function get(Request $request)
    {
        return response()->json([
            'message' => 'success',
            'user' => $request->user()
        ], 200);
    }

    public function stats(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'message' => 'success',
            'balances' => $this->userService->getBalances($user),
            'daily_stats' => [
                'incomes' => $this->dailyStatService->getIncomes($user->id),
                'expenses' => $this->dailyStatService->getExpenses($user->id)
            ],
            'monthly_stats' => [
                'incomes' => $this->monthlyStatService->getIncomes($user->id),
                'expenses' => $this->monthlyStatService->getExpenses($user->id)
            ],
        ], 200);
    }

    public function update(Request $request)
    {
        $result = $this->userService->updateUser($request);

        return response()->json($result['data'], $result['status']);
    }
}
