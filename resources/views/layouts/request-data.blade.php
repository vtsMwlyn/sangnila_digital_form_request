<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/icon.png') }}" type="image/png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Sangnila E-form</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&display=swap" rel="stylesheet">\

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Custom Styles -->
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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<x-header />
<body class="font-geologica antialiased">
    <div
        class="min-h-screen w-full bg-gradient-to-r from-[#B3C4DE] to-[#EAEFF6]"
        x-data="{ sidebarOpen: true }"
        x-on:open-sidebar.window="sidebarOpen = true"
        x-on:close-sidebar.window="sidebarOpen = false"
    >
        @include('components.navbar-request', ['sidebarOpen' => 'sidebarOpen'])
        @include('layouts.sidebar', ['sidebarOpen' => 'sidebarOpen'])

        <main
            class="min-h-screen max-w-full ml-72 flex flex-col items-center mt-5 sm:px-6 lg:px-8 transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'ml-0' : 'ml-[-0px]'"
        >
            <div
                class="w-full max-w-[1400px] rounded-xl overflow-hidden bg-transparent mx-auto py-6 sm:py-8 lg:py-5 px-4 sm:px-6 lg:px-5 transition-all duration-300 ease-in-out"
            >
                <div
                    class="w-full"
                    :class="sidebarOpen ? 'overflow-x-auto' : ''"
                >
                    @yield('content')
                </div>
            </div>
        </main>

        @include('layouts.footer')
    </div>

    <div id="global-loading"
         class="fixed inset-0 flex items-center justify-center bg-black/40 z-50">
        <div class="w-6 h-6 border-4 rounded-full animate-spin border-sky-600 border-t-transparent"></div>
    </div>

    <!-- Script -->
    <script>
        const loader = document.getElementById('global-loading');

        function showLoading() {
            loader.classList.remove('hidden');
        }
        function hideLoading() {
            loader.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => {
            showLoading();
        });

        window.addEventListener('load', () => {
            hideLoading();
        });

        document.addEventListener('submit', (e) => {
            setTimeout(() => showLoading(), 50);
        });

        document.querySelectorAll('a[href]').forEach(a => {
            a.addEventListener('click', e => {
                const href = a.getAttribute('href');
                if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
                    showLoading();
                }
            });
        });
    </script>
</body>
</html>
