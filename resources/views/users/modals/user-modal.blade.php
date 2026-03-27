<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true"
data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
<div class="modal-dialog modal-xl modal-dialog-centered">
   <div class="modal-content">
       <!-- Header do Modal -->
       <div class="modal-header bg-light border-bottom">
           <h5 class="modal-title fw-bold" id="userModalLabel">
               <i class="fas fa-user text-info me-2"></i>
               <span id="modal-title">{{ $editingUser ? 'Editar Usuário' : 'Cadastrar Novo Usuário' }}</span>
           </h5>
           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
       </div>

       <!-- Corpo do Modal -->
       <div class="modal-body p-4">
           <form wire:submit.prevent="saveUser">

               <!-- Navegação por Abas (Bootstrap puro) -->
               <nav class="mb-4">
                   <div class="nav nav-tabs border-bottom-0" id="nav-tab" role="tablist">
                       <button class="nav-link active border-0 bg-transparent fw-semibold" id="nav-basic-tab"
                               data-bs-toggle="tab" data-bs-target="#nav-basic" type="button" role="tab">
                           <i class="fas fa-info-circle text-info me-1"></i>Informações Básicas
                       </button>
                       <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-details-tab"
                               data-bs-toggle="tab" data-bs-target="#nav-details" type="button" role="tab">
                           <i class="fas fa-user-tag text-info me-1"></i>Função e Status
                       </button>
                   </div>
               </nav>

               <!-- Conteúdo das Abas -->
               <div class="tab-content" id="nav-tabContent">

                   <!-- Aba: Informações Básicas -->
                   <div class="tab-pane fade show active" id="nav-basic" role="tabpanel">
                       <div class="row g-3">
                           <!-- Nome -->
                           <div class="col-md-8">
                               <div class="form-floating mb-3">
                                   <input type="text"  autocomplete="new-password" class="form-control @error('name') is-invalid @enderror"
                                          wire:model="name" placeholder="Nome completo" required>
                                   <label><i class="fas fa-user text-info me-1"></i>Nome Completo *</label>
                                   @error('name')
                                       <div class="invalid-feedback">{{ $message }}</div>
                                   @enderror
                               </div>
                           </div>

                           <!-- Status -->
                           <div class="col-md-4">
                               <div class="form-floating mb-3">
                                   <select class="form-select @error('is_active') is-invalid @enderror"
                                           wire:model="is_active">
                                       <option value="1">Ativo</option>
                                       <option value="0">Inativo</option>
                                   </select>
                                   <label><i class="fas fa-toggle-on text-info me-1"></i>Status *</label>
                                   @error('is_active')
                                       <div class="invalid-feedback">{{ $message }}</div>
                                   @enderror
                               </div>
                           </div>

                           <!-- Email -->
                           <div class="col-md-6">
                               <div class="form-floating mb-3">
                                   <input type="email" autocomplete="new-password"  class="form-control @error('email') is-invalid @enderror"
                                          wire:model="email" placeholder="email@exemplo.com" required>
                                   <label><i class="fas fa-envelope text-info me-1"></i>Email *</label>
                                   @error('email')
                                       <div class="invalid-feedback">{{ $message }}</div>
                                   @enderror
                               </div>
                           </div>

                           <!-- Telefone -->
                           <div class="col-md-6">
                               <div class="form-floating mb-3">
                                   <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                          wire:model="phone" placeholder="+244 900 000 000">
                                   <label><i class="fas fa-phone text-info me-1"></i>Telefone</label>
                                   @error('phone')
                                       <div class="invalid-feedback">{{ $message }}</div>
                                   @enderror
                               </div>
                           </div>
                       </div>
                   </div>

                   <!-- Aba: Função e Status -->
                   <div class="tab-pane fade" id="nav-details" role="tabpanel">
                       <div class="row g-3">
                           <!-- Função -->
                           <div class="col-12">
                               <div class="form-floating mb-3">
                                   <select class="form-select @error('role') is-invalid @enderror"
                                           wire:model="role">
                                           <option value="">-- Selecione --</option>
                                           @if(Auth::user()->isRoot())
                                                <option value="root">Domínio</option>
                                           @endif
                                            <option value="super_admin">Super Administrador</option>
                                   </select>
                                   <label><i class="fas fa-user-tag text-info me-1"></i>Função *</label>
                                   @error('role')
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
                                       {{ $editingUser ? 'Editando Usuário' : 'Novo Usuário' }}
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
           <button type="button" class="btn bg-info text-light" wire:click="saveUser" wire:loading.attr="disabled">
               <span wire:loading.remove wire:target="saveUser">
                   <i class="fas fa-save me-1"></i>{{ $editingUser ? 'Atualizar Usuário' : 'Salvar Usuário' }}
               </span>
               <span wire:loading wire:target="saveUser">
                   <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingUser ? 'Atualizando...' : 'Salvando...' }}
               </span>
           </button>
       </div>
   </div>
</div>
</div>
