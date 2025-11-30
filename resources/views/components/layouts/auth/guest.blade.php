<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Iniciar sessão' }}</title>
    @include('components.layouts.head.head')
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen">

     {{-- <div id="loading">
      <div class="loader simple-loader">
          <div class="loader-body">
          </div>
      </div>
    </div> --}}

    <div class="font-sans text-gray-900 antialiased">
        {{ $slot }}
    </div>

    @include('components.layouts.footer.footer')
    <!-- Toast Styles -->
    <!-- Toast Container -->
    <div id="global-toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <!-- Toasts will be added here -->
    </div>

    <link rel="stylesheet" href="{{ asset('system/css/toast.css') }}">
    @livewireScripts
    @stack('scripts')

    <!-- CSRF Token Handler -->
    <script src="{{ asset('system/js/csrf-handler.js') }}"></script>

</body>
</html>
