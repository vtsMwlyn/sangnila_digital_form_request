<div class="flex
    {{$route === 'dashboard' || $route === 'draft'
        ? 'flex-col'
        : 'flex-col md:flex-row md:justify-between md:py-3'
    }}
    mb-6 w-full gap-5"
>
    <h2 class="text-xl xl:text-2xl font-bold text-[#012967] capitalize">{{$title}}</h2>
    <form action="{{route($route)}}" method="get">
        <div class="w-full flex flex-col md:flex-row justify-between items-start md:items-center {{$route === 'dashboard' ? 'py-2' : ''}} gap-3">
            @if ($route === 'dashboard' || $route === 'draft')
            <x-filter-data-toggle :status="$status" :type="$type" />
            @endif

            <div class="flex flex-row items-start md:items-center space-x-1 md:space-y-0 md:space-x-1 w-full md:w-auto">

                @if ($route !== 'dashboard' && $route !== 'draft')
                    <input type="hidden" name="status" value="{{$status}}">
                @endif

                <select name="month" id="month" onchange="this.form.submit()" class="border border-gray-300 rounded-full xl:w-[180px] w-[125px] py-1 px-3 focus:outline-none focus:ring-2 focus:ring-cyan-600">
                    <option value="all" {{ request('month') === 'all' ? 'selected' : '' }}>All Months</option>
                    @php
                        $months = [];
                        for ($i = -1; $i < 12; $i++) {
                            $date = now()->subMonths($i);
                            $months[] = ['value' => $date->format('m-Y'), 'label' => $date->format('F Y')];
                        }
                    @endphp
                    @foreach($months as $monthOption)
                        <option value="{{ $monthOption['value'] }}" {{ request('month') === $monthOption['value'] ? 'selected' : '' }}>
                            {{ $monthOption['label'] }}
                        </option>
                    @endforeach
                </select>

                <input
                    type="search"
                    id="search"
                    name="search"
                    placeholder="Search by Reason"
                    value="{{ request('search') }}"
                    class="border border-gray-300 rounded-full  py-1 focus:outline-none focus:ring-2 focus:ring-cyan-400 xl:mt-0 max-w-[175px] xl:max-w-[200px]"
                />
            </div>
        </div>
    </form>
</div>
