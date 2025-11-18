@extends('layouts.tables')

@section('content')

<div class="mx-1 container-draft bg-[#F0F3F8] p-6 rounded-lg w-full shadow-lg overflow-x-auto">
    <div id="filter" class="flex flex-col sm:flex-row sm:items-center mb-8 gap-4">
        <h2 class="text-2xl font-bold text-[#012967]">Manage Account</h2>

        {{-- Search --}}
        <div class="sm:ml-auto w-full sm:w-auto">
            <input
                type="search"
                id="search"
                name="search"
                placeholder="Search by name"
                class="border border-gray-300 rounded-full px-4 py-1 w-full sm:w-[200px] focus:outline-none focus:ring-2 focus:ring-cyan-400"
            />
        </div>
    </div>


    @php
        $activeToggle = request('status', 'pending');
    @endphp

    <table class="hidden sm:table text-left justify-center items-center border-b border-gray-400 w-full ">
        <a href="{{ route('register') }}"
            class="bg-gradient-to-r from-[#1EB8CD] to-[#2652B8] hover:from-cyan-600 hover:to-blue-800 text-white font-semibold py-2 px-2 rounded-lg transition duration-300 flex items-center space-x-2 w-[150px] my-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M12 6v12M6 12h12" />
            </svg>
            <span>Add Account</span>
        </a>
        <thead class="bg-transparent text-[#1e293b] border-b-2 border-gray-300 ">
            <tr class="text-center">
                <th class="py-3 px-6 whitespace-nowrap font-semibold">No</th>
                <th class="py-3 px-6 whitespace-nowrap font-semibold">Name</th>
                <th class="py-3 px-6 whitespace-nowrap font-semibold">Position & Department</th>
                {{-- <th class="py-3 px-6 whitespace-nowrap font-semibold">Role</th> --}}
                <th class="py-3 px-6 whitespace-nowrap font-semibold">Leave Balance & <br> Total Overwork</th>
                <th class="py-3 px-6 whitespace-nowrap font-semibold">Status</th>
                <th class="py-3 px-6 whitespace-nowrap font-semibold text-center">Action</th>
            </tr>
        </thead>

            <tbody>
                @forelse($data as $d)
                <tr class="{{ $loop->odd ? 'bg-white' : 'bg-[#f1f5f9]' }} border-b border-gray-300 overflow-x-auto">

                    <td class="py-4 px-6">{{ $loop->iteration }}</td>
                    <td class="py-4 px-6">{{ $d->name }}</td>
                    <td class="py-4 px-6 text-sm ">{{ $d->position. ' | ' .$d->department }}</td>
                    {{-- <td class="py-4 px-6">{{$d->role}}</td> --}}
                    <td class="py-4 px-6 text-center">
                        @php
                            $allowanceDay = intdiv($d->overwork_allowance, 8);
                            $allowanceHour = $d->overwork_allowance % 8;

                            $totalDay = intdiv($d->total_overwork, 8);
                            $totalHour = $d->total_overwork % 8;
                        @endphp

                        {{ $allowanceDay }} Day
                        {{ $allowanceHour }} Hours
                         <br>
                        {{ $totalDay }} Day
                        {{ $totalHour }} Hours
                    </td>
                    <td class="py-4 px-6 font-semibold text-center">
                        <span class="{{$d->status_account === 'active' ? 'bg-blue-500' : 'bg-red-500'}} text-white rounded-full px-3 py-1 text-sm font-semibold">
                            {{ucfirst($d->status_account)}}
                        </span>
                    </td>

                    <td class="px-1 text-center">
                        <div class="flex space-x-2 justify-center items-center">
                            <button
                                class="eye-preview-btn "
                                title="Show Details"
                                data-id="{{ $d->id }}"
                                data-cration_account="{{ Carbon\Carbon::parse($d->created_at)->format('d - m - Y') }}"
                                data-name="{{ $d->name }}"
                                data-email="{{ $d->email }}"
                                data-status="{{ $d->status_account }}"
                                data-photo="{{ $d->profile_photo }}"
                                data-phone="{{ $d->phone_number }}"
                                data-position="{{ $d->position }}"
                                data-department="{{ $d->department }}"
                                data-role="{{ $d->role }}"
                                data-status_employee="{{ $d->status }}"
                                @php
                                $balanceDay = intdiv($d->overwork_allowance, 8);
                                $balanceHour = $d->overwork_allowance % 8;

                                $overworkDay = intdiv($d->total_overwork, 8);
                                $overworkHour = $d->total_overwork % 8;

                                $formattedBalance = "{$balanceDay} Day" . ($balanceDay != 1 ? 's' : '') . " {$balanceHour} Hour" . ($balanceHour != 1 ? 's' : '');
                                $formattedOverwork = "{$overworkDay} Day" . ($overworkDay != 1 ? 's' : '') . " {$overworkHour} Hour" . ($overworkHour != 1 ? 's' : '');
                            @endphp
                                data-balance="{{ $formattedBalance }}"
                                data-overwork="{{ $formattedOverwork }}"
                            >
                             <img src="{{ asset('img/view.svg') }}" alt="view" class="h-[24px] w-[24px]" >
                             </button>

                            <button
                                class="edit-account"
                                title="Edit Account"
                                data-id="{{ $d->id }}"
                                data-name="{{ $d->name }}"
                                data-email="{{ $d->email }}"
                                data-phone="{{ $d->phone_number }}"
                                data-position="{{ $d->position }}"
                                data-department="{{ $d->department }}"
                                data-balance="{{ $d->overwork_allowance }}"
                                data-overwork="{{ $d->total_overwork }}"
                                data-status_employee="{{ $d->status }}"
                                onclick="openEditModal(this)"
                            >
                                <img src="{{ asset('img/edit.svg') }}" alt="edit" class="h-[24px] w-[24px]">
                            </button>

                            <button
                                class="late-balance"
                                title="Lateness"
                                data-id="{{ $d->id }}"
                                onclick="openLateModal(this)">
                                <img src="{{ asset('img/minus.svg') }}" alt="" class="h-[24px] w-[24px]">
                            </button>

                            @php
                                $status = request('status');
                            @endphp
                            @if ($d->email != 'superadmin@sangnila.com')
                            @if ($d->status_account === 'active')
                            <a href="{{ route('account.edit', ['id' => $d->id, 'status' => 'suspended']) }}"
                                onclick="return confirm('Are you sure you want to suspend this account?')"
                                title="Suspend">
                                <img src="{{ asset('img/ban.svg') }}" alt="Suspend" class="h-[24px] w-[24px]">
                            </a>
                            @elseif ($d->status_account === 'suspended')
                            <a href="{{ route('account.edit', ['id' => $d->id, 'status' => 'active']) }}"
                                onclick="return confirm('Are you sure you want to unsuspend this account?')"
                                title="Unsuspend">
                                <img src="{{ asset('img/unban.svg') }}" alt="Unsuspend" class="h-[24px] w-[24px]">
                            </a>
                            @endif
                            <a href="{{route('account.delete', ['id' => $d->id])}}"
                                class="{{$status === 'rejected' ? 'hidden' : 'flex'}} "
                                title="Remove"
                                onclick="return confirm('Are you sure to remove this account?')"
                            >
                                <img src="{{ asset('img/delete-button.svg') }}" alt="view" class="h-[24px] w-[24px]" >
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
            <tr class="empty">
                <td colspan="7" class="py-8 px-6 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p>No data found</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

      {{-- MOBILE CARD VIEW --}}
      <div class="sm:hidden block mt-4">
        @forelse($data as $d)
            @php
                $statusClass = match($d->request_status) {
                    'approved' => 'bg-green-500 text-white rounded-full px-3 py-1 font-semibold',
                    'review' => 'bg-gray-500 text-gray-100 rounded-full px-3 py-1 font-semibold',
                    'rejected' => 'bg-red-500 text-white rounded-full px-3 py-1 font-semibold',
                    default => 'bg-yellow-500 text-white rounded-full px-3 py-1 font-semibold',
                };
            @endphp

           <div x-data="{ open: false }" class="bg-white rounded-xl shadow-md p-4 mb-3 border border-gray-200">

               {{-- HEADER --}}
               <button class="w-full flex justify-between items-center" x-on:click="open = !open">
                    <div>
                       <div class="font-semibold text-[#012967]">
                        {{ ucfirst($d->name) }}
                       </div>
                    </div>

                   {{-- ICONS --}}
                   <svg x-show="!open" class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                   </svg>

                   <svg x-show="open" class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                   </svg>
               </button>

               {{-- BODY --}}
                <div x-show="open" x-collapse class="mt-3 text-sm">
                   <div class="flex  grid-cols-2 gap-10">
                    <div class="mb-1">
                       <span class="font-semibold text-gray-700">Position: </span>
                       <div>{{ $d->position }}</div>
                   </div>

                   <div class="mb-1">
                    <span class="font-semibold text-gray-700">Department: </span>
                    <div>{{$d->department }}</div>
                    </div>
                 </div>

                   <div class="mb-1">
                       <span class="font-semibold text-gray-700">Leave Balance: </span>
                       <br>
                       <div class="mt-2 mb-2 py-1 px-3 inline-block rounded-full capitalize text-white {{ $d->type === 'overwork' ? 'bg-amber-500' : 'bg-sky-500' }}">
                            @php
                            $allowanceDay = intdiv($d->overwork_allowance, 8);
                            $allowanceHour = $d->overwork_allowance % 8;
                            @endphp

                            {{ $allowanceDay }} Day
                            {{ $allowanceHour }} Hours
                       </div>
                   </div>

                   <div class="mb-1">
                    <span class="font-semibold text-gray-700">Total Overwork: </span>
                    <br>
                    <div class="mt-2 mb-2 py-1 px-3 inline-block rounded-full capitalize text-white {{ $d->type === 'overwork' ? 'bg-amber-500' : 'bg-sky-500' }}">
                         @php
                         $totalDay = intdiv($d->total_overwork, 8);
                         $totalHour = $d->total_overwork % 8;
                         @endphp

                         {{ $totalDay }} Day
                         {{ $totalHour }} Hours
                    </div>
                </div>


                   <div class="mb-1">
                       <span class="font-semibold text-gray-700">Name:</span>
                       <div>{{ $d->name }}</div>
                   </div>


                   <div class="mb-1">
                       <span class="font-semibold text-gray-700">Reason:</span>
                       <div>{{ $d->reason ?? $d->task_description }}</div>
                   </div>

                   <div class="mb-1">
                       <span class="font-semibold text-gray-700">Status:</span>
                       <br>
                       <div class="{{$d->status_account === 'active' ? 'bg-blue-500' : 'bg-red-500'}} mt-2 mb-2 py-1 px-3 inline-block rounded-full capitalize text-white">
                        {{ucfirst($d->status_account)}}
                       </div>
                   </div>

                   <div class="mb-1 mt-2">
                       <span class="flex font-semibold text-gray-700 mb-2">Action:</span>
                       <div class="flex space-x-2 items-left">
                            <button
                                class="eye-preview-btn "
                                title="Show Details"
                                data-id="{{ $d->id }}"
                                data-cration_account="{{ Carbon\Carbon::parse($d->created_at)->format('d - m - Y') }}"
                                data-name="{{ $d->name }}"
                                data-email="{{ $d->email }}"
                                data-status="{{ $d->status_account }}"
                                data-photo="{{ $d->profile_photo }}"
                                data-phone="{{ $d->phone_number }}"
                                data-position="{{ $d->position }}"
                                data-department="{{ $d->department }}"
                                data-role="{{ $d->role }}"
                                data-status_employee="{{ $d->status }}"
                                @php
                                $balanceDay = intdiv($d->overwork_allowance, 8);
                                $balanceHour = $d->overwork_allowance % 8;

                                $overworkDay = intdiv($d->total_overwork, 8);
                                $overworkHour = $d->total_overwork % 8;

                                $formattedBalance = "{$balanceDay} Day" . ($balanceDay != 1 ? 's' : '') . " {$balanceHour} Hour" . ($balanceHour != 1 ? 's' : '');
                                $formattedOverwork = "{$overworkDay} Day" . ($overworkDay != 1 ? 's' : '') . " {$overworkHour} Hour" . ($overworkHour != 1 ? 's' : '');
                            @endphp
                                data-balance="{{ $formattedBalance }}"
                                data-overwork="{{ $formattedOverwork }}"
                            >
                             <img src="{{ asset('img/view.svg') }}" alt="view" class="h-[40px] w-[40px]" >
                             </button>

                            <button
                                class="edit-account"
                                title="Edit Account"
                                data-id="{{ $d->id }}"
                                data-name="{{ $d->name }}"
                                data-email="{{ $d->email }}"
                                data-phone="{{ $d->phone_number }}"
                                data-position="{{ $d->position }}"
                                data-department="{{ $d->department }}"
                                data-balance="{{ $d->overwork_allowance }}"
                                data-overwork="{{ $d->total_overwork }}"
                                data-status_employee="{{ $d->status }}"
                                onclick="openEditModal(this)"
                            >
                                <img src="{{ asset('img/edit.svg') }}" alt="edit" class="h-[40px] w-[40px]">
                            </button>

                            <button
                                class="late-balance"
                                title="Lateness"
                                data-id="{{ $d->id }}"
                                onclick="openLateModal(this)">
                                <img src="{{ asset('img/minus.svg') }}" alt="" class="h-[40px] w-[40px]">
                            </button>

                            @php
                                $status = request('status');
                            @endphp
                            @if ($d->email != 'superadmin@sangnila.com')
                            @if ($d->status_account === 'active')
                            <a href="{{ route('account.edit', ['id' => $d->id, 'status' => 'suspended']) }}"
                                onclick="return confirm('Are you sure you want to suspend this account?')"
                                title="Suspend">
                                <img src="{{ asset('img/ban.svg') }}" alt="Suspend" class="h-[40px] w-[40px]">
                            </a>
                            @elseif ($d->status_account === 'suspended')
                            <a href="{{ route('account.edit', ['id' => $d->id, 'status' => 'active']) }}"
                                onclick="return confirm('Are you sure you want to unsuspend this account?')"
                                title="Unsuspend">
                                <img src="{{ asset('img/unban.svg') }}" alt="Unsuspend" class="h-[40px] w-[40px]">
                            </a>
                            @endif
                            <a href="{{route('account.delete', ['id' => $d->id])}}"
                                class="{{$status === 'rejected' ? 'hidden' : 'flex'}} "
                                title="Remove"
                                onclick="return confirm('Are you sure to remove this account?')"
                            >
                                <img src="{{ asset('img/delete-button.svg') }}" alt="view" class="h-[40px] w-[40px]" >
                                </a>
                            @endif

                       </div>
                   </div>

               </div>
           </div>
           @empty
               <p class="text-center py-4 text-gray-500 italic">No recent request.</p>
           @endforelse
       </div>

</div>

<x-modal name="leave-preview-modal" maxWidth="lg">
    <div class="p-6 flex flex-col max-h-[80vh]">
        <div class="flex justify-center items-center mb-4 relative flex-shrink-0">
            <h3 class="text-xl font-extrabold text-[#012967]">
                Leave Preview
            </h3>
            <button
                @click="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'leave-preview-modal' }))"
                class="absolute right-0 text-gray-400 hover:text-gray-600 text-xl"
            >
                &times;
            </button>
        </div>
         <div id="leave-preview-account" class="space-y-3 overflow-y-auto flex-1">
            <!-- content -->
        </div>
    </div>
</x-modal>
<x-modal-late/>
<x-modal-success />
<x-modal-edit/>
<script>
    document.getElementById('search').addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase()
        const rows = document.querySelectorAll('tbody tr')
        rows.forEach(row => {
            if (row.cells.length > 2) {
                const dataAccount = row.cells;
                let colls = ''

                for (let i = 0; i < dataAccount.length; i++) {
                    if (i === 1) {
                        colls += dataAccount[i].textContent.toLowerCase()
                    }
                }

                if (colls.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        })
    })

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.eye-preview-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const date = this.dataset.cration_account;
                    const name = this.dataset.name;
                    const email = this.dataset.email;
                    const status = this.dataset.status;
                    const balance = this.dataset.balance;
                    const photo = this.dataset.photo;
                    const phone = this.dataset.phone;
                    const position = this.dataset.position;
                    const department = this.dataset.department;
                    const role = this.dataset.role;
                    const overwork = this.dataset.overwork;
                    const status_employee = this.dataset.status_employee;
                    const statusClass = getStatusClass(status);
                    let body = `
                    <table class="w-full text-sm text-gray-800 border-collapse">
                        <tbody class="divide-y divide-gray-200">

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Creation Date Account:</th>
                            <td class="text-gray-900 py-2">${date}</td>
                        </tr>

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4">Name:</th>
                            <td class="text-gray-900 py-2 capitalize">${name}</td>
                        </tr>

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4">Email:</th>
                            <td class="text-gray-900 py-2">${email}</td>
                        </tr>

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4">Phone Number:</th>
                            <td class="text-gray-900 py-2">${phone}</td>
                        </tr>

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4">Position:</th>
                            <td class="text-gray-900 py-2">${position}</td>
                        </tr>

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4">Department:</th>
                            <td class="text-gray-900 py-2">${department}</td>
                        </tr>

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-4 pr-4">Role:</th>
                            <td class="text-gray-900 py-2 capitalize">${role}</td>
                        </tr>

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-4 pr-4">Status:</th>
                            <td class="text-gray-900 py-2 capitalize">${status_employee}</td>
                        </tr>

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4 pt-2">Status:</th>
                            <td class="${statusClass} mt-2 mb-2 py-1 px-3 inline-block rounded-full capitalize text-white">${status}</td>
                        </tr>

                        ${
                            role != 'admin'
                            ? `
                            <tr>
                                <th class="text-left font-semibold text-gray-700 py-2 pr-4">Leave Balance:</th>
                                <td class="text-gray-900 py-2">${balance}</td>
                            </tr>
                            <tr>
                                <th class="text-left font-semibold text-gray-700 py-2 pr-4">Total Overwork:</th>
                                <td class="text-gray-900 py-2">${overwork}</td>
                            </tr>
                            `
                            : ''
                        }
                        </tbody>
                    </table>
                    `;

                    document.getElementById('leave-preview-account').innerHTML = body;
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'leave-preview-modal' }));
                });
            });
        });

        function getStatusClass(status) {
            switch(status.toLowerCase()) {
                case 'active': return 'bg-blue-500 text-white rounded-full px-3 py-1 text-sm font-semibold';
                case 'suspended': return 'bg-red-500 text-white rounded-full px-3 py-1 text-sm font-semibold';
            }
        }


document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.edit-account').forEach(b => {
    b.addEventListener('click', function () {
      window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-modal' }));
    });
  });
});

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.late-balance').forEach(b => {
    b.addEventListener('click', function () {
      window.dispatchEvent(new CustomEvent('open-modal', { detail: 'late-modal' }));
    });
  });
});
</script>

<script>
    function openEditModal(button) {
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const email = button.getAttribute('data-email');
        const phone = button.getAttribute('data-phone');
        const leaveBalance = button.getAttribute('data-balance');
        const totalOverwork = button.getAttribute('data-overwork');
        const position = button.getAttribute('data-position');
        const status_employee = button.getAttribute('data-status_employee');
        const department = button.getAttribute('data-department');

        document.getElementById('user_id').value = id;
        document.getElementById('name').value = name;
        document.getElementById('email').value = email;
        document.getElementById('phone').value = phone ?? '';
        document.getElementById('Leave_Balance_Day').value = Math.floor(leaveBalance / 8);
        document.getElementById('Total_Overwork_Day').value = Math.floor(totalOverwork / 8);
        document.getElementById('Leave_Balance_Hour').value = leaveBalance - (Math.floor(leaveBalance / 8) * 8);
        document.getElementById('Total_Overwork_Hour').value = totalOverwork - (Math.floor(totalOverwork / 8) * 8);

        document.getElementById('editForm').action = `/account/update/${id}`;

        const positionSelect = document.getElementById('positionSelect');
        const positionInput = document.getElementById('positionInput');


        if ([...positionSelect.options].some(opt => opt.value === position)) {
            positionSelect.value = position;
            positionSelect.classList.remove("hidden");
            positionInput.classList.add("hidden");
        } else {
            positionSelect.value = "other";
            positionSelect.classList.add("hidden");
            positionInput.classList.remove("hidden");
            positionInput.value = position;
        }

        const departmentSelect = document.getElementById('departmentSelect');
        const departmentInput = document.getElementById('departmentInput');

        if ([...departmentSelect.options].some(opt => opt.value === department)) {
            departmentSelect.value = department;
            departmentSelect.classList.remove("hidden");
            departmentInput.classList.add("hidden");
        } else {
            departmentSelect.value = "other";
            departmentSelect.classList.add("hidden");
            departmentInput.classList.remove("hidden");
            departmentInput.value = department;
        }

        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-modal' }));
    }



    function openLateModal(button) {
        const userId = button.getAttribute('data-id');

        document.getElementById('lateUserIdLeave').value = userId;
        document.getElementById('lateUserIdOverwork').value = userId;

        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'late-modal' }));
    }

    document.addEventListener('DOMContentLoaded', () => {
        const totalLateInput = document.getElementById('totalLate');
        const totalLateLeave = document.getElementById('totalLateLeave');
        const totalLateOverwork = document.getElementById('totalLateOverwork');

        totalLateInput.addEventListener('input', () => {
            totalLateLeave.value = totalLateInput.value;
            totalLateOverwork.value = totalLateInput.value;
        });
    });

</script>
@endsection


