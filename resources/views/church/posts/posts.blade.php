<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-7">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-newspaper me-2"></i>Posts da Igreja
                        </h1>
                        <p class="mb-0 text-muted">
                            Gerencie os posts publicados na sua igreja
                        </p>
                    </div>
                    <div class="col-lg-4 col-md-5 mt-3 mt-md-0">
                        <!-- Desktop: Botões em linha -->
                        <div class="d-none d-lg-flex justify-content-end">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary btn-sm @if($abaAtiva === 'posts') active @endif" wire:click="$set('abaAtiva', 'posts')">
                                    <i class="fas fa-list me-1"></i>Posts
                                </button>
                                <button class="btn btn-outline-success btn-sm @if($abaAtiva === 'reacoes') active @endif" wire:click="$set('abaAtiva', 'reacoes')">
                                    <i class="fas fa-heart me-1"></i>Reações
                                </button>
                                <button class="btn btn-outline-info btn-sm @if($abaAtiva === 'comentarios') active @endif" wire:click="$set('abaAtiva', 'comentarios')" @if(!$postParaComentarios) disabled @endif>
                                    <i class="fas fa-comments me-1"></i>Comentários
                                </button>
                            </div>
                        </div>

                        <!-- Tablet/Mobile: Botões em grid 3x1 -->
                        <div class="d-lg-none">
                            <div class="row g-1">
                                <div class="col-4">
                                    <button class="btn btn-outline-primary btn-sm w-100 @if($abaAtiva === 'posts') active @endif" wire:click="$set('abaAtiva', 'posts')">
                                        <i class="fas fa-list me-1"></i>Posts
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-outline-success btn-sm w-100 @if($abaAtiva === 'reacoes') active @endif" wire:click="$set('abaAtiva', 'reacoes')">
                                        <i class="fas fa-heart me-1"></i>Reações
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-outline-info btn-sm w-100 @if($abaAtiva === 'comentarios') active @endif" wire:click="$set('abaAtiva', 'comentarios')" @if(!$postParaComentarios) disabled @endif>
                                        <i class="fas fa-comments me-1"></i>Comentários
                                    </button>
                                </div>
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
        @if($abaAtiva === 'posts')
            @include('church.posts.tabs.posts')
        @elseif($abaAtiva === 'reacoes')
            @include('church.posts.tabs.reacoes')
        @elseif($abaAtiva === 'comentarios')
            @include('church.posts.tabs.comentarios')
        @endif

        {{-- MODALS --}}
        @include('church.posts.modals.post-modal')
        @include('church.posts.modals.view-post-modal')
        @include('church.posts.modals.delete-post-modal')

    </div>
</div>
