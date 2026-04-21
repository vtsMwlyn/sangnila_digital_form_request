<div class="flex items-center gap-2">

    {{-- VIEW --}}
    <button class="eye-preview-btn"
        data-id="{{ $d->id }}"
        data-name="{{ $d->name }}"
        data-email="{{ $d->email }}"
        data-status="{{ $d->status_account }}"
        data-balance="{{ formatTime($d->leave_balance) }}"
        data-overtime="{{ formatTime($d->overtime_balance) }}"
        data-position="{{ $d->position }}"
        data-department="{{ $d->department }}"
        data-role="{{ $d->role }}"
        data-status_employee="{{ $d->status }}">
        <img src="{{ asset('img/view.svg') }}" alt="view" >
    </button>

    {{-- EDIT --}}
    <button class="edit-account"
        data-id="{{ $d->id }}"
        data-name="{{ $d->name }}"
        data-email="{{ $d->email }}"
        data-phone="{{ $d->phone_number }}"
        data-position="{{ $d->position }}"
        data-department="{{ $d->department }}"
        data-balance="{{ $d->leave_balance }}"
        data-overtime="{{ $d->overtime_balance }}">
        <img src="{{ asset('img/edit.svg') }}" alt="edit" >
    </button>

    {{-- LATE --}}
    <button class="late-balance"
        data-user='@json($d)'>
        <img src="{{ asset('img/minus.svg') }}" alt="late" >
    </button>

    {{-- TOGGLE STATUS --}}
    @if ($d->email !== 'superadmin@sangnila.com')
        @if ($d->status_account === 'active')
            <a href="{{ route('account.edit', ['id'=>$d->id,'status'=>'suspended']) }}" onclick="return confirm('Are you sure want to disable this account?')">
                <img src="{{ asset('img/ban.svg') }}" alt="ban" >
            </a>
        @else
            <a href="{{ route('account.edit', ['id'=>$d->id,'status'=>'active']) }}" onclick="return confirm('Are you sure want to reenable this account?')">
                <img src="{{ asset('img/unban.svg') }}" alt="unban" >
            </a>
        @endif

        <a href="{{ route('account.delete', ['id'=>$d->id]) }}" onclick="return confirm('Are you sure want to delete this account?')">
            <img src="{{ asset('img/delete-button.svg') }}" alt="delete" >
        </a>
    @endif
</div>