<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/icon.png') }}" type="image/png">
    <title>Sangnila E-form</title>

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


        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">



        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
      <x-header />
    <body class="font-geologica antialiased">
        <div class="min-h-screen w-full bg-gradient-to-r from-[#B3C4DE] to-[#EAEFF6]" x-data="{ sidebarOpen: true }" x-on:open-sidebar.window="sidebarOpen = true" x-on:close-sidebar.window="sidebarOpen = false">
            @include('layouts.navbar', ['sidebarOpen' => 'sidebarOpen'])
            @include('layouts.sidebar', ['sidebarOpen' => 'sidebarOpen'])

            <main :class="sidebarOpen ? 'ml-0' : 'ml-[-0px]'" class="ml-72 min-h-screen max-w-full flex flex-col items-center mt-20 px-4 sm:px-10 lg:px-8 transition-all duration-300 ease-in-out">
                <div :class="sidebarOpen ? 'max-w-5xl' : 'max-w-6xl'" class="w-[390px] xl:w-full xl:max-w-6xl rounded-xl overflow-hidden shadow-lg bg-white xl:py-10 px-8 py-10 mt-5 mb-8 transition-all duration-300 ease-in-out">
                    {{ $slot }}
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

  // === 1. Tampilin spinner secepat mungkin pas halaman mulai load
  document.addEventListener('DOMContentLoaded', () => {
    showLoading();
  });

  // === 2. Hilangin pas semua resource udah siap
  window.addEventListener('load', () => {
    hideLoading();
  });

  // === 3. Tampil lagi kalau user submit form
  document.addEventListener('submit', (e) => {
    // biar gak bentrok sama ajax / validation JS
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
