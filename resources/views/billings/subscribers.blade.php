<div>
    <div class="container-fluid">
        <!-- Header Elegante -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <!-- Seção do Título -->
                            <div class="col-lg-8 col-md-7">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-3 p-3 me-3">
                                        <i class="fas fa-credit-card fa-2x text-info"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-dark mb-1 fw-semibold">Pedidos de Assinaturas</h3>
                                        <p class="text-muted mb-0 small">Gerencie solicitações de pagamento pendentes</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção de Estatísticas e Ações -->
                            <div class="col-lg-4 col-md-5">
                                <div class="d-flex justify-content-end align-items-center gap-3">
                                    <!-- Contador de Pendências -->
                                    <div class="text-center">
                                        <div class="bg-info text-light bg-opacity-10 rounded-3 px-3 py-2 border">
                                            <div class="text-info fw-bold fs-5">{{ $pagamentosPendentes->count() }}</div>
                                            <small class="text-info fw-medium">Pendentes</small>
                                        </div>
                                    </div>

                                    <!-- Botão de Atualizar -->
                                <button class="btn btn-outline-primary rounded-3 px-4"
                                        wire:click="atualizarPagamentos"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="btn-loading">
                                    <i class="fas fa-sync-alt me-2" wire:loading.remove wire:target="atualizarPagamentos"></i>
                                    <i class="fas fa-spinner fa-spin me-2" wire:loading wire:target="atualizarPagamentos"></i>
                                    <span wire:loading.remove wire:target="atualizarPagamentos">Atualizar</span>
                                    <span wire:loading wire:target="atualizarPagamentos">Carregando...</span>
                                </button>
                                </div>
                            </div>
                        </div>

                        <!-- Barra de Status -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <hr class="my-3">
                                <div class="d-flex justify-content-center align-items-center gap-4">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="fas fa-clock me-2"></i>
                                        <small class="fw-medium">Última atualização: {{ now()->format('H:i:s') }}</small>
                                    </div>
                                    <div class="vr" style="height: 16px;"></div>
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="fas fa-eye me-2"></i>
                                        <small class="fw-medium">{{ $pagamentosPendentes->count() }} solicitações aguardando análise</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Pagamentos -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if($pagamentosPendentes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-church me-1"></i> Igreja</th>
                                            <th><i class="fas fa-box me-1"></i> Pacote</th>
                                            <th><i class="fas fa-money-bill-wave me-1"></i> Valor</th>
                                            <th><i class="fas fa-calendar me-1"></i> Data</th>
                                            <th><i class="fas fa-credit-card me-1"></i> Método</th>
                                            <th><i class="fas fa-info-circle me-1"></i> Status</th>
                                            <th><i class="fas fa-cogs me-1"></i> Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pagamentosPendentes as $pagamento)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">

                                                    <div class="avatar avatar-sm me-3">
                                                        @if($pagamento->igreja->logo)
                                                        <img src="{{ Storage::disk('supabase')->url($pagamento->igreja->logo) }}"
                                                        class="me-3 rounded-circle border"
                                                        alt="Logo {{ $pagamento->igreja->nome }}"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="user-avatar bg-info text-light text-white me-3">
                                                            {{ strtoupper(substr($pagamento->igreja->nome ?? 'N', 0, 2)) }}
                                                        </div>
                                                    @endif

                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ Str::limit($pagamento->igreja->nome, 18, '') }}</h6>
                                                        <small class="text-muted">{{ $pagamento->igreja->sigla }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-light">{{ $pagamento->pacote_nome }}</span>
                                                @if($pagamento->is_vitalicio)
                                                    <small class="text-success d-block">Vitalício</small>
                                                @else
                                                    <small class="text-info d-block">{{ $pagamento->duracao_meses }} meses</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-success">{{ $pagamento->getValorFormatado() }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <div>{{ $pagamento->getDataPagamentoFormatada() }}</div>
                                                    <small class="text-muted">{{ $pagamento->data_pagamento->diffForHumans() }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $pagamento->getMetodoFormatado() }}</span>
                                                @if($pagamento->temComprovativo())
                                                    <i class="fas fa-paperclip text-success ms-1"
                                                       title="Comprovativo anexado"
                                                       style="cursor: pointer;"
                                                       wire:click="mostrarComprovativoModal('{{ $pagamento->id }}')"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $pagamento->getStatusBadgeClass() }} text-light">
                                                    {{ $pagamento->getStatusFormatado() }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-success btn-sm"
                                                            wire:click="selecionarPagamento('{{ $pagamento->id }}')"
                                                            title="Aprovar Pagamento"
                                                            @if(!$pagamento->temComprovativo())
                                                                disabled
                                                            @endif>
                                                        <i class="fas fa-check"></i> Aprovar
                                                    </button>
                                                   <button class="btn btn-danger btn-sm"
                                                            wire:click="mostrarModalRejeicao('{{ $pagamento->id }}')"
                                                            title="Rejeitar Pagamento">
                                                        <i class="fas fa-times"></i> Rejeitar
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum pedido pendente</h5>
                                <p class="text-muted">Todos os pagamentos foram processados.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
