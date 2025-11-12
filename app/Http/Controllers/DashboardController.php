<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Overwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RequestController;
use App\Models\LateLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function dataSubmitted()
    {
        $controller = new RequestController;
        $requestData = $controller->showRecent(request());

        $totalOverwork = Overwork::selectRaw('SUM(TIMESTAMPDIFF(HOUR, start_overwork, finished_overwork)) AS total_hours')
            ->where('user_id', Auth::id())
            ->where('request_status', 'approved')
            ->get();

        $totalLeave = Leave::selectRaw('SUM(leave_period) AS leave_period')
            ->where('user_id', Auth::id())
            ->where('request_status', 'approved')
            ->get();

        $approved = $requestData->where('request_status', 'approved');
        $rejected = $requestData->where('request_status', 'rejected');
        $pending = $requestData->where('request_status', 'review');

        $result = $approved->concat($rejected)->concat($pending);
        return compact('totalOverwork', 'totalLeave', 'approved', 'rejected', 'pending', 'requestData', 'result');
    }

    // public function dashboard(Request $request)
    // {
    //     $data = $this->dataSubmitted();
    //     $status = $request->input('status');
    //     $type = $request->input('type');
    //     $search = $request->input('search');
    //     $month = $request->input('month');

    //     if (Auth::user()->role === 'user') {
    //         $data['requestData'] = $data['requestData']->take(5);
    //         if ($type && $type != 'all') {
    //             $data['requestData'] = $data['requestData']->where('type', $type)->take(5);
    //         }
    //     } elseif (Auth::user()->role === 'admin') {
    //         $data['requestData'] = $data['requestData']->where('request_status', $status ?? 'review')->take(8);
    //     }

    //     if ($month && $month !== 'all') {
    //         $data['requestData'] = $data['requestData']->filter(function ($item) use ($month, $search) {
    //             return stripos($item->start_leave ?? $item->overwork_date ?? '', $month) !== false ||
    //                 stripos($item->leave ?? '', $search) !== false ||
    //                 stripos($item->user->name ?? '', $search) !== false ||
    //                 stripos($item->reason ?? $item->task_description ?? $item->reason ?? '', $search) !== false;
    //         });
    //     }

    //     return view('dashboard', compact('data'));
    // }
    public function dashboard(Request $request)
    {
        $data = $this->dataSubmitted();
        $status = $request->input('status');
        $type = $request->input('type');
        $search = $request->input('search');
        $month = $request->input('month');
        $user = Auth::user();

        $totalLeaveHours = $user->overwork_allowance;

        // return $totalLeaveHours;

        $totalOverworkHours = $user->total_overwork ?? 0;
        $allowanceDays = ($user->overwork_allowance ?? 0) ;

        $leaveDays = $allowanceDays / 8;
        $overworkDays = $totalOverworkHours / 8;

        $leaveBalanceDays = $user->overwork_allowance / 8;

        $periodDays = floor($leaveDays);
        $periodHours = ($leaveDays - $periodDays) * 8;
        $totalLeaveText = $leaveDays
            ? "{$periodDays} days {$periodHours} hours"
            : ($periodDays > 0 ? "{$periodDays} days" : "{$periodHours} hours");

        $balanceDays = floor($leaveBalanceDays);
        $balanceHours = ($leaveBalanceDays - $balanceDays) * 8;
        $balanceText = $leaveBalanceDays
            ? "{$balanceDays} days {$balanceHours} hours"
            : ($balanceDays > 0 ? "{$balanceDays} days" : "{$balanceHours} hours");

        $overworkDaysInt = floor($overworkDays);
        $overworkHours = ($overworkDays - $overworkDaysInt) * 8;
        $totalOverworkText = $overworkDays
            ? "{$overworkDaysInt} days {$overworkHours} hours"
            : ($overworkDaysInt > 0 ? "{$overworkDaysInt} days" : "{$overworkHours} hours");


        $data['logs'] = LateLog::where('user_id', $user->id)
        ->latest()
        ->take(3)
        ->get();

        $data['total_leave'] = $totalLeaveText;
        $data['total_overwork'] = $totalOverworkText;
        $data['balance'] = $balanceText;

        if ($user->role === 'user') {
            $data['requestData'] = $data['requestData']->take(5);

            if ($type && $type != 'all') {
                $data['requestData'] = $data['requestData']->where('type', $type)->take(5);
            }
        } elseif ($user->role === 'admin') {
            $data['requestData'] = $data['requestData']->where('request_status', $status ?? 'review')->take(8);
        }

        if ($month && $month !== 'all') {
            $data['requestData'] = $data['requestData']->filter(function ($item) use ($month, $search) {
                return stripos($item->start_leave ?? $item->overwork_date ?? '', $month) !== false ||
                    stripos($item->leave ?? '', $search) !== false ||
                    stripos($item->user->name ?? '', $search) !== false ||
                    stripos($item->reason ?? $item->task_description ?? '', $search) !== false;
            });
        }
        return view('dashboard', compact('data'));
    }

}
