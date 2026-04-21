@php
    use Carbon\Carbon;

    // 1. Group variables to check type once
    $isLeave = $d->type === 'leave';
    $isOvertime = $d->type === 'overtime';

    // 2. Initialize default variables to keep the HTML perfectly clean later
    $durationStr = '';
    $startDateStr = '';
    $finishDateStr = '';
    
    // 3. Calculate Dates and Durations
    if ($isLeave) {
        $start = Carbon::parse($d->start_leave);
        $finish = $start->copy();
        $periodDays = $d->leave_period / 8;
        $addDays = 0;

        while ($addDays < floor($periodDays)) {
            if (!$finish->isWeekend()) {
                $addDays++;
            }
            $finish->addDay();
        }

        $periodHours = ($periodDays - floor($periodDays)) * 8;
        $floorDays = floor($periodDays);

        if ($floorDays == 0) {
            $durationStr = "{$periodHours} hours";
        } elseif ($periodHours == 0) {
            $finish->subDay();
            $durationStr = "{$floorDays} days";
        } else {
            $durationStr = "{$floorDays} days {$periodHours} hours";
        }

        $startDateStr = $start->format('d F Y');
        $finishDateStr = $finish->format('d F Y');
    } else {
        $durationStr = rtrim(rtrim(number_format($d->duration, 2, '.', ''), '0'), '.') . ' Hours';
        $startDateStr = Carbon::parse($d->start_overtime)->format('H : i');
        $finishDateStr = Carbon::parse($d->finished_overtime)->format('H : i');
    }

    // 4. Calculate Balances
    $leaveBal = $d->user->leave_balance ?? 0;
    $balanceDay = floor($leaveBal / 8);
    $balanceHour = ($leaveBal / 8 - $balanceDay) * 8;
    $formattedBalance = "{$balanceDay} Day" . ($balanceDay != 1 ? 's' : '') . " {$balanceHour} Hour" . ($balanceHour != 1 ? 's' : '');

    $overtimeBal = $d->user->overtime_balance ?? 0;
    $overtimeDay = floor($overtimeBal / 8);
    $overtimeHour = ($overtimeBal / 8 - $overtimeDay) * 8;
    $formattedOvertime = "{$overtimeDay} Day" . ($overtimeDay != 1 ? 's' : '') . " {$overtimeHour} Hour" . ($overtimeHour != 1 ? 's' : '');

    // 5. Clean strings for data attributes
    $createdDate = Carbon::parse($d->created_at)->format('d F Y');
    $overtimeDate = $d->overtime_date ? Carbon::parse($d->overtime_date)->format('d F Y') : '';
    $description = ucfirst(strtolower($d->reason ?? $d->task_description));
    $adminNote = ucfirst(strtolower($d->admin_note));
    $evidences = ($isOvertime && $d->evidence) ? $d->evidence->toJson() : null;
@endphp

{{-- Clean HTML Section --}}
<button
    class="eye-preview-btn transition hover:scale-105"
    title="Show Details"
    data-id="{{ $d->id }}"
    data-date="{{ $createdDate }}"
    data-overtime_date="{{ $overtimeDate }}"
    data-start="{{ $startDateStr }}"
    data-finished="{{ $finishDateStr }}"
    data-type="{{ $d->type }}"
    data-description="{{ $description }}"
    data-status="{{ $d->request_status }}"
    data-duration="{{ $durationStr }}"
    data-admin_change="{{ $d->action_by ?? '—' }}"
    data-admin_role="{{ $d->role }}"
    data-balance="{{ $formattedBalance }}"
    data-overtime="{{ $formattedOvertime }}"
    data-admin_note="{{ $adminNote }}"
    @if($evidences) data-evidences="{{ $evidences }}" @endif
>
    <img src="{{ asset('img/view.svg') }}" alt="view">
</button>

@if(auth()->user()->role === 'admin')
    <form
        action="{{ route('request.edit', ['id' => $d->id, 'userId' => $d->user_id]) }}"
        method="post"
        class="flex justify-between gap-1"
    >
        @csrf
        <input type="hidden" name="this_leave_period" value="{{ $d->leave_period }}" />

        {{-- Approve Button --}}
        <button
            type="submit"
            name="approved"
            id="approved-{{ $d->id }}"
            data-leaveId="{{ $d->id }}"
            data-leavePeriod="{{ $d->leave_period }}"
            data-user="{{ $d->user }}"
            value="{{ $d->type }}"
            class="approved {{ $d->request_status === 'approved' ? 'hidden' : 'flex' }} transition hover:scale-105"
            title="Accept"
            @if($isOvertime)
                onclick="return confirm('Are you sure want to accept this request?')"
            @else
                onclick="event.preventDefault(); openChooseModal(this);"
            @endif
        >
            <img src="{{ asset('img/yesbox.svg') }}" alt="accept" >
        </button>

        {{-- Reject Button --}}
        <button
            type="button"
            value="{{ $d->type }}"
            id="rejectButton-{{ $d->id }}"
            class="rejectButton {{ $d->request_status === 'rejected' ? 'hidden' : 'flex' }} transition hover:scale-105"
            title="Reject"
        >
            <img src="{{ asset('img/exit.svg') }}" alt="reject" >
        </button>
    </form>
@endif