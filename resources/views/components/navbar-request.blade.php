<div
    class="header-bar bg-[#1EB8CD] px-8 py-4 ml-72 transition-all duration-300 ease-in-out flex justify-between items-center"
    :class="sidebarOpen ? 'ml-0' : 'ml-[-0px]'"
>
    <h1 class="text-white text-2xl font-bold capitalize">{{ request()->segment(1) }}</h1>

    <div class="hidden sm:flex space-x-2 flex-wrap justify-end w-full md:w-auto">

        @php
            $type = request()->segment(1);
            $navStatus = [
                'all' => 'all',
                'review' => 'review',
                'approved' => 'approved',
                'rejected' => 'rejected',
            ];
            if (auth()->user()->role === 'user') {
                $navStatus += ['draft' => 'draft'];
            }
        @endphp

        <form action="{{ route($type . '.show') }}" method="get">
            <input type="hidden" class="buttonSubmit" name="status" value="{{ request('status', 'all') }}">
            <input type="hidden" id="monthHidden" name="month" value="{{ request('month') ?? 'all' }}">
            <input type="hidden" id="searchHidden" name="search" value="{{ request('search') ?? '' }}">

            @if (!request()->routeIs('LogActivity.show'))
            <div class="  status-grid space-x-2">
                @foreach ($navStatus as $status)
                    <button
                        type="button"
                        value="{{ $status }}"
                        class="status-btn px-4 py-2 font-medium rounded-full transition duration-300 backdrop-blur-sm
                        {{ request('status', 'all') === $status
                            ? 'bg-white text-[#1EB8CD] shadow-sm shadow-gray-300'
                            : 'text-white hover:bg-white/10 hover:shadow-md' }}"
                    >
                        {{ ucfirst($status) }}
                    </button>
                @endforeach
            </div>
        @endif

        </form>

    </div>
</div>

{{-- mobile --}}
<div class="sm:hidden block mt-4 px-4">
    @php
        $type = request()->segment(1);
        $navStatus = [
            'all' => 'all',
            'review' => 'review',
            'approved' => 'approved',
            'rejected' => 'rejected',
        ];
        if (auth()->user()->role === 'user') {
            $navStatus += ['draft' => 'draft'];
        }
    @endphp

@if (!request()->routeIs('LogActivity.show'))

    <div x-data="{ open: false }" class="bg-white rounded-xl shadow-md p-4 border border-gray-200">

        <button class="w-full flex justify-between items-center" x-on:click="open = !open">
            <h1 class="text-[#1EB8CD] text-xl font-bold capitalize">
                {{ request()->segment(1) }} Filters
            </h1>

            <svg x-show="!open" class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7"/>
            </svg>

            <svg x-show="open" class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 15l7-7 7 7"/>
            </svg>
        </button>

        <div x-show="open" x-collapse class="mt-4">

            <form action="{{ route($type . '.show') }}" method="get" class="w-full">
                <input type="hidden" class="buttonSubmit" name="status" value="{{ request('status', 'all') }}">
                <input type="hidden" id="monthHidden" name="month" value="{{ request('month') ?? 'all' }}">
                <input type="hidden" id="searchHidden" name="search" value="{{ request('search') ?? '' }}">

                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach ($navStatus as $status)
                            <button
                                type="button"
                                value="{{ $status }}"
                                class="status-btn w-full px-4 py-2 text-sm font-medium rounded-full transition duration-300
                                {{ request('status', 'all') === $status
                                    ? 'bg-[#1EB8CD] text-white shadow-md'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            >
                                {{ ucfirst($status) }}
                            </button>
                        @endforeach
                    </div>
            </form>

        </div>
    </div>
    @endif

</div>
