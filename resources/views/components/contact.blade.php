@php
    $waNumber = env('WHATSAPP_NUMBER', '6282295037691');
@endphp

<a href="https://wa.me/{{ $waNumber }}"
   target="_blank"
   class="btn btn-success btn-lg rounded-circle whatsapp-float"
   x-on:click.stop="$dispatch('skip-loading')">

    <i class="bi bi-whatsapp" style="font-size: 1.8rem;"></i>
</a>


<style>
    .whatsapp-float {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1050;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
        background-color: #25D366 !important;
        border-radius: 50px;
    }

    .whatsapp-float:hover {
        transform: scale(1.1);
        transition: all 0.3s ease-in-out;
    }

    .whatsapp-float i {
        color: #fff !important;
    }
</style>

    <script>
    document.addEventListener('skip-loading', () => {
        const loader = document.getElementById('global-loading');
        if (loader) loader.classList.add('hidden');
    });
    </script>

