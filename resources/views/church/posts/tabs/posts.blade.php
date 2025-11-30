<!-- Filtros para Posts -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tipo</label>
                <select class="form-select" wire:model.live="filtroPostTipo">
                    <option value="">Todos os tipos</option>
                    <option value="texto">Texto</option>
                    <option value="imagem">Imagem</option>
                    <option value="video">Vídeo</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold">Buscar</label>
                <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="filtroPostBusca" placeholder="Título ou conteúdo...">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Total</label>
                <div class="form-control-plaintext pt-2">
                    <strong>{{ $posts->total() }}</strong> posts encontrados
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Posts -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-newspaper text-primary me-2"></i>Posts
        </h5>
        <button class="btn btn-primary btn-sm" wire:click="abrirModalPost">
            <i class="fas fa-plus me-1"></i>Novo Post
        </button>
    </div>
    <div class="card-body">
        @if($posts->count() > 0)
            <div class="row">
                @foreach($posts as $post)
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card h-100">
                            <!-- Mídia do Post -->
                            @if($post->media_url)
                                <div class="position-relative">
                                    @if($post->is_video)
                                        <video class="card-img-top" style="height: 200px; object-fit: cover;" controls>
                                            <source src="{{ Storage::url($post->media_url) }}" type="video/mp4">
                                        </video>
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-danger">
                                                <i class="fas fa-video me-1"></i>Vídeo
                                            </span>
                                        </div>
                                    @else
                                        <img src="{{ Storage::url($post->media_url) }}" class="card-img-top" alt="Post image" style="height: 200px; object-fit: cover;">
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-info">
                                                <i class="fas fa-image me-1"></i>Imagem
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 120px;">
                                    <i class="fas fa-file-alt text-muted" style="font-size: 2rem;"></i>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title mb-2">
                                    {{ Str::limit($post->titulo, 50) }}
                                </h6>
                                <p class="card-text text-muted small mb-3 flex-grow-1">
                                    @if($post->media_url)
                                        @if($post->is_video)
                                            Vídeo
                                        @else
                                            Imagem
                                        @endif
                                    @else
                                        Post de texto
                                    @endif
                                </p>

                                <!-- Estatísticas -->
                                <div class="row text-center mb-3">
                                    <div class="col-12">
                                        <div class="text-success">
                                            <i class="fas fa-heart me-1"></i>
                                            <strong>{{ $post->likes_count }}</strong>
                                        </div>
                                        <small class="text-muted">Reações</small>
                                    </div>
                                </div>

                                <!-- Autor e Data -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>{{ $post->autor->name }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>{{ $post->created_at->format('d/m/Y') }}
                                    </small>
                                </div>

                                <!-- Ações -->
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-info" wire:click="verPost({{ $post->id }})" title="Ver Conteúdo">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" wire:click="verReacoes({{ $post->id }})" title="Ver Reações">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" wire:click="verComentarios({{ $post->id }})" title="Ver Comentários">
                                        <i class="fas fa-comments"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" wire:click="abrirModalPost({{ $post->id }})" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" wire:click="excluirPost({{ $post->id }})" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-center mt-4">
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-newspaper text-muted mb-4" style="font-size: 4rem;"></i>
                <h4 class="text-muted">Nenhum post encontrado</h4>
                <p class="text-muted mb-4">
                    @if($filtroPostTipo || $filtroPostBusca)
                        Nenhum post encontrado com os filtros aplicados.
                    @else
                        Ainda não há posts publicados na sua igreja.
                    @endif
                </p>
                @if($filtroPostTipo || $filtroPostBusca)
                    <button class="btn btn-outline-secondary me-2" wire:click="$set('filtroPostTipo', '')" wire:click="$set('filtroPostBusca', '')">
                        <i class="fas fa-times me-1"></i>Limpar Filtros
                    </button>
                @endif
                <button class="btn btn-primary" wire:click="abrirModalPost">
                    <i class="fas fa-plus me-1"></i>Criar Primeiro Post
                </button>
            </div>
        @endif
    </div>
</div>
