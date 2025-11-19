@extends('layouts.request-data')

@section('content')
@php
    $requestType = request('type', 'all');
    $requestStatus = request('status', 'all');
@endphp

<div class="container-draft bg-[#F0F3F8] p-4 sm:p-6 rounded-lg w-full max-w-[1400px] shadow-lg overflow-x-auto">
    <x-form-filter-all-data title="leave data" route="leave.show" :status="$requestStatus" :type="$requestType" />

    @if(auth()->user()->role === 'user')
        <a href="{{ route('leave.form-view') }}"
            class="mt-3 inline-flex bg-gradient-to-r from-[#1EB8CD] to-[#2652B8] hover:from-cyan-600 hover:to-blue-800 text-white font-semibold py-2 px-3 sm:px-4 rounded-lg transition duration-300 items-center space-x-2 text-sm sm:text-base">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M12 6v12M6 12h12" />
            </svg>
            <span>New Form</span>
        </a>
    @endif

    <div class="mt-4 w-full overflow-y-auto max-h-[600px] ">
        <table class="hidden sm:table w-full text-left justify-center border-b border-gray-300 mr-10 text-sm sm:text-base" >
            <thead class="bg-transparent text-[#1e293b] border-b border-gray-300">
                <tr>
                    <th class="py-3 px-4 sm:px-6 font-semibold whitespace-nowrap">No</th>
                    <th class="py-3 px-4 sm:px-6 font-semibold whitespace-nowrap">Start Date</th>
                    <th class="py-3 px-4 sm:px-6 font-semibold {{$data->isEmpty() ? '' : 'sm:w-[300px]'}} whitespace-nowrap">Reason</th>
                    @if (auth()->user()->role === 'admin')
                        <th class="py-3 px-4 sm:px-6 font-semibold whitespace-nowrap">Name</th>
                    @endif
                    <th class="py-3 px-4 sm:px-6 font-semibold whitespace-nowrap">Duration</th>
                    <th class="py-3 px-4 sm:px-6 font-semibold whitespace-nowrap">Status</th>
                    <th class="py-3 px-4 sm:px-6 font-semibold text-center whitespace-nowrap">Action</th>
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

                    <tr class="{{ $loop->odd ? 'bg-white' : 'bg-[#f1f5f9]' }} border-b border-gray-300">
                        <td class="py-3 px-4 sm:px-6">{{ $loop->iteration }}</td>
                        <td class="py-3 px-4 sm:px-6 whitespace-nowrap">{{ Carbon\Carbon::parse($d->start_leave)->format('d F Y') }}</td>
                        <td class="py-3 px-4 sm:px-6 truncate max-w-[150px] sm:max-w-none" title="{{ $d->reason }}">
                            {{ ucfirst(strtolower(Str::limit($d->reason, 25))) }}
                        </td>
                        @if (auth()->user()->role === 'admin')
                            <td class="py-3 px-4 sm:px-6">{{ Str::words($d->user->name, 2) }}</td>
                        @endif
                        <td class="py-3 px-4 sm:px-6 capitalize">{{ $duration }}</td>
                        <td class="py-3 px-4 sm:px-6">
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
                        <td class="py-3 px-4 sm:px-6 text-center">
                            <div class="flex justify-center items-center space-x-1 sm:space-x-2">
                                <x-action-navigate :d="$d" :requestStatus="$requestStatus" />
                                @if ($d->request_status === 'draft')
                                    <a href="{{ route('leave.edit', $d->id) }}"
                                        class="hover:bg-gray-100 "
                                        title="Edit">
                                        <img src="{{ asset('img/yesbox.svg') }}" alt="edit" class="w-[24px] h-[24px]">
                                    </a>
                                    <form action="{{ route('leave.delete', $d->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this leave draft?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            title="Delete">
                                            <img src="{{ asset('img/delete-button.svg') }}" alt="delete" class="w-[24px] h-[24px] mt-2">
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty">
                        <td colspan="7" class="py-8 px-6 text-gray-500">
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
                            <div class="flex items-center mb-2">
                                <span class="font-semibold text-gray-700 mr-2">Status:</span>
                                <span class="{{ $statusClass }} inline-block mt-2 mb-2">
                                    {{ ucfirst($d->request_status) }}
                                </span>
                            </div>
                           <div class="flex text-s text-gray-500">
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

                       <div class="flex items-center mb-2">
                        <span class="font-semibold text-gray-700 mr-2">Type:</span>
                        <span class="py-1/2 px-3 rounded-full capitalize text-white
                            {{ $d->type === 'overwork' ? 'bg-amber-500' : 'bg-sky-500' }}">
                            {{ $d->type }}
                        </span>
                        </div>

                       @if (auth()->user()->role === 'admin')
                       <div class="mb-1">
                           <span class="flex font-semibold text-gray-700">Name:</span>
                           <div class="flex">{{ $d->user->name }}</div>
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
                           <div class="flex  space-x-1 sm:space-x-2">
                            <x-action-navigate :d="$d" :requestStatus="$requestStatus" />
                            @if ($d->request_status === 'draft')
                                <a href="{{ route('leave.edit', $d->id) }}"
                                    class="hover:bg-gray-100 "
                                    title="Edit">
                                    <img src="{{ asset('img/edit.svg') }}" alt="edit" class="mt-2 h-[35px] w-[35px]">
                                </a>
                                <form action="{{ route('leave.delete', $d->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this leave draft?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        title="Delete">
                                        <img src="{{ asset('img/delete-button.svg') }}" alt="delete" class="h-[35px] w-[35px] mt-2">
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
