<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Evidence;
use App\Models\Overtime;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class RequestController extends Controller
{
    //! disabled for optimization
    public function requestData()
    {
        $leaves = Leave::all()->sortByDesc('created_at')->map(function ($item) {
            $item->type = 'leave';
            return $item;
        });

        $overtime = Overtime::with('evidence')->orderByDesc('created_at')->get()->map(function ($item) {
            $item->type = 'overtime';
            return $item;
        });

        return $leaves->concat($overtime)->sortByDesc('created_at');
    }

    private function applyFilters($data, $month, $search)
    {
        if ($month && $month !== 'all') {
            $data = $data->filter(function ($item) use ($month) {
                if ($item->type === 'overtime') {
                    return Carbon::parse($item->overtime_date)->format('m-Y') === $month;
                } else {
                    return Carbon::parse($item->start_leave)->format('m-Y') === $month;
                }
            });
        }

        if ($search) {
            $data = $data->filter(function ($item) use ($search) {
                return stripos($item->type ?? '', $search) !== false ||
                    stripos($item->user->name ?? '', $search) !== false ||
                    stripos($item->reason ?? $item->task_description ?? '', $search) !== false ||
                    stripos($item->request_status ?? '', $search) !== false;
            });
        }

        return $data;
    }

    public function showDraft(Request $request)
    {
        $data = $this->requestData();
        $type = $request->input('type');
        $month = $request->input('month');
        $search = $request->input('search');
        $data = $this->applyFilters($data, $month, $search)
            ->where('request_status', 'draft')
            ->where('user_id', Auth::id())
            ->sortByDesc('created_at');
        if ($type && $type != 'all') {
            $data = $data->where('type', $type);
        }

        return view('view.users.draft', compact('data'));
    }

    public function showRecent(Request $request)
    {
        $data = $this->requestData();
        $routeName = Route::currentRouteName();
        $status = $request->input('status');
        $month = $request->input('month');
        $search = $request->input('search');
        $employeeSearch = $request->input('employee') ?? '';

        if (Auth::user()->role === 'user') {
            if ($routeName != 'dashboard') {
                if (Str::before($routeName, '.') === 'overtime') {
                    $data = $this->applyFilters($data, $month, $search)->where('type', 'overtime')->where('user_id', Auth::id());
                    if ($status && $status != 'all') {
                        $data = $data->where('request_status', $status);
                    }
                    return view('view.users.overtime-data', compact('data'));
                } else {
                    $data = $this->applyFilters($data, $month, $search)->where('type', 'leave')->where('user_id', Auth::id());
                    if ($status && $status != 'all') {
                        $data = $data->where('request_status', $status);
                    }
                    return view('view.users.leave-data', compact('data'));
                }
            } else {
                $data = $this->applyFilters($data, $month, $search)->where('request_status', '!=', 'draft')->where('user_id', Auth::id());
                return $data;
            }
        } elseif (Auth::user()->role === 'admin') {
            if ($routeName != 'dashboard') {
                if (Str::before($routeName, '.') === 'overtime') {
                    $data = $this->applyFilters($data, $month, $employeeSearch)->where('type', 'overtime')->where('request_status', '!=', 'draft');
                    if ($status && $status !== 'all') {
                        $data = $data->where('request_status', $status);
                    }
                    return view('view.users.overtime-data', compact('data'));
                } else {
                    $data = $this->applyFilters($data, $month, $employeeSearch)->where('type', 'leave')->where('request_status', '!=', 'draft');
                    if ($status && $status !== 'all') {
                        $data = $data->where('request_status', $status);
                    }
                    return view('view.users.leave-data', compact('data'));
                }
            } else {
                $data = $this->applyFilters($data, $month, $search);
                return $data;
            }
        }
    }
}