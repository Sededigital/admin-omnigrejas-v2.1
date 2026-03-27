<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

    <?php echo $__env->make('components.layouts.head.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <body>

        <?php
            $superAdmin = Auth::user()?->isSuperAdmin();
            $admin = Auth::user()?->isIgrejaAdmin();
            $rootman = Auth::user()?->isRoot();
            $membro = Auth::user()?->isMembro();
            $diacono = Auth::user()?->isDiacono();
            $obreiro = Auth::user()?->isObreiro();

        ?>

        

        <?php if($superAdmin): ?>

        

        <?php echo $__env->make('components.layouts.sidebar.super-admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php elseif($admin || $membro || $diacono || $obreiro): ?>
         

        <?php echo $__env->make('components.layouts.sidebar.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php elseif($rootman): ?>
        <?php echo $__env->make('components.layouts.sidebar.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php endif; ?>

        
        <main class="main-content">
            
            <div class="position-relative iq-banner">
                <?php echo $__env->make('components.layouts.nav.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            

            
            <div class="conatiner-fluid py-0 m-3 position-relative" >

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
        

        
        <?php echo $__env->make('components.layouts.footer.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        

        <!-- Toast Container -->
        <div id="global-toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
            <!-- Toasts will be added here -->
        </div>

        <!-- Toast Styles -->
        <link rel="stylesheet" href="<?php echo e(asset('system/css/toast.css')); ?>">
        
        <?php echo $__env->make('sweetalert2::index', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

        <?php echo $__env->yieldPushContent('scripts'); ?>

        <!-- CSRF Token Handler -->
        <script src="<?php echo e(asset('system/js/csrf-handler.js')); ?>"></script>


    </body>
</html>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/components/layouts/app.blade.php ENDPATH**/ ?>