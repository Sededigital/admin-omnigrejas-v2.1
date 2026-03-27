<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php echo $__env->make('components.layouts.head.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
   <title><?php echo e($title ?? 'Upgrade de Assinatura - OMNIGREJAS'); ?></title>


   <!-- Google Fonts: Poppins para um toque moderno -->
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
   <!-- Bootstrap 5 + Font Awesome -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://kit.fontawesome.com/a2b2c1a5d9.js" crossorigin="anonymous"></script>
   <!-- Animate.css para animações suaves -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        /* Container de botões */
        .swal-equal-buttons .swal2-actions {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 1rem !important;
            width: 100% !important;
        }

        /* Botões personalizados */
        .swal-equal-buttons .swal-btn {
            flex: 1 1 0 !important;             /* 👈 distribui igualmente */
            min-width: 140px !important;
            max-width: 180px !important;
            padding: 0.65rem 1.2rem !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            border-radius: 0.4rem !important;
        }

        /* Evita que SweetAlert2 aplique width automática */
        .swal-equal-buttons .swal2-confirm,
        .swal-equal-buttons .swal2-cancel {
            width: auto !important;
        }

        /* Modal largo para spinner */
        .swal-wide-modal {
            width: 500px !important;
        }

    </style>
   <!-- CSS Personalizado -->
   <link rel="stylesheet" href="<?php echo e(asset('system/css/subscription-church.css')); ?>">

   <!-- Livewire Styles -->
   <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>


</head>

<body class="boxed-fancy">
   <div class="boxed-inner">
    <main class="main-content">
        <?php echo $__env->make('components.layouts.nav-subscription', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="container-fluid content-inner pb-0">

            <?php echo e($slot); ?>

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

    </main>
   </div>
   <!-- Livewire Scripts -->


    <!-- Toast Container -->
    <div id="global-toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <!-- Toasts will be added here -->
    </div>


    <!-- Toast Styles -->
    <link rel="stylesheet" href="<?php echo e(asset('system/css/toast.css')); ?>">
    <?php echo $__env->make('sweetalert2::index', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

     <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('components.alert-manager');

$__html = app('livewire')->mount($__name, $__params, 'lw-310083055-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
     

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>




   <script>
    if (!window.livewireScriptConfig || !window.livewireScriptConfig.uri) {
        window.livewireScriptConfig = { uri: '/livewire/update' };
    }
    </script>

   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"  data-navigate-once ></script>


   


   <script src="<?php echo e(asset('system/js/js_pages.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/core/libs.min.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/core/external.min.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/charts/widgetcharts.js')); ?>" data-navigate-once></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   
   <script src="<?php echo e(asset('assets/js/charts/vectore-chart.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/charts/dashboard.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/plugins/fslightbox.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/plugins/setting.js')); ?>" ></script>
   <script src="<?php echo e(asset('assets/js/plugins/slider-tabs.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/plugins/form-wizard.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/vendor/aos/dist/aos.js')); ?>" data-navigate-once></script>

   <script src="<?php echo e(asset('assets/js/hope-ui.js')); ?>"  data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/font-define.js')); ?>" data-navigate-once ></script>
   <script src="https://cdn.jsdelivr.net/npm/flatpickr" once-navigate-once></script>
</body>
</html>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/components/layouts/subscription.blade.php ENDPATH**/ ?>