<?php if(auth()->guard()->check()): ?>
    <?php echo $__env->make('components.layouts.footer.first-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>




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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" once-navigate-onc></script>


<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/components/layouts/footer/footer.blade.php ENDPATH**/ ?>