<!-- Modal para Configuração de Cores do Cartão -->
<div class="modal fade" id="cardConfigModal" tabindex="-1" aria-labelledby="cardConfigModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-info text-light text-white">
                <h5 class="modal-title" id="cardConfigModalLabel">
                    <i class="fas fa-palette me-2"></i>Configurar Cores do Cartão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body">
                <form wire:submit.prevent="salvarConfiguracao" enctype="multipart/form-data">

                    <!-- Informações -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Configure as cores que serão usadas nos cartões de membro da sua igreja.
                        As cores devem estar no formato hexadecimal (#RRGGBB).
                    </div>

                    <!-- Cores do Header -->
                    <div class="row g-4 mb-5">
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-info text-light text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-header"></i>
                                </div>
                                <div>
                                    <h5 class="text-info mb-1">Cores do Cabeçalho</h5>
                                    <small class="text-muted">Configure as cores do cabeçalho do cartão</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <input type="color" class="form-control form-control-color form-control-lg" wire:model="cor_fundo_header"
                                               value="{{ $cor_fundo_header ?? '#8B5CF6' }}" style="width: 80px; height: 60px;">
                                    </div>
                                    <h6 class="card-title text-info">
                                        <i class="fas fa-fill-drip me-2"></i>Fundo do Cabeçalho
                                    </h6>
                                    <p class="card-text text-muted small">{{ $cor_fundo_header ?? '#8B5CF6' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <input type="color" class="form-control form-control-color form-control-lg" wire:model="cor_texto_header"
                                               value="{{ $cor_texto_header ?? '#FFFFFF' }}" style="width: 80px; height: 60px;">
                                    </div>
                                    <h6 class="card-title text-info">
                                        <i class="fas fa-font me-2"></i>Texto do Cabeçalho
                                    </h6>
                                    <p class="card-text text-muted small">{{ $cor_texto_header ?? '#FFFFFF' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cores do Texto Principal -->
                    <div class="row g-4 mb-5">
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-text-height"></i>
                                </div>
                                <div>
                                    <h5 class="text-success mb-1">Cores do Texto</h5>
                                    <small class="text-muted">Configure as cores dos textos do cartão</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <input type="color" class="form-control form-control-color form-control-lg" wire:model="cor_texto_principal"
                                               value="{{ $cor_texto_principal ?? '#1F2937' }}" style="width: 80px; height: 60px;">
                                    </div>
                                    <h6 class="card-title text-success">
                                        <i class="fas fa-bold me-2"></i>Texto Principal
                                    </h6>
                                    <p class="card-text text-muted small">{{ $cor_texto_principal ?? '#1F2937' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <input type="color" class="form-control form-control-color form-control-lg" wire:model="cor_texto_secundario"
                                               value="{{ $cor_texto_secundario ?? '#6B7280' }}" style="width: 80px; height: 60px;">
                                    </div>
                                    <h6 class="card-title text-success">
                                        <i class="fas fa-italic me-2"></i>Texto Secundário
                                    </h6>
                                    <p class="card-text text-muted small">{{ $cor_texto_secundario ?? '#6B7280' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <input type="color" class="form-control form-control-color form-control-lg" wire:model="cor_acento"
                                               value="{{ $cor_acento ?? '#8B5CF6' }}" style="width: 80px; height: 60px;">
                                    </div>
                                    <h6 class="card-title text-success">
                                        <i class="fas fa-palette me-2"></i>Cor de Destaque
                                    </h6>
                                    <p class="card-text text-muted small">{{ $cor_acento ?? '#8B5CF6' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cores dos Status -->
                    <div class="row g-4 mb-5">
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-tags"></i>
                                </div>
                                <div>
                                    <h5 class="text-warning mb-1">Cores dos Status</h5>
                                    <small class="text-muted">Configure as cores para cada status do cartão</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <input type="color" class="form-control form-control-color form-control-lg" wire:model="cor_status_ativo"
                                               value="{{ $cor_status_ativo ?? '#10B981' }}" style="width: 80px; height: 60px;">
                                    </div>
                                    <h6 class="card-title text-success">
                                        <i class="fas fa-check-circle me-2"></i>Status Ativo
                                    </h6>
                                    <p class="card-text text-muted small">{{ $cor_status_ativo ?? '#10B981' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <input type="color" class="form-control form-control-color form-control-lg" wire:model="cor_status_inativo"
                                               value="{{ $cor_status_inativo ?? '#DC3545' }}" style="width: 80px; height: 60px;">
                                    </div>
                                    <h6 class="card-title text-danger">
                                        <i class="fas fa-times-circle me-2"></i>Status Inativo
                                    </h6>
                                    <p class="card-text text-muted small">{{ $cor_status_inativo ?? '#DC3545' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <input type="color" class="form-control form-control-color form-control-lg" wire:model="cor_status_perdido"
                                               value="{{ $cor_status_perdido ?? '#FD7E14' }}" style="width: 80px; height: 60px;">
                                    </div>
                                    <h6 class="card-title text-warning">
                                        <i class="fas fa-question-circle me-2"></i>Status Perdido
                                    </h6>
                                    <p class="card-text text-muted small">{{ $cor_status_perdido ?? '#FD7E14' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <input type="color" class="form-control form-control-color form-control-lg" wire:model="cor_status_danificado"
                                               value="{{ $cor_status_danificado ?? '#6F42C1' }}" style="width: 80px; height: 60px;">
                                    </div>
                                    <h6 class="card-title text-purple">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Status Danificado
                                    </h6>
                                    <p class="card-text text-muted small">{{ $cor_status_danificado ?? '#6F42C1' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <input type="color" class="form-control form-control-color form-control-lg" wire:model="cor_status_renovado"
                                               value="{{ $cor_status_renovado ?? '#20C997' }}" style="width: 80px; height: 60px;">
                                    </div>
                                    <h6 class="card-title text-info">
                                        <i class="fas fa-sync-alt me-2"></i>Status Renovado
                                    </h6>
                                    <p class="card-text text-muted small">{{ $cor_status_renovado ?? '#20C997' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <input type="color" class="form-control form-control-color form-control-lg" wire:model="cor_status_cancelado"
                                               value="{{ $cor_status_cancelado ?? '#6C757D' }}" style="width: 80px; height: 60px;">
                                    </div>
                                    <h6 class="card-title text-secondary">
                                        <i class="fas fa-ban me-2"></i>Status Cancelado
                                    </h6>
                                    <p class="card-text text-muted small">{{ $cor_status_cancelado ?? '#6C757D' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pré-visualização -->
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-info text-light text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div>
                                    <h5 class="text-info mb-1">Pré-visualização do Cartão</h5>
                                    <small class="text-muted">Veja como ficará o cartão com as cores selecionadas</small>
                                </div>
                            </div>

                            <div class="card border-0 shadow-lg">
                                <div class="card-body bg-gradient-to-br from-light to-white p-4">
                                    <div class="text-center mb-4">
                                        <h6 class="text-info">
                                            <i class="fas fa-id-card me-2"></i>Cartão de Membro
                                        </h6>
                                        <small class="text-muted">Pré-visualização em tempo real</small>
                                    </div>

                                    <div class="d-flex justify-content-center">
                                        <div class="position-relative">
                                            <!-- Sombra do cartão -->
                                            <div style="width: 220px; height: 140px; background: rgba(0,0,0,0.1); border-radius: 12px; position: absolute; top: 8px; left: 8px; z-index: 1;"></div>

                                            <!-- Cartão principal -->
                                            <div style="width: 220px; height: 140px; background: {{ $cor_fundo_header ?? '#8B5CF6' }}; border-radius: 12px; padding: 12px; color: {{ $cor_texto_header ?? '#FFFFFF' }}; font-size: 11px; position: relative; z-index: 2; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                                <div class="text-center mb-3">
                                                    <strong style="color: {{ $cor_texto_header ?? '#FFFFFF' }}; font-size: 9px; letter-spacing: 1px;">CARTÃO DE MEMBRO</strong>
                                                </div>
                                                <div style="background: white; border-radius: 8px; padding: 8px; height: 90px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                                                    <div style="color: {{ $cor_texto_principal ?? '#1F2937' }}; font-weight: bold; font-size: 10px; margin-bottom: 4px;">Nome do Membro</div>
                                                    <div style="color: {{ $cor_texto_secundario ?? '#6B7280' }}; font-size: 8px;">Cargo: Membro</div>
                                                    <div style="color: {{ $cor_texto_secundario ?? '#6B7280' }}; font-size: 8px;">Nº: IGREJA-2025-0001</div>
                                                    <div style="color: {{ $cor_status_ativo ?? '#10B981' }}; font-size: 8px; font-weight: bold; margin-top: 4px;">● Status: Ativo</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            As cores são aplicadas automaticamente nos cartões gerados
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer do Modal -->
            <div class="modal-footer border-top bg-gradient-to-r from-light to-white">
                <div class="d-flex justify-content-between w-100">
                    <div>
                        <button type="button" class="btn btn-outline-secondary btn-lg" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-info btn-lg me-3" wire:click="restaurarPadrao">
                            <i class="fas fa-undo me-2"></i>Restaurar Padrão
                        </button>
                        <button type="button" class="btn btn-success btn-lg" wire:click="salvarConfiguracao" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="salvarConfiguracao">
                                <i class="fas fa-save me-2"></i>Salvar Configuração
                            </span>
                            <span wire:loading wire:target="salvarConfiguracao">
                                <i class="fas fa-spinner fa-spin me-2"></i>Salvando...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
