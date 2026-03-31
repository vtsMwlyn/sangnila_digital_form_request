@php
    if ($d->type === 'leave') {
        $start = Carbon\Carbon::parse($d->start_leave);
        $finish = $start->copy();
        $periodDays = $d->leave_period / 8;
        $addDays = 0;

        while ($addDays < floor($periodDays)) {
            if (!$finish->isWeekend()) {
                $addDays++;
            }
            $finish->addDay();
        }

        $periodHours = ($periodDays - floor($periodDays) ) * 8;

        if (floor($periodDays) == '0') {
            $duration = $periodHours . ' hours';
        } elseif ($periodHours == '0') {
            $finish = $finish->copy()->subDay();
            $duration = floor($periodDays) . ' days';
        } else {
            $duration = floor($periodDays) . ' days ' . $periodHours . ' hours';
        }
    } else {
        $duration = rtrim(rtrim(number_format($d->duration, 2, '.', ''), '0'), '.') . ' Hours';
    }
@endphp

<button
    class="eye-preview-btn transition hover:scale-105"
    title="Show Details"
    data-id="{{ $d->id }}"
    data-date="{{ Carbon\Carbon::parse($d->created_at)->format('d F Y') }}"
    data-overtime_date="{{ Carbon\Carbon::parse($d->overtime_date)->format('d F Y') }}"
    data-start="{{ $d->type === 'overtime'
                                ? Carbon\Carbon::parse($d->start_overtime)->format('H : i')
                                : Carbon\Carbon::parse($d->start_leave)->format('d F Y') }}"
    data-finished="{{ $d->type === 'overtime'
                                ? Carbon\Carbon::parse($d->finished_overtime)->format('H : i')
                                : $finish->format('d F Y') }}"
    data-type="{{ $d->type }}"
    data-description="{{ ucfirst(strtolower($d->reason ?? $d->task_description)) }}"
    data-status="{{ $d->request_status }}"
    data-duration="{{ $duration }}"
    data-admin_change="{{ $d->action_by ?? '—' }}"
    @php
        // BALANCE
        $balanceDay  = floor($d->user->leave_balance / 8);
        $balanceHour = ($d->user->leave_balance / 8 - $balanceDay) * 8;

        // OVERWORK
        $overtimeDay  = floor($d->user->overtime_balance / 8);
        $overtimeHour = ($d->user->overtime_balance / 8 - $overtimeDay) * 8;

        $formattedBalance = "{$balanceDay} Day" . ($balanceDay != 1 ? 's' : '') . " {$balanceHour} Hour" . ($balanceHour != 1 ? 's' : '');
        $formattedOvertime = "{$overtimeDay} Day" . ($overtimeDay != 1 ? 's' : '') . " {$overtimeHour} Hour" . ($overtimeHour != 1 ? 's' : '');
    @endphp
    data-balance="{{ $formattedBalance }}"
    data-overtime="{{ $formattedOvertime }}"
    data-admin_note="{{ ucfirst(strtolower($d->admin_note)) }}"
    data-admin_change="{{$d->role}}"
    @if($d->type === 'overtime') data-evidences="{{ $d->evidence->toJson() }}" @endif
>
    <img src="{{ asset('img/view.svg') }}" alt="view">
</button>

@if(auth()->user()->role === 'admin')
    <form
        action="{{route('request.edit', ['id' => $d->id, 'userId' => $d->user_id])}}"
        method="post"
        class="flex justify-between gap-1"
    >
        @csrf
        <input
            type="hidden"
            name="this_leave_period"
            value="{{$d->leave_period}}"
        />

        {{-- When the approve button is clicked, the handler function is in manage-data.blade.php --}}
        <button
            type="submit"
            name="approved"
            id="approved"
            data-leaveId="{{ $d->id }}"
            data-leavePeriod="{{ $d->leave_period }}"
            data-user="{{ $d->user }}"
            value="{{ $d->type }}"
            class="approved {{ $d->request_status === 'approved' ? 'hidden' : 'flex' }} transition hover:scale-105"
            title="Accept"
            @if($d->type === 'overtime')
                onclick="return confirm('Are you sure want to accept this request?')"
            @else
                onclick="event.preventDefault(); openChooseModal(this);"
            @endif
        >
            <img src="{{ asset('img/yesbox.svg') }}" alt="view" >
        </button>

        {{-- When the reject button is clicked, a popup (modal-reject.blade.php is opened, handled in manage-data.blade.php) --}}
        <button
            type="button"
            value="{{$d->type}}"
            id="rejectButton"
            class="rejectButton {{$d->request_status === 'rejected' ? 'hidden' : 'flex'}} transition hover:scale-105"
            title="Reject"
        >
        <img src="{{ asset('img/exit.svg') }}" alt="view" >
        </button>
    </form>
@endif
