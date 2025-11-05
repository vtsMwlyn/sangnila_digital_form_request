<div class="flex space-x-2">
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
        class="eye-preview-btn text-gray-600 rounded px-2"
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
            data-admin_note="{{ ucfirst(strtolower($d->admin_note)) }}"
            @if($d->type === 'overwork') data-evidences="{{ $d->evidence->toJson() }}" @endif >

            <img src="{{ asset('img/view.svg') }}" alt="view" >
    </button>

    @if (auth()->user()->role === 'admin')
        <form
            action="{{route('request.edit', ['id' => $d->id, 'userId' => $d->user_id])}}"
            method="post"
            class="flex justify-between gap-2"
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
                data-leave="{{ $d->id}}"
                value="{{ $d->type }}"
                class="approved {{ $d->request_status === 'approved' ? 'hidden' : 'flex' }}"
                title="Accept"
                @if($d->type === 'overwork')
                    onclick="return confirm('Are you sure want to accept this request?')"
                @else
                    onclick="event.preventDefault(); openChooseModal(this);"
                @endif
            >
                <img src="{{ asset('img/yesbox.svg') }}" alt="view">
            </button>

            <button
                type="button"
                value="{{$d->type}}"
                id="rejectButton"
                class="rejectButton {{$d->request_status === 'rejected' ? 'hidden' : 'flex'}}"
                title="Reject"
            >
            <img src="{{ asset('img/exit.svg') }}" alt="view" >
            </button>

        </form>
    @endif
</div>
