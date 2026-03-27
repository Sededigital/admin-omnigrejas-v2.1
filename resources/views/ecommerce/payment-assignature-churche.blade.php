<div class="container-fluid py-1">
    <!-- Hero Section -->
    <div class="card bg-info text-light text-white border-0 shadow-lg mb-5">
        <div class="card-body p-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-receipt me-3"></i>
                        Status dos Pagamentos
                    </h1>
                    <p class="lead mb-4">
                        Acompanhe o status dos seus pagamentos de assinatura e veja quando sua assinatura será ativada.
                    </p>

                    <!-- Estatísticas Rápidas -->
                    <div class="row g-4">
                        <div class="col-auto text-center">
                            <div class="h4 mb-1">{{ $estatisticas['total'] }}</div>
                            <div class="small opacity-75">Total de Pagamentos</div>
                        </div>
                        <div class="col-auto text-center border-start border-white border-opacity-25 ps-4">
                            <div class="h4 mb-1 text-warning">{{ $estatisticas['pendentes'] }}</div>
                            <div class="small opacity-75">Pendentes</div>
                        </div>
                        <div class="col-auto text-center border-start border-white border-opacity-25 ps-4">
                            <div class="h4 mb-1 text-success">{{ $estatisticas['confirmados'] }}</div>
                            <div class="small opacity-75">Confirmados</div>
                        </div>
                        <div class="col-auto text-center border-start border-white border-opacity-25 ps-4">
                            <div class="h4 mb-1">{{ number_format($estatisticas['valor_total'], 2, ',', '.') }} AOA</div>
                            <div class="small opacity-75">Valor Total</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <i class="fas fa-chart-line fa-5x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros e Busca -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                <!-- Seleção de Igreja (se múltiplas) -->
                @if($igrejasDisponiveis->count() > 1)
                    <div class="col-lg-3">
                        <label class="form-label fw-semibold">Igreja</label>
                        <select class="form-select" wire:model.live="igrejaSelecionada">
                            @foreach($igrejasDisponiveis as $igreja)
                                <option value="{{ $igreja['id'] }}">
                                    {{ $igreja['nome'] }}
                                    @if($igreja['sigla']) ({{ $igreja['sigla'] }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Busca -->
                <div class="col-lg-3">
                    <label class="form-label fw-semibold">Buscar</label>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                           placeholder="Referência, pacote...">
                </div>

                <!-- Filtro Status -->
                <div class="col-lg-2">
                    <label class="form-label fw-semibold">Status</label>
                    <select class="form-select" wire:model.live="statusFilter">
                        <option value="">Todos</option>
                        <option value="pendente">Pendente</option>
                        <option value="confirmado">Confirmado</option>
                        <option value="rejeitado">Rejeitado</option>
                        <option value="expirado">Expirado</option>
                    </select>
                </div>

                <!-- Filtro Método -->
                <div class="col-lg-2">
                    <label class="form-label fw-semibold">Método</label>
                    <select class="form-select" wire:model.live="metodoFilter">
                        <option value="">Todos</option>
                        <option value="deposito">Depósito</option>
                        <option value="multicaixa_express">Multicaixa</option>
                        <option value="transferencia">Transferência</option>
                    </select>
                </div>

                <!-- Itens por página -->
                <div class="col-lg-2">
                    <label class="form-label fw-semibold">Mostrar</label>
                    <select class="form-select" wire:model.live="perPage">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Pagamentos -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($pagamentos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 fw-semibold">Data</th>
                                <th class="border-0 fw-semibold">Pacote</th>
                                <th class="border-0 fw-semibold">Valor</th>
                                <th class="border-0 fw-semibold">Método</th>
                                <th class="border-0 fw-semibold">Status</th>
                                <th class="border-0 fw-semibold text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pagamentos as $pagamento)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $pagamento->data_pagamento->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $pagamento->data_pagamento->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $pagamento->pacote_nome ?: $pagamento->pacote->nome }}</div>
                                        <small class="text-muted">
                                            @if($pagamento->is_vitalicio)
                                                Vitalício
                                            @elseif($pagamento->duracao_meses)
                                                {{ $pagamento->duracao_meses }} {{ $pagamento->duracao_meses == 1 ? 'mês' : 'meses' }}
                                            @else
                                                {{ $pagamento->pacote->duracao_meses }} {{ $pagamento->pacote->duracao_meses == 1 ? 'mês' : 'meses' }}
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-info">{{ $pagamento->getValorFormatado() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas {{ $pagamento->metodo_pagamento === 'deposito' ? 'fa-university' : ($pagamento->metodo_pagamento === 'multicaixa_express' ? 'fa-mobile-alt' : 'fa-exchange-alt') }} me-1"></i>
                                            {{ $pagamento->getMetodoFormatado() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $pagamento->getStatusBadgeClass() }} fs-6">
                                            {{ $pagamento->getStatusFormatado() }}
                                        </span>
                                        @if($pagamento->isConfirmado() && $pagamento->data_confirmacao)
                                            <br><small class="text-muted">{{ $pagamento->getDataConfirmacaoFormatada() }}</small>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary"
                                                wire:click="verDetalhesPagamento('{{ $pagamento->id }}')"
                                                title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($pagamento->temComprovativo())
                                            <button class="btn btn-sm btn-outline-secondary ms-1"
                                                    wire:click="verComprovativo('{{ $pagamento->id }}')"
                                                    title="Ver comprovativo">
                                                <i class="fas fa-file-{{ $pagamento->getComprovativoIcone() === 'fas fa-file-pdf' ? 'pdf' : 'image' }}"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="card-body border-top">
                    {{ $pagamentos->links() }}
                </div>
            @else
                <!-- Estado Vazio -->
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Nenhum pagamento encontrado</h4>
                    <p class="text-muted">Você ainda não fez nenhum pagamento de assinatura para esta igreja.</p>
                    <a href="{{ route('ecommerce.subscription.upgrade') }}" class="btn bg-info text-light">
                        <i class="fas fa-plus me-2"></i>Fazer Assinatura
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>
