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

        /* Tablet and mobile (<640px) — sidebar overlays, no content margin */
        @media (max-width: 639px) {
            main, #page-navbar, #page-footer {
                margin-left: 0 !important;
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

      <main id="main-content" class="ml-72 transition-all duration-300 ease-in-out p-5 xl:p-10 min-h-[90vh]">
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

      // Sidebar — unified open/close logic
      const SM = 640;

      function isMobile() {
        return window.innerWidth < SM;
      }

      function openSidebar() {
        const $aside = $('#desktop-sidebar');
        $aside.removeClass('-translate-x-full').addClass('translate-x-0');
        $('#sidebar-toggle-btn').css('left', '18rem').attr('aria-expanded', 'true');
        $('#sidebar-toggle-icon').addClass('rotate-180');
        if (isMobile()) {
          $('#mobile-overlay').show();
          $('#mobile-icon-open').hide();
          $('#mobile-icon-close').show();
        } else {
          $('#main-content, #page-navbar, #page-footer').removeClass('ml-0').addClass('ml-72');
        }
      }

      function closeSidebar() {
        const $aside = $('#desktop-sidebar');
        $aside.removeClass('translate-x-0').addClass('-translate-x-full');
        $('#sidebar-toggle-btn').css('left', '0').attr('aria-expanded', 'false');
        $('#sidebar-toggle-icon').removeClass('rotate-180');
        if (isMobile()) {
          $('#mobile-overlay').hide();
          $('#mobile-icon-open').show();
          $('#mobile-icon-close').hide();
        } else {
          $('#main-content, #page-navbar, #page-footer').removeClass('ml-72').addClass('ml-0');
        }
      }

      // Open by default on desktop
      $(document).ready(function () {
        if (!isMobile()) openSidebar();
      });

      // Desktop chevron toggle
      $(document).on('click', '#sidebar-toggle-btn', function () {
        const isOpen = $('#desktop-sidebar').hasClass('translate-x-0');
        if (isOpen) closeSidebar(); else openSidebar();
      });

      // Mobile hamburger toggle
      $(document).on('click', '#mobile-menu-btn', function () {
        const isOpen = $('#desktop-sidebar').hasClass('translate-x-0');
        if (isOpen) closeSidebar(); else openSidebar();
      });

      // Close via overlay tap
      $(document).on('click', '#mobile-overlay', function () {
        closeSidebar();
      });

      // Recalculate margins when crossing the sm breakpoint
      $(window).on('resize', function () {
        if (!isMobile()) {
          $('#mobile-overlay').hide();
          $('#mobile-icon-open').show();
          $('#mobile-icon-close').hide();
          if ($('#desktop-sidebar').hasClass('translate-x-0')) {
            $('#main-content, #page-navbar, #page-footer').removeClass('ml-0').addClass('ml-72');
          }
        } else {
          $('#main-content, #page-navbar, #page-footer').removeClass('ml-72').addClass('ml-0');
        }
      });
    </script>
  </body>
</html>
