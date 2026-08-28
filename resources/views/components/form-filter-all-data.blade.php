<div
  class="flex
    {{$route === 'dashboard' || $route === 'draft'
        ? 'flex-col'
        : 'flex-col md:flex-row md:justify-between md:py-3'
    }}
    mb-2 w-full gap-5"
>
  <h2 class="text-xl xl:text-2xl font-bold text-[#012967] capitalize">
    {{$title}}
  </h2>
  <form action="{{route($route)}}" method="get">
    <div
      class="w-full flex flex-col md:flex-row justify-between items-start md:items-center {{$route === 'dashboard' ? 'py-2' : ''}} gap-3"
    >
      @if ($route === 'dashboard' || $route === 'draft')
        <div class="w-full sm:hidden">
          <x-select id="mobileFilter" class="w-1/2">
            @if (auth()->user()->role === 'user')
              <option value="all" {{ $type === 'all' ? 'selected' : '' }}>
                All Data
              </option>
              <option value="overtime" {{ $type === 'overtime' ? 'selected' : '' }}>
                Overtime
              </option>
              <option value="leave" {{ $type === 'leave' ? 'selected' : '' }}>
                Leave
              </option>
            @elseif (auth()->user()->role === 'admin')
              <option value="review" {{ $status === 'review' ? 'selected' : '' }}>
                Review
              </option>
              <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>
                Approved
              </option>
              <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>
                Rejected
              </option>
            @endif
          </x-select>
        </div>

        <ul class="hidden sm:flex space-x-6 text-[#012967] font-semibold">
          @if (auth()->user()->role === 'user')
            <input
              type="hidden"
              name="type"
              id="buttonSubmit"
              value="{{ $type }}"
            />

            <li
              class="{{ $type === 'all' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer"
            >
              <button
                type="button"
                name="type"
                value="all"
                class="status-btn hover:text-cyan-600 transition"
              >
                All Data
              </button>
            </li>

            <li
              class="{{ $type === 'overtime' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer"
            >
              <button
                type="button"
                name="type"
                value="overtime"
                class="status-btn hover:text-cyan-600 transition"
              >
                Overtime
              </button>
            </li>

            <li
              class="{{ $type === 'leave' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer"
            >
              <button
                type="button"
                name="type"
                value="leave"
                class="status-btn hover:text-cyan-600 transition"
              >
                Leave
              </button>
            </li>

          @elseif (auth()->user()->role === 'admin')
            <input
              type="hidden"
              name="status"
              id="buttonSubmit"
              value="{{ $status }}"
            />

            <li
              class="{{ $status === 'review' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer"
            >
              <button
                type="button"
                name="status"
                value="review"
                class="status-btn hover:text-cyan-600 transition"
              >
                Review
              </button>
            </li>

            <li
              class="{{ $status === 'approved' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer"
            >
              <button
                type="button"
                name="status"
                value="approved"
                class="status-btn hover:text-cyan-600 transition"
              >
                Approved
              </button>
            </li>

            <li
              class="{{ $status === 'rejected' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer"
            >
              <button
                type="button"
                name="status"
                value="rejected"
                class="status-btn hover:text-cyan-600 transition"
              >
                Rejected
              </button>
            </li>
          @endif
        </ul>
      @endif

      <div class="flex flex-wrap w-auto gap-2">
        {{-- @if ($route !== 'dashboard' && $route !== 'draft')
          <input type="hidden" name="status" value="{{$status}}" />
        @endif --}}

        {{-- Month selector --}}
        <x-select
          name="month"
          id="month"
          class="w-60"
          onchange="this.form.submit()"
        >
          <option
            value="all"
            {{ request('month') === 'all' ? 'selected' : '' }}
          >
            All Months
          </option>
          @php
                        $months = [];
                        for ($i = -1; $i < 12; $i++) {
                            $date = now()->subMonths($i);
                            $months[] = ['value' => $date->format('m-Y'), 'label' => $date->format('F Y')];
                        }
                    @endphp
          @foreach ($months as $monthOption)
            <option
              value="{{ $monthOption['value'] }}"
              {{ request('month') === $monthOption['value'] ? 'selected' : '' }}
            >
              {{ $monthOption['label'] }}
            </option>
          @endforeach
        </x-select>

        @if (!Route::is('dashboard*'))
          <x-select
            name="status"
            id="status"
            class="w-40"
            onchange="this.form.submit()"
          >
            <option
              value=""
              {{ request('status') === 'all' ? 'selected' : '' }}
            >
              All
            </option>
            <option
              value="approved"
              {{ request('status') === 'approved' ? 'selected' : '' }}
            >
              Approved
            </option>
            <option
              value="rejected"
              {{ request('status') === 'rejected' ? 'selected' : '' }}
            >
              Rejected
            </option>
            <option
              value="review"
              {{ request('status') === 'review' ? 'selected' : '' }}
            >
              Review
            </option>
            @if (Auth::user()->role != 'admin')
              <option
                value="draft"
                {{ request('status') === 'draft' ? 'selected' : '' }}
              >
                Draft
              </option>
            @endif
          </x-select>
        @endif

        @if (Auth::user()->role != 'admin')
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

        @if (Auth::user()->role == 'admin')
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

<script>
  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".status-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        const hiddenInput = document.querySelector("#buttonSubmit");
        hiddenInput.value = this.value;
        hiddenInput.form.submit();
      });
    });

    const mobileFilter = document.getElementById("mobileFilter");
    if (mobileFilter) {
      mobileFilter.addEventListener("change", function () {
        const hiddenInput = document.querySelector("#buttonSubmit");
        hiddenInput.value = this.value;
        hiddenInput.form.submit();
      });
    }
  });
</script>
