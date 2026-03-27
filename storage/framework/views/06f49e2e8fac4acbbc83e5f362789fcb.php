<?php
    $role = Auth::user()?->role;

    $badgeClasses = match($role) {
        'root', 'super_admin' => 'badge bg-danger text-white',
        'admin', 'pastor'     => 'badge bg-info text-light text-white',
        default               => 'badge bg-secondary text-white'
    };
?>

<nav class="nav navbar navbar-expand-xl bg-light iq-navbar">
    <div class="container-fluid navbar-inner custom-navbar-inner">
      <button data-trigger="navbar_main" class="d-xl-none btn bg-info text-light rounded-pill p-1 pt-0" type="button">
        <svg class="icon-20" width="20px" viewBox="0 0 24 24">
          <path fill="currentColor" d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"></path>
        </svg>
      </button>
      <a href="<?php echo e(url('/')); ?>" class="logo-center navbar-horizontal-brand navbar-brand col-md-2 col-lg-2 col-xl-2 col-xxl-3  ms-0">
        <!--Logo start-->

             <!--Logo start-->
             <div class="logo-main">
                <div class="logo-normal">
                    <img src="<?php echo e(asset('system/img/logo-system/icon-admin-blue.png')); ?>" class="icon-40" alt=""   width="400" height="330">
                </div>
                <div class="logo-mini">
                    <img src="<?php echo e(asset('system/img/logo-system/icon-admin-blue.png')); ?>" class="icon-40" alt=""   width="400" height="330">
                </div>
            </div>
            <!--logo End-->




            <!--logo End-->
            <h4 class="logo-title fw-bold">
                <span class="text-info">Omn</span><span class="text-info">Igrejas</span>
            </h4>
      </a>
      <!-- Horizontal Menu Start -->
      <nav id="navbar_main" class="mobile-offcanvas nav navbar bg-light navbar-expand-xl hover-nav horizontal-nav mx-md-auto">
         <div class="container-fluid">
            <div class="offcanvas-header px-0">
               <a href="<?php echo e(url('/')); ?>" class="navbar-brand ms-3">
                   <!--Logo start-->
                <div class="logo-main">
                    <div class="logo-normal">
                        <img src="<?php echo e(asset('system/img/logo-system/icon-admin-blue.png')); ?>" class="icon-40" alt=""   width="400" height="330">
                    </div>
                    <div class="logo-mini">
                        <img src="<?php echo e(asset('system/img/logo-system/icon-admin-blue.png')); ?>" class="icon-40" alt=""   width="400" height="330">
                    </div>
                </div>
                <!--logo End-->


                <!--logo End-->
                <h4 class="logo-title fw-bold">
                    <span class="text-info">Omn</span><span class="text-success">Igrejas</span>
                </h4>
               </a>
               <button class="btn-close float-end"></button>
            </div>
            <ul class="navbar-nav">
               <li class="nav-item"><a class="nav-link"
                     href="<?php echo e(route('ecommerce.home')); ?>"  wire:navigate wire:current="active"> Home </a></li>
               <li class="nav-item"><a class="nav-link"
                     href="<?php echo e(route('ecommerce.churches')); ?>" wire:navigate wire:current="active"> Igrejas </a></li>
               <li class="nav-item"><a class="nav-link"
                     href="<?php echo e(route('ecommerce.alliance')); ?>" wire:navigate wire:current="active"><span class="item-name">Alianças</span></a></li>
               <li class="nav-item"><a class="nav-link "
                     href="<?php echo e(route('ecommerce.subscription.upgrade')); ?>" wire:navigate wire:current="active"> Loja </a></li>
                <?php if(auth()->guard()->check()): ?>
                  <?php if(!Auth::user()->hasTrialAccess() || Auth::user()->getIgreja()->pagamentosAssinaturaIgreja()->exists()): ?>
                  <li class="nav-item"><a class="nav-link"
                    href="<?php echo e(route('ecommerce.payment.assignature')); ?>" wire:navigate wire:current="active"> Assinaturas</a>
                  </li>
                  <?php endif; ?>
                <?php endif; ?>
               <li class="nav-item"><a class="nav-link"
                     href="<?php echo e(route('ecommerce.contact')); ?>" wire:navigate wire:current="active"> Contactos</a>
                </li>
                <li class="nav-item"><a class="nav-link"
                    href="<?php echo e(route('ecommerce.who.we')); ?>" wire:navigate wire:current="active"> Quem somos</a>
                </li>

                <?php if(auth()->guard()->check()): ?>
                  <?php if(Auth::user()->hasTrialAccess()): ?>
                    <li class="nav-item"><a class="nav-link fw-bold text-danger"
                      href="<?php echo e(route('ecommerce.trial.expirando')); ?>" wire:navigate  wire:current="active"> Trial</a>
                    </li>
                  <?php endif; ?>
                <?php endif; ?>

                <?php if(auth()->guard()->check()): ?>
                <li class="nav-item"><a class="nav-link"
                  href="<?php echo e(url('/')); ?>" > Dashboard</a>
                </li>
                <?php endif; ?>

               <?php if(auth()->guard()->guest()): ?>
               <li class="nav-item"><a class="nav-link btn bg-info text-light text-light border-2 px-4 py-1 mt-1"
                    href="<?php echo e(route('login')); ?>"> Entrar</a>
                </li>
               <?php endif; ?>

            </ul>
         </div> <!-- container-fluid.// -->
      </nav>
          <!-- Sidebar Menu End -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon">
            <span class="navbar-toggler-bar bar1 mt-2"></span>
            <span class="navbar-toggler-bar bar2"></span>
            <span class="navbar-toggler-bar bar3"></span>
          </span>
        </button>
        <div class="collapse navbar-collapse  bg-light" id="navbarSupportedContent">
          <?php if(auth()->guard()->check()): ?>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

              <li class="nav-item dropdown">
                <a class="nav-link py-0 d-flex align-items-center" href="#" id="navbarDropdown" role="button"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <div class="btn btn-icon rounded-circle" style="width: 40px; height: 40px; padding: 0;">
                      <span class="btn-inner d-block w-100 h-100">
                          <?php if(Auth::user()?->photo_url): ?>
                              <img src="<?php echo e(Storage::disk('supabase')->url(Auth::user()->photo_url)); ?>"
                                  class="w-100 h-100 rounded-circle"
                                  alt="Foto de perfil"
                                  style="object-fit: cover; display: block;">
                          <?php else: ?>
                              <span class="w-100 h-100 rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                    style="background-color: rgba(36, 115, 157, 0.912); font-size: 14px;">
                                  <?php echo e(mb_strtoupper(mb_substr(Auth::user()?->name, 0, 2, 'UTF-8'), 'UTF-8')); ?>

                              </span>
                          <?php endif; ?>
                      </span>
                  </div>
                  <div class="caption ms-3 d-none d-md-block ">
                      <h6 class="mb-0 caption-title"><?php echo e(Str::limit(Auth::user()?->name, 25, '|') ?? ''); ?></h6>
                      <p class="mb-0 caption-sub-title">
                          <span class="<?php echo e($badgeClasses); ?>" style="border-radius: 4px;">
                              <?php echo e(strtoupper($role ?? '')); ?>

                          </span>
                      </p>
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                  <li><a class="dropdown-item" href="<?php echo e(url('/')); ?>" >Dashboard</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <form method="POST" action="<?php echo e(route('logout')); ?>">
                      <?php echo csrf_field(); ?>
                  <li><button  type="submit" class="dropdown-item" >Logout</button></li>
                  </form>
              </ul>
              </li>
            </ul>
          <?php endif; ?>

        </div>

    </div>
  </nav>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/components/layouts/nav-subscription.blade.php ENDPATH**/ ?>