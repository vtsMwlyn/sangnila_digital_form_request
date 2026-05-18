{{-- Sidebar (unified for all screen sizes) --}}
<aside
    id="desktop-sidebar"
    class="sidebar fixed top-0 left-0 h-full w-72 -translate-x-full text-white shadow-lg transform transition-transform duration-300 ease-in-out z-40 flex flex-col items-center"
    style="background-image: url('{{ asset('img/sidebar-bg.webp') }}'); background-size: cover; background-position: center;"
>
    <div class="mb-2 w-full hover:bg-gray-300/20 py-8 mt-14">
        @if(Auth::user()->profile_photo)
        <a
            href="{{ route('profile.edit') }}"
            class="transition-all duration-300 hover:from-[#597493] hover:to-[#1EB8CD]/10"
        >
            <img
                src="{{ asset(Auth::user()->profile_photo) }}"
                alt="Profile Photo"
                class="w-[150px] h-[150px] rounded-full object-cover mx-auto"
            />
        </a>
        @else
        <div class="w-24 h-24 rounded-full bg-gray-300 flex items-center justify-center mx-auto">
            <i class="bi bi-person text-4xl text-gray-600"></i>
        </div>
        @endif
    </div>

    <h2 class="font-bold text-xl mb-10 px-3 text-center max-w-xs">
        Hi, {{ Auth::user()->name }}!
    </h2>

    <nav class="w-full flex flex-col">
        <a
            href="{{ route('dashboard') }}"
            class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300
            {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
        >
            <i class="bi bi-grid-1x2-fill text-2xl"></i>
            <span>Home</span>
        </a>

        <a
            href="{{ route('overtime.show') }}"
            class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300
            {{ Str::startsWith(request()->route()->getName(), 'overtime') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
        >
            <i class="bi bi-clock-history text-2xl"></i>
            <span>Overtime Requests</span>
        </a>

        <a
            href="{{ route('leave.show') }}"
            class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300
            {{ Str::startsWith(request()->route()->getName(), 'leave') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
        >
            <i class="bi bi-calendar2-event text-2xl"></i>
            <span>Leave Requests</span>
        </a>

        @if (auth()->user()->role === 'admin')
            <a
                href="{{ route('account.show') }}"
                class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300
                {{ Str::startsWith(request()->route()->getName(), 'account') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
            >
                <i class="bi bi-people-fill text-2xl"></i>
                <span>Employees</span>
            </a>

            <a
                href="{{ route('admin.fingerprint-attendance') }}"
                class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300
                {{ Request::is('admin/fingerprint-attendance*') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
            >
                <i class="bi bi-fingerprint text-2xl"></i>
                <span>Fingerprint Attendance</span>
            </a>
        @endif

        <a
            href="{{ route('LogActivity.show') }}"
            class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300
            {{ request()->routeIs('LogActivity.show') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
        >
            <i class="bi bi-file-text text-2xl"></i>
            <span>Activity Log</span>
        </a>

        <a
            href="{{ route('profile.edit') }}"
            class="flex items-center space-x-4 px-5 py-3 font-semibold transition-all duration-300
            {{ request()->routeIs('profile.edit') ? 'bg-gradient-to-r from-[#1EB8CD] to-[#1EB8CD]/10' : 'hover:bg-gradient-to-r hover:from-[#597493] hover:to-[#1EB8CD]/10' }}"
        >
            <i class="bi bi-gear-fill text-2xl"></i>
            <span>Profile</span>
        </a>

        <div class="sm:hidden border-t border-white/20 mt-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="flex items-center space-x-4 px-5 py-3 w-full font-semibold transition-all duration-300 text-red-300 hover:bg-gradient-to-r hover:from-red-500/40 hover:to-red-500/10"
                >
                    <i class="bi bi-box-arrow-right text-2xl"></i>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </nav>
</aside>

{{-- Desktop chevron toggle (hidden on mobile) --}}
<button
    id="sidebar-toggle-btn"
    aria-expanded="false"
    aria-label="Toggle sidebar"
    class="hidden sm:flex fixed top-1/2 z-50 -translate-y-1/2 bg-[#1EB8CD] text-white w-7 h-[50px] rounded-r-full items-center justify-center shadow-lg transition-all duration-300"
    style="left: 0;"
>
    <svg
        id="sidebar-toggle-icon"
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

{{-- Mobile hamburger (hidden on sm+) --}}
<button
    id="mobile-menu-btn"
    class="sm:hidden fixed top-[8px] left-3 z-50 bg-white text-[#1EB8CD] w-11 h-11 flex items-center justify-center"
>
    <svg id="mobile-icon-open" width="33" height="20" viewBox="0 0 33 26" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="33" height="6" rx="2" fill="#1EB8CD"/>
        <rect y="10" width="33" height="6" rx="2" fill="#1EB8CD"/>
        <rect y="20" width="33" height="6" rx="2" fill="#1EB8CD"/>
    </svg>

    <svg id="mobile-icon-close" class="w-7 h-7" fill="none" stroke="currentColor"
         stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
         viewBox="0 0 24 24" style="display: none;">
        <path d="M6 18L18 6M6 6l12 12"/>
    </svg>
</button>

{{-- Backdrop overlay (shown on mobile when sidebar is open) --}}
<div id="mobile-overlay" class="fixed inset-0 bg-black/40 z-30" style="display: none;"></div>
