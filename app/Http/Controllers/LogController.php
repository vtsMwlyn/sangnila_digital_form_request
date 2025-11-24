<?php

namespace App\Http\Controllers;

use App\Models\ActionLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        if ($user->role === 'user') {
            $data = ActionLog::where('user_id', $user->id)
                ->latest()
                ->get();

            return view('view.users.Log', compact('data'));
        }

        elseif ($user->role === 'admin') {
            $data = ActionLog::with('user')
                ->latest()
                ->get();

            return view('view.users.Log', compact('data'));
        }

        abort(403, 'Unauthorized');
    }
}