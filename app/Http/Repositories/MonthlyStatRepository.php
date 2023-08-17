<?php

namespace App\Http\Repositories;

use App\Models\MonthlyStat;
use App\Models\User;
use Carbon\Carbon;

class MonthlyStatRepository
{
    public function incomes($id)
    {
        $user = User::find($id);

        $year = Carbon::now()->timezone($user->tz)->year;

        $incomes = [];

        for ($month = 1; $month <= 12; $month++) {
            $income = MonthlyStat::where('user_id', $user->id)
                ->where('kategori', 'pemasukan')
                ->where('bulan', $month)
                ->where('tahun', $year)
                ->first();

            if ($income) {
                $income = $income->nominal;
            } else {
                $income = 0;
            }

            $incomes[] = [
                'bulan' => $month,
                'nominal' => $income
            ];
        }

        return $incomes;
    }


    public function expenses($id)
    {
        $user = User::find($id);

        $year = Carbon::now()->timezone($user->tz)->year;

        $expenses = [];

        for ($month = 1; $month <= 12; $month++) {
            $expense = MonthlyStat::where('user_id', $user->id)
                ->where('kategori', 'pengeluaran')
                ->where('bulan', $month)
                ->where('tahun', $year)
                ->first();

            if ($expense) {
                $expense = $expense->nominal;
            } else {
                $expense = 0;
            }

            $expenses[] = [
                'bulan' => $month,
                'nominal' => $expense
            ];
        }

        return $expenses;
    }
}
