@extends('layouts.app')

@section('content')
    @php
        function formatTime($value) {
            $day = intdiv($value, 8);
            $hour = $value % 8;
            return "{$day} Day" . ($day != 1 ? 's' : '') . " {$hour} Hour" . ($hour != 1 ? 's' : '');
        }
    @endphp

    <div class="container-draft bg-[#F0F3F8] p-6 rounded-lg shadow-lg overflow-x-auto">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
            <h2 class="text-2xl font-bold text-[#012967]">Manage Account</h2>

            <div class="sm:ml-auto w-full sm:w-[220px]">
                <input
                    type="search"
                    id="search"
                    placeholder="Search by name..."
                    class="w-full border border-gray-300 rounded-full px-4 py-2 focus:ring-2 focus:ring-cyan-400"
                >
            </div>
        </div>

        {{-- ADD BUTTON --}}
        <a href="{{ route('register') }}"
        class="inline-flex items-center gap-2 px-4 py-2 mb-4 rounded-lg text-white font-semibold bg-gradient-to-r from-[#1EB8CD] to-[#2652B8] hover:opacity-90">
            +
            <span>Add Account</span>
        </a>

        {{-- =========================
            🖥 DESKTOP TABLE
        ========================== --}}
        <table class="hidden sm:table w-full text-left border-b">
            <thead class="border-b">
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Position & Department</th>
                <th>Balance</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>

            <tbody>
            @forelse($data as $d)
                <tr class="{{ $loop->odd ? 'bg-white' : '' }}">
                    <td>{{ $loop->iteration }}</td>

                    <td class="font-medium">{{ $d->name }}</td>

                    <td>
                        {{ $d->position }} <br>
                        <span class="text-sm text-gray-500">{{ $d->department }}</span>
                    </td>

                    <td class="text-sm">
                        <div>{{ formatTime($d->leave_balance) }}</div>
                        <div class="text-gray-500">{{ formatTime($d->overtime_balance) }}</div>
                    </td>

                    <td>
                        <span class="px-3 py-1 text-white text-sm rounded-full
                            {{ $d->status_account === 'active' ? 'bg-blue-500' : 'bg-red-500' }}">
                            {{ ucfirst($d->status_account) }}
                        </span>
                    </td>

                    <td>
                        @include('view.admin.components.account-actions', ['d' => $d])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-6 text-gray-500">
                        No data found
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{-- =========================
            📱 MOBILE CARD
        ========================== --}}
        <div class="sm:hidden space-y-3 mt-4">
            @forelse($data as $d)
                <div x-data="{open:false}" class="bg-white rounded-lg p-4 shadow">

                    {{-- HEADER --}}
                    <div class="flex justify-between cursor-pointer"
                        @click="open = !open">
                        <div class="font-semibold text-[#012967]">
                            {{ $d->name }}
                        </div>
                        <span x-text="open ? '▲' : '▼'"></span>
                    </div>

                    {{-- BODY --}}
                    <div x-show="open" class="mt-3 text-sm space-y-2">

                        <div><b>Position:</b> {{ $d->position }}</div>
                        <div><b>Department:</b> {{ $d->department }}</div>

                        <div>
                            <b>Leave:</b>
                            <span class="badge">{{ formatTime($d->leave_balance) }}</span>
                        </div>

                        <div>
                            <b>Overtime:</b>
                            <span class="badge">{{ formatTime($d->overtime_balance) }}</span>
                        </div>

                        <div>
                            <b>Status:</b>
                            <span class="badge
                                {{ $d->status_account === 'active' ? 'bg-blue-500' : 'bg-red-500' }}">
                                {{ ucfirst($d->status_account) }}
                            </span>
                        </div>

                        <div>
                            <b>Action:</b>
                            @include('view.admin.components.account-actions', ['d' => $d])
                        </div>

                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500">No data</p>
            @endforelse
        </div>

    </div>

    {{-- MODALS --}}
    <x-modal name="leave-preview-modal" maxWidth="lg">
        <div class="p-6">
            <h3 class="text-xl font-bold mb-4">Account Preview</h3>
            <div id="leave-preview-account"></div>
        </div>
    </x-modal>

    <x-modal-late/>
    <x-modal-edit/>
    <x-modal-success/>

    <script>
        $(function () {

            /* =========================
            🔎 SEARCH (optimized)
            ========================= */
            const $rows = $('tbody tr');

            $('#search').on('input', function () {
                const keyword = $(this).val().toLowerCase();

                $rows.each(function () {
                    const name = $(this).find('td:eq(1)').text().toLowerCase();
                    $(this).toggle(name.includes(keyword));
                });
            });


            /* =========================
            👁 PREVIEW MODAL (delegated)
            ========================= */
            $(document).on('click', '.eye-preview-btn', function () {
                const d = $(this).data();
                const statusClass = getStatusClass(d.status);

                let body = `
                <table class="w-full text-sm text-gray-800 border-collapse">
                    <tbody class="divide-y divide-gray-200">

                        <tr><th>Creation Date Account:</th><td>${d.cration_account}</td></tr>
                        <tr><th>Name:</th><td class="capitalize">${d.name}</td></tr>
                        <tr><th>Email:</th><td>${d.email}</td></tr>
                        <tr><th>Phone:</th><td>${d.phone ?? '-'}</td></tr>
                        <tr><th>Position:</th><td>${d.position}</td></tr>
                        <tr><th>Department:</th><td>${d.department}</td></tr>
                        <tr><th>Role:</th><td class="capitalize">${d.role}</td></tr>
                        <tr><th>Status Employee:</th><td>${d.status_employee}</td></tr>

                        <tr>
                            <th>Status:</th>
                            <td class="${statusClass} inline-block px-3 py-1 rounded-full text-white">${d.status}</td>
                        </tr>

                        ${
                            d.role !== 'admin'
                            ? `
                            <tr><th>Leave Balance:</th><td>${d.balance}</td></tr>
                            <tr><th>Total Overtime:</th><td>${d.overtime}</td></tr>
                            `
                            : ''
                        }

                    </tbody>
                </table>`;

                $('#leave-preview-account').html(body);

                window.dispatchEvent(new CustomEvent('open-modal', {
                    detail: 'leave-preview-modal'
                }));
            });


            /* =========================
            ✏️ EDIT MODAL
            ========================= */
            $(document).on('click', '.edit-account', function () {
                openEditModal(this);
            });


            /* =========================
            ⏱ LATE MODAL
            ========================= */
            $(document).on('click', '.late-balance', function () {
                openLateModal(this);
            });


            /* =========================
            🔢 AUTO SYNC LATE INPUT
            ========================= */
            $('#totalLate').on('input', function () {
                const val = $(this).val();
                $('#totalLateLeave, #totalLateOvertime').val(val);
            });

        });


        /* =========================
        🎨 STATUS CLASS
        ========================= */
        function getStatusClass(status) {
            return {
                active: 'bg-blue-500 text-white text-sm font-semibold',
                suspended: 'bg-red-500 text-white text-sm font-semibold'
            }[status.toLowerCase()] || 'bg-gray-400 text-white text-sm';
        }


        /* =========================
        ✏️ OPEN EDIT MODAL (optimized)
        ========================= */
        function openEditModal(btn) {
            const $btn = $(btn);
            const d = $btn.data();

            $('#user_id').val(d.id);
            $('#name').val(d.name);
            $('#email').val(d.email);
            $('#phone').val(d.phone || '');

            // balance calc
            const leaveDay = Math.floor(d.balance / 8);
            const overtimeDay = Math.floor(d.overtime / 8);

            $('#Leave_Balance_Day').val(leaveDay);
            $('#overtime_balance_Day').val(overtimeDay);
            $('#Leave_Balance_Hour').val(d.balance % 8);
            $('#overtime_balance_Hour').val(d.overtime % 8);

            $('#editForm').attr('action', `/account/update/${d.id}`);

            toggleSelectInput('#positionSelect', '#positionInput', d.position);
            toggleSelectInput('#departmentSelect', '#departmentInput', d.department);

            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-modal' }));
        }


        /* =========================
        ⏱ OPEN LATE MODAL
        ========================= */
        function openLateModal(btn) {
            const user = $(btn).data('user');

            $('#lateUserIdLeave, #lateUserIdOvertime').val(user.id);

            const leaveDay = Math.floor(user.leave_balance / 8);
            const overtimeDay = Math.floor(user.overtime_balance / 8);

            const leaveHour = user.leave_balance % 8;
            const overtimeHour = user.overtime_balance % 8;

            $('#late-modal-user-leave-balance')
                .text(`Available: ${leaveDay} day(s) ${leaveHour} hour(s)`);

            $('#late-modal-user-overtime-balance')
                .text(`Available: ${overtimeDay} day(s) ${overtimeHour} hour(s)`);

            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'late-modal' }));
        }


        /* =========================
        🔁 REUSABLE SELECT/INPUT TOGGLE
        ========================= */
        function toggleSelectInput(selectId, inputId, value) {
            const $select = $(selectId);
            const $input = $(inputId);

            if ($select.find(`option[value="${value}"]`).length) {
                $select.val(value).removeClass('hidden');
                $input.addClass('hidden');
            } else {
                $select.val('other').addClass('hidden');
                $input.removeClass('hidden').val(value);
            }
        }
    </script>

@endsection