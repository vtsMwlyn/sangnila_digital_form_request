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

    // protected static function booted()
    // {
    //     static::saved(function ($overwork) {
    //         $user = $overwork->user;
    //         if (!$user) return;

    //         $currentTotal = $user->overwork_balance ?? 0;

    //         if ($overwork->request_status === 'approved') {
    //             $start = \Carbon\Carbon::parse($overwork->start_overwork);
    //             $end = \Carbon\Carbon::parse($overwork->finished_overwork);
    //             $hours = $start->diffInHours($end);

    //             $user->update(['overwork_balance' => $currentTotal + $hours]);
    //         } else {
    //             $total = self::getTotalApprovedHours($user->id);
    //             $user->update(['overwork_balance' => $total]);
    //         }
    //     });

    //     // Kalau data overwork dihapus
    //     static::deleted(function ($overwork) {
    //         $user = $overwork->user;
    //         if (!$user) return;

    //         // Recalculate semua total yang masih approved
    //         $total = self::getTotalApprovedHours($user->id);
    //         $user->update(['overwork_balance' => $total]);
    //     });
    // }


    public static function getTotalApprovedHours($userId)
    {
        $overworks = self::where('user_id', $userId) ->where('request_status', 'approved') ->get();

        $totalHours = $user->overwork_balance ?? 0;

        $overworks = self::where('user_id', $userId)
            ->where('request_status', 'approved')
            ->get();

        foreach ($overworks as $o) {
            $start = \Carbon\Carbon::parse($o->start_overwork);
            $end = \Carbon\Carbon::parse($o->finished_overwork);
            $totalHours += $start->diffInHours($end);
        }

        return $totalHours;
    }

}