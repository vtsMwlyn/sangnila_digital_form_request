<div class="flex
    {{$route === 'dashboard' || $route === 'draft'
        ? 'flex-col'
        : 'flex-col md:flex-row md:justify-between md:py-3'
    }}
    mb-2 w-full gap-5"
>
    <h2 class="text-xl xl:text-2xl font-bold text-[#012967] capitalize">{{$title}}</h2>
    <form action="{{route($route)}}" method="get">
        <div class="w-full flex flex-col md:flex-row justify-between items-start md:items-center {{$route === 'dashboard' ? 'py-2' : ''}} gap-3">
            @if ($route === 'dashboard' || $route === 'draft')
                <x-filter-data-toggle :status="$status" :type="$type" />
            @endif

            <div class="flex w-full md:w-auto gap-2">

                @if ($route !== 'dashboard' && $route !== 'draft')
                    <input type="hidden" name="status" value="{{$status}}">
                @endif

                <x-select name="month" id="month" class="w-60" onchange="this.form.submit()">
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
                </x-select>

                <x-select name="status" id="status" class="w-40" onchange="this.form.submit()">
                    <option value="" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="review" {{ request('status') === 'review' ? 'selected' : '' }}>Review</option>
                    @if(Auth::user()->role != 'admin')
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    @endif
                </x-select>

                @if(Auth::user()->role != 'admin')
                    <x-text-input
                        type="search"
                        id="search"
                        name="search"
                        placeholder="Search by Reason..."
                        value="{{ request('search') }}"
                        onchange="this.form.submit()"
                        class="w-60"
                    />
                @endif

                @if(Auth::user()->role == 'admin')
                    <x-text-input
                        type="employee"
                        id="employee"
                        name="employee"
                        placeholder="Search by Employee..."
                        value="{{ request('employee') }}"
                        onchange="this.form.submit()"
                        class="w-60"
                    />
                @endif
            </div>
        </div>
    </form>
</div>
