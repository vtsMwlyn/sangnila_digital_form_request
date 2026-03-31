<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Leave;
use App\Models\Overtime;
use App\Models\ActionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageDataController extends Controller
{
    // ADMIN LEAVE/OVERWORK APPROVAL/REJECTION
    public function edit(Request $request, string $leaveId, string $userId)
    {
        // return $request;
        $adminName = Auth::user()->name;
        $requestId = $leaveId;

        // Review/Approved -> Rejected
        if ($request->has('rejected')) {
            $status = $request->input('rejected');
            $adminNote = $request->input('admin_note');

            // === REJECT LEAVE ===
            if ($status === 'leave') {
                $leave = Leave::findOrFail($requestId);
                $user = User::findOrFail($userId);

                $logMessage = Auth::user()->name . ' has rejected your leave request because of ' . $adminNote . '.';
                
                // Revert user's balance if the leave has been approved before, then cancelled
                if($leave->request_status != 'review'){
                    $requested_leave_duration = $leave->leave_period;
                    $source = $leave->deduction_source;

                    // Recover the balance from the deduction source
                    if($source == 'overtime_balance'){
                        $current_overtime_balance = $user->overtime_balance;
                        $reverted_overtime_balance =  $current_overtime_balance + $requested_leave_duration;

                        $user->update([
                            'overtime_balance' => $reverted_overtime_balance,
                        ]);

                        $logMessage .= ' ' . $requested_leave_duration . ' hours has been added to your overtime balance.';
                    }
                    else {
                        $current_leave_balance = $user->leave_balance;
                        $reverted_leave_balance = $current_leave_balance + $requested_leave_duration;

                        $user->update([
                            'leave_balance' => $reverted_leave_balance,
                        ]);

                        $logMessage .= ' ' . $requested_leave_duration . ' hours has been added to your leave balance.';
                    }
                }

                // Update the leave rejection data
                $leave->update([
                    'request_status' => 'rejected',
                    'admin_note'     => $adminNote,
                    'action_by'      => $adminName,
                ]);

                // Log the action
                ActionLog::create([
                    'user_id' => $userId,
                    'mode' => 'leave',
                    'message' => $logMessage,
                ]);
            }

            // === REJECT OVERWORK ===
            else if ($status === 'overtime') {
                $user = User::findOrFail($userId);
                $overtime = Overtime::find($requestId);

                $current_overtime_balance = $user->overtime_balance;
                $overtime_duration = $overtime->duration;

                $logMessage = Auth::user()->name . ' has rejected your overtime request because of ' . $adminNote . '.';

                // If the admin has approved the overtime before, but then cancelled
                if($overtime->request_status != 'review'){
                    $reverted_overtime_balance = $current_overtime_balance - $overtime_duration;

                    if ($reverted_overtime_balance < 0) {
                        return redirect()->back()->with('fail', [
                            'title' => 'Illegal balance value',
                            'message' => 'Overtime cancelation causing the balance below 0.',
                            'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
                        ]);
                    }

                    $user->update([
                        'overtime_balance' => $reverted_overtime_balance
                    ]);

                    $logMessage .= ' ' . $overtime_duration . ' hours has been deducted from your overtime balance.';
                }

                // Update the overtime rejection data
                $overtime->update([
                    'request_status' => 'rejected',
                    'admin_note'     => $adminNote,
                    'action_by'      => $adminName,
                ]);

                // Log the action
                ActionLog::create([
                    'user_id' => $userId,
                    'mode' => 'overtime',
                    'message' => $logMessage,
                ]);
            }

            return redirect()->back()->with('success', [
                'title' => $status . ' Rejected!',
                'message' => 'This overtime request has been rejected.',
            ]);
        }

        // Review -> Approved
        else if ($request->has('approved')) {
            $status = $request->input('approved');
            $user = User::findOrFail($userId);

            // === APPROVE LEAVE ===
            if($status === 'leave'){
            //     $leave = Leave::find($requestId);

            //     // Counting balance update
            //     $current_leave_balance = $user->leave_balance; // Saldo saat ini
            //     $requested_leave_period = $leave->leave_period; // Yang di-request
            //     $updated_leave_balance = $current_leave_balance - $requested_leave_period; // New balance = Saldo saat ini - Yang di-request

            //     if ($updated_leave_balance < 0) {
            //         return redirect()->back()->with('fail', [
            //             'title' => 'Illegal balance value',
            //             'message' => 'Leave cancelation causing the balance below 0.',
            //             'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
            //         ]);
            //     }

            //     // Update balance and leave status
            //     $user->update([
            //         'leave_balance' => $updated_leave_balance
            //     ]);

            //     $leave->update([
            //         'request_status' => 'approved',
            //         'action_by'=> $adminName,
            //     ]);

            //     // Log the action
            //     ActionLog::create([
            //         'user_id' => $userId,
            //         'mode' => 'leave',
            //         'message' => Auth::user()->name . ' has approved the leave request',
            //     ]);
            }

            // === APPROVE OVERWORK ===
            else if ($status === 'overtime') {
                $overtime = Overtime::findOrFail($requestId);

                // Counting balance update
                $current_overtime_balance = $user->overtime_balance;
                $requested_overtime_duration = $overtime->duration;
                $updated_overtime_balance = $current_overtime_balance + $requested_overtime_duration;

                // Update user balance and overtime status
                $user->update([
                    'overtime_balance' => $updated_overtime_balance
                ]);

                $overtime->update([
                    'request_status' => 'approved',
                    'action_by'      => $adminName,
                ]);

                // Log the action
                ActionLog::create([
                    'user_id' => $userId,
                    'mode' => 'overtime',
                    'message' => Auth::user()->name . ' has approved your overtime request. ' . $requested_overtime_duration . ' hours has been added to your overtime balance.',
                ]);
            }

            return redirect()->back()->with('success', [
                'title' => $status . ' Approved!',
                'message' => "This {$status} request has been approved.",
            ]);
        }
    }
}