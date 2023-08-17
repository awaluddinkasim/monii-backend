<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'last_login',
        'tz',
    ];

    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function dailyIncomes()
    {
        return $this->hasMany(DailyStat::class)->where('kategori', 'pemasukan');
    }

    public function dailyExpenses()
    {
        return $this->hasMany(DailyStat::class)->where('kategori', 'pengeluaran');
    }

    public function monthlyIncomes()
    {
        return $this->hasMany(MonthlyStat::class)->where('kategori', 'pemasukan');
    }

    public function monthlyExpenses()
    {
        return $this->hasMany(MonthlyStat::class)->where('kategori', 'pengeluaran');
    }

    public function money(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->incomes()->sum('jumlah') - $this->expenses()->sum('jumlah')
        );
    }
}
