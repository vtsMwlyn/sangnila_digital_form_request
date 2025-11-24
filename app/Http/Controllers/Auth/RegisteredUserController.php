<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActionLog;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validate = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::default()],
            'phone_number' => ['required', 'string', 'max:30'],
            'status' => ['required'],
            'position' => ['required'],
            'position_other' => 'nullable|string',
            'department' => ['required'],
            'department_other' => 'nullable|string',
            'role' => ['required', 'in:admin,user'],
            'Leave_Balance_Day' => ['required', 'integer'],
            'Leave_Balance_Hour' => 'nullable|numeric',
            'overwork_balance_Day' => ['required', 'integer'],
            'overwork_balance_Hour' => 'nullable|numeric',
        ]);

        $validate['position'] = $validate['position'] === 'other'
        ? $validate['position_other']
        : $validate['position'];

        $validate['department'] = $validate['department'] === 'other'
            ? $validate['department_other']
            : $validate['department'];

        unset($validate['position_other'], $validate['department_other']);

        $overworkAllowance = (int) $validate['Leave_Balance_Day'] * 8 +  $validate['Leave_Balance_Hour'];
        $TotalOverwork = (int) $validate['overwork_balance_Day'] * 8 + $validate['overwork_balance_Hour'] ;

        $user = User::create([
            'name' => $validate['name'],
            'email' => $validate['email'],
            'password' => Hash::make($validate['password']),
            'phone_number' => $validate['phone_number'],
            'status' => $validate['status'],
            'position' => $validate['position'],
            'department' => $validate['department'],
            'role' => $validate['role'],
            'leave_balance' => $overworkAllowance,
            'overwork_balance' => $TotalOverwork,
        ]);

        event(new Registered($user));

        return redirect(route('account.show', absolute: false));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'status' => 'nullable|string|max:255',
            'Leave_Balance_Day' => 'nullable|numeric',
            'Leave_Balance_Hour' => 'nullable|numeric',
            'overwork_balance_Day' => 'nullable|numeric',
            'overwork_balance_Hour' => 'nullable|numeric',
            'position' => 'nullable|string|max:255',
            'position_other' => 'nullable|string',
            'department' => 'nullable|string|max:255',
            'department_other' => 'nullable|string',
        ]);

        $validate['position'] = $validate['position'] === 'other'
            ? $validate['position_other']
            : $validate['position'];

        $validate['department'] = $validate['department'] === 'other'
            ? $validate['department_other']
            : $validate['department'];

        unset($validate['position_other'], $validate['department_other']);

        $leaveBalanceTimes8 = isset($validate['Leave_Balance_Day'], $validate['Leave_Balance_Hour'] )
            ? (int) $validate['Leave_Balance_Day'] * 8 + $validate['Leave_Balance_Hour']
            : $user->overwork;

        $totalOverworkTimes8 = isset($validate['overwork_balance_Day'], $validate['Leave_Balance_Hour'])
            ? (int) $validate['overwork_balance_Day'] * 8 +  $validate['overwork_balance_Hour']
            : $user->overwork_balance;

        $user->update([
            'name' => $validate['name'],
            'email' => $validate['email'],
            'phone_number' => $validate['phone_number'] ?? $user->phone_number,
            'status' => $validate ['status'] ?? $user->status,
            'position' => $validate['position'] ?? $user->position,
            'department' => $validate['department'] ?? $user->department,
            'leave_balance' => $leaveBalanceTimes8,
            'overwork_balance' => $totalOverworkTimes8,
        ]);

        return redirect()->back()->with('success', [
            'title' => 'User data updated successfully!',
            'message' => '',
            'time' => now()->setTimezone('Asia/Jakarta')->format('Y-m-d | H:i'),
        ]);
    }


    public function approve(Request $request, $mode)
    {
        $adminName = Auth::user()->name;
        $userId = $request->input('userId');
        $user = User::findOrFail($userId);

        $totalLate = floatval($request->input('totalLateValue')) * 8;

        if ($mode === 'leave') {
            if ($user->leave_balance < $totalLate) {
                return redirect()->back()->with('fail', [
                    'title'   => 'Exceeds Limit',
                    'message' => 'This user exceeds the allowed overwork limit.',
                ]);
            }
            $user->leave_balance =max(0, $user->leave_balance - $totalLate);
        } elseif ($mode === 'overwork') {
            if ($user->overwork_balance < $totalLate) {
                return redirect()->back()->with('fail', [
                    'title'   => 'Exceeds Limit',
                    'message' => 'This user exceeds the allowed overwork limit.',
                ]);
            }
            $user->overwork_balance = max(0, $user->overwork_balance - $totalLate);
        } else {
            return redirect()->back()->with('fail', [
                'title' => 'Invalid Mode',
                'message' => 'Mode must be either leave or overwork.',
            ]);
        }

        // dd([
        //     'user' => $user,
        //     'total' =>  $totalLate,
        //     'dt leave' =>   $user->leave_balance,
        //     'dt leaveoverwork' =>  $user->overwork_balance,
        // ]);

        $user->save();

        ActionLog::create([
            'user_id' => $user->id,
            'mode' => $mode,
            'amount' => $totalLate,
            'message' => " $adminName has reduced your {$mode} balance by $totalLate Hours due to lateness.",
        ]);

        return redirect()->back()->with('success', [
            'title' => 'Balance Updated',
            'message' => ucfirst($mode) . " balance has been reduced by $totalLate Hours.",
        ]);
    }
}