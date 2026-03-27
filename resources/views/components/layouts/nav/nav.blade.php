@php
    $role = Auth::user()?->role;

    $badgeClasses = match($role) {
        'root', 'super_admin' => 'badge bg-danger text-white',
        'admin', 'pastor'     => 'badge bg-info text-light text-white',
        default               => 'badge bg-secondary text-white'
    };

    use App\Helpers\RBAC\PermissionHelper;
    use App\Helpers\Billings\ResourceHelper;
    
    $permissionHelper = new PermissionHelper(Auth::user());
    $resourceHelper = new ResourceHelper();

@endphp

<div class="position-relative ">
    {{-- usar aqui:  navs-sticky  --}}
    <nav class="nav navbar  navbar-expand-xl navbar-light iq-navbar">
        <div class="container-fluid navbar-inner">
            <a href="#" class="navbar-brand">
                <!--Logo start-->
                <div class="logo-main">
                    <div class="logo-normal">
                        <svg class="text-info icon-30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2" transform="rotate(-45 -0.757324 19.2427)" fill="currentColor"/>
                            <rect x="7.72803" y="27.728" width="28" height="4" rx="2" transform="rotate(-45 7.72803 27.728)" fill="currentColor"/>
                            <rect x="10.5366" y="16.3945" width="16" height="4" rx="2" transform="rotate(45 10.5366 16.3945)" fill="currentColor"/>
                            <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2" transform="rotate(45 10.5562 -0.556152)" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="logo-mini">
                        <svg class="text-info icon-30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2" transform="rotate(-45 -0.757324 19.2427)" fill="currentColor"/>
                            <rect x="7.72803" y="27.728" width="28" height="4" rx="2" transform="rotate(-45 7.72803 27.728)" fill="currentColor"/>
                            <rect x="10.5366" y="16.3945" width="16" height="4" rx="2" transform="rotate(45 10.5366 16.3945)" fill="currentColor"/>
                            <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2" transform="rotate(45 10.5562 -0.556152)" fill="currentColor"/>
                        </svg>
                    </div>
                </div>
                <!--logo End-->
            </a>
            <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
                <i class="icon">
                <svg  width="20px" class="icon-20" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
                </svg>
                </i>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon">
                    <span class="mt-2 navbar-toggler-bar bar1"></span>
                    <span class="navbar-toggler-bar bar2"></span>
                    <span class="navbar-toggler-bar bar3"></span>
                </span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                {{-- ADMIN IGREJAS --}}
                @if (Auth::user()?->isIgrejaAdmin())
                  
                        
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @if(($permissionHelper->hasPermission('gerenciar_posts') || $permissionHelper->hasPermission('gerenciar_comunicacoes')
                    || $permissionHelper->hasPermission('gerenciar_chats') || $permissionHelper->hasPermission('gerenciar_chats_igreja')
                    || $permissionHelper->hasPermission('gerenciar_mensagens_privadas'))
                    {{-- Verificar se recursos estão disponíveis no pacote --}}
                    && $resourceHelper->userCanAccessSection(['gerenciar_posts', 'gerenciar_comunicacoes', 'gerenciar_chats', 'gerenciar_chats_igreja', 'gerenciar_mensagens_privadas']))

                    @if($permissionHelper->hasPermission('gerenciar_comunicacoes') || $resourceHelper->userCanAccessResource('gerenciar_comunicacoes'))
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="{{ route('churches.community-nav') }}" wire:navigate wire:current='active'>
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.67 13.13C18.04 14.06 19 15.32 19 17V20H23V17C23 14.82 19.43 13.53 16.67 13.13Z" fill="currentColor"></path>
                                <path d="M15 12C17.21 12 19 10.21 19 8C19 5.79 17.21 4 15 4C14.53 4 14.09 4.1 13.67 4.24C14.5 5.27 15 6.58 15 8C15 9.42 14.5 10.73 13.67 11.76C14.09 11.9 14.53 12 15 12Z" fill="currentColor"></path>
                                <path d="M9 12C11.21 12 13 10.21 13 8C13 5.79 11.21 4 9 4C6.79 4 5 5.79 5 8C5 10.21 6.79 12 9 12ZM9 6C10.1 6 11 6.9 11 8C11 9.1 10.1 10 9 10C7.9 10 7 9.1 7 8C7 6.9 7.9 6 9 6Z" fill="currentColor"></path>
                                <path d="M9 13C6.33 13 1 14.34 1 17V20H17V17C17 14.34 11.67 13 9 13ZM3 18V17.01C3.2 16.29 6.3 15 9 15C11.7 15 14.8 16.29 15 17V18H3Z" fill="currentColor"></path>
                            </svg>
                            <span class="nav-text ms-2">Comunidade</span>
                        </a>
                    </li>
                    @endif
                    @if (($permissionHelper->hasPermission('gerenciar_chats') || $permissionHelper->hasPermission('gerenciar_chats_igreja'))
                            && ($resourceHelper->userCanAccessResource('gerenciar_chats') || $resourceHelper->userCanAccessResource('gerenciar_chats_igreja')))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPages" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2Z" fill="currentColor"></path>
                                    <path opacity="0.4" d="M6 9H18V11H6V9ZM6 12H16V14H6V12ZM6 6H18V8H6V6Z" fill="currentColor"></path>
                                </svg>
                                <span class="nav-text ms-2">Chats</span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownPages">
                                @if(($permissionHelper->hasPermission('gerenciar_chats') || $permissionHelper->hasPermission('gerenciar_chats_igreja'))
                                && ($resourceHelper->userCanAccessResource('gerenciar_chats') || $resourceHelper->userCanAccessResource('gerenciar_chats_igreja')))
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('churches.chat-nav', []) }}" wire:navigate>
                                            Igreja
                                        </a>
                                    </li>
                                @endif
                                
                                @if($permissionHelper->hasPermission('gerenciar_mensagens_privadas') && $resourceHelper->userCanAccessResource('gerenciar_mensagens_privadas'))
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('churches.private-chat-nav') }}" wire:navigate>
                                        Privado
                                    </a>
                                </li>
                                @endif
                                
                            </ul>
                        </li>
                    @endif

                    @endif

                    
                    {{-- SELETOR DE IGREJA --}}
                    @if(Auth::user()?->isIgrejaAdmin())
                    <li class="nav-item">
                        @livewire('navbar.select-church')
                    </li>
                    @endif
                </ul>

                @endif

                <ul class="mb-2 navbar-nav ms-auto align-items-center navbar-list mb-lg-0">
                    <li class="nav-item d-flex align-items-center ms-3 font-size-toggle">

                        <label for="font-size-sm" class="btn btn-border bg-info text-light text-light border-0 btn-icon btn-sm" data-size="small" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Tamanho de letra 14px">
                            <span class="mb-0 h6" style="color: inherit !important;">A</span>
                        </label>
                        <label for="font-size-md" class="btn btn-border text-light border-0 btn-icon" data-size="medium" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Tamanho de letra 16px">
                            <span class="mb-0 h4" style="color: inherit !important;">A</span>
                        </label>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('ecommerce.home') }}" class="nav-link text-warning" wire:navigate title="Loja Omnigrejas">
                            <i class="fas fa-shopping-cart" style="font-size: 20px;"></i> 
                            </a>
                    </li>
                    
                    <li class="nav-item dropdown" id="itemdropdown1">

                        <a class="py-0 nav-link d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                         <div class="btn btn-icon rounded-circle" style="width: 40px; height: 40px; padding: 0;">
                               <span class="btn-inner d-block w-100 h-100">
                                   @if(Auth::user()?->photo_url)
                                       <img src="{{ Storage::disk('supabase')->url(Auth::user()->photo_url) }}"
                                            class="w-100 h-100 rounded-circle"
                                            alt="Foto de perfil"
                                            style="object-fit: cover; display: block;">
                                   @else
                                       <span class="w-100 h-100 rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                             style="background-color: rgba(170, 169, 72, 0.878); font-size: 14px;">
                                           {{ mb_strtoupper(mb_substr(Auth::user()?->name, 0, 2, 'UTF-8'), 'UTF-8') }}
                                       </span>
                                   @endif
                               </span>
                           </div>
                        <div class="caption ms-3 d-none d-md-block ">
                            <h6 class="mb-0 caption-title">{{ Str::limit(Auth::user()?->name, 25, '|') ?? '' }}</h6>
                            <p class="mb-0 caption-sub-title">
                                <span class="{{ $badgeClasses }}" style="border-radius: 4px;">
                                    {{ strtoupper($role ?? '') }}
                                </span>
                            </p>
                        </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ url('/profile') }}" wire:navigate >Perfil</a></li>
                            <li><a class="dropdown-item" href="{{ url('user/two-factor-authentication') }}" wire:navigate >Definições</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <form method="POST" action="{{ route('logout') }}">
                                 @csrf
                             <li><button  type="submit" class="dropdown-item" >Logout</button></li>
                            </form>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Script otimizado para Livewire 3 e SPA - Navbar -->
    <script>
        // Declarar variável global para evitar redeclaração
        if (typeof window.navbarCheckInterval === 'undefined') {
            window.navbarCheckInterval = null;
        }

        // Função para verificar se elementos da navbar existem
        function checkNavbarElements() {
            const navbarImg = document.querySelector('.navbar .btn-icon img');
            const navbarContainer = document.querySelector('.navbar .btn-icon .btn-inner');

            return { navbarImg, navbarContainer };
        }

        // Inicialização para Livewire 3 e SPA
        document.addEventListener('livewire:navigated', function() {
            checkNavbarElements();
        });

        // Fallback para carregamento inicial (não SPA)
        document.addEventListener('DOMContentLoaded', function() {
            checkNavbarElements();
        });

        // Listener adicional para garantir funcionamento
        document.addEventListener('livewire:init', function() {
            setTimeout(() => {
                checkNavbarElements();
            }, 100);
        });

        // Verificação periódica para elementos dinâmicos (SPA)
        document.addEventListener('livewire:navigated', function() {
            if (window.navbarCheckInterval) {
                clearInterval(window.navbarCheckInterval);
            }

            window.navbarCheckInterval = setInterval(() => {
                const elements = checkNavbarElements();
                if (elements.navbarImg || elements.navbarContainer) {
                    clearInterval(window.navbarCheckInterval);
                }
            }, 500);

            setTimeout(() => {
                if (window.navbarCheckInterval) {
                    clearInterval(window.navbarCheckInterval);
                }
            }, 5000);
        });
    </script>

</div>
