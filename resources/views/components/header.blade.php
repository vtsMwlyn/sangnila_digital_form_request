<div class="flex bg-white w-full h-[60px] sticky z-50 top-0 justify-between items-center">

    <div class="flex items-center space-x-4">
        <img
        src="{{ asset('img/logo.png') }}"
        alt="Logo"
        class="cursor-pointer h-[45px] xl:h-[50px] ml-3 invert-0 brightness-0 saturate-100 hue-rotate-[200deg]"
        />
        <div class="bg-[#042E66] w-[3px] h-12"></div>
        <h1 class="text-md xl:text-xl text-[#042E66] uppercase">Digital Leave & Overwork Form</h1>
    </div>

    <div class="hidden sm:flex items-center mr-4">
        <div class="relative flex flex-col items-end dropdown-container">
            <button type="button" class="dropdown-toggler"><img src="{{ asset('img/burger-icon-navbar-pc.svg') }}" alt="burger-icon" class="h-6 hover:scale-110"></button>
            <div class="text-base absolute z-10 top-14 w-80 rounded-3xl flex flex-col py-2 dropdown-menu backdrop-blur-xl bg-[rgba(255,255,255,0.8)] overflow-hidden" style="display: none; ">
                <button type="button" id="help-and-support-menu"><div class="w-full px-5 py-1.5 text-black font-semibold flex items-center gap-1 hover:bg-slate-100"><img src="{{ asset('img/navbar-help-and-support.svg') }}" class="h-5 w-5" alt="sidebar-icon"> Help and Support</div></a>
                <button type="button" id="send-feedback-menu"><div class="w-full px-5 py-1.5 text-black font-semibold flex items-center gap-1 hover:bg-slate-100"><img src="{{ asset('img/navbar-send-feedback.svg') }}" class="h-5 w-5" alt="sidebar-icon"> Send Feedback</div></a>
                <a href="{{ route("profile.edit") }}" class="block xl:hidden"><div class="w-full px-5 py-0.5 text-black font-semibold flex items-center gap-1 hover:bg-slate-100"><i class="bi bi-person-fill text-slate-400 text-lg mr-0.5"></i> Profile</div></a>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-5 py-1 text-black font-bold flex items-center gap-1 hover:bg-slate-100">
                        <img src="{{ asset('img/navbar-logout.svg') }}" class="h-5 w-5" alt="sidebar-icon"> {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(() => {
        $('#help-and-support-menu').on('click', function(e){
            e.preventDefault();

            window.open('https://wa.me/6285693257411?text=Halo%20admin%20Sangnila!%0APerkenalkan%20nama%20saya%20{{ Auth::check() ? Auth::user()->full_name : '...' }}%20dan%20saya%20membutuhkan%20bantuan%20terkait%20hal-hal%20berikut%20yang%20saya%20jumpai%20dalam%20penggunaan%20aplikasi%20Sangnila%20Form%3A', "_blank");
        });

        $('#send-feedback-menu').on('click', function(e){
            e.preventDefault();

            window.open('https://wa.me/6285693257411?text=Halo%20admin%20Sangnila!%0APerkenalkan%20nama%20saya%20{{ Auth::check() ? Auth::user()->full_name : '...' }}%20dan%20saya%20memiliki%20feedback%20untuk%20disampaikan%20terkait%20penggunaan%20aplikasi%20Sangnila%20Form%3A', "_blank");
        });
    });
</script>
