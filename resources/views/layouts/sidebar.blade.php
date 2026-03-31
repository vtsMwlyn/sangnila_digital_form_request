<div
    x-data="{ open: {{ $sidebarOpen ?? 'false' }} }"
    x-on:open-sidebar.window="open = true"
    x-on:close-sidebar.window="open = false"
>
<div class="hidden sm:flex flex-wrap justify-end w-full md:w-auto">

    <aside
        :class="open ? 'translate-x-0' : '-translate-x-full'"
        class="fixed top-0 left-0 h-full w-72  text-white shadow-lg transform transition-transform duration-300 ease-in-out z-40 flex flex-col items-center"
        style="background-image: url('{{ asset('img/sidebar-bg.webp') }}'); background-size: cover; background-position: center;"
    >
        <div class="mb-2 w-full hover:bg-gray-300/20 py-8 mt-14">
            @if(Auth::user()->profile_photo)
             <a
                href="{{ route('profile.edit') }}"
                class="transition-all duration-300 hover:from-[#597493] hover:to-[#1EB8CD]/10'"
            >
            <img
                src="{{ asset(Auth::user()->profile_photo) }}"
                alt="Profile Photo"
                class="w-[150px] h-[150px] rounded-full object-cover mx-auto"
            />
            @else
            <div
                class="w-24 h-24 rounded-full bg-gray-300 flex items-center justify-center mx-auto"
            >
                <i class="bi bi-person text-4xl text-gray-600"></i>
            </div>
            </a>
            @endif
        </div>

        <h2 class="font-bold text-xl mb-10 px-3 text-center max-w-xs">
            Hi, {{ Auth::user()->name }}!
        </h2>

        <nav class="w-full flex flex-col">
            <a
                href="{{ route('dashboard') }}"
                class="flex items-center space-x-4 px-5 py-3
                font-semibold transition-all duration-300
                {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
                    >
                <i class="bi bi-grid-1x2-fill text-2xl"></i>
                <span>Home</span>
            </a>

            <a
                href="{{ route('overtime.show') }}"
                class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300 {{ Str::startsWith(request()->route()->getName(), 'overtime') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
            >
                <i class="bi bi-clock-history text-2xl"></i>
                <span>Overtime Requests</span>
            </a>

            <a
                href="{{ route('leave.show') }}"
                class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300 {{ Str::startsWith(request()->route()->getName(), 'leave') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
            >
                <i class="bi bi-calendar2-event text-2xl"></i>
                <span>Leave Requests</span>
            </a>

            @if (auth()->user()->role === 'admin')
                <a
                href="{{ route('account.show') }}"
                    class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300 {{ Str::startsWith(request()->route()->getName(), 'account') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
                >
                    <i class="bi bi-people-fill text-2xl"></i>
                    <span>Employees</span>
                </a>

                <a
                href="{{ route('admin.fingerprint-attendance') }}"
                    class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300 {{ Request::is('admin/fingerprint-attendance*') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
                >
                    <i class="bi bi-fingerprint text-2xl"></i>
                    <span>Fingerprint Attendance</span>
                </a>
            @endif

            <a
                href="{{ route('LogActivity.show') }}"
                class="flex items-center space-x-4 px-5 py-3
                font-semibold transition-all duration-300
                {{ request()->routeIs('LogActivity.show') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
                    >
                <i class="bi bi-file-text text-2xl"></i>
                <span>Activity Log</span>
            </a>

            <a
                href="{{ route('profile.edit') }}"
                class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300 {{ request()->routeIs('profile.edit') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
            >
                <i class="bi bi-gear-fill text-2xl"></i>
                <span>Profile</span>
            </a>
        </nav>
    </aside>

    <button
        @click="open = !open; $dispatch(open ? 'open-sidebar' : 'close-sidebar')"
        :aria-expanded="open.toString()"
        aria-label="Toggle sidebar"
        class="fixed top-1/2 z-50 -translate-y-1/2 bg-[#1EB8CD] text-white w-7 h-[50px] rounded-r-full flex items-center justify-center shadow-lg transition-all duration-300"
        :style="{
        left: open ? '18rem' : '0',
        transformOrigin: 'center right'
    }"
    >
        <svg
            :class="open ? 'rotate-180' : ''"
            class="w-6 h-6 transition-transform duration-300"
            fill="none"
            stroke="currentColor"
            stroke-width="4"
            stroke-linecap="round"
            stroke-linejoin="round"
            viewBox="0 0 24 24"
        >
            <path d="M9 18l6-6-6-6" />
        </svg>
    </button>
</div>


{{-- mobile --}}
<div x-data="{ open: false }" class="md:hidden">
    <button @click="open = !open" class="fixed top-[8px] left-3 z-50 bg-white text-[#1EB8CD] w-11 h-11 flex items-center justify-center">

        <svg x-show="!open" x-cloakwidth="33" height="20" viewBox="0 0 33 26" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="33" height="6" rx="2" fill="#1EB8CD"/>
            <rect y="10" width="33" height="6" rx="2" fill="#1EB8CD"/>
            <rect y="20" width="33" height="6" rx="2" fill="#1EB8CD"/>
        </svg>

        <svg x-show="open" x-cloak class="w-7 h-7" fill="none" stroke="currentColor"
             stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
             viewBox="0 0 24 24">
            <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    <div
        x-show="open"
        x-transition
        x-cloak
        class="fixed top-12 left-0 right-0 bg-white shadow-lg z-40 rounded-b-lg overflow-hidden"
    >
        {{-- <div class="pt-20 pb-6 text-center bg-cover bg-center"
             style="background-image: url('{{ asset('img/sidebar-bg.webp') }}')">

            @if(Auth::user()->profile_photo)
                <a href="{{ route('profile.edit') }}">
                    <img src="{{ asset(Auth::user()->profile_photo) }}"
                        class="w-24 h-24 rounded-full mx-auto object-cover shadow-md">
                </a>
            @else
                <div class="w-24 h-24 rounded-full bg-gray-200 mx-auto flex items-center justify-center">
                    <i class="bi bi-person text-4xl text-gray-600"></i>
                </div>
            @endif

            <h2 class="text-white font-bold text-lg mt-3">
                Hi, {{ Auth::user()->name }}!
            </h2>
        </div> --}}

        <nav class="flex flex-col text-gray-800 divide-y">
            <a href="{{ route('dashboard') }}" class="px-5 py-4 font-semibold hover:bg-gray-100">Home</a>
            <a href="{{ route('overtime.show') }}" class="px-5 py-4 font-semibold hover:bg-gray-100">Overtime Data</a>
            <a href="{{ route('leave.show') }}" class="px-5 py-4 font-semibold hover:bg-gray-100">Leave Data</a>
            <a href="{{ route('LogActivity.show') }}" class="px-5 py-4 font-semibold hover:bg-gray-100">Log Activity</a>

            @if(auth()->user()->role === 'admin')
                <a href="{{ route('account.show') }}" class="px-5 py-4 font-semibold hover:bg-gray-100">Manage Account</a>
            @endif

            <a href="{{ route('profile.edit') }}" class="px-5 py-4 font-semibold hover:bg-gray-100">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-5 py-4 font-semibold text-red-500 hover:bg-gray-100 w-full text-left">
                    Log Out
                </button>
            </form>
        </nav>
    </div>

    <!-- OVERLAY -->
    <div
        x-show="open"
        x-transition.opacity
        @click="open = false"
        class="fixed inset-0 bg-black/40 z-30"
        x-cloak
    ></div>

</div>




