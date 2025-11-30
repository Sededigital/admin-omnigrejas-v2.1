<div>
   <div class="wrapper">
      <section class="login-content">
         <div class="row m-0 align-items-center bg-white h-100">
               <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
               <img src="../../assets/images/auth/05.png" class="img-fluid gradient-main animated-scaleX" alt="images">
            </div>
            <div class="col-md-6">
               <div class="row justify-content-center">
                  <div class="col-md-10">
                     <div class="card card-transparent auth-card shadow-none d-flex justify-content-center mb-0">
                        <div class="card-body">
                             @if (session('status'))
                                <div class="alert alert-bottom alert-{{ session('status_type', 'success') }} alert-dismissible fade show " role="alert">
                                    <span> {{ session('status') }}</span>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                @endif

                           <a href="{{ route('login') }}" class="navbar-brand d-flex align-items-center mb-3">

                              <!--Logo start-->
                           <a href="{{ url('/') }}" wire:navigate class="navbar-brand d-flex align-items-center mb-3" style="gap:0;">
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
                           <h2 class="mb-2 text-center">Atualizar nova senha</h2>

                              <div class="row">

                                  <div class="col-lg-12">
                                    <div class="form-group">
                                       <label for="email" class="form-label">Email</label>
                                       <input type="email" autocomplete="new-password"  class="form-control @error('email') is-invalid @enderror" id="email" wire:model="email"  aria-describedby="email" placeholder="seu email">
                                        @error('email') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                                    </div>
                                 </div>
                                 <div class="col-lg-12">
                                    <div class="form-group">
                                       <label for="password" class="form-label">Nova senha</label>
                                       <input type="password"  autocomplete="new-password"  class="form-control  @error('password') is-invalid @enderror" id="password" wire:model="password" aria-describedby="password" placeholder="*****">
                                        @error('password') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                                    </div>
                                 </div>
                                  <div class="col-lg-12">
                                    <div class="form-group">
                                       <label for="password" class="form-label">Confirme a senha</label>
                                       <input type="password"  autocomplete="new-password"  class="form-control  @error('password_confirmacao') is-invalid @enderror" id="password_confirmacao" wire:model="password_confirmacao" aria-describedby="password" placeholder="*****">
                                        @error('password_confirmacao') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                                    </div>
                                 </div>

                              </div>
                              <div class="d-flex justify-content-center">
                                 <button type="button" wire:click='resetPassword' class="btn btn-primary bg-primary border-0">
                                     <span wire:loading wire:target="resetPassword" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Atualizar
                                 </button>
                              </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="sign-bg sign-bg-right">
                  <svg width="280" height="230" viewBox="0 0 421 359" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <g opacity="0.05">
                        <rect x="-15.0845" y="154.773" width="543" height="77.5714" rx="38.7857" transform="rotate(-45 -15.0845 154.773)" fill="#3A57E8"/>
                        <rect x="149.47" y="319.328" width="543" height="77.5714" rx="38.7857" transform="rotate(-45 149.47 319.328)" fill="#3A57E8"/>
                        <rect x="203.936" y="99.543" width="310.286" height="77.5714" rx="38.7857" transform="rotate(45 203.936 99.543)" fill="#3A57E8"/>
                        <rect x="204.316" y="-229.172" width="543" height="77.5714" rx="38.7857" transform="rotate(45 204.316 -229.172)" fill="#3A57E8"/>
                     </g>
                  </svg>
               </div>
            </div>
         </div>
      </section>
      </div>
</div>
