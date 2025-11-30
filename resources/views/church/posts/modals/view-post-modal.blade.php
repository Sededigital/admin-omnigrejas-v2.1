<!-- Modal de Visualização de Post -->
<div class="modal fade" id="viewPostModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye text-info me-2"></i>{{ $postSelecionado ? Str::limit($postSelecionado->titulo, 50) : 'Visualizar Post' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($postSelecionado)
                    <!-- Mídia do Post -->
                    @if($postSelecionado->media_url)
                        <div class="mb-3">
                            @if($postSelecionado->is_video)
                                <video class="w-100 rounded" controls style="max-height: 250px;">
                                    <source src="{{ \App\Helpers\SupabaseHelper::obterUrl($postSelecionado->media_url) }}" type="video/mp4">
                                    Seu navegador não suporta vídeos.
                                </video>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-video me-1"></i>
                                        Vídeo: {{ $postSelecionado->media_nome ?? 'Vídeo' }}
                                        @if($postSelecionado->media_tamanho)
                                            ({{ number_format($postSelecionado->media_tamanho / 1024 / 1024, 2) }} MB)
                                        @endif
                                    </small>
                                </div>
                            @else
                                <img src="{{ \App\Helpers\SupabaseHelper::obterUrl($postSelecionado->media_url) }}"
                                     class="w-100 rounded" alt="Post image" style="max-height: 250px; object-fit: cover;">
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-image me-1"></i>
                                        Imagem: {{ $postSelecionado->media_nome ?? 'Imagem' }}
                                        @if($postSelecionado->media_tamanho)
                                            ({{ number_format($postSelecionado->media_tamanho / 1024 / 1024, 2) }} MB)
                                        @endif
                                    </small>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Conteúdo do Post -->
                    <div class="mb-3">
                        <h6 class="fw-bold">Conteúdo:</h6>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($postSelecionado->content)) !!}
                        </div>
                    </div>

                    <!-- Informações do Post -->
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>
                                <strong>Autor:</strong> {{ $postSelecionado->autor->name }}
                            </small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                <strong>Data:</strong> {{ $postSelecionado->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>

                @else
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                        <h5>Post não encontrado</h5>
                        <p class="text-muted">O post solicitado não pôde ser carregado.</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Fechar
                </button>
                @if($postSelecionado)
                    <button class="btn btn-primary" wire:click="verReacoes({{ $postSelecionado->id }})" data-bs-dismiss="modal">
                        <i class="fas fa-heart me-1"></i>Ver Reações
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('open-view-post-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('viewPostModal'));
        modal.show();
    });

    Livewire.on('close-view-post-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('viewPostModal'));
        if (modal) {
            modal.hide();
        }
    });
});
</script>