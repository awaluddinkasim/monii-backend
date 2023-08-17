<?php

namespace App\Http\Services;

use App\Http\Repositories\MonthlyStatRepository;

class MonthlyStatService
{
    private $monthlyStatRepository;

    public function __construct(MonthlyStatRepository $repository)
    {
        $this->monthlyStatRepository = $repository;
    }

    public function getIncomes($id)
    {
        return $this->monthlyStatRepository->incomes($id);
    }

    public function getExpenses($id)
    {
        return $this->monthlyStatRepository->expenses($id);
    }
}
