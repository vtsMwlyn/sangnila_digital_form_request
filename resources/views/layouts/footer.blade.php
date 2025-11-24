<footer class="px-5 py-10  text-white bg-slate-800 text-base lg:text-xl ml-72 transition-all duration-300 ease-in-out flex items-center" :class="sidebarOpen ? 'ml-0' : 'ml-[-0px]'">
    <div class="flex flex-col md:flex-row items-start justify-center gap-3">
        <div class="w-full xl:w-1/3 self-center px-4">
            <p class="font-extrabold text-start text-lg lg:text-xl ">Sangnila Form Request WebApp</p>
            <p class="text-start mt-2">Copyright &copy; 2025 - Sangnila Interactive Media and Technology</p>
        </div>

        <div class="w-full md:w-1/3 xl:w-1/4 flex flex-col items-start px-4 text-lg lg:text-md">
            <span class="font-bold mb-2 mt-6 xl:mt-0">Find Us</span>
            <a href="https://maps.app.goo.gl/Did4dZueYwVNtSM9A" class="hover:underline hover:font-medium" target="_blank">
                Paskal Hyper Square B70, Jl.Pasir Kaliki No.23, Kec. Cicendo, Kota Bandung
            </a>

            <span class="font-bold mb-2 mt-4">Contact Us</span>
            <a href="https://wa.me/6285693257411" class="flex items-center gap-2 hover:underline hover:font-medium hover:bg-rounded-lg" target="_blank">
                <i class="text-lg"><i class="bi bi-telephone"></i></i>
                +62 856-9325-7411
            </a>
            <a href="mailto:admin@sangnilaindonesia.com" class="flex items-center gap-2 hover:underline hover:font-medium hover:bg-rounded-lg">
                <i class="text-lg"><i class="bi bi-envelope"></i></i>
                admin@sangnilaindonesia.com
            </a>
        </div>

        <div class="flex flex-row w-full xl:w-1/3 px-4 text-lg lg:text-md">
            <div class="w-1/2 flex flex-col items-start mt-6 xl:mt-0">
                <span class="font-bold mb-2">Social Media</span>
                <a href="https://www.instagram.com/sangnila.academy/" class="flex items-center gap-2 hover:underline hover:font-medium hover:bg-rounded-lg" target="_blank">
                    <i class="bi bi-instagram text-lg"></i> Instagram
                </a>
                <a href="https://www.facebook.com/sangnila.artsacademy" class="flex items-center gap-2 hover:underline hover:font-medium hover:bg-rounded-lg" target="_blank">
                    <i class="bi bi-facebook text-lg"></i> Facebook
                </a>
                <a href="https://www.tiktok.com/@sangnilaartsacademy" class="flex items-center gap-2 hover:underline hover:font-medium hover:bg-rounded-lg" target="_blank">
                    <i class="bi bi-tiktok text-lg"></i> TikTok
                </a>
                <a href="https://www.youtube.com/@sangnilaartsacademy1709" class="flex items-center gap-2 hover:underline hover:font-medium hover:bg-rounded-lg" target="_blank">
                    <i class="bi bi-youtube text-lg"></i> YouTube
                </a>
                <a href="https://www.linkedin.com/company/pt-sangnila-interaktif-media-dan-teknologi/" class="flex items-center gap-2 hover:underline hover:font-medium hover:bg-rounded-lg" target="_blank">
                    <i class="bi bi-linkedin text-lg"></i> LinkedIn
                </a>
            </div>

            <div class="w-1/2 flex flex-col items-start mt-6 xl:mt-0 text-lg lg:text-md">
                <span class="font-bold mb-2">Sangnila WebApps</span>
                <a href="https://sangnilaindonesia.com" class="flex items-center gap-2 hover:underline hover:font-medium hover:bg-rounded-lg" target="_blank">
                    <i class="bi bi-globe text-lg"></i> Main Website
                </a>
                <a href="https://finance.sangnilaindonesia.com/" class="flex items-center gap-2 hover:underline hover:font-medium hover:bg-rounded-lg" target="_blank">
                    <i class="bi bi-globe text-lg"></i> Finance
                </a>
                <a href="https://lms.sangnilaindonesia.com/" class="flex items-center gap-2 hover:underline hover:font-medium hover:bg-rounded-lg" target="_blank">
                    <i class="bi bi-globe text-lg"></i> LMS
                </a>
                <a href="https://register.sangnilaindonesia.com/admin-login" class="flex items-center gap-2 hover:underline hover:font-medium hover:bg-rounded-lg" target="_blank">
                    <i class="bi bi-globe text-lg"></i> Academy Registration
                </a>
            </div>
        </div>
    </div>
</footer>

<style>
    body {
        font-family: 'Geologica', sans-serif;
    }

    /* === RESPONSIVE SECTION === */
    @media (max-width: 1024px) {
        main {
            margin-left: 0 !important;
            padding: 0 1rem !important;
        }
    }

    @media (max-width: 768px) {
        .ml-72 {
            margin-left: 0 !important;
        }

        main {
            padding: 0.5rem 1rem !important;
        }

        .max-w-[1400px] {
            max-width: 100% !important;
        }

        .px-6, .px-5 {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        .sm\\:px-6, .lg\\:px-8 {
            padding: 1rem !important;
        }
    }

    @media (max-width: 480px) {
        main {
            margin: 0 !important;
            padding: 0.5rem !important;
        }

        .rounded-xl {
            border-radius: 0.5rem !important;
        }

        .py-6, .sm\\:py-8, .lg\\:py-5 {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }
    }
</style>
