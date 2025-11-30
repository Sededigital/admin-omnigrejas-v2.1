<!-- Modal para Cadastro/Edição de Igreja -->
<div class="modal fade" id="churchModal" tabindex="-1" aria-labelledby="churchModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="churchModalLabel">
                    <i class="fas fa-building text-primary me-2"></i>
                    <span id="modal-title">{{ $this->isEditing ? 'Editar Igreja' : 'Cadastrar Nova Igreja' }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-4">
                <form wire:submit.prevent="saveChurch">

                    <!-- Navegação por Abas (Bootstrap puro) -->
                    <nav class="mb-4">
                        <div class="nav nav-tabs border-bottom-0" id="nav-tab" role="tablist">
                            <button class="nav-link active border-0 bg-transparent fw-semibold" id="nav-basic-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-basic" type="button" role="tab">
                                <i class="fas fa-info-circle text-primary me-1"></i>Informações Básicas
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-details-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-details" type="button" role="tab">
                                <i class="fas fa-file-alt text-primary me-1"></i>Detalhes
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-config-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-config" type="button" role="tab">
                                <i class="fas fa-cog text-primary me-1"></i>Configurações
                            </button>
                        </div>
                    </nav>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content" id="nav-tabContent">

                        <!-- Aba: Informações Básicas -->
                        <div class="tab-pane fade show active" id="nav-basic" role="tabpanel">
                            <div class="row g-3">
                                <!-- Nome da Igreja -->
                                <div class="col-md-8">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('nome') is-invalid @enderror"
                                               wire:model="nome" placeholder="Digite o nome completo da igreja" required>
                                        <label><i class="fas fa-building text-primary me-1"></i>Nome da Igreja *</label>
                                        @error('nome')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Sigla -->
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control text-uppercase @error('sigla') is-invalid @enderror"
                                               wire:model="sigla" placeholder="Ex: IBC" maxlength="10">
                                        <label><i class="fas fa-tag text-primary me-1"></i>Sigla</label>
                                        @error('sigla')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Contacto -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="tel" class="form-control @error('contacto') is-invalid @enderror"
                                               wire:model="contacto" placeholder="+244 900 000 000" required>
                                        <label><i class="fas fa-phone text-primary me-1"></i>Contacto *</label>
                                        @error('contacto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- NIF -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 position-relative">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('nif') is-invalid @enderror"
                                               wire:model="nif" placeholder="Número de Identificação Fiscal" required style="padding-right: 40px;">
                                        <i class="fas fa-check-circle text-success position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none;"></i>
                                        <label><i class="fas fa-id-card text-primary me-1"></i>NIF *</label>
                                        @error('nif')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Localização -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('localizacao') is-invalid @enderror"
                                                  wire:model="localizacao" rows="2" style="height: 80px;"
                                                  placeholder="Endere��o completo da igreja" required></textarea>
                                        <label><i class="fas fa-map-marker-alt text-primary me-1"></i>Localização *</label>
                                        @error('localizacao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Logo -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fas fa-image text-primary me-1"></i>Logo da Igreja</label>
                                        <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                               wire:model="logo" accept="image/*">
                                        @error('logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text text-muted small">
                                            Formatos aceites: JPG, PNG, GIF. Tamanho máximo: 2MB
                                        </div>
                                        @if ($logo)
                                            <div class="mt-2">
                                                <img src="{{ $logo->temporaryUrl() }}" class="img-thumbnail" style="max-height: 100px;">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Aba: Detalhes -->
                        <div class="tab-pane fade" id="nav-details" role="tabpanel">
                            <div class="row g-3">
                                <!-- Descrição -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('descricao') is-invalid @enderror"
                                                  wire:model="descricao" rows="3" style="height: 100px;"
                                                  placeholder="Breve descrição da igreja"></textarea>
                                        <label><i class="fas fa-align-left text-primary me-1"></i>Descrição</label>
                                        @error('descricao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Sobre -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('sobre') is-invalid @enderror"
                                                  wire:model="sobre" rows="4" style="height: 120px;"
                                                  placeholder="Informações detalhadas sobre a história e missão da igreja"></textarea>
                                        <label><i class="fas fa-info-circle text-primary me-1"></i>Sobre a Igreja</label>
                                        @error('sobre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Designação -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('designacao') is-invalid @enderror"
                                               wire:model="designacao" placeholder="Designação oficial/legal da igreja">
                                        <label><i class="fas fa-certificate text-primary me-1"></i>Designação Oficial</label>
                                        @error('designacao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Configurações -->
                        <div class="tab-pane fade" id="nav-config" role="tabpanel">
                            <div class="row g-3">
                                <!-- Tipo de Igreja -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('tipo') is-invalid @enderror"
                                                wire:model="tipo" id="tipo-select">
                                            <option value="independente">Independente</option>
                                            <option value="sede">Sede</option>
                                            <option value="filial">Filial</option>
                                        </select>
                                        <label><i class="fas fa-sitemap text-primary me-1"></i>Tipo de Igreja *</label>
                                        @error('tipo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Categoria da Igreja -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('categoria_id') is-invalid @enderror"
                                                wire:model="categoria_id">
                                            <option value="">Selecione a categoria</option>
                                            @foreach($categorias as $categoria)
                                                <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                                            @endforeach
                                        </select>
                                        <label><i class="fas fa-tags text-primary me-1"></i>Categoria da Igreja *</label>
                                        @error('categoria_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Aliança da Igreja -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('alianca_id') is-invalid @enderror"
                                                wire:model="alianca_id">
                                            <option value="">Selecione a aliança (opcional)</option>
                                            @foreach($aliancas as $alianca)
                                                <option value="{{ $alianca->id }}">{{ $alianca->nome }}</option>
                                            @endforeach
                                        </select>
                                        <label><i class="fas fa-handshake text-primary me-1"></i>Aliança da Igreja</label>
                                        @error('alianca_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status de Aprovação -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('status_aprovacao') is-invalid @enderror"
                                                wire:model="status_aprovacao">
                                            <option value="pendente">Pendente</option>
                                            <option value="aprovado">Aprovado</option>
                                            <option value="rejeitado">Rejeitado</option>
                                        </select>
                                        <label><i class="fas fa-check-circle text-primary me-1"></i>Status de Aprovação *</label>
                                        @error('status_aprovacao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Espaço vazio para alinhamento -->
                                <div class="col-md-6"></div>

                                <!-- Campo Sede (apenas para filiais) -->
                                <div class="col-12" id="sede-field" style="display: none;">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('sede_id') is-invalid @enderror"
                                                wire:model="sede_id">
                                            <option value="">Selecione a igreja sede</option>
                                            @foreach($sedes as $sede)
                                                <option value="{{ $sede->id }}">{{ $sede->nome }}</option>
                                            @endforeach
                                        </select>
                                        <label><i class="fas fa-building text-primary me-1"></i>Igreja Sede</label>
                                        @error('sede_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status Visual -->
                                <div class="col-12">
                                    <div class="alert alert-light border">
                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                        <strong>Status:</strong>
                                        <span class="text-muted">
                                            {{ $this->isEditing ? 'Editando Igreja' : 'Nova Igreja' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer do Modal -->
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" wire:click="saveChurch" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="saveChurch">
                        <i class="fas fa-save me-1"></i>{{ $this->isEditing ? 'Atualizar Igreja' : 'Salvar Igreja' }}
                    </span>
                    <span wire:loading wire:target="saveChurch">
                        <i class="fas fa-spinner fa-spin me-1"></i>{{ $isEditing ? 'Atualizando...' : 'Salvando...' }}
                    </span>
                </button>
            </div>
        </div>
    </div>


</div>
