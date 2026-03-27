<!-- Modal de Atribuição -->
<div class="modal fade" id="atribuicaoModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-tag text-warning me-2"></i>Atribuir Função
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form wire:submit="atribuirFuncao">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Membro <span class="text-danger">*</span></label>
                            <select class="form-select @error('atribuicaoMembroId') is-invalid @enderror" wire:model="atribuicaoMembroId">
                                <option value="">Selecione um membro</option>
                                @foreach($membrosDisponiveis as $membro)
                                    <option value="{{ $membro['id'] }}">{{ $membro['nome'] }} ({{ ucfirst($membro['cargo']) }})</option>
                                @endforeach
                            </select>
                            @error('atribuicaoMembroId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Função <span class="text-danger">*</span></label>
                            <select class="form-select @error('atribuicaoFuncaoId') is-invalid @enderror" wire:model="atribuicaoFuncaoId">
                                <option value="">Selecione uma função</option>
                                @foreach($funcoesDisponiveis as $funcao)
                                    <option value="{{ $funcao->id }}">{{ $funcao->nome }}</option>
                                @endforeach
                            </select>
                            @error('atribuicaoFuncaoId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Válido até</label>
                            <input type="date" class="form-control @error('atribuicaoValidoAte') is-invalid @enderror"
                                   wire:model="atribuicaoValidoAte" min="{{ now()->format('Y-m-d') }}">
                            <small class="text-muted">Deixe em branco para atribuição permanente</small>
                            @error('atribuicaoValidoAte')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Permissões da Função</label>
                            <div class="form-control-plaintext pt-2">
                                @if($atribuicaoFuncaoId)
                                    @php
                                        $funcaoSelecionada = $funcoesDisponiveis->find($atribuicaoFuncaoId);
                                    @endphp
                                    @if($funcaoSelecionada && $funcaoSelecionada->permissoes->count() > 0)
                                        <span class="badge bg-info text-light me-1">{{ $funcaoSelecionada->permissoes->count() }}</span>
                                        <small>permissões incluídas</small>
                                    @else
                                        <small class="text-muted">Nenhuma permissão</small>
                                    @endif
                                @else
                                    <small class="text-muted">Selecione uma função</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Observações</label>
                            <textarea class="form-control @error('atribuicaoObservacoes') is-invalid @enderror"
                                      wire:model="atribuicaoObservacoes" rows="2"
                                      placeholder="Observações sobre esta atribuição..."></textarea>
                            @error('atribuicaoObservacoes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-user-tag me-1"></i>Atribuir Função
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin me-1"></i>Atribuindo...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

