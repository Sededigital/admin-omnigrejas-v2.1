<!-- Modal para Cadastro/Edição de Igreja - Only Churches -->
<div class="modal fade" id="churchModal" tabindex="-1" aria-labelledby="churchModalLabel" aria-hidden="true"
      data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
     <div class="modal-dialog modal-xl modal-dialog-centered">
         <div class="modal-content">
             <!-- Header do Modal -->
             <div class="modal-header bg-light border-bottom">
                 <h5 class="modal-title fw-bold" id="churchModalLabel">
                     <i class="fas fa-building text-info me-2"></i>
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
                                 <i class="fas fa-info-circle text-info me-1"></i>Informações Básicas
                             </button>
                             <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-details-tab"
                                     data-bs-toggle="tab" data-bs-target="#nav-details" type="button" role="tab">
                                 <i class="fas fa-file-alt text-info me-1"></i>Detalhes
                             </button>
                             <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-config-tab"
                                     data-bs-toggle="tab" data-bs-target="#nav-config" type="button" role="tab">
                                 <i class="fas fa-cog text-info me-1"></i>Configurações
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
                                         <label><i class="fas fa-building text-info me-1"></i>Nome da Igreja *</label>
                                         @error('nome')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>

                                 <!-- NIF -->
                                 <div class="col-md-4">
                                     <div class="form-floating mb-3">
                                         <input type="text"  autocomplete="new-password" class="form-control @error('nif') is-invalid @enderror"
                                                wire:model="nif" placeholder="Número de Identificação Fiscal" required>
                                         <label><i class="fas fa-id-card text-info me-1"></i>NIF *</label>
                                         @error('nif')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>

                                 <!-- Sigla -->
                                 <div class="col-md-4">
                                     <div class="form-floating mb-3">
                                         <input type="text"  autocomplete="new-password" class="form-control text-uppercase @error('sigla') is-invalid @enderror"
                                                wire:model="sigla" placeholder="Ex: IBC" maxlength="10">
                                         <label><i class="fas fa-tag text-info me-1"></i>Sigla</label>
                                         @error('sigla')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>

                                 <!-- Tipo -->
                                 <div class="col-md-4">
                                     <div class="form-floating mb-3">
                                         <select class="form-select @error('tipo') is-invalid @enderror" wire:model.live="tipo" required>
                                             <option value="independente">🏛️ Independente</option>
                                             <option value="sede">🏢 Sede</option>
                                             <option value="filial">🏪 Filial</option>
                                         </select>
                                         <label><i class="fas fa-sitemap text-info me-1"></i>Tipo *</label>
                                         @error('tipo')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>

                                 <!-- Sede (apenas para filiais) -->
                                 <div class="col-md-4" id="sede-field" style="display: {{ $this->tipo === 'filial' ? 'block' : 'none' }};">
                                     <div class="form-floating mb-3">
                                         <select class="form-select @error('sede_id') is-invalid @enderror" wire:model="sede_id">
                                             <option value="">Selecione a sede</option>
                                             @foreach($sedes as $sede)
                                                 <option value="{{ $sede->id }}">{{ $sede->nome }}</option>
                                             @endforeach
                                         </select>
                                         <label><i class="fas fa-building text-info me-1"></i>Igreja Sede</label>
                                         @error('sede_id')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>

                                 <!-- Status Visual -->
                                 <div class="col-12">
                                     <div class="alert alert-light border">
                                         <i class="fas fa-info-circle text-info me-2"></i>
                                         <strong>Status:</strong>
                                         <span class="text-muted">
                                             {{ $this->isEditing ? 'Editando Igreja' : 'Nova Igreja' }}
                                         </span>
                                     </div>
                                 </div>
                             </div>
                        </div>

                        {{-- JavaScript para controlar campos dinâmicos --}}
                        <script>
                        document.addEventListener('livewire:updated', function () {
                            // Controlar visibilidade do campo sede baseado no tipo
                            const tipoSelect = document.querySelector('select[name="tipo"]');
                            const sedeField = document.getElementById('sede-field');

                            if (tipoSelect && sedeField) {
                                function toggleSedeField() {
                                    if (tipoSelect.value === 'filial') {
                                        sedeField.style.display = 'block';
                                    } else {
                                        sedeField.style.display = 'none';
                                    }
                                }

                                // Executar no carregamento
                                toggleSedeField();

                                // Executar quando o valor mudar
                                tipoSelect.addEventListener('change', toggleSedeField);
                            }
                        });
                        </script>

                         <!-- Aba: Detalhes -->
                         <div class="tab-pane fade" id="nav-details" role="tabpanel">
                             <div class="row g-3">
                                 <!-- Localização -->
                                 <div class="col-md-6">
                                     <div class="form-floating mb-3">
                                         <input type="text"  autocomplete="new-password" class="form-control @error('localizacao') is-invalid @enderror"
                                                wire:model="localizacao" placeholder="Endereço completo">
                                         <label><i class="fas fa-map-marker-alt text-info me-1"></i>Localização</label>
                                         @error('localizacao')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>

                                 <!-- Contato -->
                                 <div class="col-md-6">
                                     <div class="form-floating mb-3">
                                         <input type="text"  autocomplete="new-password" class="form-control @error('contacto') is-invalid @enderror"
                                                wire:model="contacto" placeholder="Telefone, email, etc.">
                                         <label><i class="fas fa-phone text-info me-1"></i>Contato</label>
                                         @error('contacto')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>

                                 <!-- Categoria -->
                                 <div class="col-md-6">
                                     <div class="form-floating mb-3">
                                         <select class="form-select @error('categoria_id') is-invalid @enderror" wire:model="categoria_id">
                                             <option value="">Selecione uma categoria</option>
                                             @foreach($categorias as $categoria)
                                                 <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                                             @endforeach
                                         </select>
                                         <label><i class="fas fa-tags text-info me-1"></i>Categoria</label>
                                         @error('categoria_id')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>

                                 <!-- Aliança -->
                                 <div class="col-md-6">
                                     <div class="form-floating mb-3">
                                         <select class="form-select @error('alianca_id') is-invalid @enderror" wire:model="alianca_id">
                                             <option value="">Selecione uma aliança (opcional)</option>
                                             @foreach($aliancas as $alianca)
                                                 <option value="{{ $alianca->id }}">{{ $alianca->nome }}</option>
                                             @endforeach
                                         </select>
                                         <label><i class="fas fa-handshake text-info me-1"></i>Aliança</label>
                                         @error('alianca_id')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>

                                 <!-- Descrição -->
                                 <div class="col-12">
                                     <div class="form-floating mb-3">
                                         <textarea class="form-control @error('descricao') is-invalid @enderror" rows="3"
                                                   wire:model="descricao" placeholder="Descrição breve da igreja" style="height: 100px;"></textarea>
                                         <label><i class="fas fa-align-left text-info me-1"></i>Descrição</label>
                                         @error('descricao')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>

                                 <!-- Sobre -->
                                 <div class="col-12">
                                     <div class="form-floating mb-3">
                                         <textarea class="form-control @error('sobre') is-invalid @enderror" rows="4"
                                                   wire:model="sobre" placeholder="Informações detalhadas sobre a igreja, missão, visão, etc." style="height: 120px;"></textarea>
                                         <label><i class="fas fa-info-circle text-info me-1"></i>Sobre a Igreja</label>
                                         @error('sobre')
                                             <div class="invalid-feedback">{{ $message }}</div>
                                         @enderror
                                     </div>
                                 </div>
                             </div>
                         </div>

                         <!-- Aba: Configurações -->
                         <div class="tab-pane fade" id="nav-config" role="tabpanel">
                             <div class="row g-3">
                                 <!-- Logo da Igreja -->
                                 <div class="col-12">
                                     <div class="card border-0 bg-light">
                                         <div class="card-body">
                                             <h6 class="fw-bold text-info mb-3">
                                                 <i class="fas fa-image me-2"></i>Logo da Igreja
                                             </h6>
                                             <div class="row g-3 align-items-center">
                                                 <div class="col-md-8">
                                                     <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                                            wire:model="logo" accept="image/*">
                                                     <small class="text-muted mt-1 d-block">
                                                         <i class="fas fa-info-circle me-1"></i>Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB
                                                     </small>
                                                     @error('logo')
                                                         <div class="invalid-feedback">{{ $message }}</div>
                                                     @enderror
                                                 </div>
                                                 <div class="col-md-4 text-center">
                                                     @if($logo)
                                                         <div class="border rounded p-2 bg-white">
                                                             <img src="{{ $logo->temporaryUrl() }}" alt="Preview" class="img-fluid rounded shadow-sm" style="max-height: 120px;">
                                                             <small class="text-muted d-block mt-2">Preview</small>
                                                         </div>
                                                     @else
                                                         <div class="border rounded p-4 bg-white text-muted">
                                                             <i class="fas fa-image fa-3x mb-2"></i>
                                                             <br><small>Sem logo</small>
                                                         </div>
                                                     @endif
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>

                                 <!-- Configurações Adicionais (futuras) -->
                                 <div class="col-12">
                                     <div class="alert alert-info">
                                         <i class="fas fa-info-circle me-2"></i>
                                         <strong>Configurações Avançadas</strong><br>
                                         <small>Outras configurações estarão disponíveis em breve.</small>
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
                 <button type="button" class="btn bg-info text-light" wire:click="saveChurch" wire:loading.attr="disabled">
                     <span wire:loading.remove wire:target="saveChurch">
                         <i class="fas fa-save me-1"></i>{{ $this->isEditing ? 'Atualizar Igreja' : 'Salvar Igreja' }}
                     </span>
                     <span wire:loading wire:target="saveChurch">
                         <i class="fas fa-spinner fa-spin me-1"></i>{{ $this->isEditing ? 'Atualizando...' : 'Salvando...' }}
                     </span>
                 </button>
             </div>
         </div>
     </div>
</div>
