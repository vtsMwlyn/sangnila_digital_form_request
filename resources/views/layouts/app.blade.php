<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('img/icon.png') }}" type="image/png">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

        <title>Sangnila E-Form</title>
        <style>
            body {
                font-family: 'Geologica', sans-serif;
                scroll-behavior: smooth;
            }

            /* ===============================
               RESPONSIVE LAYOUT SETTINGS
               =============================== */

            /* Untuk tablet (<=1024px) dan HP */
            @media (max-width: 1024px) {
                /* Sidebar */
                [x-data] > .sidebar {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 70%;
                    max-width: 280px;
                    height: 100%;
                    background-color: #fff;
                    box-shadow: 0 0 10px rgba(0,0,0,0.2);
                    z-index: 50;
                    transform: translateX(-100%);
                    transition: transform 0.3s ease-in-out;
                }

                [x-data] > .sidebar[x-show="sidebarOpen"] {
                    transform: translateX(0);
                }

                /* Main content */
                main {
                    margin-left: 0 !important;
                    width: 100%;
                    padding-left: 1rem;
                    padding-right: 1rem;
                }

                /* Navbar and footer padding */
                .navbar, .footer {
                    padding-left: 1rem;
                    padding-right: 1rem;
                }
            }

            /* Untuk layar sangat kecil (HP <640px) */
            @media (max-width: 640px) {
                main {
                    padding-left: 0.75rem;
                    padding-right: 0.75rem;
                }
            }
        </style>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Geologica&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
      </head>
      <body>
      <x-header />
      <div class="min-h-screen bg-gradient-to-r from-[#B3C4DE] to-[#EAEFF6]" x-data="{ sidebarOpen: true }" x-on:open-sidebar.window="sidebarOpen = true" x-on:close-sidebar.window="sidebarOpen = false">
                @include('layouts.navbar')

                <!-- Include sidebar with state sharing -->
                @include('layouts.sidebar', ['sidebarOpen' => 'sidebarOpen'])

            <!-- Page Content with reactive margin -->
            <main :class="sidebarOpen ? 'ml-0' : 'ml-[-0px]'" class="ml-72 transition-all duration-300 ease-in-out pb-10">
                {{ $slot }}
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
    if (new URLSearchParams(window.location.search).size > 0) {
      setTimeout(() => {
        const target = document.getElementById('data');
        if (target) {
          target.scrollIntoView({ behavior: "instant" });
        }
      }, 100);
    }
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
