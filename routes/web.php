<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\OverworkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeductionController;
use Symfony\Component\Routing\RequestContext;
use App\Http\Controllers\ManageDataController;
use App\Http\Controllers\ManageAccountController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

// Home
Route::get('/', function () {
    $view = Auth::id() === null ? route('login') : route('dashboard');
    return redirect($view);
});

// Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Account Suspended
Route::middleware(['active'])->group(function () {
    Route::prefix('info')->name('info.')->group(function () {
        Route::get('/account-suspended', function () {
            return view('suspended');
        })->name('account-suspended');
    });
});

// MAIN APP ROUTES
Route::middleware(['auth', 'verified', 'suspended'])->group(function () {
    // ==== ACCESSIBLE BY ADMIN AND EMPLOYEE ==== //
    // Dashboard
    Route::match(['get', 'post'], '/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // All overworks
    Route::get('/overwork', [RequestController::class, 'showRecent'])->name('overwork.show');

    // All leaves
    Route::get('/leave', [RequestController::class, 'showRecent'])->name('leave.show');

    // Logging
    Route::get('/LogActivity', [LogController::class, 'show'])->name('LogActivity.show');


    // ==== EMPLOYEE ==== //
    Route::middleware('role:user')->group(function(){
        // Draft
        Route::get('/draft', [RequestController::class, 'showDraft'])->name('draft');

        // Overwork
        Route::prefix('overwork')->name('overwork.')->group(function () {
            Route::middleware(['auth', 'role:user'])->group(function () {
                // New overwork request page
                Route::get('/form', [OverworkController::class, 'create'])->name('form-view');

                // Store new overwork request
                Route::post('/proccess', [OverworkController::class, 'store'])->name('insert');

                // Edit overwork request page
                Route::get('/{overwork}/edit', [OverworkController::class, 'edit'])->name('edit');

                // Update overwork request data
                Route::put('/{overwork}', [OverworkController::class, 'update'])->name('update');

                // Destroy overwork request data
                Route::delete('/{overwork}', [OverworkController::class, 'destroy'])->name('delete');

                // Remove overwork request evidence
                Route::delete('/evidence/{evidence}', [OverworkController::class, 'deleteEvidence'])->name('evidence.delete');
            });
        });

        // Leave
        Route::prefix('leave')->name('leave.')->group(function () {
            Route::middleware(['auth', 'role:user'])->group(function () {
                // New leave request page
                Route::get('/form', [LeaveController::class, 'create'])->name('form-view')->middleware('balance');

                // Create and store leave request
                Route::match(['get', 'post'], '/proccess', [LeaveController::class, 'store'])->name('insert');

                // Edit leave request page
                Route::get('/{leave}/edit', [LeaveController::class, 'edit'])->name('edit');

                // Update leave request data
                Route::put('/{leave}', [LeaveController::class, 'update'])->name('update');

                // Destroy leave request data
                Route::delete('/{leave}', [LeaveController::class, 'destroy'])->name('delete');
            });
            
        });
    });


    // ==== ADMIN ==== //
    Route::middleware('role:admin')->group(function () {
        // Manage overwork and leave requests

        // Approve and reject overwork, reject leave
        Route::match(['get', 'post'], '/request/edit/{id}/{userId}', [ManageDataController::class, 'edit'])->name('request.edit');

        // Approve leave
        Route::post('/admin/leave/approve/{mode}', [LeaveController::class, 'approve'])->name('admin.leave.approve');

        // Manage employee accounts
        Route::prefix('account')->name('account.')->group(function () {
            // All accounts
            Route::get('/', [ManageAccountController::class, 'show'])->name('show');

            // Edit account
            Route::get('edit/user/{id}/status/{status}', [ManageAccountController::class, 'edit'])
            ->name('edit');

            // Destroy account
            Route::get('delete/{id}', [ManageAccountController::class, 'destroy'])->name('delete');
        });
    });


    // ==== MISCELANEOUS ==== //
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/update', [ProfileController::class, 'update'])->name('update');
        Route::delete('/delete', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Get current leave balance
    Route::get('leave_allowance', function (Request $request) {
        if (!$request->expectsJson()) {
            abort(403, 'Forbidden');
        }
        $user = Auth::user()->id;
        $allowance = User::findOrFail($user)->leave_balance;
        $total = Leave::where('user_id', $user)->where('request_status', 'approved')->sum('leave_period');

        return response()->json([
            'leave_allowance' => $allowance,
            'leave_period' => $total
        ]);
    });
});

// API for external apps
Route::get('/api/employee/leave/calendar', function(){
    $leaves = Leave::whereHas('user', function($q) {
        return $q->where('is_teacher', 1);
    })->where('request_status', 'approved')->where('start_leave', 'like', Carbon::today()->format('Y-m') . '%')->get();

    $data['calendar_events'] = $leaves->flatMap(function ($leave) {

        $days = max(1, ceil($leave->leave_period / 8));
        $startDate = Carbon::parse($leave->start_leave);
        $endDate   = $startDate->copy()->addWeekdays($days - 1);

        $segments = [];
        $current = $startDate->copy();

        while ($current->lte($endDate)) {

            // Skip weekends
            if ($current->isWeekend()) {
                $current->addDay();
                continue;
            }

            // End of current weekday streak
            $segmentStart = $current->copy();

            while ($current->isWeekday() && $current->lte($endDate)) {
                $current->addDay();
            }

            $segmentEnd = $current->copy(); // exclusive for FullCalendar

            $segments[] = [
                'id'       => $leave->id,
                'title'    => implode(' ', array_slice(explode(' ', trim($leave->user->name)), 0, 2)),
                'subtitle' => $leave->reason,
                'start'    => $segmentStart->toDateString(),
                'end'      => $segmentEnd->toDateString(),
                'color'    => 'oklch(71.5% 0.143 215.221)',
            ];
        }

        return response()->json(['data' => $segments]);
    });
});

require __DIR__ . '/auth.php';