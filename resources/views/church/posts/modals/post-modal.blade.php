<!-- Modal de Post -->
<div class="modal fade" id="postModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-newspaper text-primary me-2"></i>
                    {{ $isEditingPost ? 'Editar Post' : 'Novo Post' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form wire:submit="salvarPost">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                            <input type="text"  autocomplete="new-password" class="form-control @error('postTitulo') is-invalid @enderror"
                                   wire:model="postTitulo" placeholder="Digite o título do post">
                            @error('postTitulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Conteúdo <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('postContent') is-invalid @enderror"
                                      wire:model="postContent" rows="4"
                                      placeholder="Digite o conteúdo do post..."></textarea>
                            @error('postContent')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Mídia (Imagem ou Vídeo)</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="file" class="form-control @error('postMedia') is-invalid @enderror"
                                           wire:model="postMedia" accept="image/*,video/*">
                                    <small class="text-muted">
                                        Tipos aceitos: JPG, PNG, GIF, MP4, MOV, AVI. Tamanho máximo: 10MB
                                    </small>
                                    @error('postMedia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <!-- Preview da mídia existente -->
                                    @if($postMediaUrl && !$postMedia)
                                        <div>
                                            <label class="form-label">Mídia Atual:</label>
                                            <div class="border rounded p-2">
                                                @if($postIsVideo)
                                                    <video controls style="max-width: 100%; max-height: 150px;">
                                                        <source src="{{ Storage::url($postMediaUrl) }}" type="video/mp4">
                                                        Seu navegador não suporta vídeos.
                                                    </video>
                                                @else
                                                    <img src="{{ Storage::url($postMediaUrl) }}" alt="Post media" style="max-width: 100%; max-height: 150px;">
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Preview da nova mídia -->
                                    @if($postMedia)
                                        <div>
                                            <label class="form-label">Nova Mídia:</label>
                                            <div class="border rounded p-2">
                                                @if(Str::startsWith($postMedia->getMimeType(), 'video/'))
                                                    <video controls style="max-width: 100%; max-height: 150px;">
                                                        <source src="{{ $postMedia->temporaryUrl() }}" type="{{ $postMedia->getMimeType() }}">
                                                        Seu navegador não suporta vídeos.
                                                    </video>
                                                @else
                                                    <img src="{{ $postMedia->temporaryUrl() }}" alt="New post media" style="max-width: 100%; max-height: 150px;">
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Placeholder quando não há mídia -->
                                    @if(!$postMediaUrl && !$postMedia)
                                        <div class="text-center text-muted mt-4">
                                            <i class="fas fa-image fa-2x mb-2"></i>
                                            <p class="small">Nenhuma mídia selecionada</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-save me-1"></i>{{ $isEditingPost ? 'Atualizar' : 'Publicar' }} Post
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin me-1"></i>Salvando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('open-post-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('postModal'));
        modal.show();
    });

    Livewire.on('close-post-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('postModal'));
        if (modal) {
            modal.hide();
        }
    });
});
</script>