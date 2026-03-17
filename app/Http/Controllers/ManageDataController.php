<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Leave;
use App\Models\Overwork;
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
                    if($source == 'overwork_balance'){
                        $current_overwork_balance = $user->overwork_balance;
                        $reverted_overwork_balance =  $current_overwork_balance + $requested_leave_duration;

                        $user->update([
                            'overwork_balance' => $reverted_overwork_balance,
                        ]);

                        $logMessage .= ' ' . $requested_leave_duration . ' hours has been added to your overwork balance.';
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
            else if ($status === 'overwork') {
                $user = User::findOrFail($userId);
                $overwork = Overwork::find($requestId);

                $current_overwork_balance = $user->overwork_balance;
                $overwork_duration = $overwork->duration;

                $logMessage = Auth::user()->name . ' has rejected your overwork request because of ' . $adminNote . '.';

                // If the admin has approved the overwork before, but then cancelled
                if($overwork->request_status != 'review'){
                    $reverted_overwork_balance = $current_overwork_balance - $overwork_duration;

                    if ($reverted_overwork_balance < 0) {
                        return redirect()->back()->with('fail', [
                            'title' => 'Illegal balance value',
                            'message' => 'Overwork cancelation causing the balance below 0.',
                            'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
                        ]);
                    }

                    $user->update([
                        'overwork_balance' => $reverted_overwork_balance
                    ]);

                    $logMessage .= ' ' . $overwork_duration . ' hours has been deducted from your overwork balance.';
                }

                // Update the overwork rejection data
                $overwork->update([
                    'request_status' => 'rejected',
                    'admin_note'     => $adminNote,
                    'action_by'      => $adminName,
                ]);

                // Log the action
                ActionLog::create([
                    'user_id' => $userId,
                    'mode' => 'overwork',
                    'message' => $logMessage,
                ]);
            }

            return redirect()->back()->with('success', [
                'title' => $status . ' Rejected!',
                'message' => 'This overwork request has been rejected.',
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
            else if ($status === 'overwork') {
                $overwork = Overwork::findOrFail($requestId);

                // Counting balance update
                $current_overwork_balance = $user->overwork_balance;
                $requested_overwork_duration = $overwork->duration;
                $updated_overwork_balance = $current_overwork_balance + $requested_overwork_duration;

                // Update user balance and overwork status
                $user->update([
                    'overwork_balance' => $updated_overwork_balance
                ]);

                $overwork->update([
                    'request_status' => 'approved',
                    'action_by'      => $adminName,
                ]);

                // Log the action
                ActionLog::create([
                    'user_id' => $userId,
                    'mode' => 'overwork',
                    'message' => Auth::user()->name . ' has approved your overwork request. ' . $requested_overwork_duration . ' hours has been added to your overwork balance.',
                ]);
            }

            return redirect()->back()->with('success', [
                'title' => $status . ' Approved!',
                'message' => "This {$status} request has been approved.",
            ]);
        }
    }
}