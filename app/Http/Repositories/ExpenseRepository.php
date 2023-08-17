<?php

namespace App\Http\Repositories;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Expense;
use App\Models\DailyStat;
use App\Models\MonthlyStat;

class ExpenseRepository
{
    public function get($id)
    {
        $expenses = Expense::where('user_id', $id)->orderBy('tanggal', 'DESC')->orderBy('created_at', 'DESC')->paginate(10);

        return $expenses;
    }

    public function store($id, $data)
    {
        $user = User::find($id);

        $tanggal = Carbon::parse($data->tanggal);

        $expense = new Expense();
        $expense->user_id = $user->id;
        $expense->keperluan = $data->keperluan;
        $expense->jumlah = convertToNumber($data->jumlah);
        $expense->tanggal = $tanggal;
        $expense->save();

        $daily = DailyStat::where('user_id', $user->id)
            ->where('kategori', 'pengeluaran')
            ->whereDate('tanggal', $tanggal)
            ->first();
        if ($daily) {
            $daily->nominal = $daily->nominal + convertToNumber($data->jumlah);
            $daily->update();
        } else {
            $daily = new DailyStat();
            $daily->user_id = $user->id;
            $daily->nominal = convertToNumber($data->jumlah);
            $daily->tanggal = $tanggal;
            $daily->kategori = 'pengeluaran';
            $daily->save();
        }

        $monthly = MonthlyStat::where('user_id', $user->id)
            ->where('kategori', 'pengeluaran')
            ->where('bulan', $tanggal->month)
            ->where('tahun', $tanggal->year)
            ->first();
        if ($monthly) {
            $monthly->nominal = $monthly->nominal + convertToNumber($data->jumlah);
            $monthly->update();
        } else {
            $monthly = new MonthlyStat();
            $monthly->user_id = $user->id;
            $monthly->nominal = convertToNumber($data->jumlah);
            $monthly->bulan = $tanggal->month;
            $monthly->tahun = $tanggal->year;
            $monthly->kategori = 'pengeluaran';
            $monthly->save();
        }

        return [
            'message' => 'Berhasil',
            'expenses' => $this->get($user->id)
        ];
    }

    public function update($id, $data)
    {
        $user = User::find($id);

        $expense = Expense::find($data->id);

        if ($expense->tanggal == $data->tanggal) {
            $tanggal = Carbon::parse($data->tanggal);

            $daily = DailyStat::where('user_id', $user->id)
                ->where('kategori', 'pengeluaran')
                ->whereDate('tanggal', $tanggal)
                ->first();
            $daily->nominal = ($daily->nominal - $expense->jumlah) + convertToNumber($data->jumlah);
            $daily->update();

            $monthly = MonthlyStat::where('user_id', $user->id)
                ->where('kategori', 'pengeluaran')
                ->where('bulan', $tanggal->month)
                ->where('tahun', $tanggal->year)
                ->first();
            $monthly->nominal = ($monthly->nominal - $expense->jumlah) + convertToNumber($data->jumlah);
            $monthly->update();
        } else {
            $tanggalLama = Carbon::parse($expense->tanggal);
            $tanggalBaru = Carbon::parse($data->tanggal);

            $dailyOld = DailyStat::where('user_id', $user->id)
                ->where('kategori', 'pengeluaran')
                ->whereDate('tanggal', $tanggalLama)
                ->first();
            $dailyOld->nominal = $dailyOld->nominal - $expense->jumlah;
            $dailyOld->update();

            $monthlyOld = MonthlyStat::where('user_id', $user->id)
                ->where('kategori', 'pengeluaran')
                ->where('bulan', $tanggalLama->month)
                ->where('tahun', $tanggalLama->year)
                ->first();
            $monthlyOld->nominal = $monthlyOld->nominal - $expense->jumlah;
            $monthlyOld->update();

            $dailyNew = DailyStat::where('user_id', $user->id)
                ->where('kategori', 'pengeluaran')
                ->whereDate('tanggal', $tanggalBaru)
                ->first();
            if ($dailyNew) {
                $dailyNew->nominal = $dailyNew->nominal + convertToNumber($data->jumlah);
                $dailyNew->update();
            } else {
                $dailyNew = new DailyStat();
                $dailyNew->user_id = $user->id;
                $dailyNew->nominal = convertToNumber($data->jumlah);
                $dailyNew->tanggal = $tanggalBaru;
                $dailyNew->kategori = 'pengeluaran';
                $dailyNew->save();
            }

            $monthlyNew = MonthlyStat::where('user_id', $user->id)
                ->where('kategori', 'pengeluaran')
                ->where('bulan', $tanggalBaru->month)
                ->where('tahun', $tanggalBaru->year)
                ->first();
            if ($monthlyNew) {
                $monthlyNew->nominal = $monthlyNew->nominal + convertToNumber($data->jumlah);
                $monthlyNew->update();
            } else {
                $monthlyNew = new MonthlyStat();
                $monthlyNew->user_id = $user->id;
                $monthlyNew->nominal = convertToNumber($data->jumlah);
                $monthlyNew->bulan = $tanggalBaru->month;
                $monthlyNew->tahun = $tanggalBaru->year;
                $monthlyNew->kategori = 'pengeluaran';
                $monthlyNew->save();
            }
        }

        $expense->keperluan = $data->keperluan;
        $expense->jumlah = convertToNumber($data->jumlah);
        $expense->tanggal = $data->tanggal;
        $expense->update();

        return [
            'message' => 'Berhasil diperbaharui',
            'expenses' => $this->get($user->id)
        ];
    }

    public function destroy($id)
    {
        $expense = Expense::find($id);

        $daily = DailyStat::where('user_id', $expense->user_id)
            ->where('kategori', 'pengeluaran')
            ->whereDate('tanggal', $expense->tanggal)
            ->first();
        if ($daily) {
            $daily->nominal = $daily->nominal - $expense->jumlah;
            $daily->update();
        } else {
            $daily = new DailyStat();
            $daily->user_id = $expense->user_id;
            $daily->nominal = 0;
            $daily->tanggal = $expense->tanggal;
            $daily->kategori = 'pengeluaran';
            $daily->save();
        }

        $monthly = MonthlyStat::where('user_id', $expense->user_id)
            ->where('kategori', 'pengeluaran')
            ->where('bulan', Carbon::parse($expense->tanggal)->month)
            ->where('tahun', Carbon::parse($expense->tanggal)->year)
            ->first();
        if ($monthly) {
            $monthly->nominal = $monthly->nominal - $expense->jumlah;
            $monthly->update();
        } else {
            $monthly = new MonthlyStat();
            $monthly->user_id = $expense->user_id;
            $monthly->nominal = 0;
            $monthly->bulan = Carbon::parse($expense->tanggal)->month;
            $monthly->tahun = Carbon::parse($expense->tanggal)->year;
            $monthly->kategori = 'pengeluaran';
            $monthly->save();
        }

        $expense->delete();

        return [
            'message' => 'Berhasil dihapus',
        ];
    }

    public function destroyAll($id)
    {
        $user = User::find($id);

        foreach ($user->expenses as $expense) {
            $expense->delete();
        }

        foreach ($user->dailyExpenses as $dailyExpense) {
            $dailyExpense->delete();
        }

        foreach ($user->monthlyExpenses as $monthlyExpense) {
            $monthlyExpense->delete();
        }

        return [
            'data' => [
                'message' => 'Data telah terhapus',
            ],
            'status' => 200
        ];
    }
}
