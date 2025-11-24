<div
    class="header-bar bg-[#1EB8CD] px-8 py-4 ml-72 transition-all duration-300 ease-in-out flex justify-between items-center"
    :class="sidebarOpen ? 'ml-0' : 'ml-[-0px]'"
>
    <!-- Left: Greeting -->
    <h1 class="text-white text-xl xl:text-2xl font-bold">
        {{ __('Hi ') . auth()->user()->name }}!
    </h1>
</div>
