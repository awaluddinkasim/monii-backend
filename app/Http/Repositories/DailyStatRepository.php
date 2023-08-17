<?php

namespace App\Http\Repositories;

use Carbon\Carbon;
use App\Models\User;
use App\Models\DailyStat;

class DailyStatRepository
{
    public function incomes($id)
    {
        $user = User::find($id);

        $startDate = Carbon::now()->timezone($user->tz)->startOfMonth();
        $endDate = Carbon::now()->timezone($user->tz)->endOfMonth();

        $incomes = [];

        while ($startDate->lte($endDate)) {
            $income = DailyStat::where('user_id', $user->id)
                ->where('kategori', 'pemasukan')
                ->whereDate('tanggal', $startDate)
                ->first();

            if ($income) {
                $income = $income->nominal;
            } else {
                $income = 0;
            }

            $incomes[] = ['tanggal' =>  $startDate->format('d'), 'nominal' => $income];

            $startDate->addDay();
        }

        return $incomes;
    }


    public function expenses($id)
    {
        $user = User::find($id);

        $startDate = Carbon::now()->timezone($user->tz)->startOfMonth();
        $endDate = Carbon::now()->timezone($user->tz)->endOfMonth();

        $expenses = [];

        while ($startDate->lte($endDate)) {
            $expense = DailyStat::where('user_id', $user->id)
                ->where('kategori', 'pengeluaran')
                ->whereDate('tanggal', $startDate)
                ->first();

            if ($expense) {
                $expense = $expense->nominal;
            } else {
                $expense = 0;
            }

            $expenses[] = ['tanggal' =>  $startDate->format('d'), 'nominal' => $expense];

            $startDate->addDay();
        }

        return $expenses;
    }
}
