<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Leave;
use App\Models\Overwork;
use Illuminate\Http\Request;
use App\Http\Controllers\RequestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Exists;

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

    /**
     * Update the specified resource in storage.
     */
    // public function edit(Request $request, string $leaveId, string $userId)
    // {
    //     // Review/Approved -> Rejected
    //     if ($request->has('rejected')) {
    //         // Overwork/leave flag
    //         $status = $request->input('rejected');

    //         // Reason
    //         $adminNote = $request->input('admin_note');


    //         // Update balance
    //         if ($status === 'leave') {
    //             $user = User::findOrFail($userId);
    //             $leave = Leave::findOrFail($leaveId);

    //             $newApproval = $leave->leave_period;
    //             $source = $leave->deduction_source;
    //             $allowance = User::findOrFail($userId)->total_overwork;
    //             $leave_balance = User::findOrFail($userId)->overwork_allowance;

    //             if ($source === 'total_overwork') {
    //                 $new_total_overwork =  $allowance + $newApproval;
    //                 // dd([
    //                 //     'mode' => 'REJECT LEAVE',
    //                 //     'userId' => $userId,
    //                 //     'source' => $source,
    //                 //     'total overwork sebelum' =>$allowance,
    //                 //     'returned_amount' => $newApproval,
    //                 //     'new_total_overwork' => $new_total_overwork,
    //                 //     'new_overwork_allowance' => $user->overwork_allowance,
    //                 // ]);
    //                 User::findOrFail($userId)->update(['total_overwork' => $new_total_overwork]);
    //             } else {
    //                 $new_overwork_allowance = $leave_balance + $newApproval;
    //                 // dd([
    //                 //     'mode' => 'REJECT LEAVE',
    //                 //     'userId' => $userId,
    //                 //     'source' => $source,
    //                 //     'total leave_balance sebelum' =>$leave_balance,
    //                 //     'returned_amount' => $newApproval,
    //                 //     'new_total_overwork' => $new_overwork_allowance,
    //                 //     'new_overwork_allowance' => $user->overwork_allowance,
    //                 // ]);
    //                 User::findOrFail($userId)->update(['overwork_allowance' => $new_overwork_allowance]);
    //             }

    //             $adminNote = $request->input('admin_note');
    //             $leave->update([
    //                 'request_status' => 'rejected',
    //                 'admin_note'     => $adminNote,
    //             ]);

    //         }
    //         else if($status === 'overwork'){
    //             $allowance = User::findOrFail($userId)->total_overwork; // Saldo saat ini
    //             $start = Carbon::parse(Overwork::find($leaveId)->start_overwork);
    //             $end = Carbon::parse(Overwork::find($leaveId)->finished_overwork);
    //             $diff = $start->diffInHours($end); // Yang di-request
    //             $validateBalanceApproval = $allowance - $diff; // New balance = Saldo saat ini - Yang di-request

    //             if ($validateBalanceApproval < 0) {
    //                 return redirect()->back()->with('fail', [
    //                     'title' => 'Illegal balance value',
    //                     'message' => 'Overwork cancelation causing the balance below 0.',
    //                     'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
    //                 ]);
    //             }


    //             User::findOrFail($userId)->update(['total_overwork' => $validateBalanceApproval]);
    //             Overwork::where('id', $leaveId)->update(['request_status' => 'rejected', 'admin_note' => $adminNote]);
    //         }

    //         // Redirect
    //         return redirect()->back()->with('success', [
    //             'title' => $status . ' Rejected!',
    //             'message' => 'This overwork request has been rejected.',
    //         ]);
    //     }

    //     // Review/Rejected -> Approved
    //     else if ($request->has('approved')) {
    //         // Overwork/leave flag
    //         $status = $request->input('approved');

    //         // Update balance
    //         if($status === 'leave'){
    //             $allowance = User::findOrFail($userId)->overwork_allowance; // Saldo saat ini
    //             $newApproval = Leave::find($leaveId)->leave_period; // Yang di-request
    //             $validateBalanceApproval = $allowance - $newApproval; // New balance = Saldo saat ini - Yang di-request

    //             if ($validateBalanceApproval < 0) {
    //                 return redirect()->back()->with('fail', [
    //                     'title' => 'Illegal balance value',
    //                     'message' => 'Leave cancelation causing the balance below 0.',
    //                     'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
    //                 ]);
    //             }

    //             User::findOrFail($userId)->update(['overwork_allowance' => $validateBalanceApproval]);
    //             Leave::where('id', $leaveId)->update(['request_status' => 'approved']);
    //         }
    //         else if($status === 'overwork'){
    //             $allowance = User::findOrFail($userId)->total_overwork; // Saldo saat ini
    //             $start = Carbon::parse(Overwork::find($leaveId)->start_overwork);
    //             $end = Carbon::parse(Overwork::find($leaveId)->finished_overwork);
    //             $diff = $start->diffInHours($end); // Yang di-request
    //             $validateBalanceApproval = $allowance + $diff; // New balance = Saldo saat ini + Yang di-request

    //             User::findOrFail($userId)->update(['total_overwork' => $validateBalanceApproval]);
    //             Overwork::where('id', $leaveId)->update(['request_status' => 'approved']);
    //         }

    //         // Redirect
    //         return redirect()->back()->with('success', [
    //             'title' => $status . ' Approved!',
    //             'message' => "This {$status} request has been approved.",
    //         ]);
    //     }
    // }
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
                $allowance = User::findOrFail($userId)->total_overwork;
                $leave_balance = User::findOrFail($userId)->overwork_allowance;

                if ($source === 'total_overwork') {
                    $new_total_overwork =  $allowance + $newApproval;
                    // dd([
                    //     'mode' => 'REJECT LEAVE',
                    //     'userId' => $userId,
                    //     'source' => $source,
                    //     'total overwork sebelum' =>$allowance,
                    //     'returned_amount' => $newApproval,
                    //     'new_total_overwork' => $new_total_overwork,
                    //     'new_overwork_allowance' => $user->overwork_allowance,
                    // ]);
                    User::findOrFail($userId)->update(['total_overwork' => $new_total_overwork]);
                } else {
                    $new_overwork_allowance = $leave_balance + $newApproval;
                    // dd([
                    //     'mode' => 'REJECT LEAVE',
                    //     'userId' => $userId,
                    //     'source' => $source,
                    //     'total leave_balance sebelum' =>$leave_balance,
                    //     'returned_amount' => $newApproval,
                    //     'new_total_overwork' => $new_overwork_allowance,
                    //     'new_overwork_allowance' => $user->overwork_allowance,
                    // ]);
                    User::findOrFail($userId)->update(['overwork_allowance' => $new_overwork_allowance]);
                }

                $adminNote = $request->input('admin_note');
                $leave->update([
                    'request_status' => 'rejected',
                    'admin_note'     => $adminNote,
                    'action_by'      => $adminName,
                ]);

            }

            // === REJECT OVERWORK ===
            else if ($status === 'overwork') {

                $allowance = User::findOrFail($userId)->total_overwork;
                $start = Carbon::parse(Overwork::find($leaveId)->start_overwork);
                $end   = Carbon::parse(Overwork::find($leaveId)->finished_overwork);
                $diff  = $start->diffInHours($end);

                $validateBalanceApproval = $allowance - $diff;

                // Prevent saldo minus
                if ($validateBalanceApproval < 0) {
                    return redirect()->back()->with('fail', [
                        'title' => 'Illegal balance value',
                        'message' => 'Overwork cancelation causing the balance below 0.',
                        'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
                    ]);
                }

                User::findOrFail($userId)->update([
                    'total_overwork' => $validateBalanceApproval
                ]);

                Overwork::where('id', $leaveId)->update([
                    'request_status' => 'rejected',
                    'admin_note'     => $adminNote,
                    'action_by'      => $adminName,
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
                $allowance = User::findOrFail($userId)->overwork_allowance; // Saldo saat ini
                $newApproval = Leave::find($leaveId)->leave_period; // Yang di-request
                $validateBalanceApproval = $allowance - $newApproval; // New balance = Saldo saat ini - Yang di-request

                if ($validateBalanceApproval < 0) {
                    return redirect()->back()->with('fail', [
                        'title' => 'Illegal balance value',
                        'message' => 'Leave cancelation causing the balance below 0.',
                        'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
                    ]);
                }

                User::findOrFail($userId)->update(['overwork_allowance' => $validateBalanceApproval]);
                Leave::where('id', $leaveId)->update([
                    'request_status' => 'approved',
                    'action_by'=> $adminName,
                    ]);
                }

            // === APPROVE OVERWORK ===
            else if ($status === 'overwork') {

                $allowance = User::findOrFail($userId)->total_overwork;
                $start = Carbon::parse(Overwork::find($leaveId)->start_overwork);
                $end   = Carbon::parse(Overwork::find($leaveId)->finished_overwork);
                $diff  = $start->diffInHours($end);

                $validateBalanceApproval = $allowance + $diff;

                User::findOrFail($userId)->update([
                    'total_overwork' => $validateBalanceApproval
                ]);

                Overwork::where('id', $leaveId)->update([
                    'request_status' => 'approved',
                    'action_by'      => $adminName,
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