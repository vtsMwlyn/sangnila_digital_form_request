<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Leave;
use App\Models\ActionLog;
use App\Models\Overwork;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isNull;

class LeaveController
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $allowance = Auth::user()->leave_balance;
        $leave_period = Leave::where('user_id', Auth::user()->id)->where('request_status', 'approved')->sum('leave_period') / 8;
        return view('view.users.leave-request', compact('allowance', 'leave_period'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'start_leave' => ['required'],
            'many_days' => 'nullable|numeric|required_without_all:many_hours',
            'many_hours' => 'nullable|numeric|required_without_all:many_days',
            'reason' => ['required'],
            'user_id' => ['required'],
        ], [
            'many_days.required_without_all' => 'Please fill at least one of Days or Hours.',
            'many_hours.required_without_all' => 'Please fill at least one of Days or Hours.',
        ]);

        if ($validate['many_days'] == '0' && $validate['many_hours'] == '0') {
            return back()
                ->withErrors(['many_days' => 'Either days or hours must be greater than 0.'])
                ->withErrors(['many_hours' => 'Either days or hours must be greater than 0.'])
                ->withInput();
        }

        $totalPeriod = (float) ($validate['many_days'] * 8) + $validate['many_hours'];
        $status = $request->action === 'submit' ? 'review' : 'draft';

        Leave::create([
            'start_leave' => $validate['start_leave'],
            'leave_period' => $totalPeriod,
            'reason' => $validate['reason'],
            'request_status' => $status,
            'user_id' => $validate['user_id']
        ]);

        ActionLog::create([
            'user_id' => Auth::id(),
            'mode' => 'leave',
            'message' => $status !== 'draft' ? 'Submitted a leave request' : 'Created a leave request draft',
        ]);

        if ($status == 'draft')
            return redirect()->route('leave.show')->with('success', [
                'title' => 'Saved to draft!',
                'message' => 'Your leave request has been saved to draft.',
                'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
            ]);

        if ($status === 'review') return redirect()->route('leave.show')->with('success', [
            'title' => 'Leave request Submitted!',
            'message' => 'Please wait for admin approval.',
            'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Leave $leave)
    {
        $allowance = Auth::user()->leave_balance;
        $leave_period = Leave::where('user_id', Auth::user()->id)->where('request_status', 'approved')->sum('leave_period') / 8;
        return view('view.users.leave-request', compact('leave', 'allowance', 'leave_period'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Leave $leave)
    {
        $validate = $request->validate([
            'start_leave' => ['required'],
            'many_days' => 'nullable|numeric|required_without_all:many_hours',
            'many_hours' => 'nullable|numeric|required_without_all:many_days',
            'reason' => ['required'],
            'user_id' => ['required'],
        ], [
            'many_days.required_without_all' => 'Please fill at least one of Days or Hours.',
            'many_hours.required_without_all' => 'Please fill at least one of Days or Hours.',
        ]);

        if ($validate['many_days'] == '0' && $validate['many_hours'] == '0') {
            return back()
                ->withErrors(['many_days' => 'Either days or hours must be greater than 0.'])
                ->withErrors(['many_hours' => 'Either days or hours must be greater than 0.'])
                ->withInput();
        }

        $totalPeriod = (float) ($validate['many_days'] * 8) + $validate['many_hours'];
        $status = $request->action === 'submit' ? 'review' : 'draft';

        $leave->update([
            'start_leave' => $validate['start_leave'],
            'leave_period' =>  $totalPeriod,
            'reason' => $validate['reason'],
            'request_status' => $status,
        ]);

        ActionLog::create([
            'user_id' => Auth::id(),
            'mode' => 'leave',
            'message' => 'Updated a leave request draft',
        ]);

        if ($status == 'draft')
            return redirect()->route('leave.show')->with('success', [
                'title' => 'Draft updated!',
                'message' => 'Your leave request has been draft updated.',
                'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
            ]);

        if ($status === 'review') return redirect()->route('leave.show')->with('success', [
            'title' => 'Leave request Submitted!',
            'message' => 'Please wait for admin approval.',
            'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Leave $leave)
    {
        try {
            $leave->delete();

            ActionLog::create([
                'user_id' => Auth::id(),
                'mode' => 'leave',
                'message' => 'Deleted a leave request draft',
            ]);

            return redirect()->back()->with('success', [
                'title' => 'Leave draft deleted successfully',
                'message' => 'Your leave draft has been deleted.',
                'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
            ]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete leave draft: ' . $e->getMessage()]);
        }
    }

    public function approve(Request $request, $mode)
    {
        $adminName = Auth::user()->name;
        $leaveId = $request->leaveId;
        $leave   = Leave::find($leaveId);

        if (!$leave) {
            return redirect()->back()->with('fail', [
                'title'   => 'Leave not found',
                'message' => 'The selected leave request was not found.',
            ]);
        }

        $userId  = $leave->user_id;
        $user    = User::findOrFail($userId);
        $newApproval = $leave->leave_period;

        if ($mode === 'leave') {
            $allowance = $user->leave_balance;
            $totalLate = floatval($request->input('totalLateValue')) * 8;

            if ($newApproval > $allowance) {
                return redirect()->back()->with('fail', [
                    'title'   => 'Insufficient Balance',
                    'message' => 'This user does not have enough leave balance.',
                ]);
            }

            $user->leave_balance = $allowance - $newApproval - $totalLate;
            $user->save();

            $leave->update([
                'request_status'   => 'approved',
                'deduction_source' => 'leave_balance',
                'action_by'      => $adminName,
            ]);

            ActionLog::create([
                'user_id' => $userId,
                'mode' => 'leave',
                'message' => Auth::user()->name . ' has approved your leave request',
            ]);
        }

        elseif ($mode === 'overwork') {
            $newApproval = $leave->leave_period;
            $totalApprovedOverwork = $user->overwork_balance;
            $totalLate = floatval($request->input('totalLateValue')) * 8;

            if ($totalApprovedOverwork < $newApproval) {
                return redirect()->back()->with('fail', [
                    'title'   => 'Exceeds Limit',
                    'message' => 'This user exceeds the allowed overwork limit.',
                ]);
            }

            $newTotalOverwork = max($totalApprovedOverwork - $newApproval - $totalLate, 0);

            $user->update(['overwork_balance' => $newTotalOverwork]);

            $leave->update([
                'request_status'   => 'approved',
                'deduction_source' => 'overwork_balance',
                'action_by'      => $adminName
            ]);

            ActionLog::create([
                'user_id' => $userId,
                'mode' => 'overwork',
                'message' => Auth::user()->name .  ' has approved your overwork request',
            ]);
        }

        return redirect()->back()->with('success', [
            'title'   => ucfirst($mode) . ' Approved!',
            'message' => "This {$mode} request has been approved successfully.",
        ]);
    }
}