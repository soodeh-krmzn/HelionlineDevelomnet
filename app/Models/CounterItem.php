<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounterItem extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function pastTime()
    {
        $startDate = $this->start_date;
        $now = now();
        $diff = $now->diffInSeconds($startDate);
        $hours = floor($diff / 3600);
        $minutes = floor(($diff % 3600) / 60);
        $seconds = $diff % 60;

        return [
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds
        ];
    }

    public function pastMinutes()
    {
        $startDate = $this->start_date;
        $now = now();
        return $now->diffInMinutes($startDate);
    }
    public function pastSeconds()
    {
        $startDate = $this->start_date;
        $now = now();
        return $now->diffInSeconds($startDate);
    }
}
