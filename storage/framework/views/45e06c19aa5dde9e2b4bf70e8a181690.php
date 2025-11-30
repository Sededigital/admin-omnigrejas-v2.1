<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'Iniciar sessão'); ?></title>
    <?php echo $__env->make('components.layouts.head.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body class="bg-gray-100 min-h-screen">

     

    <div class="font-sans text-gray-900 antialiased">
        <?php echo e($slot); ?>

    </div>

    <?php echo $__env->make('components.layouts.footer.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <!-- Toast Styles -->
    <!-- Toast Container -->
    <div id="global-toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <!-- Toasts will be added here -->
    </div>

    <link rel="stylesheet" href="<?php echo e(asset('system/css/toast.css')); ?>">
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <!-- CSRF Token Handler -->
    <script src="<?php echo e(asset('system/js/csrf-handler.js')); ?>"></script>

</body>
</html>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/components/layouts/auth/guest.blade.php ENDPATH**/ ?>