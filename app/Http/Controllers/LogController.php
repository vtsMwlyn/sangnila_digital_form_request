<?php

namespace App\Http\Controllers;

use App\Models\LateLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        if ($user->role === 'user') {
            $data = LateLog::where('user_id', $user->id)
                ->latest()
                ->get();

            return view('view.users.Log', compact('data'));
        }

        elseif ($user->role === 'admin') {
            $data = LateLog::with('user')
                ->latest()
                ->get();

            return view('view.users.Log', compact('data'));
        }

        abort(403, 'Unauthorized');
    }
}