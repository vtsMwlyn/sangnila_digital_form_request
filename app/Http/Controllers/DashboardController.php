<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Overwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RequestController;
use App\Models\ActionLog;
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

    public function dashboard(Request $request)
    {
        $data = $this->dataSubmitted();
        $status = $request->input('status');
        $type = $request->input('type');
        $search = $request->input('search');
        $month = $request->input('month');
        $user = Auth::user();

        $leaveHours = $user->leave_balance;
        $overworkHours = $user->overwork_balance;
        $allHours = $leaveHours + $overworkHours;

        $leaveDays = floor($leaveHours / 8);
        $overworkDays = floor($overworkHours / 8);
        $allDays = floor($allHours / 8);

        $remainingLeave = $leaveHours - ($leaveDays * 8);
        $remainingOverwork = $overworkHours - ($overworkDays * 8);
        $remainingAll = $allHours - ($allDays * 8);

        $leaveBalanceText = "{$leaveDays} days {$remainingLeave} hours";
        $overworkBalanceText = "{$overworkDays} days {$remainingOverwork} hours";
        $allBalanceText = "{$allDays} days {$remainingAll} hours";

        $data['logs'] = ActionLog::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $data['overwork_balance'] = $overworkBalanceText;
        $data['total_leave'] = $leaveBalanceText;
        $data['total_all'] = $allBalanceText;

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
