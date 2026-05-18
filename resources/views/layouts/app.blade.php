<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/icon.png') }}" type="image/png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <title>Sangnila EMS</title>
    <style>
        body {
            font-family: 'Geologica', sans-serif;
            scroll-behavior: smooth;
        }

        /* Tablet and mobile (<=1024px) */
        @media (max-width: 1024px) {
            .sidebar {
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

            .sidebar.sidebar-open {
                transform: translateX(0);
            }

            main, #page-navbar, #page-footer {
                margin-left: 0 !important;
            }

            main {
                width: 100%;
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        /* Mobile (<640px) */
        @media (max-width: 640px) {
            main {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
        }
      </style>

      <link rel="preconnect" href="https://fonts.bunny.net">
      <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
      <link href="https://fonts.googleapis.com/css2?family=Geologica&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

      @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body>
    <x-header />

    <div class="min-h-screen bg-gradient-to-r from-[#B3C4DE] to-[#EAEFF6]" id="app-wrapper">
      @include('layouts.navbar')

      @include('layouts.sidebar')

      <main id="main-content" class="ml-72 transition-all duration-300 ease-in-out p-10 min-h-[90vh]">
          @yield('content')

          @if (auth()->user()->role === 'user')
            <x-contact />
          @endif
      </main>

      @include('layouts.footer')
    </div>

    <x-global-loading />

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
        if (new URLSearchParams(window.location.search).size > 0) {
          setTimeout(() => {
            const target = document.getElementById('data');
            if (target) {
              target.scrollIntoView({ behavior: "instant" });
            }
          }, 100);
        }
      });

      window.addEventListener('load', () => {
        hideLoading();
      });

      document.addEventListener('submit', () => {
        setTimeout(() => showLoading(), 50);
      });

      document.querySelectorAll('a[href]').forEach(a => {
        a.addEventListener('click', () => {
          const href = a.getAttribute('href');
          if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
            showLoading();
          }
        });
      });

      // Dropdowns
      $(".dropdown-toggler").click(function (e) {
        e.stopPropagation();
        let $dropdownMenu = $(this).closest(".dropdown-container").find(".dropdown-menu");
        $(".dropdown-menu").not($dropdownMenu).hide();
        $dropdownMenu.toggle();
      });

      $(document).click(function (e) {
        if (!$(e.target).closest(".dropdown-menu, .dropdown-toggler").length) {
          $(".dropdown-menu").hide();
        }
      });

      // Desktop sidebar toggle
      $(document).on('click', '#sidebar-toggle-btn', function () {
        const $aside = $('#desktop-sidebar');
        const $main = $('#main-content');
        const $icon = $('#sidebar-toggle-icon');
        const isOpen = $aside.hasClass('translate-x-0');

        if (isOpen) {
          $aside.removeClass('translate-x-0').addClass('-translate-x-full');
          $(this).css('left', '0');
          $icon.removeClass('rotate-180');
          $main.add('#page-navbar').add('#page-footer').removeClass('ml-72').addClass('ml-0');
          $(this).attr('aria-expanded', 'false');
        } else {
          $aside.removeClass('-translate-x-full').addClass('translate-x-0');
          $(this).css('left', '18rem');
          $icon.addClass('rotate-180');
          $main.add('#page-navbar').add('#page-footer').removeClass('ml-0').addClass('ml-72');
          $(this).attr('aria-expanded', 'true');
        }
      });

      // Mobile sidebar toggle
      $(document).on('click', '#mobile-menu-btn', function () {
        const isOpen = $('#mobile-sidebar-menu').is(':visible');
        if (isOpen) {
          $('#mobile-sidebar-menu').hide();
          $('#mobile-overlay').hide();
          $('#mobile-icon-open').show();
          $('#mobile-icon-close').hide();
        } else {
          $('#mobile-sidebar-menu').show();
          $('#mobile-overlay').show();
          $('#mobile-icon-open').hide();
          $('#mobile-icon-close').show();
        }
      });

      $(document).on('click', '#mobile-overlay', function () {
        $('#mobile-sidebar-menu').hide();
        $('#mobile-overlay').hide();
        $('#mobile-icon-open').show();
        $('#mobile-icon-close').hide();
      });
    </script>
  </body>
</html>
