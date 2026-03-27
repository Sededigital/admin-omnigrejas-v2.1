<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

    @include('components.layouts.head.head')

    @livewireStyles
    <body>

        @php
            $superAdmin = Auth::user()?->isSuperAdmin();
            $admin = Auth::user()?->isIgrejaAdmin();
            $rootman = Auth::user()?->isRoot();
            $membro = Auth::user()?->isMembro();
            $diacono = Auth::user()?->isDiacono();
            $obreiro = Auth::user()?->isObreiro();

        @endphp

        {{-- START SIDEBAR --}}

        @if($superAdmin)

        {{-- SIDEBAR SUPER ADMIN --}}

        @include('components.layouts.sidebar.super-admin-sidebar')

        @elseif($admin || $membro || $diacono || $obreiro)
         {{-- SIDEBAR ADMIN/IGREJA --}}

        @include('components.layouts.sidebar.admin-sidebar')

        @elseif($rootman)
        @include('components.layouts.sidebar.sidebar')

        {{-- END SIDEBAR --}}
        @endif

        {{-- START MAIN CONTENT --}}
        <main class="main-content">
            {{-- START Nav --}}
            <div class="position-relative iq-banner">
                @include('components.layouts.nav.nav')
            </div>
            {{-- END NAV --}}

            {{-- START SLOT --}}
            <div class="conatiner-fluid py-0 m-3 position-relative" >

                {{ $slot }}

                 <div id="spa-loader" class="spa-loader d-none">
                    <div class="dot-spinner">
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                    </div>
                </div>
            </div>
            {{-- END SLOT --}}
        </main>
        {{-- END MAIN --}}

        {{-- START FOOTER --}}
        @include('components.layouts.footer.footer')
        {{-- END FOOTER --}}

        <!-- Toast Container -->
        <div id="global-toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
            <!-- Toasts will be added here -->
        </div>

        <!-- Toast Styles -->
        <link rel="stylesheet" href="{{ asset('system/css/toast.css') }}">

        @livewireScripts
        @stack('scripts')

        <!-- CSRF Token Handler -->
        <script src="{{ asset('system/js/csrf-handler.js') }}"></script>


    </body>
</html>
