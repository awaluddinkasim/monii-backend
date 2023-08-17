<?php

namespace App\Http\Services;

use App\Http\Repositories\DailyStatRepository;

class DailyStatService
{
    private $dailyStatRepository;

    public function __construct(DailyStatRepository $repository)
    {
        $this->dailyStatRepository = $repository;
    }

    public function getIncomes($id)
    {
        return $this->dailyStatRepository->incomes($id);
    }

    public function getExpenses($id)
    {
        return $this->dailyStatRepository->expenses($id);
    }
}
