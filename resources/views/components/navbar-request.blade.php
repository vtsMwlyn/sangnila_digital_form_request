<div
    class="header-bar bg-[#1EB8CD] px-8 py-4 ml-72 transition-all duration-300 ease-in-out flex justify-between items-center"
    :class="sidebarOpen ? 'ml-0' : 'ml-[-0px]'"
>
    <h1 class="text-white text-2xl font-bold capitalize">{{ request()->segment(1) }}</h1>

    <div class="flex space-x-2 flex-wrap justify-end w-full md:w-auto">
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
            <div class="status-grid">
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


<style>
 @media (max-width: 1024px) {
    .header-bar {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        margin-left: 0 !important;
    }

    .header-bar h1 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .header-bar form {
        width: 100%;
    }

    .header-bar .status-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.5rem;
        width: 100%;
    }

    .header-bar .status-btn {
        width: 100%;
        text-align: center;
        padding: 0.6rem 0;
        font-size: 0.9rem;
    }
}

@media (max-width: 640px) {
    .header-bar h1 {
        font-size: 1.25rem;
        text-align: center;
        width: 100%;
    }

    .header-bar .status-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.4rem;
    }

    .header-bar .status-btn {
        font-size: 0.85rem;
        padding: 0.5rem 0;
    }
}
</style>
