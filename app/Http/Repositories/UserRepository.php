<?php

namespace App\Http\Repositories;

use App\Models\DailyStat;
use App\Models\MonthlyStat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function login($creds)
    {
        $user = User::where('email', $creds->email)->first();

        if ($user->hasVerifiedEmail()) {
            if ($user && Hash::check($creds->password, $user->password)) {
                $user->last_login = now();
                $user->tz = $creds->timezone;
                $user->update();

                $token = $user->createToken('auth-token');

                return [
                    'data' => [
                        'message' => 'success',
                        'token' => $token->plainTextToken,
                        'user' => $user,
                        'money' => $user->money
                    ],
                    'status' => 200
                ];
            }
            return [
                'data' => [
                    'message' => 'Email atau Password salah!'
                ],
                'status' => 401
            ];
        }

        return [
            'data' => [
                'message' => 'Email belum diverifikasi'
            ],
            'status' => 401
        ];
    }

    public function register($data)
    {
        $user = new User();
        $user->nama = $data->nama;
        $user->email = $data->email;
        $user->password = bcrypt($data->password);
        $user->tz = $data->tz;
        $user->save();

        event(new Registered($user));

        return [
            'data' => [
                'message' => 'success',
            ],
            'status' => 200
        ];
    }

    public function update($data)
    {
        $user = User::find($data->user()->id);
        $user->nama = $data->nama;
        if ($data->password) {
            $user->password = bcrypt($data->password);
        }
        $user->update();

        return [
            'data' => [
                'message' => 'success',
                'user' => $user
            ],
            'status' => 200
        ];
    }

    public function balances($user)
    {
        $todayIncome = DailyStat::where('user_id', $user->id)
            ->where('kategori', 'pemasukan')
            ->whereDate('tanggal', Carbon::today()->timezone($user->tz))
            ->first();
        $todayExpense = DailyStat::where('user_id', $user->id)
            ->where('kategori', 'pengeluaran')
            ->whereDate('tanggal', Carbon::today()->timezone($user->tz))
            ->first();

        $thisMonthIncome = MonthlyStat::where('user_id', $user->id)
            ->where('kategori', 'pemasukan')
            ->where('bulan', Carbon::today()->timezone($user->tz)->month)
            ->where('tahun', Carbon::today()->timezone($user->tz)->year)
            ->first();
        $thisMonthExpense = MonthlyStat::where('user_id', $user->id)
            ->where('kategori', 'pengeluaran')
            ->where('bulan', Carbon::today()->timezone($user->tz)->month)
            ->where('tahun', Carbon::today()->timezone($user->tz)->year)
            ->first();

        $today = [
            'income' => $todayIncome ? $todayIncome->nominal : 0,
            'expense' => $todayExpense ? $todayExpense->nominal : 0,
        ];
        $thisMonth = [
            'income' => $thisMonthIncome ? $thisMonthIncome->nominal : 0,
            'expense' => $thisMonthExpense ? $thisMonthExpense->nominal : 0,
        ];

        return [
            'money' => $user->money,
            'today' => $today,
            'this_month' => $thisMonth
        ];
    }
}
