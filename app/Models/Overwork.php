<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Overwork extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function evidence()
    {
        return $this->hasMany(Evidence::class);
    }

    protected static function booted()
    {
        static::saved(function ($overwork) {
            $userId = $overwork->user_id;

            $total = self::getTotalApprovedHours($userId);
            $overwork->user->update(['total_overwork' => $total]);
        });

        static::deleted(function ($overwork) {
            $userId = $overwork->user_id;
            $total = self::getTotalApprovedHours($userId);

            $overwork->user->update(['total_overwork' => $total]);
        });
    }

    public static function getTotalApprovedHours($userId)
    {
        $overworks = self::where('user_id', $userId)
            ->where('request_status', 'approved')
            ->get();

        $totalHours = 0;

        foreach ($overworks as $o) {
            $start = \Carbon\Carbon::parse($o->start_overwork);
            $end = \Carbon\Carbon::parse($o->finished_overwork);
            $totalHours += $start->diffInHours($end);
        }

        return $totalHours;
    }
}