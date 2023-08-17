<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStat extends Model
{
    use HasFactory;

    protected $appends = ['day'];

    public function day(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->tanggal)->day
        );
    }
}
