@extends('layouts.app')

@section('content')
    @php
        $requestType = request('type', 'all');
        $requestStatus = request('status', 'review');
        $currentMonth = request('month', 'all');
        $currentSearch = request('search', '');
    @endphp

    <x-slot name="header">
        <div class="bg-gradient-to-r from-cyan-600 to-blue-600 p-6 rounded-b-lg">
            <h2 class="text-white font-bold text-2xl leading-tight">
                {{ __('Hi ') . auth()->user()->name }}!
            </h2>
        </div>
    </x-slot>

    <div class="w-full flex flex-col gap-3">
        {{-- Statistics --}}
        <div class="w-full flex flex-col">
            {{-- Upper row cards --}}
            <div class="w-full grid grid-cols-4 grid-rows-2 gap-8">
                <div class="bg-[#F0F3F8] rounded-2xl shadow-md p-6 relative">
                    <small class="text-[#012967] font-semibold flex items-center justify-between text-[15px]">
                        {{ __('Approved Request') }}
                        <i class="bi bi-check-circle-fill text-green-600 text-lg"></i>
                    </small>
                    <h1 class="text-3xl font-extrabold text-gray-900 py-2">{{ $data['approved']->count() }}</h1>
                    <span class="text-sm text-gray-500">{{ __('Request has been approved') }}</span>
                </div>

                <div class="bg-[#F0F3F8] rounded-2xl shadow-md p-6 relative">
                    <small class="text-[#012967] font-semibold flex items-center justify-between text-[15px]">
                        {{ __('Rejected Request') }}
                        <i class="bi bi-x-circle-fill text-red-600 text-lg"></i>
                    </small>
                    <h1 class="text-3xl font-extrabold text-gray-900 py-2">{{ $data['rejected']->count() }}</h1>
                    <span class="text-sm text-gray-500">{{ __('Request rejected') }}</span>
                </div>

                <div class="bg-[#F0F3F8] rounded-2xl shadow-md p-6 relative">
                    <small class="text-[#012967] font-semibold flex items-center justify-between text-[15px]">
                        {{ __('Pending Request') }}
                        <i class="bi bi-hourglass-split text-gray-500 text-lg"></i>
                    </small>
                    <h1 class="text-3xl font-extrabold text-gray-900 py-2">{{ $data['pending']->count() }}</h1>
                    <span class="text-sm text-gray-500">{{ __('Total submission which still under review') }}</span>
                </div>

                {{-- Taking Leave --}}
                <div class="bg-[#F0F3F8] row-span-2 rounded-2xl shadow-md p-6">
                    <small class="text-[#012967] font-semibold flex items-center justify-between text-[15px] mb-3">
                        {{ __('Employees Taking Leave Today') }}
                        <i class="bi bi-file-text text-lg text-gray-500"></i>
                    </small>
                    <x-separator-line/>

                    <a href="{{ route('LogActivity.show') }}" class="bg-[#F0F3F8] max-h-80 mt-4 relative overflow-y-auto block">
                        @if(($data['taking_leaves'] ?? collect())->isEmpty())
                            <p class="text-gray-500 text-sm italic">No employees taking leave today.</p>
                        @else
                            <ul class="space-y-2">
                                @foreach($data['taking_leaves'] as $log)
                                    <li class="text-sm text-gray-800">
                                        <span class="block font-semibold">{{ $log->user->name }} ({{ $log->leave_period >= 8 ? 'Full Day' : rtrim(rtrim($log->leave_period, '0'), '.') . ' hours' }})</span>
                                        <span class="block italic">{{ $log->reason }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </a>
                </div>

                {{-- Bottom row cards --}}
                <div class="bg-[#F0F3F8] rounded-2xl shadow-md p-6 relative">
                    <small class="text-[#012967] font-semibold flex items-center justify-between text-[15px]">
                        {{ __('Total Overwork') }}
                        <i class="bi bi-clock-history text-gray-500 text-lg"></i>
                    </small>
                    <h1 class="text-3xl font-extrabold text-gray-900 py-2">{{$data['approved']->where('type', 'overwork')->count()}} {{__('Data')}}</h1>
                    <span class="text-sm text-gray-500">{{ __('Total overwork approved') }}</span>
                </div>

                <div class="bg-[#F0F3F8] rounded-2xl shadow-md p-6 relative">
                    <small class="text-[#012967] font-semibold flex items-center justify-between text-[15px]">
                        {{ __('Total Leave ') }}
                        <i class="bi bi-calendar-check text-gray-500 text-lg"></i>
                    </small>
                    <h1 class="text-3xl font-extrabold text-gray-900 py-2">{{$data['approved']->where('type', 'leave')->count()}} {{__('Data')}}</h1>
                    <span class="text-sm text-gray-500">{{ __('Total leave approved') }}</span>
                </div>

                <a href="{{ route('register') }}" class="flex flex-col h-full items-start bg-gradient-to-r from-[#1EB8CD] to-[#2652B8] rounded-xl p-5 shadow-lg text-white hover:from-cyan-600 hover:to-blue-800 transition">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-calendar-plus text-2xl"></i>
                        <span class="font-semibold text-lg">Add Employee</span>
                    </div>
                    <small class="mt-4 text-cyan-200">Create new employee account</small>
                </a>

                {{-- Recent Request --}}
                <div id="data" class="bg-[#F0F3F8] rounded-xl shadow-6xl p-6 overflow-x-auto col-span-3 h-[500px]">
                    <x-form-filter-all-data title="recent request" route="dashboard" :status="$requestStatus" :type="$requestType" />
                    
                    {{-- Table --}}
                    <div class="overflow-auto max-h-80">
                        <table class="min-w-full text-left border-collapse text-sm md:text-base hidden sm:table mb-3">
                            <thead class="bg-transparent text-[#1e293b] border-b-2 border-gray-400">
                                <tr>
                                    <th class="w-12">No</th>
                                    <th class="w-15">Date</th>
                                    <th>Type</th>
                                    @if (auth()->user()->role === 'admin')
                                        <th>Name</th>
                                    @endif
                                    <th class="w-30">Reason</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data['requestData'] as $d)
                                    <tr class="{{ $loop->odd ? 'bg-white' : '' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ Carbon\Carbon::parse($d->start_leave ?? $d->overwork_date)->format('d F Y') }}</td>
                                        <td>
                                            <span class="py-1 px-3 rounded-full capitalize text-white  {{ $d->type === 'overwork' ? 'bg-amber-500' : 'bg-sky-500' }}">{{ $d->type }}</span>
                                        </td>
                                        @if (auth()->user()->role === "admin")
                                            <td>{{ $d->user->name }}</td>
                                        @endif
                                        <td class="max-w-xs" title="{{ $d->reason ?? $d->task_description }}">
                                            {{ ucfirst(strtolower(Str::limit($d->reason ?? $d->task_description, 40))) }}
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($d->request_status) {
                                                    'approved' => 'bg-green-500 text-white rounded-full px-3 py-1 font-semibold',
                                                    'draft' => 'bg-gray-500 text-gray-100 rounded-full px-3 py-1 font-semibold',
                                                    'rejected' => 'bg-red-500 text-white rounded-full px-3 py-1 font-semibold',
                                                    default => 'bg-yellow-500 text-white rounded-full px-3 py-1 font-semibold',
                                                };
                                            @endphp
                                            <span class="{{ $statusClass }} capitalize">{{ ucfirst($d->request_status) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="flex justify-center gap-1 w-full">
                                            <x-action-navigate :d="$d" :requestStatus="$requestStatus" />
                                        </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty">
                                        <td colspan="7" class="py-8 px-6 text-center text-gray-500">
                                            @include('view.admin.components.status-data-empty')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- MOBILE CARD VIEW --}}
                        <div class="sm:hidden block mt-4 mb-3">
                            @forelse ($data['requestData'] as $d)
                                @php
                                    $statusClass = match($d->request_status) {
                                        'approved' => 'bg-green-500 text-white rounded-full px-3 py-1/2 font-semibold',
                                        'review' => 'bg-gray-500 text-gray-100 rounded-full px-3 py-1/2 font-semibold',
                                        'rejected' => 'bg-red-500 text-white rounded-full px-3 py-1/2 font-semibold',
                                        default => 'bg-yellow-500 text-white rounded-full px-3 py-1/2 font-semibold',
                                    };
                                @endphp

                                <div x-data="{ open: false }" class="bg-white rounded-xl shadow-md p-4 mb-3 border border-gray-200">

                                    {{-- HEADER --}}
                                    <button class="w-full flex justify-between items-center" x-on:click="open = !open">
                                        <div>
                                            @if (auth()->user()->role === 'admin')
                                            <div class="flex items-center mb-2">
                                                <span class="items-start font-semibold text-[#012967] mr-1">Name: </span>
                                                <div class="items-start font-semibold text-[#012967]"> {{ $d->user->name }}</div>
                                            </div>
                                            @endif
                                            <div class="flex items-center mb-2">
                                                <span class="font-semibold text-gray-700 mr-2">Type:</span>
                                                <span class="py-1/2 px-2 rounded-full capitalize text-white
                                                    {{ $d->type === 'overwork' ? 'bg-amber-500' : 'bg-sky-500' }}">
                                                    {{ $d->type }}
                                                </span>
                                            </div>
                                            <div class="flex items-center text-xs text-gray-500">
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
                                    <div x-show="open" x-collapse class="mt-3 text-sm">

                                        <div class="mb-1">
                                            <span class="font-semibold text-gray-700 mb-2">Date:</span>
                                            <div>{{ Carbon\Carbon::parse($d->start_leave ?? $d->overwork_date)->format('d F Y') }}</div>
                                        </div>

                                        <div class="mb-1">
                                            <span class="font-semibold text-gray-700">Reason:</span>
                                            <div>{{ $d->reason ?? $d->task_description }}</div>
                                        </div>

                                        <div class="mb-1">
                                            <span class="font-semibold text-gray-700 py-2">Status:</span>
                                            <br>
                                            <div class="{{ $statusClass }} inline-block ">
                                                {{ ucfirst($d->request_status) }}
                                            </div>
                                        </div>

                                        <div class="mb-1">
                                            <span class="font-semibold text-gray-700">Action:</span>
                                            <div>
                                                <x-action-navigate :d="$d" :requestStatus="$requestStatus" />
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @empty
                                <p class="text-center py-4 text-gray-500 italic">No recent request.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Activity Logs --}}
                <div class="bg-[#F0F3F8] row-span-2 rounded-2xl shadow-md p-6 h-[500px]">
                    <small class="text-[#012967] font-semibold flex items-center justify-between text-[15px] mb-3">
                        {{ __('Recent Activities') }}
                        <i class="bi bi-file-text text-lg text-gray-500"></i>
                    </small>
                    <x-separator-line/>

                    <a href="{{ route('LogActivity.show') }}" class="bg-[#F0F3F8] max-h-[400px] mt-4 relative overflow-y-auto block">
                        @if(($data['logs'] ?? collect())->isEmpty())
                            <p class="text-gray-500 text-sm italic">No recent activities.</p>
                        @else
                            <ul class="space-y-2">
                                @foreach($data['logs'] as $log)
                                    <li class="text-sm text-gray-800">
                                        <span class="block font-semibold">{{ $log->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}</span>
                                        <span class="block italic">{{ $log->message }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        {{-- Calendar --}}
        <div class="bg-[#F0F3F8] rounded-2xl shadow-md p-6">
            <div id="calendar" class="w-full h-full"></div>
        </div>
    </div>

    <x-modal-success />
    <x-modal-reject/>
    <x-modal-choose/>
    <x-preview-data title="request" />
    <x-manage-data />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');

            const events = @json($data['calendar_events']);
            console.log(events);

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 'auto',
                events: events,
                firstDay: 1,

                eventContent: function(arg) {
                    return {
                        html: `
                            <div class="text-xs leading-tight">
                                ${arg.event.title}
                            </div>
                            <div class="text-[10px] opacity-80">
                                ${arg.event.extendedProps.subtitle}
                            </div>
                        `
                    };
                },
            });

            calendar.render();
        });
    </script>
@endsection
