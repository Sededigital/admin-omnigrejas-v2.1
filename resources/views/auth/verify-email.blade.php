<div>
 <div class="wrapper">
      <section class="login-content">
         <div class="row m-0 align-items-center bg-white vh-100">
            <div class="col-md-6 p-0">
               <div class="card card-transparent auth-card shadow-none d-flex justify-content-center mb-0">
                  <div class="card-body">
                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-left alert-success alert-dismissible fade show mb-3" role="alert">
                            <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                                <use xlink:href="#check-circle-fill" />
                            </svg>
                            <div>
                               <strong>Email enviado!</strong> Um novo link de verificação foi enviado para <strong>{{ Auth::user()?->email }}</strong>
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

                        <h2 class="logo-title fw-bold m-0"
                            style="font-size:60px; line-height:1; margin-left:-22px;">
                            <span class="text-primary">mn</span><span class="text-success">Igrejas</span>
                        </h2>
                    </a>

                        <h2 class="mt-3 mb-2 display-6">Verificar Email</h2>
                        <p class="cnf-mail mb-1 h6">Verifique seu endereço de email para continuar</p>
                        <p>
                            <span class="fw-bold">Email atual</span>:

                            <span class="text-danger fw-bold">
                                {{ Auth::user()?->email }}
                            </span>
                            <br>
                            Se você não recebeu o email, clique no botão abaixo para solicitar um novo.
                        </p>
                        <div class="d-inline-block w-100">
                           <button wire:click="resend" class="btn btn-primary bg-primary border-0"  wire:loading.attr="disabled">
                             <span wire:loading wire:target="resend" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Reenviar o Email de verificação</button>
                        </div>
                  </div>
               </div>
            </div>
            <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
               <img src="../../assets/images/auth/03.png" class="img-fluid gradient-main animated-scaleX" alt="images">
            </div>
         </div>
      </section>
      </div>
</div>
