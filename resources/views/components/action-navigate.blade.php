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
        $duration = Carbon\Carbon::parse($d->start_overwork)->diff($d->finished_overwork);
    }
@endphp

<button
    class="eye-preview-btn transition hover:scale-105"
    title="Show Details"
    data-id="{{ $d->id }}"
    data-date="{{ Carbon\Carbon::parse($d->created_at)->format('d F Y') }}"
    data-overwork_date="{{ Carbon\Carbon::parse($d->overwork_date)->format('d F Y') }}"
    data-start="{{ $d->type === 'overwork'
                                ? Carbon\Carbon::parse($d->start_overwork)->format('H : i')
                                : Carbon\Carbon::parse($d->start_leave)->format('d F Y') }}"
    data-finished="{{ $d->type === 'overwork'
                                ? Carbon\Carbon::parse($d->finished_overwork)->format('H : i')
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
        $overworkDay  = floor($d->user->overwork_balance / 8);
        $overworkHour = ($d->user->overwork_balance / 8 - $overworkDay) * 8;

        $formattedBalance = "{$balanceDay} Day" . ($balanceDay != 1 ? 's' : '') . " {$balanceHour} Hour" . ($balanceHour != 1 ? 's' : '');
        $formattedOverwork = "{$overworkDay} Day" . ($overworkDay != 1 ? 's' : '') . " {$overworkHour} Hour" . ($overworkHour != 1 ? 's' : '');
    @endphp
    data-balance="{{ $formattedBalance }}"
    data-overwork="{{ $formattedOverwork }}"
    data-admin_note="{{ ucfirst(strtolower($d->admin_note)) }}"
    data-admin_change="{{$d->role}}"
    @if($d->type === 'overwork') data-evidences="{{ $d->evidence->toJson() }}" @endif
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
            @if($d->type === 'overwork')
                onclick="return confirm('Are you sure want to accept this request?')"
            @else
                onclick="event.preventDefault(); openChooseModal(this);"
            @endif
        >
            <img src="{{ asset('img/yesbox.svg') }}" alt="view" >
        </button>

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
