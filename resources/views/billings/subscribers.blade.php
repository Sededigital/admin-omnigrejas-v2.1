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
                                        <i class="fas fa-credit-card fa-2x text-primary"></i>
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
                                        <div class="bg-primary bg-opacity-10 rounded-3 px-3 py-2 border">
                                            <div class="text-primary fw-bold fs-5">{{ $pagamentosPendentes->count() }}</div>
                                            <small class="text-primary fw-medium">Pendentes</small>
                                        </div>
                                    </div>

                                    <!-- Botão de Atualizar -->
                                    <button class="btn btn-outline-primary rounded-3 px-4"
                                            wire:click="$refresh"
                                            wire:loading.attr="disabled"
                                            wire:loading.class="btn-loading">
                                        <i class="fas fa-sync-alt me-2" wire:loading.remove wire:target="$refresh"></i>
                                        <i class="fas fa-spinner fa-spin me-2" wire:loading wire:target="$refresh"></i>
                                        <span wire:loading.remove wire:target="$refresh">Atualizar</span>
                                        <span wire:loading wire:target="$refresh">Carregando...</span>
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
                                                        <div class="user-avatar bg-primary text-white me-3">
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
                                                <span class="badge bg-info">{{ $pagamento->pacote_nome }}</span>
                                                @if($pagamento->is_vitalicio)
                                                    <small class="text-success d-block">Vitalício</small>
                                                @else
                                                    <small class="text-primary d-block">{{ $pagamento->duracao_meses }} meses</small>
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
                                                       title="Comprovativo anexado - Clique para visualizar"
                                                       style="cursor: pointer;"
                                                       wire:click="visualizarComprovativo('{{ $pagamento->id }}')"></i>
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
                                                            wire:click="selecionarPagamentoRejeicao('{{ $pagamento->id }}')"
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

    @push('scripts')
    <script>
        /**
         * JavaScript para a página de Subscribers (Pedidos de Assinaturas)
         * Arquivo: subscribers.js (inline)
         * Compatível com Livewire 3 e navegação SPA
         */

        // Instâncias globais dos modais SweetAlert - evitar redeclaração
        if (typeof window.subscribersModalInstances === 'undefined') {
            window.subscribersModalInstances = {
                confirmation: null,
                rejection: null,
                processing: null
            };
        }

        // Inicialização global - executada imediatamente quando o script carrega
        if (typeof window.initSubscribersPage === 'undefined') {
            window.initSubscribersPage = () => {
                // console.log('Subscribers page JavaScript loaded');

                // Setup SweetAlert modal listeners
                const setupSweetAlertListeners = () => {
                    // Listeners já são configurados no livewire:init
                    // Esta função pode ser usada para configurações adicionais se necessário
                };

                // Executar todas as inicializações
                setupSweetAlertListeners();

                // console.log('All Subscribers page JavaScript initialized successfully');
            };
        }

        // Event listeners para Livewire e navegação SPA
        document.addEventListener('livewire:loaded', () => {
            if (window.initSubscribersPage) window.initSubscribersPage();
        });
        document.addEventListener('livewire:navigated', () => {
            if (window.initSubscribersPage) window.initSubscribersPage();
        });
        document.addEventListener('DOMContentLoaded', () => {
            if (window.initSubscribersPage) window.initSubscribersPage();
        });

        // Cleanup para SPA navigation
        document.addEventListener('livewire:navigating', () => {
            // Limpar instâncias dos modais SweetAlert antes da navegação
            window.subscribersModalInstances.confirmation = null;
            window.subscribersModalInstances.rejection = null;
            window.subscribersModalInstances.processing = null;

            // Fechar qualquer modal SweetAlert aberto
            if (Swal.isVisible()) {
                Swal.close();
            }
        });

        document.addEventListener('livewire:init', () => {
            // Listener para modal de rejeição
            Livewire.on('mostrarModalRejeicao', (data) => {
                // Fechar modal anterior se existir
                if (window.subscribersModalInstances.rejection) {
                    window.subscribersModalInstances.rejection.close();
                }

                window.subscribersModalInstances.rejection = Swal.fire({
                    title: 'Confirmar Rejeição de Pagamento',
                    html: `
                        <div class="text-start">
                            <div class="bg-light rounded p-2 mb-2 border">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-church text-primary me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Igreja</small>
                                                <strong class="text-dark">${data[0].igreja || 'N/A'}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-box text-info me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Pacote</small>
                                                <strong class="text-dark">${data[0].pacote || 'N/A'}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-money-bill-wave text-success me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Valor</small>
                                                <strong class="text-success">${data[0].valor ? 'Kz ' + data[0].valor.toLocaleString('pt-AO', {minimumFractionDigits: 2}) : 'N/A'}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar text-warning me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Data do Pagamento</small>
                                                <strong class="text-dark">${data[0].data_pagamento ? new Date(data[0].data_pagamento).toLocaleDateString('pt-AO') : 'N/A'}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-danger border border-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Atenção:</strong> Esta ação rejeitará permanentemente o pagamento e a igreja precisará fazer um novo pedido.
                            </div>

                            <div class="mb-2">
                                <label for="observacoes_rejeicao" class="form-label fw-semibold text-danger">
                                    <i class="fas fa-comment-alt me-1"></i>
                                    Motivo da Rejeição <span class="text-danger">*</span>
                                </label>
                                <textarea id="observacoes_rejeicao" class="form-control border-danger"
                                          wire:model.live="observacoesRejeicao"
                                          placeholder="Explique o motivo da rejeição do pagamento..."
                                          rows="3"></textarea>
                                <div class="form-text text-muted">
                                    Este motivo será registrado no histórico e poderá ser visto pela igreja.
                                </div>
                            </div>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Confirmar Rejeição',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'swal-equal-buttons',
                        confirmButton: 'btn btn-danger fw-bold swal-btn',
                        cancelButton: 'btn btn-secondary fw-bold swal-btn'
                    },
                    customClass: {
                        popup: 'swal-equal-buttons',
                        confirmButton: 'btn btn-danger fw-bold swal-btn m-2',
                        cancelButton: 'btn btn-secondary fw-bold swal-btn'
                    },
                    width: '900px',
                    heightAuto: false,
                    buttonsStyling: false,
                    backdrop: true,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    },
                    preConfirm: () => {
                        // Chamar método do Livewire para rejeitar
                        @this.rejeitarPagamento();
                        return false; // Impede fechamento automático
                    },
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        // console.log('Rejection modal opened');
                    },
                    didClose: () => {
                        window.subscribersModalInstances.rejection = null;
                        // console.log('Rejection modal closed');
                    }
                });
            });

            // Listener para modal de confirmação
            Livewire.on('mostrarModalConfirmacao', (data) => {
                // Fechar modal anterior se existir
                if (window.subscribersModalInstances.confirmation) {
                    window.subscribersModalInstances.confirmation.close();
                }

                // Preparar HTML da assinatura se existir
                let assinaturaHtml = '';
                if (data[0] && data[0].assinatura_atual) {
                    const assinatura = data[0].assinatura_atual;
                    const tipo = assinatura.tipo === 'trial' ? 'TRIAL ATIVO' : 'ASSINATURA ATIVA';
                    const iconeCor = assinatura.tipo === 'trial' ? 'text-warning' : 'text-primary';
                    const iconeInicioCor = assinatura.tipo === 'trial' ? 'text-warning' : 'text-success';
                    const iconeFimCor = assinatura.tipo === 'trial' ? 'text-warning' : 'text-danger';
                    const iconeDiasCor = assinatura.dias_restantes > 7 ? 'text-success' : assinatura.dias_restantes > 0 ? 'text-warning' : 'text-danger';

                    assinaturaHtml = `
                        <div id="assinatura-info">
                            <hr class="my-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-info-circle text-info me-2"></i>
                                <small class="text-muted fw-semibold">${tipo}</small>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-box ${iconeCor} me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Pacote Atual</small>
                                            <strong class="text-dark">${assinatura.pacote || 'N/A'}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-check ${iconeInicioCor} me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Data Início</small>
                                            <strong class="text-dark">${assinatura.data_inicio || 'N/A'}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-times ${iconeFimCor} me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Data Fim</small>
                                            <strong class="text-dark">${assinatura.data_fim || 'N/A'}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock ${iconeDiasCor} me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Dias Restantes</small>
                                            <strong class="text-dark">${assinatura.dias_restantes !== null ? assinatura.dias_restantes + ' dias' : 'Vitalício'}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                window.subscribersModalInstances.confirmation = Swal.fire({
                    title: 'Confirmar Aprovação de Pagamento',
                    html: `
                        <div class="text-start">
                            <div class="bg-light rounded p-2 mb-2 border">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-church text-primary me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Igreja</small>
                                                <strong class="text-dark">${data[0].igreja || 'N/A'}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-box text-info me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Pacote</small>
                                                <strong class="text-dark">${data[0].pacote || 'N/A'}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-money-bill-wave text-success me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Valor</small>
                                                <strong class="text-success">${data[0].valor ? 'Kz ' + data[0].valor.toLocaleString('pt-AO', {minimumFractionDigits: 2}) : 'N/A'}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar text-warning me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Data do Pagamento</small>
                                                <strong class="text-dark">${data[0].data_pagamento ? new Date(data[0].data_pagamento).toLocaleDateString('pt-AO') : 'N/A'}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                ${assinaturaHtml}
                            </div>

                            <div class="mb-2">
                                <label for="observacoes" class="form-label fw-semibold">Observações (opcional)</label>
                                <textarea id="observacoes" class="form-control" rows="2"
                                          wire:model.live="observacoesConfirmacao"
                                          placeholder="Digite observações sobre a aprovação..."></textarea>
                            </div>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Confirmar Aprovação',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'swal-equal-buttons',
                        confirmButton: 'btn btn-success fw-bold swal-btn',
                        cancelButton: 'btn btn-secondary fw-bold swal-btn'
                    },
                    customClass: {
                        popup: 'swal-equal-buttons',
                        confirmButton: 'btn btn-success fw-bold swal-btn m-2',
                        cancelButton: 'btn btn-secondary fw-bold swal-btn'
                    },
                    width: '900px',
                    heightAuto: false,
                    buttonsStyling: false,
                    backdrop: true,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    },
                    preConfirm: () => {
                        // Chamar método do Livewire para iniciar processamento
                        @this.iniciarProcessamento();
                        return false; // Impede fechamento automático
                    },
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        // console.log('Confirmation modal opened');
                    },
                    didClose: () => {
                        window.subscribersModalInstances.confirmation = null;
                        // console.log('Confirmation modal closed');
                    }
                });
            });

            // Listener para processamento
            Livewire.on('processando', () => {
                // Fechar modal anterior se existir
                if (window.subscribersModalInstances.processing) {
                    window.subscribersModalInstances.processing.close();
                }

                window.subscribersModalInstances.processing = Swal.fire({
                    title: 'Processando Aprovação...',
                    html: `
                        <div class="text-center">
                            <div class="mb-4">
                                <i class="fas fa-spinner fa-spin fa-4x text-primary"></i>
                            </div>
                            <p class="h5 fw-semibold text-body-emphasis mb-3">Processando aprovação do pagamento</p>
                            <p class="text-muted">Aguarde enquanto criamos a assinatura...</p>
                            <div class="mt-3">
                                <small class="text-muted">Processamento será concluído em alguns segundos</small>
                            </div>
                        </div>
                    `,
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: true,
                    customClass: {
                        popup: 'swal-wide-modal'
                    },
                    timer: 10000,
                    timerProgressBar: true,
                    didOpen: () => {
                        // console.log('Processing modal opened');
                        // Executar processamento após 10 segundos
                        setTimeout(() => {
                            @this.confirmarPagamento();
                        }, 10000);
                    },
                    didClose: () => {
                        window.subscribersModalInstances.processing = null;
                        // console.log('Processing modal closed');
                    }
                });
            });

            // Alert de sucesso
            Livewire.on('show-success', (message) => {
                // Fechar modal de processamento se estiver aberto
                if (window.subscribersModalInstances.processing) {
                    window.subscribersModalInstances.processing.close();
                }

                Swal.fire({
                    title: '<span class="fw-bold text-dark">Aprovação Concluída</span>',
                    icon: 'success',
                    html: `
                        <div class="text-center">
                            <div class="d-inline-flex justify-content-center align-items-center p-3 rounded-circle bg-success-subtle mb-3 border border-success border-2">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                            <p class="h5 fw-semibold text-body-emphasis mb-2">Pagamento aprovado com sucesso!</p>
                            <p class="text-muted small mb-4">${message}</p>
                        </div>
                    `,
                    customClass: {
                        popup: 'swal2-responsive-modal shadow-lg',
                        confirmButton: 'btn btn-success btn-lg px-4 fw-bold'
                    },
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fas fa-home me-2"></i> Continuar',
                    buttonsStyling: false,
                    backdrop: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        // console.log('Success modal opened');
                    },
                    didClose: () => {
                        // console.log('Success modal closed');
                    }
                });
            });

            // Alert de erro
            Livewire.on('show-error', (message) => {
                // Fechar modal de processamento se estiver aberto
                if (window.subscribersModalInstances.processing) {
                    window.subscribersModalInstances.processing.close();
                }

                Swal.fire({
                    title: 'Erro na Aprovação',
                    text: message,
                    icon: 'error',
                    confirmButtonText: 'Entendi',
                    confirmButtonColor: '#dc3545',
                    customClass: {
                        popup: 'swal-equal-buttons',
                        confirmButton: 'btn btn-danger fw-bold swal-btn'
                    },
                    buttonsStyling: false,
                    backdrop: true,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    },
                    didOpen: () => {
                        // console.log('Error modal opened');
                    },
                    didClose: () => {
                        // console.log('Error modal closed');
                    }
                });
            });

            // Listener para mostrar comprovativo
            Livewire.on('mostrarComprovativo', (data) => {
                const { url, tipo, nome, tamanho, igreja, pacote, valor } = data[0];

                // Verificar se é imagem
                const tiposImagem = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                const tipoString = tipo ? tipo.toString().toLowerCase() : '';
                const isImagem = tiposImagem.some(ext => tipoString.includes(ext));

                if (isImagem) {
                    // Mostrar imagem no modal
                    Swal.fire({
                        title: 'Comprovativo de Pagamento',
                        html: `
                            <div class="text-center">
                                <div class="mb-3">
                                    <img src="${url}" class="img-fluid rounded shadow"
                                          style="max-width: 100%; max-height: 400px; object-fit: contain;"
                                          alt="Comprovativo">
                                </div>
                                <div class="row g-2 text-start">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Tamanho</small>
                                        <strong class="text-dark">${tamanho}</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Igreja</small>
                                        <strong class="text-dark">${igreja}</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Valor</small>
                                        <strong class="text-success">${valor}</strong>
                                    </div>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-download"></i> Baixar',
                        cancelButtonText: 'Fechar',
                        customClass: {
                            popup: 'swal-wide-modal',
                            confirmButton: 'btn btn-primary fw-bold m-2',
                            cancelButton: 'btn btn-secondary fw-bold m-2'
                        },
                        buttonsStyling: false,
                        width: '800px',
                        heightAuto: false,
                        didOpen: () => {
                            // console.log('Comprovativo image modal opened');
                        },
                        didClose: () => {
                            // console.log('Comprovativo image modal closed');
                        },
                        preConfirm: () => {
                            // Tentar download forçado primeiro
                            try {
                                // Criar blob URL para forçar download
                                fetch(url)
                                    .then(response => response.blob())
                                    .then(blob => {
                                        const blobUrl = URL.createObjectURL(blob);
                                        const link = document.createElement('a');
                                        link.href = blobUrl;
                                        link.download = nome;
                                        document.body.appendChild(link);
                                        link.click();
                                        document.body.removeChild(link);
                                        URL.revokeObjectURL(blobUrl);
                                    })
                                    .catch(() => {
                                        // Fallback: abrir em nova aba
                                        window.open(url, '_blank', 'noopener,noreferrer');
                                    });
                            } catch (error) {
                                // Fallback: abrir em nova aba
                                window.open(url, '_blank', 'noopener,noreferrer');
                            }
                        }
                    });
                } else {
                    // Para outros arquivos (PDF, etc.), mostrar spinner e fazer download direto
                    Swal.fire({
                        title: 'Preparando Download...',
                        html: `
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="fas fa-spinner fa-spin fa-4x text-primary"></i>
                                </div>
                                <p class="h5 fw-semibold text-body-emphasis mb-3">Preparando comprovativo para download</p>
                                <p class="text-muted">Arquivo: ${nome}</p>
                                <p class="text-muted">Tamanho: ${tamanho}</p>
                                <div class="mt-3">
                                    <small class="text-muted">O download começará automaticamente</small>
                                </div>
                            </div>
                        `,
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        customClass: {
                            popup: 'swal-wide-modal'
                        },
                        didOpen: () => {
                            // console.log('Comprovativo download modal opened');
                            // Fazer download automático após 2 segundos
                            setTimeout(() => {
                                try {
                                    // Tentar download forçado com blob
                                    fetch(url)
                                        .then(response => response.blob())
                                        .then(blob => {
                                            const blobUrl = URL.createObjectURL(blob);
                                            const link = document.createElement('a');
                                            link.href = blobUrl;
                                            link.download = nome;
                                            document.body.appendChild(link);
                                            link.click();
                                            document.body.removeChild(link);
                                            URL.revokeObjectURL(blobUrl);

                                            // Fechar modal após download
                                            setTimeout(() => {
                                                Swal.close();
                                            }, 1000);
                                        })
                                        .catch(() => {
                                            // Fallback: abrir em nova aba
                                            window.open(url, '_blank', 'noopener,noreferrer');
                                            setTimeout(() => {
                                                Swal.close();
                                            }, 1000);
                                        });
                                } catch (error) {
                                    // Fallback: abrir em nova aba
                                    window.open(url, '_blank', 'noopener,noreferrer');
                                    setTimeout(() => {
                                        Swal.close();
                                    }, 1000);
                                }
                            }, 2000);
                        },
                        didClose: () => {
                            // console.log('Comprovativo download modal closed');
                        }
                    });
                }
            });
        });
    </script>
    @endpush
 
</div>
