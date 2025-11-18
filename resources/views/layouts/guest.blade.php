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
            scroll-behavior: smooth;
        }

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
    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
        rel="stylesheet"
    />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="font-sans text-gray-900 antialiased max-h-screen"
    style="background: url('{{ asset('img/bg.webp') }}') no-repeat center center / cover;"
>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div>
            {{ $slot }}
        </div>
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
