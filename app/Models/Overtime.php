<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
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

    public static function getTotalApprovedHours($userId)
    {
        $overtimes = self::where('user_id', $userId) ->where('request_status', 'approved') ->get();

        $totalHours = $user->overtime_balance ?? 0;

        $overtimes = self::where('user_id', $userId)
            ->where('request_status', 'approved')
            ->get();

        foreach ($overtimes as $o) {
            $start = \Carbon\Carbon::parse($o->start_overtime);
            $end = \Carbon\Carbon::parse($o->finished_overtime);
            $totalHours += $start->diffInHours($end);
        }

        return $totalHours;
    }

}