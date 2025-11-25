<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Leave;
use App\Models\Overwork;
use App\Models\ActionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Exists;
use App\Http\Controllers\RequestController;

class ManageDataController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $requestData = new RequestController;
        $status = $request->input('status');
        $month = $request->input('month');
        $search = $request->input('search');
        $data = $requestData->requestData()->where('request_status', '!=', 'draft');

        if ($status && $status !== 'all') {
            $data = $data->where('request_status', $status);
        }
        if ($month && $month !== 'all') {
            $data = $data->filter(function ($item) use ($month) {
                return Carbon::parse($item->start_leave ?? $item->overwork_date ?? '')->format('m-Y') === $month;
            });
        }
        if ($search) {
            $data = $data->filter(function ($item)  use ($search, $month) {
                return stripos($item->type ?? '', $search) !== false ||
                    stripos($item->user->name ?? '', $search) !== false ||
                    stripos($item->reason ?? $item->task_description ?? '', $search) !== false ||
                    stripos($item->request_status ?? '', $search) !== false;
            });
        }

        return view('view.admin.manage-data', compact('data'));
    }

    public function edit(Request $request, string $leaveId, string $userId)
    {
        $adminName = Auth::user()->name;

        // Review/Approved -> Rejected
        if ($request->has('rejected')) {
            // Overwork/leave flag
            $status = $request->input('rejected');

            // Reason
            $adminNote = $request->input('admin_note');

            // Update balance
            if ($status === 'leave') {
                $user = User::findOrFail($userId);
                $leave = Leave::findOrFail($leaveId);

                $newApproval = $leave->leave_period;
                $source = $leave->deduction_source;
                $allowance = User::findOrFail($userId)->overwork_balance;
                $leave_balance = User::findOrFail($userId)->leave_balance;

                if ($source === 'overwork_balance') {
                    $new_overwork_balance =  $allowance + $newApproval;

                    User::findOrFail($userId)->update(['overwork_balance' => $new_overwork_balance]);
                } else {
                    $new_leave_balance = $leave_balance + $newApproval;

                    User::findOrFail($userId)->update(['leave_balance' => $new_leave_balance]);
                }

                $adminNote = $request->input('admin_note');
                $leave->update([
                    'request_status' => 'rejected',
                    'admin_note'     => $adminNote,
                    'action_by'      => $adminName,
                ]);

                ActionLog::create([
                    'user_id' => $userId,
                    'mode' => 'leave',
                    'message' => Auth::user()->name . ' has rejected your leave request',
                ]);
            }

            // === REJECT OVERWORK ===
            else if ($status === 'overwork') {
                // Make sure kelipatan setengah jam
                $allowance = User::findOrFail($userId)->overwork_balance;
                $overwork_duration = Overwork::find($leaveId)->duration;
                $validateBalanceApproval = $allowance - $overwork_duration;

                // Prevent saldo minus
                if ($validateBalanceApproval < 0) {
                    return redirect()->back()->with('fail', [
                        'title' => 'Illegal balance value',
                        'message' => 'Overwork cancelation causing the balance below 0.',
                        'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
                    ]);
                }

                User::findOrFail($userId)->update([
                    'overwork_balance' => $validateBalanceApproval
                ]);

                Overwork::where('id', $leaveId)->update([
                    'request_status' => 'rejected',
                    'admin_note'     => $adminNote,
                    'action_by'      => $adminName,
                ]);

                ActionLog::create([
                    'user_id' => $userId,
                    'mode' => 'overwork',
                    'message' => Auth::user()->name . ' has rejected your overwork request',
                ]);
            }

            return redirect()->back()->with('success', [
                'title' => $status . ' Rejected!',
                'message' => 'This overwork request has been rejected.',
            ]);
        }


        else if ($request->has('approved')) {

            $status = $request->input('approved');

            // === APPROVE LEAVE ===
            if($status === 'leave'){
                $allowance = User::findOrFail($userId)->leave_balance; // Saldo saat ini
                $newApproval = Leave::find($leaveId)->leave_period; // Yang di-request
                $validateBalanceApproval = $allowance - $newApproval; // New balance = Saldo saat ini - Yang di-request

                if ($validateBalanceApproval < 0) {
                    return redirect()->back()->with('fail', [
                        'title' => 'Illegal balance value',
                        'message' => 'Leave cancelation causing the balance below 0.',
                        'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
                    ]);
                }

                User::findOrFail($userId)->update(['leave_balance' => $validateBalanceApproval]);
                Leave::where('id', $leaveId)->update([
                    'request_status' => 'approved',
                    'action_by'=> $adminName,
                ]);

                ActionLog::create([
                    'user_id' => $userId,
                    'mode' => 'leave',
                    'message' => Auth::user()->name . ' has approved the leave request',
                ]);
            }

            // === APPROVE OVERWORK ===
            else if ($status === 'overwork') {

                $allowance = User::findOrFail($userId)->overwork_balance;
                $overwork_duration = Overwork::find($leaveId)->duration;
                $validateBalanceApproval = $allowance + $overwork_duration;

                User::findOrFail($userId)->update([
                    'overwork_balance' => $validateBalanceApproval
                ]);

                Overwork::where('id', $leaveId)->update([
                    'request_status' => 'approved',
                    'action_by'      => $adminName,
                ]);

                ActionLog::create([
                    'user_id' => $userId,
                    'mode' => 'overwork',
                    'message' => Auth::user()->name . ' has approved your overwork request',
                ]);
            }

            return redirect()->back()->with('success', [
                'title' => $status . ' Approved!',
                'message' => "This {$status} request has been approved.",
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}