<div>
     <div class="wrapper">
      <section class="login-content">
         <div class="row m-0 align-items-center bg-white vh-100">
            <div class="col-md-6 d-md-block d-none bg-info text-light p-0 mt-n1 vh-100 overflow-hidden">
               <img src="../../assets/images/auth/02.png" class="img-fluid gradient-main animated-scaleX" alt="images">
            </div>
            <div class="col-md-6 p-0">
               <div class="card card-transparent auth-card shadow-none d-flex justify-content-center mb-0">
                  <div class="card-body z-3">

                    @if (session('status'))
                        <div class="alert alert-left alert-success alert-dismissible fade show mb-3" role="alert">
                            <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                                <use xlink:href="#check-circle-fill" />
                            </svg>
                            <div>
                               {{ session('status') }}
                            </div>
                        </div>
                    @endif

                    <a href="" class="navbar-brand d-flex align-items-center mb-3" style="gap:0;">
                        <!--Logo start-->
                        <div class="logo-main">
                            <div class="logo-normal">
                                <img src="{{ asset('system/img/logo-system/icon-admin-blue.png') }}"
                                    alt="logo"
                                    class="img-fluid opacity-75"
                                    width="400" height="330"
                                    style="max-width:140px; max-height:140px;">
                            </div>
                            <div class="logo-mini">
                                <img src="{{ asset('system/img/logo-system/icon-admin-blue.png') }}"
                                    alt="logo"
                                    class="img-fluid opacity-75"
                                    width="400" height="330"
                                    style="max-width:140px; max-height:140px;">
                            </div>
                        </div>
                        <!--logo End-->

                      
                    </a>
                     <h2 class="mb-2">Redefinir senha</h2>
                     <p>Digite seu endereço de e-mail e lhe enviaremos um e-mail com instruções para redefinir sua senha..</p>
                        <div class="row">
                           <div class="col-lg-12">
                              <div class="floating-label form-group">
                                 <label for="email" class="form-label">Email</label>
                                 <input type="email" autocomplete="new-password" class="form-control @error('email') is-invalid @enderror" id="email" aria-describedby="email" placeholder="Sua senha" wire:model="email" autofocus>
                                 @error('email') <span class="error-text text-danger">{{ $message }}</span> @enderror
                              </div>
                           </div>
                        </div>
                             <div class="d-inline-block w-100">
                           <button wire:click="sendResetLink" class="btn bg-info text-light bg-info text-light border-0"  wire:loading.attr="disabled">
                             <span wire:loading wire:target="sendResetLink" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Enviar o Email</button>
                        </div>
                  </div>


               </div>

            </div>
         </div>
      </section>
      </div>
</div>
