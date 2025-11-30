<div>
     <div class="wrapper">
      <section class="login-content">
         <div class="row m-0 align-items-center bg-white vh-100">
            <div class="col-md-6">
               <div class="row justify-content-center">
                  <div class="col-md-10">
                     <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
                        <div class="card-body z-3 px-md-0 px-lg-4">
                           <a href="<?php echo e(url('/login')); ?>" class="navbar-brand d-flex align-items-center mb-3">

                              <!--Logo start-->
                              <div class="logo-main">
                                 <div class="logo-mini">

                                        <img src="<?php echo e(asset('system/img/logo-system/icon.png')); ?>" alt="logo">

                                    </div>

                                  <div class="logo-mini">
                                     <img src="<?php echo e(asset('system/img/logo-system/icon.png')); ?>" alt="logo">
                                  </div>

                              </div>
                              <!--logo End-->

                              <h1 class="logo-title fw-bold">
                                <span class="text-primary">Omn</span><span class="text-success">Igrejas</span>
                                </h1>
                           </a>
                           <h2 class="mb-2 text-center">Entrar</h2>
                           <p class="text-center">Faça login para permanecer conectado</p>
                           <!--[if BLOCK]><![endif]--><?php if(session()->has('login_error')): ?>
                                <div class="alert alert-warning">
                                    <?php echo e(session('login_error')); ?>

                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                           <form wire:submit.prevent='login' id="login-form">
                              <div class="row">
                                 <div class="col-lg-12">
                                    <div class="form-group">
                                       <label for="email" class="form-label">Email / telefone</label>
                                       <input type="text"  autocomplete="new-password"  autocomplete="new-password" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" wire:model="email"  aria-describedby="email" placeholder="nome@omnigreja.com">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                 </div>
                                 <div class="col-lg-12">
                                    <div class="form-group">
                                       <label for="password" class="form-label">Palavra-passe</label>
                                       <input type="password" autocomplete="new-password"  class="form-control  <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" autocomplete="new-password" id="password" wire:model="password" aria-describedby="password" placeholder="*****">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                 </div>
                                 <div class="col-lg-12 d-flex justify-content-between">
                                    <div class="form-check mb-3">
                                       <input type="checkbox" class="form-check-input" id="customCheck1" wire:model="remember">
                                       <label class="form-check-label" for="customCheck1">Lembra-se</label>
                                    </div>
                                    <a href="<?php echo e(route('password.request')); ?>" wire:navigate >Esqueceu palavra-passe?</a>
                                 </div>
                              </div>
                              <div class="d-flex justify-content-center">
                                <button id="login-button" class="btn btn-primary bg-primary border-0 d-flex align-items-center" type="submit" wire:loading.attr="disabled" wire:loading.class="disabled" <?php if($loginSuccessful): ?> disabled <?php endif; ?>>
                                    <span wire:loading wire:target="login" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                   Entrar
                                </button>

                              </div>
                              
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="sign-bg">
               <img src="<?php echo e(asset('system/img/logo-system/icon.png')); ?>"
                alt="logo"
                class="img-fluid opacity-75"
                width="400" height="330"
                style="max-width: 200px; max-height: 200px;" >

               </div>
            </div>
            <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden position-relative">
               <img src="<?php echo e(asset('assets/images/auth/01.png')); ?>" class="img-fluid gradient-main animated-scaleX" alt="images">
               <a href="<?php echo e(route('ecommerce.home')); ?>" class="btn btn-light position-absolute top-50 start-50 translate-middle fw-bold" style="z-index: 10;">E-commerce</a>
            </div>
         </div>
      </section>
      </div>

   <script>
       document.addEventListener('livewire:navigated', function () {
           // Re-inicializar listeners após navegação SPA
           initLoginListeners();
       });

       document.addEventListener('livewire:updated', function () {
           // Re-inicializar listeners após atualização do componente
           initLoginListeners();
       });

       function initLoginListeners() {
           // Listener para quando credenciais são válidas
           Livewire.on('login-credentials-valid', () => {
               showLoginSpinner();
           });

           // Listener para quando login falha
           Livewire.on('login-failed', () => {
               hideLoginSpinner();
           });
       }

       function showLoginSpinner() {
           const button = document.getElementById('login-button');
           const spinner = button.querySelector('.spinner-border');

           // Alterar texto do botão
           button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Entrando...';

           // Desabilitar botão
           button.disabled = true;
           button.classList.add('disabled');
       }

       function hideLoginSpinner() {
           const button = document.getElementById('login-button');

           // Restaurar botão original
           button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Entrar';

           // Habilitar botão
           button.disabled = false;
           button.classList.remove('disabled');
       }

       // Inicializar listeners na primeira carga
       initLoginListeners();
   </script>
</div>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/auth/login.blade.php ENDPATH**/ ?>