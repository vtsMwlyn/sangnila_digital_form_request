{{-- @extends('layouts.request-data')

@section('content')
@php
    $requestType = request('type', 'all');
    $requestStatus = request('status', 'all');
@endphp

<div class="container-draft bg-[#F0F3F8] p-4 sm:p-6 rounded-lg w-full max-w-[1400px] shadow-lg overflow-x-auto">
    <h2 class="text-xl xl:text-2xl font-bold text-[#012967] mb-[10px]">Log Activity</h2>

    <div class="mt-4 w-full overflow-y-auto max-h-[600px] ">
        <table class="w-full text-left justify-center border-b border-gray-300 mr-10 text-sm sm:text-base" >
                <thead class="bg-transparent text-[#1e293b] border-b border-gray-300 text-center">
                    <tr>
                        <th class="py-3 px-4 sm:px-6 font-semibold whitespace-nowrap">No</th>
                        <th class="py-3 px-4 sm:px-6 font-semibold whitespace-nowrap">Log Date</th>
                        @if (auth()->user()->role === 'admin')
                        <th class="py-3 px-4 sm:px-6 font-semibold whitespace-nowrap">Name</th>
                        @endif
                        <th class="py-3 px-4 sm:px-6 font-semibold whitespace-nowrap">Message</th>
                    </tr>
                </thead>

            <tbody>
                @forelse ($data as $log)
                    <tr class="{{ $loop->odd ? 'bg-white' : 'bg-[#f1f5f9]' }} border-b border-gray-300">
                        <td class="py-3 px-4 sm:px-6">{{ $loop->iteration }}</td>
                        <td class="py-3 px-4 sm:px-6 whitespace-nowrap">
                            {{ $log->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}
                        </td>
                        @if (auth()->user()->role === 'admin')
                        <td class="py-2 px-4">{{ $log->user->name ?? 'Unknown User' }}</td>
                        @endif
                        <td class="py-3 px-4 sm:px-6 truncate max-w-[150px] sm:max-w-none" title="{{ $log->message }}">
                            {{ ucfirst(strtolower(Str::limit($log->message, 80))) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-gray-500 italic">
                            No activity logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection --}}


@extends('layouts.request-data')

@section('content')
@php
    $requestType = request('type', 'all');
    $requestStatus = request('status', 'all');
@endphp

<div class="container-draft bg-[#F0F3F8] p-4 sm:p-6 rounded-lg w-full max-w-[1400px] shadow-lg overflow-x-auto">
    <h2 class="text-xl xl:text-2xl font-bold text-[#012967] mb-[10px]">Log Activity</h2>

    <div class="mt-4 w-full overflow-y-auto max-h-[600px]">

        <table class="w-full text-left justify-center border-b border-gray-300 mr-10 text-sm sm:text-base hidden sm:table">
            <thead class="bg-transparent text-[#1e293b] border-b border-gray-300 text-center">
                <tr>
                    <th class="py-3 px-4 sm:px-6 font-semibold whitespace-nowrap">No</th>
                    <th class="py-3 px-4 sm:px-6 font-semibold whitespace-nowrap">Log Date</th>
                    @if (auth()->user()->role === 'admin')
                    <th class="py-3 px-4 sm:px-6 font-semibold whitespace-nowrap">Name</th>
                    @endif
                    <th class="py-3 px-4 sm:px-6 font-semibold whitespace-nowrap">Message</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($data as $log)
                    <tr class="{{ $loop->odd ? 'bg-white' : 'bg-[#f1f5f9]' }} border-b border-gray-300">
                        <td class="py-3 px-4 sm:px-6">{{ $loop->iteration }}</td>
                        <td class="py-3 px-4 sm:px-6 whitespace-nowrap">
                            {{ $log->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}
                        </td>
                        @if (auth()->user()->role === 'admin')
                        <td class="py-2 px-4">{{ $log->user->name ?? 'Unknown User' }}</td>
                        @endif
                        <td class="py-3 px-4 sm:px-6 truncate max-w-[150px] sm:max-w-none" title="{{ $log->message }}">
                            {{ ucfirst(strtolower(Str::limit($log->message, 80))) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-gray-500 italic">
                            No activity logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="sm:hidden block">
            @forelse ($data as $log)
                <div x-data="{ open: false }" class="bg-white rounded-xl shadow-md p-4 mb-3 border border-gray-200">

                    <button
                        class="w-full flex justify-between items-center"
                        x-on:click="open = !open"
                    >

                        <div>
                            @if (auth()->user()->role === 'admin')
                            <div class="flex font-semibold text-[#012967]">Name: {{ $log->user->name ?? 'Unknown User' }}</div>
                            @endif
                            <div class="flex text-xs text-gray-500">
                                {{ $log->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}
                            </div>
                        </div>

                        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>

                        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-collapse class="mt-3 text-sm">

                        @if (auth()->user()->role === 'admin')
                        <div class="mb-2">
                            <span class="flex font-semibold text-gray-700">Name:</span>
                            <div>{{ $log->user->name ?? 'Unknown User' }}</div>
                        </div>
                        @endif

                        <div class="mb-1">
                            <span class="flex font-semibold text-gray-700">Message:</span>
                            <div class="text-gray-700 text-justify">
                                {{ ucfirst(strtolower($log->message)) }}
                            </div>
                        </div>
                    </div>

                </div>
            @empty
                <p class="text-center py-4 text-gray-500 italic">No activity logs found.</p>
            @endforelse
        </div>


    </div>
</div>
@endsection
