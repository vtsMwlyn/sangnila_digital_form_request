@extends('layouts.app')

@section('content')
    @php
        $requestType = request('type', 'all');
        $requestStatus = request('status', 'all');
    @endphp

    <div class="container-draft bg-[#FEFEFEB2] p-4 sm:p-6 rounded-2xl w-full shadow-lg overflow-x-auto">
        <x-form-filter-all-data title="My Leave List" route="leave.show" :status="$requestStatus" :type="$requestType" />

        @if(auth()->user()->role === 'user')
            <x-anchor-button href="{{ route('leave.form-view') }}" class="mt-6 xl:mt-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M12 6v12M6 12h12" />
                </svg>
                <span>New Leave Request</span>
            </x-anchor-button>
        @endif

        <div class="mt-4 w-full overflow-y-auto max-h-[600px] ">
            <table class="hidden sm:table w-full text-left justify-center text-sm sm:text-base" >
                <thead class="bg-transparent text-[#1e293b] border-b-2 border-slate-400">
                    <tr>
                        <th>No</th>
                        <th>Start Date</th>
                        <th>Duration</th>
                        <th>Reason</th>
                        @if (auth()->user()->role === 'admin')
                            <th>Name</th>
                        @endif
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($data as $d)
                        @php
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

                            $periodHours = ($periodDays - floor($periodDays)) * 8;

                            if (floor($periodDays) == '0') {
                                $duration = $periodHours . ' hours';
                            } elseif ($periodHours == '0') {
                                $finish = $finish->copy()->subDay();
                                $duration = floor($periodDays) . ' days';
                            } else {
                                $duration = floor($periodDays) . ' days ' . $periodHours . ' hours';
                            }
                        @endphp

                        <tr class="{{ $loop->odd ? 'bg-white' : '' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ Carbon\Carbon::parse($d->start_leave)->format('d F Y') }}</td>
                            <td class="capitalize">{{ $duration }}</td>
                            <td title="{{ $d->reason }}">
                                {{ ucfirst(strtolower(Str::limit($d->reason, 50))) }}
                            </td>
                            @if (auth()->user()->role === 'admin')
                                <td>{{ Str::words($d->user->name, 2) }}</td>
                            @endif
                            <td>
                                @php
                                    $statusClass = match($d->request_status) {
                                        'approved' => 'bg-green-500 text-white rounded-full px-3 py-1 text-xs sm:text-sm',
                                        'review' => 'bg-yellow-500 text-white rounded-full px-3 py-1 text-xs sm:text-sm',
                                        'rejected' => 'bg-red-500 text-white rounded-full px-3 py-1 text-xs sm:text-sm',
                                        default => 'bg-gray-400 text-white rounded-full px-3 py-1 text-xs sm:text-sm',
                                    };
                                @endphp
                                <span class="{{ $statusClass }} capitalize font-semibold">{{ $d->request_status }}</span>
                            </td>
                            <td>
                                <div class="flex justify-center items-stretch space-x-1">
                                    <x-action-navigate :d="$d" :requestStatus="$requestStatus" />
                                    @if ($d->request_status === 'draft')
                                        <a href="{{ route('leave.edit', $d->id) }}" class="transition hover:scale-105" title="Edit">
                                            <img src="{{ asset('img/edit.svg') }}" alt="edit">
                                        </a>
                                        <form action="{{ route('leave.delete', $d->id) }}" method="POST" class="flex items-center" onsubmit="return confirm('Are you sure you want to delete this leave draft?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete" class="transition hover:scale-105">
                                                <img src="{{ asset('img/delete-button.svg') }}" alt="delete">
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty">
                            <td colspan="7" class="py-8 px-6 text-gray-500 bg-white">
                                <div class="flex flex-col items-center text-center">
                                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-300 mb-3 sm:mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                        <path d="M16 2v4M8 2v4M3 10h18" />
                                    </svg>
                                    <p class="capitalize text-sm sm:text-base">No leave {{request()->segment(2)}} data found</p>
                                    @if (auth()->user()->role === 'user')
                                        <a href="{{ route('leave.form-view') }}" class="text-[#1EB8CD] hover:underline mt-2 text-sm sm:text-base">
                                            Create your first leave request
                                        </a>
                                    @endif
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
                        'approved' => 'bg-green-500 text-white rounded-full px-3 py-1/2 font-semibold',
                        'draft' => 'bg-gray-500 text-gray-100 rounded-full px-3 py-1/2 font-semibold',
                        'rejected' => 'bg-red-500 text-white rounded-full px-3 py-1/2 font-semibold',
                        default => 'bg-yellow-500 text-white rounded-full px-3 py-1/2 font-semibold',
                    };
                @endphp

                <div x-data="{ open: false }" class="bg-white rounded-xl shadow-md p-4 mb-3 border border-gray-200">

                    {{-- HEADER --}}
                    <button class="w-full flex justify-between items-center" x-on:click="open = !open">
                        <div>
                                @if (auth()->user()->role === 'admin')
                                <div class="flex font-semibold text-[#012967]">{{ $d->user->name }}</div>
                                @endif
                                <div class="flex items-center mb-1">
                                    <span class="font-semibold text-gray-700 mr-2">Status:</span>
                                    <span class="{{ $statusClass }} inline-block text-sm mt-2 mb-2">
                                        {{ ucfirst($d->request_status) }}
                                    </span>
                                </div>
                            <div class="flex text-sm text-gray-500">
                                {{ Carbon\Carbon::parse($d->start_leave ?? $d->overwork_date)->format('d F Y') }}
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
                    <div x-show="open" x-collapse class="mt-6 text-sm">
                        @if (auth()->user()->role === 'admin')
                            <div class="mb-4">
                                <span class="flex font-semibold text-gray-700">Name:</span>
                                <div class="flex">{{ $d->user->name }}</div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <span class="font-semibold text-gray-700">Duration:</span>
                            <div>{{ $duration }}</div>
                        </div>

                        <div class="mb-4">
                            <span class="font-semibold text-gray-700">Reason:</span>
                            <div>{{ $d->reason ?? $d->task_description }}</div>
                        </div>

                        <div>
                            <span class="font-semibold text-gray-700">Action:</span>
                            <div class="flex items-center space-x-3 mt-3">
                                <x-action-navigate :d="$d" :requestStatus="$requestStatus" />
                                @if ($d->request_status === 'draft')
                                    <a href="{{ route('leave.edit', $d->id) }}" title="Edit">
                                        <img src="{{ asset('img/edit.svg') }}" alt="edit">
                                    </a>
                                    <form action="{{ route('leave.delete', $d->id) }}" method="POST" class="flex items-center" onsubmit="return confirm('Are you sure you want to delete this leave draft?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            title="Delete">
                                            <img src="{{ asset('img/delete-button.svg') }}" alt="delete">
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                    <p class="text-center py-4 text-gray-500 italic">No recent request.</p>
                @endforelse
            </div>
            @if (auth()->user()->role === 'user')
                <x-contact />
            @endif
        </div>
    </div>

    <x-modal-reject/>
    <x-modal-choose/>
    <x-preview-data title="leave" />
    <x-modal-success />
    <x-manage-data />
@endsection
