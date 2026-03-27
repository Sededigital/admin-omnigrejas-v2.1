<aside class="sidebar sidebar-default sidebar-white sidebar-base navs-rounded-all " wire:ignore>
    <div class="sidebar-header d-flex align-items-center justify-content-start">
        <a href="{{ url('/') }}" class="navbar-brand">

            {{-- Logo start --}}
            <div class="logo-main">
                <div class="logo-normal">
                    <img src="{{ asset('system/img/logo-system/icon-admin-blue.png') }}" class="icon-40" alt=""   width="400" height="330">
                </div>
                <div class="logo-mini">
                    <img src="{{ asset('system/img/logo-system/icon-admin-blue.png') }}" class="icon-40" alt=""   width="400" height="330">
                </div>
            </div>
            {{-- logo End --}}


            <h4 class="logo-title fw-bold">
                <span class="text-info">Omn</span><span class="text-info">Igrejas</span>
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
    <div class="sidebar-body pt-0" style="overflow-y: auto; max-height: calc(100vh - 120px);" wire:ignore.self>

        <div class="sidebar-list">
             {{-- Sidebar Menu Start  --}}
            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Home</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
                                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Painel principal</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#horizontal-menu" role="button" aria-expanded="false" aria-controls="horizontal-menu">
                        <i class="icon">
                            <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
                                <path d="M4 2h16c1.1 0 2 .9 2 2v16c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2z" fill="currentColor" opacity="0.4"/>
                                <path d="M6 6h12v2H6V6zm0 4h12v2H6v-2zm0 4h8v2H6v-2z" fill="currentColor"/>
                                <path d="M18 14l-2 2 2 2" stroke="currentColor" stroke-width="1.5" fill="none"/>
                            </svg>
                        </i>
                        <span class="item-name">A Bíblia</span>
                        <i class="right-icon">
                            <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </i>
                    </a>
                    <ul class="sub-nav collapse" id="horizontal-menu" data-bs-parent="#sidebar-menu">
                      <li class="nav-item">
                            <a class="nav-link" href="{{ url('churches/church-bible-reading') }}" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.4" d="M3 5C3 4.44772 3.44772 4 4 4H10C11.6569 4 13 5.34315 13 7V19C13 20.6569 11.6569 22 10 22H4C3.44772 22 3 21.5523 3 21V5Z" fill="currentColor"/>
                                        <path d="M21 5C21 4.44772 20.5523 4 20 4H14C12.3431 4 11 5.34315 11 7V19C11 20.6569 12.3431 22 14 22H20C20.5523 22 21 21.5523 21 21V5Z" fill="currentColor"/>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> L </i>
                                <span class="item-name"> Leitura Bíblica </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link " href="{{ url('churches/church-good-practices') }}" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> B </i>
                                <span class="item-name">Boas práticas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="{{ url('churches/church-pastoral') }}" wire:navigate wire:current="active">
                                <i class="icon svg-icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> P </i>
                                <span class="item-name">Pastoral</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ url('geral/logs') }}" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" fill="currentColor"/>
                                <path d="M19 5v14H5V8h14V5z" fill="currentColor"/>
                                <path d="M7 10h5v5H7z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Logs</span>
                    </a>
                </li>

                <li><hr class="hr-horizontal"></li>
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1" >
                        <span class="default-icon">Gestão Organizacional</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" id="teste" href="#sidebar-special" role="button" aria-expanded="false" aria-controls="sidebar-special">
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

                    <ul class="sub-nav collapse" id="sidebar-special" data-bs-parent="#sidebar-menu">
                        <li class="nav-item">
                            <a class="nav-link " href="{{ url('geral/list-users') }}" wire:navigate wire:current='active'>
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> U </i>
                                <span class="item-name">Usuários</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="{{ url('/churches/only-churches') }}" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> I </i>
                                <span class="item-name">Igrejas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="{{ url('churches/church-members') }}" wire:navigate wire:current="active">
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
                        <li class="nav-item">
                            <a class="nav-link " href="{{ url('churches/church-ministries') }}" wire:navigate wire:current="active">
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
                    </ul>
                </li>

                  <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-user" role="button" aria-expanded="false" aria-controls="sidebar-user">
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
                        <ul class="sub-nav collapse" id="sidebar-user" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link " href="{{ url('churches/church-events') }}" wire:navigate wire:current="active">
                                    <i class="icon">
                                        <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                            <g>
                                            <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                            </g>
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon"> U </i>
                                    <span class="item-name">Eventos</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " href="{{ url('churches/church-scale') }}" wire:navigate wire:current="active">
                                    <i class="icon">
                                        <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                            <g>
                                            <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                            </g>
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon"> A </i>
                                    <span class="item-name">Escalas</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " href="{{ url('churches/church-standard-cult') }}" wire:navigate wire:current="active">
                                    <i class="icon">
                                        <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                            <g>
                                            <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                            </g>
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon"> U </i>
                                    <span class="item-name">Cultos Padrão</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('churches/church-talent-map') }}" wire:navigate wire:current="active">
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
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('churches/church-items') }}" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" fill="currentColor"/>
                                <path d="M8 8h8v2H8V8zm0 4h8v2H8v-2zm0 4h5v2H8v-2z" fill="currentColor"/>
                                <path d="M6 8h1v1H6V8zm0 4h1v1H6v-1zm0 4h1v1H6v-1z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Itens</span>
                    </a>
                </li>


                    <li class="nav-item">
                        <a class="nav-link " href="{{ url('churches/church-calendar') }}" wire:navigate wire:current="active">
                            <i class="icon">
                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" fill="currentColor"/>
                                    <path d="M19 8H5v11h14V8z" fill="currentColor"/>
                                    <path d="M7 10h2v2H7v-2zm4 0h2v2h-2v-2zm4 0h2v2h-2v-2z" fill="currentColor"/>
                                </svg>
                            </i>
                            <i class="sidenav-mini-icon"> V </i>
                            <span class="item-name">Calendário</span>
                        </a>
                    </li>

                    <li><hr class="hr-horizontal"></li>
                 <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Discipulado e Educação Cristã</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-maps" role="button" aria-expanded="false" aria-controls="sidebar-maps">
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
                    <ul class="sub-nav collapse" id="sidebar-maps" data-bs-parent="#sidebar-menu">
                        <li class="nav-item">
                            <a class="nav-link "  href="{{ url('churches/church-courses') }}" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> G </i>
                                <span class="item-name">Cursos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="{{ url('churches/church-course-registered') }}" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> V </i>
                                <span class="item-name">Inscritos</span>
                            </a>
                        </li>
                    </ul>
                </li>
                 <li class="nav-item">
                    <a class="nav-link " href="{{ url('churches/church-cristian-life') }}" wire:navigate wire:current="active">
                         <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2Z" fill="currentColor"/>
                                <path d="M12 8C15.31 8 18 10.69 18 14C18 17.31 15.31 20 12 20C8.69 20 6 17.31 6 14C6 10.69 8.69 8 12 8Z" fill="currentColor"/>
                                <path d="M12 10C13.1 10 14 10.9 14 12C14 13.1 13.1 14 12 14C10.9 14 10 13.1 10 12C10 10.9 10.9 10 12 10Z" fill="currentColor" opacity="0.4"/>
                                <path d="M12 16L10 18H14L12 16Z" fill="currentColor"/>
                            </svg>
                        </i>
                        <i class="sidenav-mini-icon"> V </i>
                        <span class="item-name">Vida Critã</span>
                    </a>
                </li>

                <li class="nav-item">
                        <a class="nav-link " href="{{ url('churches/church-progress-report') }}" wire:navigate wire:current="active">
                             <i class="icon">
                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M3 3h18c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2H3c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2z" fill="currentColor"/>
                                    <path d="M7 8h10v2H7V8zm0 4h10v2H7v-2zm0 4h7v2H7v-2z" fill="currentColor"/>
                                    <path d="M5 8h1v1H5V8zm0 4h1v1H5v-1zm0 4h1v1H5v-1z" fill="currentColor"/>
                                    <path d="M19 6L17 8L15 6" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                </svg>
                            </i>
                            <i class="sidenav-mini-icon"> V </i>
                            <span class="item-name">Relatórios de Progresso</span>
                        </a>
                    </li>

                <li><hr class="hr-horizontal"></li>
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Gestão de Assinaturas</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-assignatures" role="button" aria-expanded="false" aria-controls="sidebar-assignatures">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20Z" fill="currentColor"/>
                                <path d="M15.5 12H13V9.5C13 9.22 12.78 9 12.5 9H12C11.72 9 11.5 9.22 11.5 9.5V13C11.5 13.28 11.72 13.5 12 13.5H15.5C15.78 13.5 16 13.28 16 13C16 12.72 15.78 12.5 15.5 12Z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Assinaturas</span>
                        <i class="right-icon">
                            <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </i>
                    </a>
                    <ul class="sub-nav collapse" id="sidebar-assignatures" data-bs-parent="#sidebar-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('admin/assignatures/pacotes') }}" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> P </i>
                                <span class="item-name">Pacotes</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('admin/assignatures/assinaturas-atuais') }}" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> AA </i>
                                <span class="item-name">Assinaturas Atuais</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('admin/assignatures/assinaturas-historico') }}" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> AH </i>
                                <span class="item-name">Histórico de Assinaturas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('admin/assignatures/modulos') }}" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> M </i>
                                <span class="item-name">Módulos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('admin/assignatures/pacote-permissoes') }}" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> PP </i>
                                <span class="item-name">Permissões de Pacotes</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('admin/assignatures/pagamentos') }}" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> PG </i>
                                <span class="item-name">Pagamentos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('admin/assignatures/igrejas-assinadas') }}" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> IA </i>
                                <span class="item-name">Igrejas Assinadas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('admin/assignatures/logs') }}" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> LG </i>
                                <span class="item-name">Logs de Assinaturas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('admin/assignatures/cupons') }}" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> CP </i>
                                <span class="item-name">Cupons de Desconto</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('admin/assignatures/notifications') }}" wire:navigate wire:current="active">
                                <i class="icon">
                                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24" fill="currentColor">
                                        <g>
                                        <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                </i>
                                <i class="sidenav-mini-icon"> N </i>
                                <span class="item-name">Gestão de Notificações</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li><hr class="hr-horizontal"></li>
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Gestão Financeira</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>



                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-widget" role="button" aria-expanded="false" aria-controls="sidebar-widget">
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
                    <ul class="sub-nav collapse" id="sidebar-widget" data-bs-parent="#sidebar-menu">
                        <li class="nav-item">
                            <a class="nav-link " href="{{ url('churches/church-financial-moviment') }}" wire:navigate wire:current="active">
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
                            <a class="nav-link " href="{{ url('churches/church-financial/accounts') }}" wire:navigate wire:current="active">
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
                        <li class="nav-item">
                            <a class="nav-link " href="{{ url('churches/church-financial-report') }}" wire:navigate wire:current="active">
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
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ url('churches/church-online-donations') }}" wire:navigate wire:current="active">
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

                <li><hr class="hr-horizontal"></li>
                    <li class="nav-item static-item">
                        <a class="nav-link static-item disabled" href="#" tabindex="-1">
                            <span class="default-icon">Social & Comunicação</span>
                            <span class="mini-icon">-</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-social" role="button" aria-expanded="false" aria-controls="sidebar-social">
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

                        <ul class="sub-nav collapse" id="sidebar-social" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/only-posts') }}" wire:navigate wire:current="active">
                                    <i class="icon"><svg class="icon-10"><circle cx="12" cy="12" r="8" fill="currentColor"/></svg></i>
                                    <i class="sidenav-mini-icon"> P </i>
                                    <span class="item-name">Posts</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/communications') }}" wire:navigate wire:current="active">
                                    <i class="icon"><svg class="icon-10"><circle cx="12" cy="12" r="8" fill="currentColor"/></svg></i>
                                    <i class="sidenav-mini-icon"> CO </i>
                                    <span class="item-name">Comunicações</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/private-messages') }}" wire:navigate wire:current="active">
                                    <i class="icon"><svg class="icon-10"><circle cx="12" cy="12" r="8" fill="currentColor"/></svg></i>
                                    <i class="sidenav-mini-icon"> MP </i>
                                    <span class="item-name">Mensagens Privadas</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('churches/church-chats') }}" wire:navigate wire:current="active">
                                    <i class="icon"><svg class="icon-10"><circle cx="12" cy="12" r="8" fill="currentColor"/></svg></i>
                                    <i class="sidenav-mini-icon"> CC </i>
                                    <span class="item-name">Chats da Igreja</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('churches/church-chat-messages') }}" wire:navigate wire:current="active">
                                    <i class="icon"><svg class="icon-10"><circle cx="12" cy="12" r="8" fill="currentColor"/></svg></i>
                                    <i class="sidenav-mini-icon"> CM </i>
                                    <span class="item-name">Mensagens de Chat</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                <li><hr class="hr-horizontal"></li>


                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Voluntariado e Recursos</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ url('churches/church-volunteers') }}" wire:navigate wire:current="active">
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
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('churches/church-resources') }}" wire:navigate wire:current="active">
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
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('churches/church-pastoral-care') }}" wire:navigate wire:current="active">
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

                <li><hr class="hr-horizontal"></li>


                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Marketplace</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ url('churches/church-products') }}" wire:navigate wire:current="active">
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

                <li class="nav-item">
                    <a class="nav-link" href="{{ url('churches/church-orders') }}" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M7 4V2C7 1.45 7.45 1 8 1H16C16.55 1 17 1.45 17 2V4H20C20.55 4 21 4.45 21 5S20.55 6 20 6H19V18C19 19.1 18.1 20 17 20H7C5.9 20 5 19.1 5 18V6H4C3.45 6 3 5.55 3 5S3.45 4 4 4H7Z" fill="currentColor"/>
                                <path d="M9 3V4H15V3H9Z" fill="currentColor"/>
                                <path d="M7 8H17V18H7V8Z" fill="currentColor"/>
                                <path d="M9 10H11V16H9V10ZM13 10H15V16H13V10Z" fill="currentColor"/>
                                <path d="M16 6L15 7L14 6L13 7L12 6L11 7L10 6L9 7L8 6H16Z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Pedidos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('churches/church-payments') }}" wire:navigate wire:current="active">
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
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('churches/church-settings') }}" wire:navigate wire:current="active">
                        <i class="icon">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2Z" fill="currentColor"/>
                                <path d="M19.14 12.94C19.18 12.64 19.2 12.33 19.2 12C19.2 11.67 19.18 11.36 19.14 11.06L21.16 9.48C21.34 9.34 21.39 9.07 21.28 8.87L19.36 5.55C19.24 5.33 18.99 5.26 18.77 5.33L16.38 6.29C15.88 5.91 15.35 5.59 14.76 5.35L14.4 2.81C14.36 2.57 14.16 2.4 13.92 2.4H10.08C9.84 2.4 9.64 2.57 9.6 2.81L9.24 5.35C8.65 5.59 8.12 5.92 7.62 6.29L5.23 5.33C5.01 5.25 4.76 5.33 4.64 5.55L2.72 8.87C2.61 9.08 2.66 9.34 2.84 9.48L4.86 11.06C4.82 11.36 4.8 11.67 4.8 12C4.8 12.33 4.82 12.64 4.86 12.94L2.84 14.52C2.66 14.66 2.61 14.93 2.72 15.13L4.64 18.45C4.76 18.67 5.01 18.74 5.23 18.67L7.62 17.71C8.12 18.09 8.65 18.41 9.24 18.65L9.6 21.19C9.64 21.43 9.84 21.6 10.08 21.6H13.92C14.16 21.6 14.36 21.43 14.4 21.19L14.76 18.65C15.35 18.41 15.88 18.09 16.38 17.71L18.77 18.67C18.99 18.75 19.24 18.67 19.36 18.45L21.28 15.13C21.39 14.93 21.34 14.66 21.16 14.52L19.14 12.94ZM12 15.6C10.02 15.6 8.4 13.98 8.4 12C8.4 10.02 10.02 8.4 12 8.4C13.98 8.4 15.6 10.02 15.6 12C15.6 13.98 13.98 15.6 12 15.6Z" fill="currentColor"/>
                            </svg>
                        </i>
                        <span class="item-name">Definições</span>
                    </a>
                </li>

                <li class="nav-item m-2">
                    <a class="nav-link" href="#" wire:navigate >

                        <span class="item-name"></span>
                    </a>
                </li>


            </ul>

        </div>

            {{-- Sidebar Menu End         --}}
    </div>
    <div class="sidebar-footer"></div>
</aside>
