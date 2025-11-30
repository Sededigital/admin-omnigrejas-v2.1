<!-- Modal de Função -->
<div class="modal fade" id="funcaoModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-users-cog text-success me-2"></i>
                    {{ $isEditingFuncao ? 'Editar Função' : 'Nova Função' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form wire:submit="salvarFuncao">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nome da Função <span class="text-danger">*</span></label>
                            <input type="text"  autocomplete="new-password" class="form-control @error('funcaoNome') is-invalid @enderror"
                                   wire:model="funcaoNome" placeholder="Ex: Tesoureiro">
                            @error('funcaoNome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nível <span class="text-danger">*</span></label>
                            <select class="form-select @error('funcaoNivel') is-invalid @enderror" wire:model="funcaoNivel">
                                <option value="baixo">Baixo</option>
                                <option value="medio" selected>Médio</option>
                                <option value="alto">Alto</option>
                                <option value="critico">Crítico</option>
                            </select>
                            @error('funcaoNivel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control @error('funcaoDescricao') is-invalid @enderror"
                                      wire:model="funcaoDescricao" rows="2"
                                      placeholder="Descreva as responsabilidades desta função..."></textarea>
                            @error('funcaoDescricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Permissões <span class="text-danger">*</span></label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                @if($permissoesDisponiveis->count() > 0)
                                    @foreach($permissoesDisponiveis->groupBy('categoria.nome') as $categoriaNome => $permissoes)
                                        <div class="mb-3">
                                            <h6 class="text-primary mb-2">{{ $categoriaNome }}</h6>
                                            <div class="row">
                                                @foreach($permissoes as $permissao)
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                   wire:model="funcaoPermissoesSelecionadas"
                                                                   value="{{ $permissao->id }}"
                                                                   id="permissao-{{ $permissao->id }}">
                                                            <label class="form-check-label" for="permissao-{{ $permissao->id }}">
                                                                <strong>{{ $permissao->nome }}</strong>
                                                                @if($permissao->descricao)
                                                                    <br><small class="text-muted">{{ Str::limit($permissao->descricao, 60) }}</small>
                                                                @endif
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">Nenhuma permissão disponível. Crie permissões primeiro.</p>
                                @endif
                            </div>
                            @error('funcaoPermissoesSelecionadas')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="funcaoAtiva" id="funcaoAtiva">
                                <label class="form-check-label fw-semibold" for="funcaoAtiva">
                                    Função Ativa
                                </label>
                                <br><small class="text-muted">Funções inativas não podem ser atribuídas a membros</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-save me-1"></i>{{ $isEditingFuncao ? 'Atualizar' : 'Criar' }} Função
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