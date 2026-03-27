

<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">
  <head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>Omnigrejas - Acesso negado</title>

      <!-- Favicon -->
      <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

      <!-- Library / Plugin Css Build -->
      <link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}">


      <!-- Hope Ui Design System Css -->
      <link rel="stylesheet" href="{{ asset('assets/css/hope-ui.min.css') }}">

      <!-- Custom Css -->
      <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css') }}">

      <!-- Customizer Css -->
      <link rel="stylesheet" href="{{ asset('assets/css/customizer.min.css') }}">

      <!-- RTL Css -->
      <link rel="stylesheet" href="{{ asset('assets/css/rtl.min.css') }}">


  </head>
  <body class=" " data-bs-spy="scroll" data-bs-target="#elements-section" data-bs-offset="0" tabindex="0">
    <!-- loader Start -->
    <div id="loading">
      <div class="loader simple-loader">
          <div class="loader-body">
          </div>
      </div>    </div>
    <!-- loader END -->


      <div class="wrapper">
<script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/1.18.0/TweenMax.min.js"></script>

<div class="gradient">
    <div class="container">
        <img src="assets/images/error/403.png" class="img-fluid mb-4 w-50" alt="">
        <h2 class="mb-0 mt-4 text-white">Acesso Negado - 403</h2>
        <p class="mt-2 text-white">Você não tem permissão para acessar esta página.</p>
        <div class="alert alert-info bg-white bg-opacity-10 border-white border-opacity-25 text-white mt-3">
            <h6 class="alert-heading mb-2"><i class="fas fa-info-circle me-2"></i>Por que isso aconteceu?</h6>
            <ul class="mb-0 small">
                <li>Você pode não ter as permissões necessárias para acessar este recurso</li>
                <li>Sua conta pode estar suspensa ou inativa</li>
                <li>O link pode ter expirado ou ser inválido</li>
                <li>A igreja pode não ter uma assinatura ativa</li>
                <li>Você pode não estar associado a nenhuma igreja</li>
            </ul>
            <hr class="border-white border-opacity-25">
            <p class="mb-0 small"><strong>Soluções possíveis:</strong></p>
            <ul class="mb-2 small">
                <li>Verifique se você está logado com a conta correta</li>
                <li>Entre em contato com o administrador da sua igreja</li>
                <li>Verifique se a assinatura da igreja está ativa</li>
                <li>Caso seja um erro, contate o suporte técnico</li>
            </ul>

        </div>
        <a class="btn bg-white text-info d-inline-flex align-items-center" href="{{ url('/') }}">
            <i class="fas fa-home me-2"></i>Voltar ao Dashboard
        </a>
    </div>
    <div class="box">
        <div class="c xl-circle">
            <div class="c lg-circle">
                <div class="c md-circle">
                    <div class="c sm-circle">
                        <div class="c xs-circle">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
      </div>
    <!-- Library Bundle Script -->
    <script src="{{ asset('assets/js/core/libs.min.js') }}"></script>

    <!-- External Library Bundle Script -->
    <script src="{{ asset('assets/js/core/external.min.js') }}"></script>

    <!-- Widgetchart Script -->
    <script src="{{ asset('assets/js/charts/widgetcharts.js') }}"></script>

    <!-- mapchart Script -->
    <script src="{{ asset('assets/js/charts/vectore-chart.js') }}"></script>
    <script src="{{ asset('assets/js/charts/dashboard.js') }}" ></script>

    <!-- fslightbox Script -->
    <script src="{{ asset('assets/js/plugins/fslightbox.js') }}"></script>

    <!-- Settings Script -->
    <script src="{{ asset('assets/js/plugins/setting.js') }}"></script>

    <!-- Slider-tab Script -->
    <script src="{{ asset('assets/js/plugins/slider-tabs.js') }}"></script>

    <!-- Form Wizard Script -->
    <script src="{{ asset('assets/js/plugins/form-wizard.js') }}"></script>

    <!-- AOS Animation Plugin-->

    <!-- App Script -->
    <script src="{{ asset('assets/js/hope-ui.js') }}" defer></script>


  </body>
</html>
