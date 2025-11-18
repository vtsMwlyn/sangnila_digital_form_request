@extends('layouts.request-data')

@section('content')

@php
    $requestType = request('type', 'all');
    $requestStatus = request('status', 'all');
@endphp

<div class="container-draft bg-[#F0F3F8] p-6 rounded-lg w-full max-w-[1400px] shadow-lg overflow-x-auto">
    <x-form-filter-all-data title="overwork data" route="overwork.show" :status="$requestStatus" :type="$requestType" />
            <!-- New Data Button -->
    @if (auth()->user()->role === 'user')
        <a href="{{ route('overwork.form-view') }}"
            class="bg-gradient-to-r from-[#1EB8CD] to-[#2652B8] hover:from-cyan-600 hover:to-blue-800 text-white font-semibold py-2 px-2 rounded-lg transition duration-300 flex items-center space-x-2 w-[130px] text-sm sm:text-base">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M12 6v12M6 12h12" />
            </svg>
            <span>New Form</span>
        </a>
    @endif

    <!-- Overwork Table -->
    <div class="mt-4 w-full overflow-x-auto overflow-y-auto max-h-[600px] ">
    <table class="hidden sm:table w-full text-left justify-center border-b border-gray-300 mr-10 text-sm sm:text-base" >
        <thead class="bg-transparent text-[#1e293b] border-b border-gray-300">
            <tr>
                <th class="py-4 sm:px-6 px-6 font-semibold whitespace-nowrap">No</th>
                <th class="py-4 sm:px-6 px-6 font-semibold whitespace-nowrap">Overwork Date</th>
                <th class="py-4 sm:px-6 px-6 font-semibold whitespace-nowrap w-[250px]">Task Description</th>
                @if (auth()->user()->role === 'admin')
                    <th class="py-4 sm:px-6 px-6 font-semibold whitespace-nowrap">
                            Name
                    </th>
                @endif
                    <th class="py-4 sm:px-6 px-6 font-semibold whitespace-nowrap">Duration</th>
                <th class="py-4 sm:px-6 px-6 font-semibold whitespace-nowrap">Evidence</th>
                <th class="py-4 sm:px-6 px-6 font-semibold whitespace-nowrap">Status</th>
                <th class="py-4 sm:px-6 px-6 font-semibold whitespace-nowrap text-center">Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse($data as $d)
            <tr class="{{ $loop->odd ? 'bg-white' : 'bg-[#f1f5f9]' }} border-b border-gray-300 items-center justify-center">
                <td class="py-4 sm:px-6 px-6">
                    {{ $loop->iteration }}
                </td>

                <td class="py-4 sm:px-6 px-6">
                    {{ Carbon\Carbon::parse($d->overwork_date)->format('d F Y') }}
                </td>

                <td class="py-4 sm:px-6 px-6" title="{{ $d->task_description }}">
                    {{ ucfirst(strtolower(Str::limit($d->task_description, 25))) }}
                </td>

                @if (auth()->user()->role === 'admin')
                    <td class="py-4 sm:px-6 px-6">
                        {{ Str::words($d->user->name, 2) ?? 'N/A' }}
                    </td>
                @endif

                <td class="py-4 sm:px-6 px-6">
                    @php
                        $duration = \Carbon\Carbon::parse($d->start_overwork)->diff(\Carbon\Carbon::parse($d->finished_overwork));
                        @endphp
                    @if ($duration->format('%i') == '0')
                        {{ $duration->format('%h hours') }}
                    @else
                        {{ $duration->format('%h hours %i minutes') }}
                    @endif
                </td>

                <td class="py-4 sm:px-6 px-6">
                    @php
                        $totalEvidence = $d->evidence->count();
                        $firstImage = $d->evidence->first(fn($e) => in_array(strtolower(pathinfo($e->path, PATHINFO_EXTENSION)), ['jpg', 'png', 'jpeg', 'webp']));
                        $firstVideo = $d->evidence->first(fn($e) => in_array(strtolower(pathinfo($e->path, PATHINFO_EXTENSION)), ['mp4', 'mov', 'avi']));
                    @endphp
                    @if($totalEvidence >= 1)
                        <span class="text-sm bg-blue-100 text-blue-600 px-auto w-[90px] py-2 rounded-full justify-center flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $totalEvidence }} Media
                        </span>
                    @else
                        <span class="text-gray-500 text-sm">No evidence</span>
                    @endif
                </td>
                <td class="py-4 sm:px-6 px-6">
                    @php
                        $statusClass = match($d->request_status) {
                            'approved' => 'bg-green-500 text-white rounded-full px-3 py-1 text-sm',
                            'review' => 'bg-yellow-500 text-white rounded-full px-3 py-1 text-sm',
                            'rejected' => 'bg-red-500 text-white rounded-full px-3 py-1 text-sm',
                            default => 'bg-gray-400 text-white rounded-full px-3 py-1 text-sm',
                        };
                    @endphp
                    <span class="{{ $statusClass }} capitalize font-semibold">{{ $d->request_status }}</span>
                </td>
                <td class="py-4 sm:px-6 px-6 text-center">
                    <div class="flex justify-center items-center space-x-2">
                        <!-- Show Details Button -->
                        <x-action-navigate :d="$d" :requestStatus="$requestStatus" />
                        @if ($d->request_status === 'draft')
                            <a
                            href="{{ route('overwork.edit', $d->id) }}"

                                title="Edit"
                            >
                            <img src="{{ asset('img/edit.svg') }}" alt="edit" class=" h-6 w-6">
                        </a>
                            <form action="{{ route('overwork.delete', $d->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure want to delete this overwork draft?')">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"

                                    title="Delete"
                                    >
                                    <img src="{{ asset('img/delete-button.svg') }}" alt="edit" class=" h-6 w-6 mt-2">
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty">
                <td colspan="8" class="py-8 px-6 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 3" />
                        </svg>
                        <p class="capitalize text-sm sm:text-base">No overwork {{request()->segment(2)}} data found</p>
                        @if (auth()->user()->role === 'user')
                            <a href="{{ route('overwork.form-view') }}" class="text-[#1EB8CD] hover:underline mt-2 text-sm sm:text-base">
                                Create your first overwork request
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
                        <div class="flex font-semibold text-[#012967]">{{ $d->user->name }}</div>
                        @endif
                        <div class="flex text-xs text-gray-500">
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
                        <span class="font-semibold text-gray-700">Date:</span>
                        <div>{{ Carbon\Carbon::parse($d->start_leave ?? $d->overwork_date)->format('d F Y') }}</div>
                    </div>

                    <div class=" items-center mb-2">
                        <span class="font-semibold text-gray-700 mr-2">Type:</span>
                        <span class="py-1/2 px-3 rounded-full capitalize text-white
                            {{ $d->type === 'overwork' ? 'bg-amber-500' : 'bg-sky-500' }}">
                            {{ $d->type }}
                        </span>
                    </div>


                    @if (auth()->user()->role === 'admin')
                    <div class="mb-1">
                        <span class="font-semibold text-gray-700">Name:</span>
                        <div>{{ $d->user->name }}</div>
                    </div>
                    @endif

                    <div class="mb-1">
                        <span class="font-semibold text-gray-700">Reason:</span>
                        <div>{{ $d->reason ?? $d->task_description }}</div>
                    </div>

                    <div class="flex items-center mb-2">
                        <span class="font-semibold text-gray-700 mr-2">Status:</span>
                        <span class="{{ $statusClass }} inline-block mt-2 mb-2">
                            {{ ucfirst($d->request_status) }}
                        </span>
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
    @if (auth()->user()->role === 'user')
    <x-contact />
     @endif
</div>

<x-preview-data title="overwork" />
<x-modal-reject/>
<x-modal-choose/>

<x-modal-success />

<x-manage-data />

@endsection
