<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-7">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-shield-alt me-2"></i>Controle de Acesso
                        </h1>
                        <p class="mb-0 text-muted">
                            Gerencie permissões, funções e atribuições de acesso da sua igreja
                        </p>
                    </div>
                    <div class="col-lg-4 col-md-5 mt-3 mt-md-0">
                        <!-- Desktop: Botões em linha -->
                        <div class="d-none d-lg-flex justify-content-end">
                            <div class="btn-group" role="group">
                                @foreach($abasDisponiveisInfo as $key => $aba)
                                    @if($aba['disponivel'])
                                        <button class="btn btn-outline-{{ $aba['cor'] }} btn-sm @if($abaAtiva === $key) active @endif" wire:click="$set('abaAtiva', '{{ $key }}')">
                                            <i class="{{ $aba['icone'] }} me-1"></i>{{ $aba['titulo'] }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Tablet/Mobile: Botões dinâmicos baseados em permissões -->
                        <div class="d-lg-none">
                            <div class="row g-1">
                                @foreach($abasDisponiveisInfo as $key => $aba)
                                    @if($aba['disponivel'])
                                        <div class="col-6">
                                            <button class="btn btn-outline-{{ $aba['cor'] }} btn-sm w-100 @if($abaAtiva === $key) active @endif" wire:click="$set('abaAtiva', '{{ $key }}')">
                                                <i class="{{ $aba['icone'] }} me-1"></i><span class="d-none d-sm-inline">{{ $aba['titulo'] }}</span>
                                            </button>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('styles')
        <link rel="stylesheet" href="{{ asset('system/css/community.css') }}">
        @endpush


        <!-- Conteúdo das Abas -->
        @if($abaAtiva === 'permissoes')
            @include('rbac-control.tabs.permissoes')
        @elseif($abaAtiva === 'funcoes')
            @include('rbac-control.tabs.funcoes')
        @elseif($abaAtiva === 'atribuicoes')
            @include('rbac-control.tabs.atribuicoes')
        @elseif($abaAtiva === 'logs')
            @include('rbac-control.tabs.logs')
        @endif

        {{-- MODALS --}}
        @include('rbac-control.modals.permissao-modal')
        @include('rbac-control.modals.funcao-modal')
        @include('rbac-control.modals.atribuicao-modal')
        @include('rbac-control.modals.confirmacao-modal')

    </div>
</div>
