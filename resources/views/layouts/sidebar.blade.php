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
                <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    viewBox="0 0 24 24"
                >
                    <path
                        d="M3 9.75L12 3l9 6.75V21a1.5 1.5 0 01-1.5 1.5H4.5A1.5 1.5 0 013 21V9.75z"
                    />
                    <path d="M9 22.5V12h6v10.5" />
                </svg>
                <span>Home</span>
            </a>

            <a
                href="{{ route('overwork.show') }}"
                class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300 {{ Str::startsWith(request()->route()->getName(), 'overwork') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
            >
                <i class="bi bi-alarm text-2xl"></i>
                <span>Overwork Data</span>
            </a>

            <a
                href="{{ route('leave.show') }}"
                class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300 {{ Str::startsWith(request()->route()->getName(), 'leave') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
            >
                <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    viewBox="0 0 24 24"
                >
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                    <path d="M16 2v4M8 2v4M3 10h18" />
                </svg>
                <span>Leave Data</span>
            </a>

            <a
                href="{{ route('LogActivity.show') }}"
                class="flex items-center space-x-4 px-5 py-3
                font-semibold transition-all duration-300
                {{ request()->routeIs('LogActivity.show') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
                    >
                <i class="bi bi-file-text text-2xl"></i>
                <span>Log Activity</span>
            </a>

            @if (auth()->user()->role === 'admin')
                <a
                href="{{ route('account.show') }}"
                    class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300 {{ Str::startsWith(request()->route()->getName(), 'account') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <circle cx="12" cy="7" r="3" />
                        <path
                            d="M12 10c-2.5 0-4.5 2-4.5 4.5V18h9v-3.5c0-2.5-2-4.5-4.5-4.5z"
                        />

                        <circle cx="5" cy="8" r="2.5" />
                        <path d="M5 10.5c-2 0-3 1.5-3 3V17h4" />

                        <circle cx="19" cy="8" r="2.5" />
                        <path d="M19 10.5c2 0 3 1.5 3 3V17h-4" />
                    </svg>

                    <span>Manage Account</span>
                </a>
            @endif

            <a
                href="{{ route('profile.edit') }}"
                class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300 {{ request()->routeIs('profile.edit') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
            >
                <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    viewBox="0 0 24 24"
                >
                    <circle cx="12" cy="7" r="4" />
                    <path d="M5.5 21a6.5 6.5 0 0113 0" />
                </svg>
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
        class="fixed top-0 left-0 right-0 bg-white shadow-lg z-40 rounded-b-lg overflow-hidden"
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
            <a href="{{ route('overwork.show') }}" class="px-5 py-4 font-semibold hover:bg-gray-100">Overwork Data</a>
            <a href="{{ route('leave.show') }}" class="px-5 py-4 font-semibold hover:bg-gray-100">Leave Data</a>
            <a href="{{ route('LogActivity.show') }}" class="px-5 py-4 font-semibold hover:bg-gray-100">Log Activity</a>

            @if(auth()->user()->role === 'admin')
                <a href="{{ route('account.show') }}" class="px-5 py-4 font-semibold hover:bg-gray-100">Manage Account</a>
            @endif

            <a href="{{ route('profile.edit') }}" class="px-5 py-4 font-semibold hover:bg-gray-100">Profile</a>
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




