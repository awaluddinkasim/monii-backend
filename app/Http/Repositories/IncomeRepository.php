<?php

namespace App\Http\Repositories;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Income;
use App\Models\DailyStat;
use App\Models\MonthlyStat;

class IncomeRepository
{
    public function get($id)
    {
        $incomes = Income::where('user_id', $id)->orderBy('tanggal', 'DESC')->orderBy('created_at', 'DESC')->paginate(10);

        return $incomes;
    }

    public function store($id, $data)
    {
        $user = User::find($id);

        $tanggal = Carbon::parse($data->tanggal);

        $income = new Income();
        $income->user_id = $user->id;
        $income->sumber = $data->sumber;
        $income->jumlah = convertToNumber($data->jumlah);
        $income->tanggal = $tanggal;
        $income->save();

        $daily = DailyStat::where('user_id', $user->id)
            ->where('kategori', 'pemasukan')
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
            $daily->kategori = 'pemasukan';
            $daily->save();
        }

        $monthly = MonthlyStat::where('user_id', $user->id)
            ->where('kategori', 'pemasukan')
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
            $monthly->kategori = 'pemasukan';
            $monthly->save();
        }

        return [
            'message' => 'Berhasil',
            'incomes' => $this->get($user->id)
        ];
    }

    public function update($id, $data)
    {
        $user = User::find($id);

        $income = Income::find($data->id);

        if ($income->tanggal == $data->tanggal) {
            $tanggal = Carbon::parse($data->tanggal);

            $daily = DailyStat::where('user_id', $user->id)
                ->where('kategori', 'pemasukan')
                ->whereDate('tanggal', $tanggal)
                ->first();
            $daily->nominal = ($daily->nominal - $income->jumlah) + convertToNumber($data->jumlah);
            $daily->update();

            $monthly = MonthlyStat::where('user_id', $user->id)
                ->where('kategori', 'pemasukan')
                ->where('bulan', $tanggal->month)
                ->where('tahun', $tanggal->year)
                ->first();
            $monthly->nominal = ($monthly->nominal - $income->jumlah) + convertToNumber($data->jumlah);
            $monthly->update();
        } else {
            $tanggalLama = Carbon::parse($income->tanggal);
            $tanggalBaru = Carbon::parse($data->tanggal);

            $dailyOld = DailyStat::where('user_id', $user->id)
                ->where('kategori', 'pemasukan')
                ->whereDate('tanggal', $tanggalLama)
                ->first();
            $dailyOld->nominal = $dailyOld->nominal - $income->jumlah;
            $dailyOld->update();

            $monthlyOld = MonthlyStat::where('user_id', $user->id)
                ->where('kategori', 'pemasukan')
                ->where('bulan', $tanggalLama->month)
                ->where('tahun', $tanggalLama->year)
                ->first();
            $monthlyOld->nominal = $monthlyOld->nominal - $income->jumlah;
            $monthlyOld->update();

            $dailyNew = DailyStat::where('user_id', $user->id)
                ->where('kategori', 'pemasukan')
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
                $dailyNew->kategori = 'pemasukan';
                $dailyNew->save();
            }

            $monthlyNew = MonthlyStat::where('user_id', $user->id)
                ->where('kategori', 'pemasukan')
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
                $monthlyNew->kategori = 'pemasukan';
                $monthlyNew->save();
            }
        }

        $income->sumber = $data->sumber;
        $income->jumlah = convertToNumber($data->jumlah);
        $income->tanggal = $data->tanggal;
        $income->update();

        return [
            'message' => 'Berhasil diperbaharui',
            'incomes' => $this->get($user->id)
        ];
    }

    public function destroy($id)
    {
        $income = Income::find($id);

        $daily = DailyStat::where('user_id', $income->user_id)
            ->where('kategori', 'pemasukan')
            ->whereDate('tanggal', $income->tanggal)
            ->first();
        if ($daily) {
            $daily->nominal = $daily->nominal - $income->jumlah;
            $daily->update();
        } else {
            $daily = new DailyStat();
            $daily->user_id = $income->user_id;
            $daily->nominal = 0;
            $daily->tanggal = $income->tanggal;
            $daily->kategori = 'pemasukan';
            $daily->save();
        }

        $monthly = MonthlyStat::where('user_id', $income->user_id)
            ->where('kategori', 'pemasukan')
            ->where('bulan', Carbon::parse($income->tanggal)->month)
            ->where('tahun', Carbon::parse($income->tanggal)->year)
            ->first();
        if ($monthly) {
            $monthly->nominal = $monthly->nominal - $income->jumlah;
            $monthly->update();
        } else {
            $monthly = new MonthlyStat();
            $monthly->user_id = $income->user_id;
            $monthly->nominal = 0;
            $monthly->bulan = Carbon::parse($income->tanggal)->month;
            $monthly->tahun = Carbon::parse($income->tanggal)->year;
            $monthly->kategori = 'pemasukan';
            $monthly->save();
        }

        $income->delete();

        return [
            'message' => 'Berhasil dihapus',
        ];
    }

    public function destroyAll($id)
    {
        $user = User::find($id);

        foreach ($user->incomes as $income) {
            $income->delete();
        }

        foreach ($user->dailyIncomes as $dailyIncome) {
            $dailyIncome->delete();
        }

        foreach ($user->monthlyIncomes as $monthlyIncome) {
            $monthlyIncome->delete();
        }

        return [
            'data' => [
                'message' => 'Data telah terhapus',
            ],
            'status' => 200
        ];
    }
}
