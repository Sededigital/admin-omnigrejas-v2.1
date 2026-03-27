<div class="row">
    <div class="col-lg-8">
        <h5 class="mb-4">
            <i class="fas fa-church text-info me-2"></i>Dados da Igreja
        </h5>

        <!-- Logo da Igreja -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-image me-2"></i>Logo da Igreja
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($igreja && $igreja->logo)
                        <img src="{{ Storage::disk('supabase')->url($igreja->logo) }}" alt="Logo da Igreja"
                             class="img-fluid rounded border shadow-sm"
                             style="max-height: 150px;">
                    @else
                        <div class="bg-light border rounded d-flex align-items-center justify-content-center shadow-sm"
                             style="height: 120px;">
                            <i class="fas fa-church text-muted" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                </div>
                <div class="mb-3">
                    <label for="logo-upload" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-upload me-1"></i>Alterar Logo
                    </label>
                    <input type="file" id="logo-upload" class="d-none" wire:model="igreja_logo" accept="image/*">
                    @error('igreja_logo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Recomendado: 300x300px, PNG ou JPG, máximo 2MB
                </div>
            </div>
        </div>

        <!-- Estatísticas Rápidas da Igreja -->
        @if($igreja)
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-center border-primary">
                    <div class="card-body">
                        <i class="fas fa-users text-info display-6 mb-2"></i>
                        <div class="h4 mb-1 text-info">{{ $igreja->membrosAtivos()->count() }}</div>
                        <div class="text-muted small">Membros Ativos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-success">
                    <div class="card-body">
                        <i class="fas fa-calendar-alt text-success display-6 mb-2"></i>
                        <div class="h4 mb-1 text-success">{{ $igreja->eventos()->where('data_evento', '>=', now())->count() }}</div>
                        <div class="text-muted small">Eventos Ativos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-info">
                    <div class="card-body">
                        <i class="fas fa-graduation-cap text-info display-6 mb-2"></i>
                        <div class="h4 mb-1 text-info">{{ $igreja->cursos()->where('status', 'ativo')->count() }}</div>
                        <div class="text-muted small">Cursos Ativos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-warning">
                    <div class="card-body">
                        <i class="fas fa-hand-holding-heart text-warning display-6 mb-2"></i>
                        <div class="h4 mb-1 text-warning">{{ $igreja->doacoesOnline()->whereMonth('data', now()->month)->count() }}</div>
                        <div class="text-muted small">Doações (Mês)</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Formulário de Dados da Igreja -->
        <form wire:submit.prevent="updateIgreja">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nome da Igreja *</label>
                    <input type="text"  autocomplete="new-password" class="form-control @error('igreja_nome') is-invalid @enderror"
                           wire:model="igreja_nome" placeholder="Nome completo da igreja">
                    @error('igreja_nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Sigla</label>
                    <input type="text"  autocomplete="new-password" class="form-control @error('igreja_sigla') is-invalid @enderror"
                           wire:model="igreja_sigla" placeholder="Ex: IBAL">
                    @error('igreja_sigla') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Descrição/Sobre</label>
                    <textarea class="form-control @error('igreja_descricao') is-invalid @enderror"
                              rows="3" wire:model="igreja_descricao"
                              placeholder="Breve descrição sobre a igreja"></textarea>
                    @error('igreja_descricao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contato</label>
                    <input type="text"  autocomplete="new-password" class="form-control @error('igreja_contacto') is-invalid @enderror"
                           wire:model="igreja_contacto" placeholder="Telefone ou email">
                    @error('igreja_contacto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Localização</label>
                    <input type="text"  autocomplete="new-password" class="form-control @error('igreja_localizacao') is-invalid @enderror"
                           wire:model="igreja_localizacao" placeholder="Cidade, Província">
                    @error('igreja_localizacao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-info" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <i class="fas fa-save me-2"></i>Salvar Dados da Igreja
                    </span>
                    <span wire:loading>
                        <i class="fas fa-spinner fa-spin me-2"></i>Salvando...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <!-- Informações Adicionais da Igreja -->
        @if($igreja)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informações Gerais
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Status de Aprovação</small>
                        <span class="badge bg-{{ $igreja->isAprovada() ? 'success' : 'warning' }} fs-6">
                            {{ $igreja->isAprovada() ? 'Aprovada' : 'Pendente' }}
                        </span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Criada em</small>
                        <span class="text-dark">{{ $igreja->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
                @if($igreja->localizacao)
                <div class="mb-3">
                    <small class="text-muted d-block">Localização</small>
                    <span class="text-dark">{{ $igreja->localizacao }}</span>
                </div>
                @endif
                @if($igreja->contacto)
                <div class="mb-0">
                    <small class="text-muted d-block">Contato</small>
                    <span class="text-dark">{{ $igreja->contacto }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>

<!-- Scripts específicos para a aba admin -->
<script>
document.addEventListener('livewire:loaded', () => {
    // Preview do logo quando selecionado
    document.getElementById('logo-upload')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                console.log('Logo selecionado:', file.name);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
