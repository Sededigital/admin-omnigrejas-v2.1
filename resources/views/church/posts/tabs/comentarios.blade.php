<!-- Header com informações do post -->
@if($postParaComentarios)
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-1">
                    <i class="fas fa-comments text-info me-2"></i>Comentários do Post
                </h5>
                <p class="mb-0 text-muted">
                    <strong>"{{ Str::limit($postParaComentarios->titulo, 60) }}"</strong>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-outline-secondary btn-sm" wire:click="voltarParaPostsComentarios">
                    <i class="fas fa-arrow-left me-1"></i>Voltar para Posts
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Formulário para adicionar comentário -->
@if($postParaComentarios)
<div class="card mb-4">
    <div class="card-body">
        <h6 class="mb-3">
            <i class="fas fa-plus-circle text-primary me-2"></i>Adicionar Comentário
        </h6>
        <form wire:submit="adicionarComentario">
            <div class="mb-3">
                <textarea
                    class="form-control"
                    rows="3"
                    wire:model="novoComentario"
                    placeholder="Digite seu comentário..."
                    maxlength="1000"
                ></textarea>
                @error('novoComentario')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    {{ strlen($novoComentario) }}/1000 caracteres
                </small>
                <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <i class="fas fa-paper-plane me-1"></i>Comentar
                    </span>
                    <span wire:loading>
                        <i class="fas fa-spinner fa-spin me-1"></i>Enviando...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Filtros para Comentários -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Buscar Comentários</label>
                <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="filtroComentarioBusca" placeholder="Buscar no conteúdo...">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Total</label>
                <div class="form-control-plaintext pt-2">
                    <strong>{{ $comentarios->total() }}</strong> comentários encontrados
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Comentários -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-comments text-info me-2"></i>Comentários
        </h5>
    </div>
    <div class="card-body">
        @if($comentarios->count() > 0)
            <div class="row">
                @foreach($comentarios as $comentario)
                    <div class="col-12 mb-3">
                        <div class="card border-light">
                            <div class="card-body">
                                <!-- Conteúdo do comentário -->
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            <!-- Avatar pequeno -->
                                            <div class="avatar avatar-xs me-2">
                                                @if($comentario->usuario_photo_url)
                                                <img src="{{ Storage::disk('supabase')->url($comentario->usuario_photo_url) }}" alt="Avatar" class="rounded-circle" style="width: 24px; height: 24px;">
                                                @else
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 10px;">
                                                        <span class="fw-bold">{{ substr($comentario->usuario_name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <strong class="text-dark">{{ $comentario->usuario_name }}</strong>
                                                <small class="text-muted ms-2">
                                                    <i class="fas fa-calendar me-1"></i>{{ $comentario->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                        </div>

                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <button class="dropdown-item" wire:click="editarComentario('{{ $comentario->id }}')">
                                                            <i class="fas fa-edit me-2"></i>Editar
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item text-danger" wire:click="excluirComentario('{{ $comentario->id }}')">
                                                            <i class="fas fa-trash me-2"></i>Excluir
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>

                                        </div>

                                        <!-- Conteúdo do comentário -->
                                        @if($comentarioEditando && $comentarioEditando->id === $comentario->id)
                                            <!-- Modo de edição -->
                                            <form wire:submit="salvarEdicaoComentario" class="mb-2">
                                                <div class="mb-2">
                                                    <textarea
                                                        class="form-control"
                                                        rows="3"
                                                        wire:model="comentarioEditado"
                                                        maxlength="1000"
                                                    ></textarea>
                                                    @error('comentarioEditado')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                                                        <span wire:loading.remove>
                                                            <i class="fas fa-save me-1"></i>Salvar
                                                        </span>
                                                        <span wire:loading>
                                                            <i class="fas fa-spinner fa-spin me-1"></i>Salvando...
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="cancelarEdicaoComentario">
                                                        <i class="fas fa-times me-1"></i>Cancelar
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <!-- Visualização normal -->
                                            <div class="text-dark">
                                                {{ $comentario->conteudo }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-center mt-4">
                {{ $comentarios->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-comments text-muted mb-4" style="font-size: 4rem;"></i>
                <h4 class="text-muted">Nenhum comentário encontrado</h4>
                <p class="text-muted mb-4">
                    @if($filtroComentarioBusca)
                        Nenhum comentário encontrado com os filtros aplicados.
                    @else
                        @if($postParaComentarios)
                            Este post ainda não recebeu comentários.
                        @else
                            Selecione um post para visualizar seus comentários.
                        @endif
                    @endif
                </p>
                @if($filtroComentarioBusca)
                    <button class="btn btn-outline-secondary me-2" wire:click="$set('filtroComentarioBusca', '')">
                        <i class="fas fa-times me-1"></i>Limpar Filtro
                    </button>
                @endif
                @if(!$postParaComentarios)
                    <button class="btn btn-outline-secondary" wire:click="voltarParaPostsComentarios">
                        <i class="fas fa-arrow-left me-1"></i>Voltar para Posts
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
