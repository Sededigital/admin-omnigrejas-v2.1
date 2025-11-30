<?php
    use App\Helpers\RBAC\PermissionHelper;
    use App\Helpers\Billings\ResourceHelper;
    
    $permissionHelper = new PermissionHelper(Auth::user());
    $resourceHelper = new ResourceHelper();
?>

<aside class="sidebar sidebar-default sidebar-white sidebar-base navs-rounded-all ">
    <div class="sidebar-header d-flex align-items-center justify-content-start">
        <a href="<?php echo e(url('/')); ?>" class="navbar-brand">

            
            <div class="logo-main">
                <div class="logo-normal">
                    <img src="<?php echo e(asset('system/img/logo-system/icon-admin-blue.png')); ?>" class="icon-40" alt=""   width="400" height="330">
                </div>
                <div class="logo-mini">
                    <img src="<?php echo e(asset('system/img/logo-system/icon-admin-blue.png')); ?>" class="icon-40" alt=""   width="400" height="330">
                </div>
            </div>
            


            <h4 class="logo-title fw-bold">
                <span class="text-primary">Omn</span><span class="text-success">Igrejas</span>
            </h4>
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </i>
        </div>
    </div>
    <div class="sidebar-body pt-0" style="overflow-y: auto; max-height: calc(100vh - 120px); scroll-behavior: smooth;"  wire:ignore.self>

        <div class="sidebar-list">
             
            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Home</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('/')); ?>" scroll="false" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
                                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Painel principal</span>
                    </a>
                </li>
                

                
                <?php if(($permissionHelper->hasPermission('gerenciar_corpo_lideranca')  || $permissionHelper->hasPermission('gerenciar_igrejas')
                || $permissionHelper->hasPermission('ver_igrejas') || $permissionHelper->hasPermission('gerenciar_aliancas')
                || $permissionHelper->hasPermission('gerenciar_ministerios') || $permissionHelper->hasPermission('ver_ministerios')
                || $permissionHelper->hasPermission('gerenciar_membros') || $permissionHelper->hasPermission('ver_membros')
                || $permissionHelper->hasPermission('gerenciar_cartoes_membros') || $permissionHelper->hasPermission('gerenciar_eventos') || $permissionHelper->hasPermission('gerenciar_escalas')
                || $permissionHelper->hasPermission('gerenciar_cultos') || $permissionHelper->hasPermission('gerenciar_mapa_talentos')
                || $permissionHelper->hasPermission('visualizar_mapa_talentos') || $permissionHelper->hasPermission('gerenciar_relatorios') || $permissionHelper->hasPermission('visualizar_relatorios')
                || $permissionHelper->hasPermission('gerenciar_pedidos_especiais') || $permissionHelper->hasPermission('ver_pedidos') || $permissionHelper->hasPermission('aprovar_pedidos')
                || $permissionHelper->hasPermission('gerenciar_estatisticas') || $permissionHelper->hasPermission('visualizar_estatisticas')
                || $permissionHelper->hasPermission('gerenciar_calendario') || $permissionHelper->hasPermission('visualizar_calendario'))
                    
                    && $resourceHelper->userCanAccessSection(['gerenciar_corpo_lideranca', 'gerenciar_igrejas', 'ver_igrejas', 'gerenciar_aliancas', 'gerenciar_ministerios', 'ver_ministerios', 'gerenciar_membros', 'ver_membros', 'gerenciar_cartoes_membros', 'gerenciar_eventos', 'gerenciar_escalas', 'gerenciar_cultos', 'gerenciar_mapa_talentos', 'visualizar_mapa_talentos', 'gerenciar_relatorios', 'visualizar_relatorios', 'gerenciar_pedidos_especiais', 'ver_pedidos', 'aprovar_pedidos', 'gerenciar_estatisticas', 'visualizar_estatisticas', 'gerenciar_calendario', 'visualizar_calendario'])): ?>
                    
                <li><hr class="hr-horizontal"></li>
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1" >
                        <span class="default-icon">Gestão Organizacional</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>

                
                <?php if($permissionHelper->hasPermission('gerenciar_corpo_lideranca')  || $permissionHelper->hasPermission('gerenciar_igrejas')
                || $permissionHelper->hasPermission('ver_igrejas') || $permissionHelper->hasPermission('gerenciar_aliancas')
                || $permissionHelper->hasPermission('gerenciar_ministerios') || $permissionHelper->hasPermission('ver_ministerios')
                || $permissionHelper->hasPermission('gerenciar_membros') || $permissionHelper->hasPermission('ver_membros')
                || $permissionHelper->hasPermission('gerenciar_cartoes_membros')): ?>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-gerenciar-organizacional" role="button" aria-expanded="false" aria-controls="sidebar-gerenciar-organizacional">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2C12.5523 2 13 2.44772 13 3V4.126C14.0978 4.42376 15.0978 4.95149 15.9393 5.70711L16.9393 5.06066C17.3787 4.77805 17.9726 4.89995 18.2552 5.33934L19.3393 7.06066C19.6219 7.50005 19.5 8.09392 19.0607 8.37653L18.0607 9.023C18.435 9.92125 18.635 10.9212 18.635 11.9999C18.635 13.0787 18.435 14.0787 18.0607 14.977L19.0607 15.6235C19.5 15.9061 19.6219 16.5 19.3393 16.9393L18.2552 18.6606C17.9726 19.1 17.3787 19.2219 16.9393 18.9393L15.9393 18.2929C15.0978 19.0485 14.0978 19.5762 13 19.874V21C13 21.5523 12.5523 22 12 22H11C10.4477 22 10 21.5523 10 21V19.874C8.90218 19.5762 7.90218 19.0485 7.06066 18.2929L6.06066 18.9393C5.62127 19.2219 5.0274 19.1 4.74479 18.6606L3.66066 16.9393C3.37805 16.5 3.5 15.9061 3.93934 15.6235L4.93934 14.977C4.56502 14.0787 4.36502 13.0787 4.36502 11.9999C4.36502 10.9212 4.56502 9.92125 4.93934 9.023L3.93934 8.37653C3.5 8.09392 3.37805 7.50005 3.66066 7.06066L4.74479 5.33934C5.0274 4.89995 5.62127 4.77805 6.06066 5.06066L7.06066 5.70711C7.90218 4.95149 8.90218 4.42376 10 4.126V3C10 2.44772 10.4477 2 11 2H12Z" fill="currentColor"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 15.5C13.933 15.5 15.5 13.933 15.5 12C15.5 10.067 13.933 8.5 12 8.5C10.067 8.5 8.5 10.067 8.5 12C8.5 13.933 10.067 15.5 12 15.5Z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Gerenciar</span>
                        <i class="right-icon">
                            <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </i>
                    </a>

                    <ul class="sub-nav collapse" id="sidebar-gerenciar-organizacional" data-bs-parent="#sidebar-menu">
                        <?php if($permissionHelper->hasPermission('gerenciar_corpo_lideranca') && $resourceHelper->userCanAccessResource('gerenciar_corpo_lideranca')): ?>
                        <li class="nav-item">
                            <a class="nav-link " href="<?php echo e(url('churches/leadership-body')); ?>" wire:navigate wire:current='active'>
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> CL </i>
                                <span class="item-name">Corpo de Liderença</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(($permissionHelper->hasPermission('gerenciar_igrejas') || $permissionHelper->hasPermission('ver_igrejas')) && $resourceHelper->userCanAccessResource('gerenciar_igrejas')): ?>
                        <li class="nav-item">
                            <a class="nav-link " href="<?php echo e(url('/churches/only-churches')); ?>" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> IG </i>
                                <span class="item-name">Igrejas</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($permissionHelper->hasPermission('gerenciar_aliancas') && $resourceHelper->userCanAccessResource('gerenciar_aliancas')): ?>
                        <li class="nav-item">
                            <a class="nav-link " href="<?php echo e(url('/churches/alliance-church')); ?>" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> A </i>
                                <span class="item-name">Alianças</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(($permissionHelper->hasPermission('gerenciar_ministerios') || $permissionHelper->hasPermission('ver_ministerios')) && $resourceHelper->userCanAccessResource('gerenciar_ministerios')): ?>
                        <li class="nav-item">
                            <a class="nav-link " href="<?php echo e(url('churches/church-ministries')); ?>" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> MI </i>
                                <span class="item-name">Ministérios</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(($permissionHelper->hasPermission('gerenciar_membros') || $permissionHelper->hasPermission('ver_membros')) && $resourceHelper->userCanAccessResource('gerenciar_membros')): ?>
                        <li class="nav-item">
                            <a class="nav-link " href="<?php echo e(url('churches/church-members')); ?>" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> M </i>
                                <span class="item-name">Membros</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($permissionHelper->hasPermission('gerenciar_cartoes_membros') && $resourceHelper->userCanAccessResource('gerenciar_cartoes_membros')): ?>
                        <li class="nav-item">
                            <a class="nav-link " href="<?php echo e(url('churches/church-member-cards')); ?>" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> CM </i>
                                <span class="item-name">Cartões Membros</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                

                
                <?php if(($permissionHelper->hasPermission('gerenciar_eventos') || $permissionHelper->hasPermission('gerenciar_escalas')
                || $permissionHelper->hasPermission('gerenciar_cultos'))
                
                && $resourceHelper->userCanAccessSection(['gerenciar_eventos', 'gerenciar_escalas', 'gerenciar_cultos'])): ?>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-eventos-escalas" role="button" aria-expanded="false" aria-controls="sidebar-eventos-escalas">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" fill="currentColor"/>
                                <path d="M19 5v14H5V8h14V5z" fill="currentColor"/>
                                <path d="M7 10h5v5H7z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Eventos e Escalas</span>
                        <i class="right-icon">
                            <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </i>
                    </a>
                    <ul class="sub-nav collapse" id="sidebar-eventos-escalas" data-bs-parent="#sidebar-menu">
                        <?php if($permissionHelper->hasPermission('gerenciar_eventos') && $resourceHelper->userCanAccessResource('gerenciar_eventos')): ?>
                        <li class="nav-item">
                            <a class="nav-link " href="<?php echo e(url('churches/church-events')); ?>" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> E </i>
                                <span class="item-name">Eventos</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($permissionHelper->hasPermission('gerenciar_escalas') && $resourceHelper->userCanAccessResource('gerenciar_escalas')): ?>
                        <li class="nav-item">
                            <a class="nav-link " href="<?php echo e(url('churches/church-scale')); ?>" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> E </i>
                                <span class="item-name">Escalas</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($permissionHelper->hasPermission('gerenciar_cultos') && $resourceHelper->userCanAccessResource('gerenciar_cultos')): ?>
                        <li class="nav-item">
                            <a class="nav-link " href="<?php echo e(url('churches/church-standard-cult')); ?>" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> CP </i>
                                <span class="item-name">Cultos Padrão</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                


                <?php if(($permissionHelper->hasPermission('gerenciar_mapa_talentos') || $permissionHelper->hasPermission('visualizar_mapa_talentos'))
                
                && $resourceHelper->userCanAccessSection(['gerenciar_mapa_talentos', 'visualizar_mapa_talentos'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/church-talent-map')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2Z" fill="currentColor"/>
                                <path d="M21 9V7L18.5 7.5L18 6L19.5 4.5L18 3L16.5 4.5L15 4L15.5 1.5H13.5L14 4L12.5 4.5L11 3L9.5 4.5L11 6L10.5 7.5L8 7V9L10.5 8.5L11 10L9.5 11.5L11 13L12.5 11.5L14 12L13.5 14.5H15.5L15 12L16.5 11.5L18 13L19.5 11.5L18 10L18.5 8.5L21 9Z" fill="currentColor"/>
                                <path d="M12 8C13.66 8 15 9.34 15 11C15 12.66 13.66 14 12 14C10.34 14 9 12.66 9 11C9 9.34 10.34 8 12 8Z" fill="currentColor"/>
                                <path opacity="0.4" d="M7 18C7 16.9 7.9 16 9 16H15C16.1 16 17 16.9 17 18V20H7V18Z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Mapa de Talentos</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(($permissionHelper->hasPermission('gerenciar_relatorios') || $permissionHelper->hasPermission('visualizar_relatorios'))
                
                && $resourceHelper->userCanAccessSection(['gerenciar_relatorios', 'visualizar_relatorios'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/church-reports')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V8L14 2Z" fill="currentColor"/>
                                <path d="M14 8V2L20 8H14Z" fill="currentColor"/>
                                <path d="M16 13H8V15H16V13ZM16 17H8V19H16V17ZM8 9H10V11H8V9Z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Relatórios</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if(($permissionHelper->hasPermission('gerenciar_pedidos_especiais') || $permissionHelper->hasPermission('ver_pedidos') || $permissionHelper->hasPermission('aprovar_pedidos'))
                
                && $resourceHelper->userCanAccessSection(['gerenciar_pedidos_especiais', 'ver_pedidos', 'aprovar_pedidos'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/church-special-requests')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path d="M12 2a10 10 0 100 20 10 10 0 000-20Z" fill="currentColor" opacity="0.4"/>
                                <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="1.5" fill="none"/>
                            </svg>
                        </i>
                        <i class="sidenav-mini-icon"> PE </i>
                        <span class="item-name">Pedidos Especiais</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(($permissionHelper->hasPermission('gerenciar_estatisticas') || $permissionHelper->hasPermission('visualizar_estatisticas'))
                
                && $resourceHelper->userCanAccessSection(['gerenciar_estatisticas', 'visualizar_estatisticas'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/church-statistics')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path d="M5 12h2v7H5zm6-5h2v12h-2zm6 3h2v9h-2z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Estatísticas de Membros</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if(($permissionHelper->hasPermission('gerenciar_calendario') || $permissionHelper->hasPermission('visualizar_calendario'))
                
                && $resourceHelper->userCanAccessSection(['gerenciar_calendario', 'visualizar_calendario'])): ?>
                <li class="nav-item">
                        <a class="nav-link " href="<?php echo e(url('churches/church-calendar')); ?>" wire:navigate wire:current="active">
                            <i class="icon">
                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" fill="currentColor"/>
                                    <path d="M19 8H5v11h14V8z" fill="currentColor"/>
                                    <path d="M7 10h2v2H7v-2zm4 0h2v2h-2v-2zm4 0h2v2h-2v-2z" fill="currentColor"/>
                                </svg>
                            </i>
                            <i class="sidenav-mini-icon"> C </i>
                            <span class="item-name">Calendário</span>
                        </a>
                    </li>
                <?php endif; ?>


                <?php endif; ?>
                

                
                <?php if((Auth::user()->role =='admin' || Auth::user()->role =='pastor' || Auth::user()->role =='ministro') && $resourceHelper->userCanAccessResource('gerenciar_controle_acesso')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/rbac-control')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2Z" fill="currentColor"/>
                                <path d="M19.14 12.94C19.18 12.64 19.2 12.33 19.2 12C19.2 11.67 19.18 11.36 19.14 11.06L21.16 9.48C21.34 9.34 21.39 9.07 21.28 8.87L19.36 5.55C19.24 5.33 18.99 5.26 18.77 5.33L16.38 6.29C15.88 5.91 15.35 5.59 14.76 5.35L14.4 2.81C14.36 2.57 14.16 2.4 13.92 2.4H10.08C9.84 2.4 9.64 2.57 9.6 2.81L9.24 5.35C8.65 5.59 8.12 5.92 7.62 6.29L5.23 5.33C5.01 5.25 4.76 5.33 4.64 5.55L2.72 8.87C2.61 9.08 2.66 9.34 2.84 9.48L4.86 11.06C4.82 11.36 4.8 11.67 4.8 12C4.8 12.33 4.82 12.64 4.86 12.94L2.84 14.52C2.66 14.66 2.61 14.93 2.72 15.13L4.64 18.45C4.76 18.67 5.01 18.74 5.23 18.67L7.62 17.71C8.12 18.09 8.65 18.41 9.24 18.65L9.6 21.19C9.64 21.43 9.84 21.6 10.08 21.6H13.92C14.16 21.6 14.36 21.43 14.4 21.19L14.76 18.65C15.35 18.41 15.88 18.09 16.38 17.71L18.77 18.67C18.99 18.75 19.24 18.67 19.36 18.45L21.28 15.13C21.39 14.93 21.34 14.66 21.16 14.52L19.14 12.94ZM12 15.6C10.02 15.6 8.4 13.98 8.4 12C8.4 10.02 10.02 8.4 12 8.4C13.98 8.4 15.6 10.02 15.6 12C15.6 13.98 13.98 15.6 12 15.6Z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Controle de Acesso</span>
                    </a>
                </li>
                <?php endif; ?>
                


                
                <?php if(($permissionHelper->hasPermission('gerenciar_cursos') || $permissionHelper->hasPermission('ver_cursos')
                    || $permissionHelper->hasPermission('gerenciar_inscricoes')
                    || $permissionHelper->hasPermission('inscrever_alunos')
                    || $permissionHelper->hasPermission('visualizar_certificados')
                    || $permissionHelper->hasPermission('emitir_certificados'))
                    
                    && $resourceHelper->userCanAccessSection(['gerenciar_cursos', 'ver_cursos', 'gerenciar_inscricoes', 'inscrever_alunos', 'visualizar_certificados', 'emitir_certificados'])): ?>


                <li><hr class="hr-horizontal"></li>
                 <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Discipulado e Educação Cristã</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>
                
                <?php if(($permissionHelper->hasPermission('gerenciar_cursos') || $permissionHelper->hasPermission('ver_cursos') || $permissionHelper->hasPermission('gerenciar_inscricoes')
               || $permissionHelper->hasPermission('inscrever_alunos'))
               && $resourceHelper->userCanAccessSection(['gerenciar_cursos', 'ver_cursos', 'gerenciar_inscricoes', 'inscrever_alunos'])): ?>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-discipulado-gerir" role="button" aria-expanded="false" aria-controls="sidebar-discipulado-gerir">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2C13.1 2 14 2.9 14 4V6H18C19.1 6 20 6.9 20 8V19C20 20.1 19.1 21 18 21H6C4.9 21 4 20.1 4 19V8C4 6.9 4.9 6 6 6H10V4C10 2.9 10.9 2 12 2Z" fill="currentColor"/>
                                <path d="M12 4V6H12V4ZM8 10H16V12H8V10ZM8 14H16V16H8V14Z" fill="currentColor"/>
                                <path d="M12 7C12.55 7 13 7.45 13 8S12.55 9 12 9 11 8.55 11 8 11.45 7 12 7Z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Gerir</span>
                        <i class="right-icon">
                            <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </i>
                    </a>
                    <ul class="sub-nav collapse" id="sidebar-discipulado-gerir" data-bs-parent="#sidebar-menu">
                        <?php if(($permissionHelper->hasPermission('gerenciar_cursos') || $permissionHelper->hasPermission('ver_cursos'))
                        && ($resourceHelper->userCanAccessResource('gerenciar_cursos') || $resourceHelper->userCanAccessResource('ver_cursos'))): ?>
                        <li class="nav-item">
                            <a class="nav-link "  href="<?php echo e(url('churches/church-courses/courses')); ?>" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> C </i>
                                <span class="item-name">Cursos</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($permissionHelper->hasPermission('gerenciar_inscricoes') || $permissionHelper->hasPermission('inscrever_alunos')): ?>
                        <li class="nav-item">
                            <a class="nav-link " href="<?php echo e(url('churches/church-courses/registration')); ?>" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> I </i>
                                <span class="item-name">Inscritos</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                 <?php endif; ?>
                 


                <?php if(($permissionHelper->hasPermission('visualizar_certificados') || $permissionHelper->hasPermission('emitir_certificados'))
                && ($resourceHelper->userCanAccessResource('visualizar_certificados') || $resourceHelper->userCanAccessResource('emitir_certificados'))): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/church-courses/certificates')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path d="M6 4h12v16H6z" fill="currentColor" opacity="0.4"/>
                                <path d="M9 8h6M9 12h4" stroke="currentColor" stroke-width="1.5" fill="none"/>
                            </svg>
                        </i>
                        <span class="item-name">Certificados</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php endif; ?>
                

                
                <?php if(($permissionHelper->hasPermission('gerenciar_financeiro') || $permissionHelper->hasPermission('visualizar_financeiro')
                    || $permissionHelper->hasPermission('gerenciar_doacoes_online'))
                    
                    && $resourceHelper->userCanAccessSection(['gerenciar_financeiro', 'visualizar_financeiro', 'gerenciar_doacoes_online'])): ?>


                <li><hr class="hr-horizontal"></li>
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Gestão Financeira</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>

                <?php if(($permissionHelper->hasPermission('gerenciar_financeiro') || $permissionHelper->hasPermission('visualizar_financeiro'))
                && ($resourceHelper->userCanAccessResource('gerenciar_financeiro') || $resourceHelper->userCanAccessResource('visualizar_financeiro'))): ?>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-gerenciar-financeiro" role="button" aria-expanded="false" aria-controls="sidebar-gerenciar-financeiro">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M21.25 13.4764C20.429 13.4764 19.761 12.8145 19.761 12.001C19.761 11.1865 20.429 10.5246 21.25 10.5246C21.449 10.5246 21.64 10.4463 21.78 10.3076C21.921 10.1679 22 9.97864 22 9.78146L21.999 7.10415C21.999 4.84102 20.14 3 17.856 3H6.144C3.86 3 2.001 4.84102 2.001 7.10415L2 9.86766C2 10.0648 2.079 10.2541 2.22 10.3938C2.36 10.5325 2.551 10.6108 2.75 10.6108C3.599 10.6108 4.239 11.2083 4.239 12.001C4.239 12.8145 3.571 13.4764 2.75 13.4764C2.336 13.4764 2 13.8093 2 14.2195V16.8949C2 19.158 3.858 21 6.143 21H17.857C20.142 21 22 19.158 22 16.8949V14.2195C22 13.8093 21.664 13.4764 21.25 13.4764Z" fill="currentColor"></path>
                                <path d="M15.4303 11.5887L14.2513 12.7367L14.5303 14.3597C14.5783 14.6407 14.4653 14.9177 14.2343 15.0837C14.0053 15.2517 13.7063 15.2727 13.4543 15.1387L11.9993 14.3737L10.5413 15.1397C10.4333 15.1967 10.3153 15.2267 10.1983 15.2267C10.0453 15.2267 9.89434 15.1787 9.76434 15.0847C9.53434 14.9177 9.42134 14.6407 9.46934 14.3597L9.74734 12.7367L8.56834 11.5887C8.36434 11.3907 8.29334 11.0997 8.38134 10.8287C8.47034 10.5587 8.70034 10.3667 8.98134 10.3267L10.6073 10.0897L11.3363 8.61268C11.4633 8.35868 11.7173 8.20068 11.9993 8.20068H12.0013C12.2843 8.20168 12.5383 8.35968 12.6633 8.61368L13.3923 10.0897L15.0213 10.3277C15.2993 10.3667 15.5293 10.5587 15.6173 10.8287C15.7063 11.0997 15.6353 11.3907 15.4303 11.5887Z" fill="currentColor"></path>
                            </svg>
                        </i>
                        <span class="item-name">Gerenciar</span>
                        <i class="right-icon">
                            <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </i>
                    </a>
                    <ul class="sub-nav collapse" id="sidebar-gerenciar-financeiro" data-bs-parent="#sidebar-menu">
                        <?php if(($permissionHelper->hasPermission('gerenciar_financeiro') || $permissionHelper->hasPermission('lancar_movimentos'))
                        && ($resourceHelper->userCanAccessResource('gerenciar_financeiro') || $resourceHelper->userCanAccessResource('lancar_movimentos'))): ?>
                        <li class="nav-item">
                            <a class="nav-link " href="<?php echo e(url('churches/church-financial/moviment')); ?>" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> MF </i>
                                <span class="item-name">Movimentos Financeiros</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="<?php echo e(url('churches/church-financial/accounts')); ?>" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> CF </i>
                                <span class="item-name">Contas Financeiras</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($permissionHelper->hasPermission('visualizar_financeiro') && $resourceHelper->userCanAccessResource('visualizar_financeiro')): ?>
                        <li class="nav-item">
                            <a class="nav-link " href="<?php echo e(url('churches/church-financial/report')); ?>" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> R </i>
                                <span class="item-name">Relatórios Financeiros</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if($permissionHelper->hasPermission('gerenciar_doacoes_online') && $resourceHelper->userCanAccessResource('gerenciar_doacoes_online')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/church-financial/online-donations')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z" fill="currentColor"/>
                                <path d="M4 8h16v2H4V8zm0 4h16v2H4v-2z" fill="currentColor"/>
                                <path d="M12 14c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z" fill="currentColor"/>
                                <path d="M8 16h8c.55 0 1 .45 1 1s-.45 1-1 1H8c-.55 0-1-.45-1-1s.45-1 1-1z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Doações Online</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php endif; ?>
                


                
                <?php if(($permissionHelper->hasPermission('gerenciar_posts') || $permissionHelper->hasPermission('gerenciar_comunicacoes')
                    || $permissionHelper->hasPermission('gerenciar_chats') || $permissionHelper->hasPermission('gerenciar_chats_igreja')
                    || $permissionHelper->hasPermission('gerenciar_mensagens_privadas')
                    || $permissionHelper->hasPermission('gerenciar_migracao_membros') || $permissionHelper->hasPermission('ver_historico_migracao')
                    || $permissionHelper->hasPermission('migrar_membro') || $permissionHelper->hasPermission('aprovar_migracao')
                    || $permissionHelper->hasPermission('gerenciar_vitrine_igrejas') || $permissionHelper->hasPermission('ver_vitrine_igrejas'))
                    
                    && $resourceHelper->userCanAccessSection(['gerenciar_posts', 'gerenciar_comunicacoes', 'gerenciar_chats', 'gerenciar_chats_igreja', 'gerenciar_mensagens_privadas', 'gerenciar_migracao_membros', 'ver_historico_migracao', 'migrar_membro', 'aprovar_migracao', 'gerenciar_vitrine_igrejas', 'ver_vitrine_igrejas'])): ?>

                <li><hr class="hr-horizontal"></li>
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Social & Comunicação</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-social-comunicacao" role="button" aria-expanded="false" aria-controls="sidebar-social-comunicacao">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.4" d="M12 2C12.5523 2 13 2.44772 13 3V4.126C14.0978 4.42376 15.0978 4.95149 15.9393 5.70711L16.9393 5.06066C17.3787 4.77805 17.9726 4.89995 18.2552 5.33934L19.3393 7.06066C19.6219 7.50005 19.5 8.09392 19.0607 8.37653L18.0607 9.023C18.435 9.92125 18.635 10.9212 18.635 11.9999C18.635 13.0787 18.435 14.0787 18.0607 14.977L19.0607 15.6235C19.5 15.9061 19.6219 16.5 19.3393 16.9393L18.2552 18.6606C17.9726 19.1 17.3787 19.2219 16.9393 18.9393L15.9393 18.2929C15.0978 19.0485 14.0978 19.5762 13 19.874V21C13 21.5523 12.5523 22 12 22H11C10.4477 22 10 21.5523 10 21V19.874C8.90218 19.5762 7.90218 19.0485 7.06066 18.2929L6.06066 18.9393C5.62127 19.2219 5.0274 19.1 4.74479 18.6606L3.66066 16.9393C3.37805 16.5 3.5 15.9061 3.93934 15.6235L4.93934 14.977C4.56502 14.0787 4.36502 13.0787 4.36502 11.9999C4.36502 10.9212 4.56502 9.92125 4.93934 9.023L3.93934 8.37653C3.5 8.09392 3.37805 7.50005 3.66066 7.06066L4.74479 5.33934C5.0274 4.89995 5.62127 4.77805 6.06066 5.06066L7.06066 5.70711C7.90218 4.95149 8.90218 4.42376 10 4.126V3C10 2.44772 10.4477 2 11 2H12Z" fill="currentColor"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 15.5C13.933 15.5 15.5 13.933 15.5 12C15.5 10.067 13.933 8.5 12 8.5C10.067 8.5 8.5 10.067 8.5 12C8.5 13.933 10.067 15.5 12 15.5Z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Gerenciar</span>
                        <i class="right-icon">
                            <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </i>
                    </a>

                    <ul class="sub-nav collapse" id="sidebar-social-comunicacao" data-bs-parent="#sidebar-menu">
                        <?php if($permissionHelper->hasPermission('gerenciar_posts') && $resourceHelper->userCanAccessResource('gerenciar_posts')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(url('/churches/only-posts')); ?>" wire:navigate wire:current="active">
                                <i class="icon"><svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="8" fill="currentColor"/></svg></i>
                                <i class="sidenav-mini-icon"> P </i>
                                <span class="item-name">Posts</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($permissionHelper->hasPermission('gerenciar_comunicacoes') && $resourceHelper->userCanAccessResource('gerenciar_comunicacoes')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(url('churches/alliance-church/community')); ?>" wire:navigate wire:current="active">
                                    <i class="icon"><svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="8" fill="currentColor"/></svg></i>
                                    <i class="sidenav-mini-icon"> CO </i>
                                    <span class="item-name">Comunicações</span>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if(($permissionHelper->hasPermission('gerenciar_chats') || $permissionHelper->hasPermission('gerenciar_chats_igreja'))
                            && ($resourceHelper->userCanAccessResource('gerenciar_chats') || $resourceHelper->userCanAccessResource('gerenciar_chats_igreja'))): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('churches.chat.churches')); ?>" wire:navigate wire:current="active">
                                <i class="icon"><svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="8" fill="currentColor"/></svg></i>
                                <i class="sidenav-mini-icon"> CI </i>
                                <span class="item-name">Chats da Igreja</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($permissionHelper->hasPermission('gerenciar_mensagens_privadas') && $resourceHelper->userCanAccessResource('gerenciar_mensagens_privadas')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(url('churches/chat/private-chat')); ?>" wire:navigate wire:current="active">
                                <i class="icon"><svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="8" fill="currentColor"/></svg></i>
                                <i class="sidenav-mini-icon"> MP </i>
                                <span class="item-name">Mensagens Privadas</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                


                
                <?php if(($permissionHelper->hasPermission('gerenciar_voluntarios') || $permissionHelper->hasPermission('gerenciar_recursos')
                    || $permissionHelper->hasPermission('ver_recursos') || $permissionHelper->hasPermission('gerenciar_atendimentos_pastorais') || $permissionHelper->hasPermission('ver_atendimentos')
                    || $permissionHelper->hasPermission('gerenciar_atendimentos'))
                    
                    && $resourceHelper->userCanAccessSection(['gerenciar_voluntarios', 'gerenciar_recursos', 'ver_recursos', 'gerenciar_atendimentos_pastorais', 'ver_atendimentos', 'gerenciar_atendimentos'])): ?>

                <li><hr class="hr-horizontal"></li>
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Voluntariado e Recursos</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>

                <?php if($permissionHelper->hasPermission('gerenciar_voluntarios') && $resourceHelper->userCanAccessResource('gerenciar_voluntarios')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/church-volunteers')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2Z" fill="currentColor"/>
                                <path d="M16 4C17.1 4 18 4.9 18 6C18 7.1 17.1 8 16 8C14.9 8 14 7.1 14 6C14 4.9 14.9 4 16 4Z" fill="currentColor"/>
                                <path d="M8 4C9.1 4 10 4.9 10 6C10 7.1 9.1 8 8 8C6.9 8 6 7.1 6 6C6 4.9 6.9 4 8 4Z" fill="currentColor"/>
                                <path d="M12 8C15.31 8 18 10.69 18 14V16C18 17.1 17.1 18 16 18H8C6.9 18 6 17.1 6 16V14C6 10.69 8.69 8 12 8Z" fill="currentColor"/>
                                <path d="M20 12C21.1 12 22 12.9 22 14V16C22 17.1 21.1 18 20 18H19V16V14C19 13.45 18.55 13 18 13C17.45 13 17 13.45 17 14H19C19.55 14 20 14.45 20 15V16C20 16.55 19.55 17 19 17H20C20.55 17 21 16.55 21 16V14C21 13.45 20.55 13 20 13V12Z" fill="currentColor" opacity="0.4"/>
                                <path d="M4 12C2.9 12 2 12.9 2 14V16C2 17.1 2.9 18 4 18H5V16V14C5 13.45 5.45 13 6 13C6.55 13 7 13.45 7 14H5C4.45 14 4 14.45 4 15V16C4 16.55 4.45 17 5 17H4C3.45 17 3 16.55 3 16V14C3 13.45 3.45 13 4 13V12Z" fill="currentColor" opacity="0.4"/>
                            </svg>
                        </i>
                        <span class="item-name">Voluntários</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(($permissionHelper->hasPermission('gerenciar_migracao_membros') || $permissionHelper->hasPermission('ver_historico_migracao')
                || $permissionHelper->hasPermission('migrar_membro') || $permissionHelper->hasPermission('aprovar_migracao'))
                && ($resourceHelper->userCanAccessResource('gerenciar_migracao_membros') || $resourceHelper->userCanAccessResource('ver_historico_migracao')
                || $resourceHelper->userCanAccessResource('migrar_membro') || $resourceHelper->userCanAccessResource('aprovar_migracao'))): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/church-member-migration')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2Z" fill="currentColor"/>
                                <path d="M21 9V7L18.5 7.5L18 6L19.5 4.5L18 3L16.5 4.5L15 4L15.5 1.5H13.5L14 4L12.5 4.5L11 3L9.5 4.5L11 6L10.5 7.5L8 7V9L10.5 8.5L11 10L9.5 11.5L11 13L12.5 11.5L14 12L13.5 14.5H15.5L15 12L16.5 11.5L18 13L19.5 11.5L18 10L18.5 8.5L21 9Z" fill="currentColor"/>
                                <path d="M12 8C15.31 8 18 10.69 18 14V16C18 17.1 17.1 18 16 18H8C6.9 18 6 17.1 6 16V14C6 10.69 8.69 8 12 8Z" fill="currentColor"/>
                                <path d="M20 12C21.1 12 22 12.9 22 14V16C22 17.1 21.1 18 20 18H19V16V14C19 13.45 18.55 13 18 13C17.45 13 17 13.45 17 14H19C19.55 14 20 14.45 20 15V16C20 16.55 19.55 17 19 17H20C20.55 17 21 16.55 21 16V14C21 13.45 20.55 13 20 13V12Z" fill="currentColor" opacity="0.4"/>
                                <path d="M4 12C2.9 12 2 12.9 2 14V16C2 17.1 2.9 18 4 18H5V16V14C5 13.45 5.45 13 6 13C6.55 13 7 13.45 7 14H5C4.45 14 4 14.45 4 15V16C4 16.55 4.45 17 5 17H4C3.45 17 3 16.55 3 16V14C3 13.45 3.45 13 4 13V12Z" fill="currentColor" opacity="0.4"/>
                            </svg>
                        </i>
                        <span class="item-name">Migração de Membros</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(($permissionHelper->hasPermission('gerenciar_vitrine_igrejas') || $permissionHelper->hasPermission('ver_vitrine_igrejas'))
                && ($resourceHelper->userCanAccessResource('gerenciar_vitrine_igrejas') || $resourceHelper->userCanAccessResource('ver_vitrine_igrejas'))): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/church-showcase')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2Z" fill="currentColor"/>
                                <path d="M12 8C15.31 8 18 10.69 18 14V16C18 17.1 17.1 18 16 18H8C6.9 18 6 17.1 6 16V14C6 10.69 8.69 8 12 8Z" fill="currentColor"/>
                                <path d="M12 10C13.1 10 14 10.9 14 12C14 13.1 13.1 14 12 14C10.9 14 10 13.1 10 12C10 10.9 10.9 10 12 10Z" fill="currentColor" opacity="0.4"/>
                                <path d="M8 18C8 16.9 8.9 16 10 16H14C15.1 16 16 16.9 16 18V20C16 21.1 15.1 22 14 22H10C8.9 22 8 21.1 8 20V18Z" fill="currentColor"/>
                                <path d="M12 14L10 16H14L12 14Z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Vitrine de Igrejas</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(($permissionHelper->hasPermission('gerenciar_recursos') || $permissionHelper->hasPermission('ver_recursos'))
                && ($resourceHelper->userCanAccessResource('gerenciar_recursos') || $resourceHelper->userCanAccessResource('ver_recursos'))): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/church-resources')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" fill="currentColor"/>
                                <path d="M6 8h12v2H6V8zm0 4h12v2H6v-2zm0 4h8v2H6v-2z" fill="currentColor"/>
                                <path d="M8 2h8v2H8V2z" fill="currentColor"/>
                                <path d="M18 6h2v2h-2V6zm0 4h2v2h-2v-2zm0 4h2v2h-2v-2z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Recursos</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(($permissionHelper->hasPermission('gerenciar_atendimentos_pastorais') || $permissionHelper->hasPermission('ver_atendimentos') || $permissionHelper->hasPermission('gerenciar_atendimentos'))
                && ($resourceHelper->userCanAccessResource('gerenciar_atendimentos_pastorais') || $resourceHelper->userCanAccessResource('ver_atendimentos') || $resourceHelper->userCanAccessResource('gerenciar_atendimentos'))): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/church-pastoral-care')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2Z" fill="currentColor"/>
                                <path d="M12 8C15.31 8 18 10.69 18 14C18 17.31 15.31 20 12 20C8.69 20 6 17.31 6 14C6 10.69 8.69 8 12 8Z" fill="currentColor"/>
                                <path d="M12 10C13.1 10 14 10.9 14 12C14 13.1 13.1 14 12 14C10.9 14 10 13.1 10 12C10 10.9 10.9 10 12 10Z" fill="currentColor" opacity="0.4"/>
                                <path d="M8 18C8 16.9 8.9 16 10 16H14C15.1 16 16 16.9 16 18V20C16 21.1 15.1 22 14 22H10C8.9 22 8 21.1 8 20V18Z" fill="currentColor"/>
                                <path d="M12 14L10 16H14L12 14Z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Atendimentos Pastorais</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php endif; ?>
                


                
                <?php if(($permissionHelper->hasPermission('gerenciar_produtos') || $permissionHelper->hasPermission('gerenciar_pedidos')
                || $permissionHelper->hasPermission('ver_pedidos') || $permissionHelper->hasPermission('processar_pedidos')
                || $permissionHelper->hasPermission('gerenciar_pagamentos'))
                
                && $resourceHelper->userCanAccessSection(['gerenciar_produtos', 'gerenciar_pedidos', 'ver_pedidos', 'processar_pedidos', 'gerenciar_pagamentos'])): ?>


                <li><hr class="hr-horizontal"></li>

                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Marketplace</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>

                <?php if($permissionHelper->hasPermission('gerenciar_produtos') && $resourceHelper->userCanAccessResource('gerenciar_produtos')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/church-marketplace/products')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" fill="currentColor"/>
                                <path d="M8 8h8v2H8V8zm0 4h8v2H8v-2zm0 4h5v2H8v-2z" fill="currentColor"/>
                                <path d="M6 8h1v1H6V8zm0 4h1v1H6v-1zm0 4h1v1H6v-1z" fill="currentColor"/>
                                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2Z" fill="currentColor"/>
                                <path d="M18 6h2v2h-2V6zm0 4h2v2h-2v-2zm0 4h2v2h-2v-2z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Produtos</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(($permissionHelper->hasPermission('gerenciar_pedidos') || $permissionHelper->hasPermission('ver_pedidos') || $permissionHelper->hasPermission('processar_pedidos'))
                && ($resourceHelper->userCanAccessResource('gerenciar_pedidos') || $resourceHelper->userCanAccessResource('ver_pedidos') || $resourceHelper->userCanAccessResource('processar_pedidos'))): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/church-marketplace/orders')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" fill="currentColor"/>
                                <path d="M8 8h8v2H8V8zm0 4h8v2H8v-2zm0 4h5v2H8v-2z" fill="currentColor"/>
                                <path d="M6 8h1v1H6V8zm0 4h1v1H6v-1zm0 4h1v1H6v-1z" fill="currentColor"/>
                                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2Z" fill="currentColor"/>
                                <path d="M18 6h2v2h-2V6zm0 4h2v2h-2v-2zm0 4h2v2h-2v-2z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Pedidos</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if($permissionHelper->hasPermission('gerenciar_pagamentos') && $resourceHelper->userCanAccessResource('gerenciar_pagamentos')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/church-marketplace/payments')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z" fill="currentColor"/>
                                <path d="M4 8h16v2H4V8zm0 4h16v2H4v-2z" fill="currentColor"/>
                                <path d="M12 14c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z" fill="currentColor"/>
                                <path d="M8 16h8c.55 0 1 .45 1 1s-.45 1-1 1H8c-.55 0-1-.45-1-1s.45-1 1-1z" fill="currentColor"/>
                                <path d="M6 10h2v2H6v-2zm10 0h2v2h-2v-2z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Pagamentos</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php endif; ?>
                


                
                <?php if(($permissionHelper->hasPermission('gerenciar_engajamento') || $permissionHelper->hasPermission('ver_engajamento') || $permissionHelper->hasPermission('gerenciar_badges')
                    || $permissionHelper->hasPermission('gerenciar_engajamento') || $permissionHelper->hasPermission('ver_engajamento') || $permissionHelper->hasPermission('gerenciar_pontos')
                    || $permissionHelper->hasPermission('gerenciar_engajamento') || $permissionHelper->hasPermission('ver_engajamento') || $permissionHelper->hasPermission('gerenciar_enquetes')
                    || $permissionHelper->hasPermission('gerenciar_definicoes') || $permissionHelper->hasPermission('acessar_definicoes')
                    || $permissionHelper->hasPermission('gerenciar_assinaturas'))
                    
                    && $resourceHelper->userCanAccessSection(['gerenciar_engajamento', 'ver_engajamento', 'gerenciar_badges', 'gerenciar_pontos', 'gerenciar_enquetes', 'gerenciar_definicoes', 'acessar_definicoes', 'gerenciar_assinaturas'])): ?>


                <li><hr class="hr-horizontal"></li>
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Engajamento & Gamificação</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>

                <?php if(($permissionHelper->hasPermission('gerenciar_engajamento') || $permissionHelper->hasPermission('ver_engajamento') || $permissionHelper->hasPermission('gerenciar_badges')
                    || $permissionHelper->hasPermission('gerenciar_pontos') || $permissionHelper->hasPermission('gerenciar_enquetes'))
                    && ($resourceHelper->userCanAccessResource('gerenciar_engajamento') || $resourceHelper->userCanAccessResource('ver_engajamento') || $resourceHelper->userCanAccessResource('gerenciar_badges')
                    || $resourceHelper->userCanAccessResource('gerenciar_pontos') || $resourceHelper->userCanAccessResource('gerenciar_enquetes'))): ?>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-engajamento" role="button" aria-expanded="false" aria-controls="sidebar-engajamento">
                        <i class="icon">
                            <svg class="icon-20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path d="M12 2l3 7h7l-5.5 4.5L18 22l-6-4-6 4 1.5-8.5L2 9h7l3-7z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Engajamento</span>
                        <i class="right-icon">
                            <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </i>
                    </a>
                
                    <ul class="sub-nav collapse" id="sidebar-engajamento" data-bs-parent="#sidebar-menu">
                        <?php if(($permissionHelper->hasPermission('gerenciar_engajamento') || $permissionHelper->hasPermission('ver_engajamento') || $permissionHelper->hasPermission('gerenciar_badges'))
                        && ($resourceHelper->userCanAccessResource('gerenciar_engajamento') || $resourceHelper->userCanAccessResource('ver_engajamento') || $resourceHelper->userCanAccessResource('gerenciar_badges'))): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo e(url('churches/church-engagement/badges')); ?>" wire:navigate wire:current='active'>🏅 Badges</a></li>
                        <?php endif; ?>
                        <?php if(($permissionHelper->hasPermission('gerenciar_engajamento') || $permissionHelper->hasPermission('ver_engajamento') || $permissionHelper->hasPermission('gerenciar_pontos'))
                        && ($resourceHelper->userCanAccessResource('gerenciar_engajamento') || $resourceHelper->userCanAccessResource('ver_engajamento') || $resourceHelper->userCanAccessResource('gerenciar_pontos'))): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo e(url('churches/church-engagement/points')); ?>" wire:navigate wire:current='active'>⭐ Pontos</a></li>
                        <?php endif; ?>
                        <?php if(($permissionHelper->hasPermission('gerenciar_engajamento') || $permissionHelper->hasPermission('ver_engajamento') || $permissionHelper->hasPermission('gerenciar_enquetes'))
                        && ($resourceHelper->userCanAccessResource('gerenciar_engajamento') || $resourceHelper->userCanAccessResource('ver_engajamento') || $resourceHelper->userCanAccessResource('gerenciar_enquetes'))): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo e(url('churches/church-engagement/polls')); ?>" wire:navigate  wire:current='active'>📊 Enquetes</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <?php endif; ?>
                
                <?php if($permissionHelper->hasPermission('gerenciar_assinaturas')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(url('churches/church-subscription')); ?>" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </i>
                        <span class="item-name">Assinaturas</span>
                        <?php
                            $alertsCount = \App\Helpers\Billings\SubscriptionHelper::getUnreadAlertsCount(Auth::user()->getIgrejaId());
                        ?>
                        <?php if($alertsCount > 0): ?>
                            <span class="badge badge-danger right"><?php echo e($alertsCount); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(($permissionHelper->hasPermission('gerenciar_definicoes') || $permissionHelper->hasPermission('acessar_definicoes'))
                && ($resourceHelper->userCanAccessResource('gerenciar_definicoes') || $resourceHelper->userCanAccessResource('acessar_definicoes'))): ?>
                <li class="nav-item">
                    <a class="nav-link" href="" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2Z" fill="currentColor"/>
                                <path d="M19.14 12.94C19.18 12.64 19.2 12.33 19.2 12C19.2 11.67 19.18 11.36 19.14 11.06L21.16 9.48C21.34 9.34 21.39 9.07 21.28 8.87L19.36 5.55C19.24 5.33 18.99 5.26 18.77 5.33L16.38 6.29C15.88 5.91 15.35 5.59 14.76 5.35L14.4 2.81C14.36 2.57 14.16 2.4 13.92 2.4H10.08C9.84 2.4 9.64 2.57 9.6 2.81L9.24 5.35C8.65 5.59 8.12 5.92 7.62 6.29L5.23 5.33C5.01 5.25 4.76 5.33 4.64 5.55L2.72 8.87C2.61 9.08 2.66 9.34 2.84 9.48L4.86 11.06C4.82 11.36 4.8 11.67 4.8 12C4.8 12.33 4.82 12.64 4.86 12.94L2.84 14.52C2.66 14.66 2.61 14.93 2.72 15.13L4.64 18.45C4.76 18.67 5.01 18.74 5.23 18.67L7.62 17.71C8.12 18.09 8.65 18.41 9.24 18.65L9.6 21.19C9.64 21.43 9.84 21.6 10.08 21.6H13.92C14.16 21.6 14.36 21.43 14.4 21.19L14.76 18.65C15.35 18.41 15.88 18.09 16.38 17.71L18.77 18.67C18.99 18.75 19.24 18.67 19.36 18.45L21.28 15.13C21.39 14.93 21.34 14.66 21.16 14.52L19.14 12.94ZM12 15.6C10.02 15.6 8.4 13.98 8.4 12C8.4 10.02 10.02 8.4 12 8.4C13.98 8.4 15.6 10.02 15.6 12C15.6 13.98 13.98 15.6 12 15.6Z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Definições</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php endif; ?>
                

                <li class="nav-item m-2">
                    <a class="nav-link" href="#" >

                        <span class="item-name"></span>
                    </a>
                </li>

            </ul>

        </div>

            
    </div>

    <script>
        // Função para fazer scroll para o link ativo
        function scrollToActiveLink() {
            const sidebarBody = document.querySelector('.sidebar-body');
            const activeLink = document.querySelector('.sidebar-body .nav-link.active');

            if (sidebarBody && activeLink) {
                // Calcular posição relativa
                const sidebarRect = sidebarBody.getBoundingClientRect();
                const linkRect = activeLink.getBoundingClientRect();

                // Posição do link relativa ao sidebar
                const relativeTop = linkRect.top - sidebarRect.top;

                // Calcular posição para centralizar o link
                const targetScroll = sidebarBody.scrollTop + relativeTop - (sidebarBody.clientHeight / 2) + (linkRect.height / 2);

                // Scroll suave
                sidebarBody.scrollTo({
                    top: targetScroll,
                    behavior: 'smooth'
                });

               // console.log('Scroll executado para link ativo:', activeLink.textContent.trim());
            }
        }

        // Scroll no carregamento da página
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(scrollToActiveLink, 100);
        });

        // Scroll após navegação do Livewire
        document.addEventListener('livewire:navigated', function() {
            setTimeout(function() {
                scrollToActiveLink();
            }, 200);
        });
    </script>
    <div class="sidebar-footer"></div>
</aside>

<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/components/layouts/sidebar/admin-sidebar.blade.php ENDPATH**/ ?>