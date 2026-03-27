<!-- Modal para Gerenciar Tipos de Pedido -->
<div class="modal fade" id="typesListModal" tabindex="-1" aria-labelledby="typesListModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="typesListModalLabel">
                    <i class="fas fa-tags me-2"></i>Gerenciar Tipos de Pedido
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulário de Adição/Edição -->
                @if($showTypeForm)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-plus me-2"></i>
                            @if($editingType)
                                Editar Tipo de Pedido
                            @else
                                Novo Tipo de Pedido
                            @endif
                        </h6>
                    </div>
                    <div class="card-body">
                        <form wire:submit="saveType">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="typeNome" class="form-label">Nome do Tipo <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('typeNome') is-invalid @enderror"
                                               id="typeNome" wire:model="typeNome" placeholder="Ex: Batismo, Casamento">
                                        @error('typeNome')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="typeCategoriaId" class="form-label">Categoria <span class="text-danger">*</span></label>
                                        <select class="form-select @error('typeCategoriaId') is-invalid @enderror"
                                                id="typeCategoriaId" wire:model="typeCategoriaId" disabled>
                                            <option value="">Selecione uma categoria</option>
                                            @foreach(\App\Models\Igrejas\CategoriaIgreja::all() as $categoria)
                                                <option value="{{ $categoria->id }}" {{ $categoria->id == $typeCategoriaId ? 'selected' : '' }}>
                                                    {{ $categoria->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Categoria preenchida automaticamente baseada na igreja</small>
                                        @error('typeCategoriaId')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="typeDescricao" class="form-label">Descrição</label>
                                <textarea class="form-control @error('typeDescricao') is-invalid @enderror"
                                          id="typeDescricao" wire:model="typeDescricao" rows="3"
                                          placeholder="Descrição opcional do tipo de pedido"></textarea>
                                @error('typeDescricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-secondary" wire:click="cancelTypeForm">
                                    <i class="fas fa-times me-1"></i>Cancelar
                                </button>
                                <button type="submit" class="btn bg-info text-light">
                                    <i class="fas fa-save me-1"></i>
                                    @if($editingType)
                                        Atualizar Tipo
                                    @else
                                        Criar Tipo
                                    @endif
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Lista de Tipos -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Tipos de Pedido da Igreja</h6>
                    @if(!$showTypeForm)
                    <button class="btn bg-info text-light btn-sm" wire:click="showAddTypeForm">
                        <i class="fas fa-plus me-1"></i>Novo Tipo
                    </button>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Descrição</th>
                                <th>Status</th>
                                <th>Total Pedidos</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\Pedidos\PedidoTipo::where('igreja_id', Auth::user()->getIgrejaId())->get() as $type)
                            <tr>
                                <td>
                                    <strong>{{ $type->nome }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info text-light">{{ $type->categoria->nome ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($type->descricao ?? 'Sem descrição', 50) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $type->ativo ? 'success' : 'secondary' }}">
                                        {{ $type->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-light">{{ $type->total_pedidos }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" wire:click="editType('{{ $type->id }}')" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-{{ $type->ativo ? 'warning' : 'success' }}"
                                                wire:click="toggleTypeStatus('{{ $type->id }}')"
                                                title="{{ $type->ativo ? 'Desativar' : 'Ativar' }}">
                                            <i class="fas fa-{{ $type->ativo ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                        @if($type->total_pedidos == 0)
                                        <button class="btn btn-outline-danger" wire:click="deleteType('{{ $type->id }}')" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-tags text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum tipo de pedido encontrado</div>
                                    @if(!$showTypeForm)
                                    <button class="btn bg-info text-light mt-3" wire:click="showAddTypeForm">
                                        <i class="fas fa-plus me-1"></i>Criar Primeiro Tipo
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:loaded', () => {
    Livewire.on('show-modal', (modalId) => {
        const modal = new bootstrap.Modal(document.getElementById(modalId));
        modal.show();
    });

    Livewire.on('close-modal', (modalId) => {
        const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
        if (modal) {
            modal.hide();
        }
    });
});
</script>
