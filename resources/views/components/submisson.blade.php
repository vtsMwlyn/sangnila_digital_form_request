@props(['allowance', 'leave_period'])
<div class="flex flex-col gap-2 w-full">
    <x-text-input class="font-medium shadow-none" type="hidden" name="user_id" id="" value="{{auth()->user()->id}}" />
    <x-input-label for="name" class="font-bold text-md">Name:</x-input-label>
    <h1 class="mb-1 border-[3px] border-gray-300 py-2 px-3 bg-white rounded-2xl">{{auth()->user()->name}}</h1>
    <x-input-label for="position" class="font-bold text-md">Position:</x-input-label>
    <h1 class="mb-1 border-[3px] border-gray-300 py-2 px-3 bg-white rounded-2xl">{{auth()->user()->position}}</h1>
    <x-input-label for="department" class="font-bold text-md">Department:</x-input-label>
    <h1 class="mb-1 border-[3px] border-gray-300 py-2 px-3 bg-white rounded-2xl">{{auth()->user()->department}}</h1>
    @if (request()->segment(1) === 'leave')
        @php
            $leaveBalance = $allowance / 8;
            $overworkBalance = Auth::user()->overwork_balance / 8;
            $balanceL = floor($leaveBalance) . ' days ' . ($leaveBalance - floor($leaveBalance)) * 8 . ' hours';
            $balanceO = floor($overworkBalance) . ' days ' . ($overworkBalance - floor($overworkBalance)) * 8 . ' hours';
        @endphp
        <x-input-label for="department" class="font-bold text-md">Leave Balance:</x-input-label>
        <h1 class="mb-1 border-[3px] border-gray-300 py-2 px-3 bg-white rounded-2xl">{{ $balanceL }}</h1>
        <x-input-label for="department" class="font-bold text-md">Overwork Balance:</x-input-label>
        <h1 class="mb-1 border-[3px] border-gray-300 py-2 px-3 bg-white rounded-2xl">{{ $balanceO }}</h1>
    @endif
</div>
