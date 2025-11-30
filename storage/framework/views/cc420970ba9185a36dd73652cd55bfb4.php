<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php echo $__env->make('components.layouts.head.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
   <title><?php echo e($title ?? 'Upgrade de Assinatura - OMNIGREJAS'); ?></title>


   <!-- Google Fonts: Poppins para um toque moderno -->
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
   <!-- Bootstrap 5 + Font Awesome -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://kit.fontawesome.com/a2b2c1a5d9.js" crossorigin="anonymous"></script>
   <!-- Animate.css para animações suaves -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        /* Container de botões */
        .swal-equal-buttons .swal2-actions {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 1rem !important;
            width: 100% !important;
        }

        /* Botões personalizados */
        .swal-equal-buttons .swal-btn {
            flex: 1 1 0 !important;             /* 👈 distribui igualmente */
            min-width: 140px !important;
            max-width: 180px !important;
            padding: 0.65rem 1.2rem !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            border-radius: 0.4rem !important;
        }

        /* Evita que SweetAlert2 aplique width automática */
        .swal-equal-buttons .swal2-confirm,
        .swal-equal-buttons .swal2-cancel {
            width: auto !important;
        }

        /* Modal largo para spinner */
        .swal-wide-modal {
            width: 500px !important;
        }

    </style>
   <!-- CSS Personalizado -->
   <link rel="stylesheet" href="<?php echo e(asset('system/css/subscription-church.css')); ?>">

   <!-- Livewire Styles -->
   <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body class="boxed-fancy">
   <div class="boxed-inner">
    <main class="main-content">
        <?php echo $__env->make('components.layouts.nav-subscription', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="container-fluid content-inner pb-0">
            <?php echo e($slot); ?>

            <div id="spa-loader" class="spa-loader d-none">
                <div class="dot-spinner">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>
        </div>
        
    </main>
   </div>
   <!-- Livewire Scripts -->


    <!-- Toast Container -->
    <div id="global-toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <!-- Toasts will be added here -->
    </div>


    <!-- Toast Styles -->
    <link rel="stylesheet" href="<?php echo e(asset('system/css/toast.css')); ?>">
    
   <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

   <script>
    if (!window.livewireScriptConfig || !window.livewireScriptConfig.uri) {
        window.livewireScriptConfig = { uri: '/livewire/update' };
    }
    </script>
    
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"  data-navigate-once ></script>

    <script>
        /**
         * JavaScript para o Layout de Subscription
         * Arquivo: subscription.js (inline)
         * Compatível com Livewire 3 e navegação SPA
         */

        // Instâncias globais dos modais SweetAlert - evitar redeclaração
        if (typeof window.subscriptionModalInstances === 'undefined') {
            window.subscriptionModalInstances = {
                plano: null,
                pagamento: null,
                login: null,
                alert: null
            };
        }

        // Inicialização global - executada imediatamente quando o script carrega
        if (typeof window.initSubscriptionLayout === 'undefined') {
            window.initSubscriptionLayout = () => {
                // console.log('Subscription layout JavaScript loaded');

                // Setup SweetAlert modal listeners
                const setupSweetAlertListeners = () => {
                    // Listeners já são configurados no livewire:init
                    // Esta função pode ser usada para configurações adicionais se necessário
                };

                // Executar todas as inicializações
                setupSweetAlertListeners();

                // console.log('All Subscription layout JavaScript initialized successfully');
            };
        }

        // Event listeners para Livewire e navegação SPA
        document.addEventListener('livewire:loaded', () => {
            if (window.initSubscriptionLayout) window.initSubscriptionLayout();
        });
        document.addEventListener('livewire:navigated', () => {
            if (window.initSubscriptionLayout) window.initSubscriptionLayout();
        });
        document.addEventListener('DOMContentLoaded', () => {
            if (window.initSubscriptionLayout) window.initSubscriptionLayout();
        });

        // Cleanup para SPA navigation
        document.addEventListener('livewire:navigating', () => {
            // Limpar instâncias dos modais SweetAlert antes da navegação
            window.subscriptionModalInstances.plano = null;
            window.subscriptionModalInstances.pagamento = null;
            window.subscriptionModalInstances.login = null;
            window.subscriptionModalInstances.alert = null;

            // Fechar qualquer modal SweetAlert aberto
            if (Swal.isVisible()) {
                Swal.close();
            }
        });

        document.addEventListener('livewire:init', () => {
            Livewire.on('abrirModalPlano', (data) => {
                const plano = data[0];
                const id = plano.id;
                const acaoSugerida = plano.acao || 'nova_assinatura'; // Ação sugerida

                // Determinar opções disponíveis baseado no contexto e pacote selecionado
                let opcoesDisponiveis = [];
                let titulo = 'Escolher Tipo de Assinatura';

                // Verificar se é o pacote atual do usuário
                const isPacoteAtual = plano.pacote_atual && plano.pacote_atual.id === plano.id;

                // Sempre permitir nova assinatura, exceto se for o pacote atual
                if (!isPacoteAtual) {
                    opcoesDisponiveis.push({
                        value: 'nova_assinatura',
                        label: 'Nova Assinatura',
                        description: 'Assinar este plano pela primeira vez'
                    });
                }

                // Verificar se pode renovar (assinatura expirada ou ativa)
                if (acaoSugerida === 'renovar' || acaoSugerida === 'upgrade') {
                    opcoesDisponiveis.push({
                        value: 'renovar',
                        label: 'Renovar Assinatura',
                        description: isPacoteAtual ? 'Renovar sua assinatura atual' : 'Renovar com este plano'
                    });
                }

                // Verificar se pode fazer upgrade (assinatura ativa e pacote superior)
                if (acaoSugerida === 'upgrade' && !isPacoteAtual) {
                    opcoesDisponiveis.push({
                        value: 'upgrade',
                        label: 'Fazer Upgrade',
                        description: 'Atualizar para um plano superior'
                    });
                }

                // Se é o pacote atual, só permitir renovar
                if (isPacoteAtual) {
                    opcoesDisponiveis = [{
                        value: 'renovar',
                        label: 'Renovar Assinatura',
                        description: 'Renovar sua assinatura atual'
                    }];
                }


                // Se só há uma opção, usar modal simples
                if (opcoesDisponiveis.length === 1) {
                    const unicaOpcao = opcoesDisponiveis[0];
                    Swal.fire({
                        title: `Confirmar ${unicaOpcao.label}`,
                        html: `
                            <div class="text-start">
                                <p><strong>Plano:</strong> ${plano.nome}</p>
                                <p><strong>Valor:</strong> ${plano.preco}/mês</p>
                                <p class="text-muted small">${plano.descricao}</p>
                                ${plano.preco_vitalicio ? `<p class="text-success small"><strong>Ou ${plano.preco_vitalicio} vitalício</strong></p>` : ''}
                                <p class="text-info small mt-2"><em>${unicaOpcao.description}</em></p>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, confirmar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true,
                        customClass: {
                            popup: 'swal-equal-buttons',
                            confirmButton: 'btn btn-primary fw-bold swal-btn',
                            cancelButton: 'btn btn-secondary fw-bold swal-btn'
                        },
                        buttonsStyling: false,
                        backdrop: true,
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown animate__faster'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp animate__faster'
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            processarConfirmacao(plano, unicaOpcao.value);
                        }
                    });
                } else {
                    // Modal com seleção de tipo
                    let opcoesHtml = opcoesDisponiveis.map(opcao => `
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="tipoAssinatura" value="${opcao.value}" id="opcao${opcao.value}" ${opcao.value === acaoSugerida ? 'checked' : ''}>
                            <label class="form-check-label" for="opcao${opcao.value}">
                                <strong>${opcao.label}</strong>
                                <br><small class="text-muted">${opcao.description}</small>
                            </label>
                        </div>
                    `).join('');

                    Swal.fire({
                        title: titulo,
                        html: `
                            <div class="text-start mb-4">
                                <p><strong>Plano:</strong> ${plano.nome}</p>
                                <p><strong>Valor:</strong> ${plano.preco}/mês</p>
                                <p class="text-muted small">${plano.descricao}</p>
                                ${plano.preco_vitalicio ? `<p class="text-success small"><strong>Ou ${plano.preco_vitalicio} vitalício</strong></p>` : ''}
                            </div>
                            <div class="text-start">
                                <p class="mb-3"><strong>Escolha o tipo de assinatura:</strong></p>
                                ${opcoesHtml}
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Continuar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true,
                        customClass: {
                            popup: 'swal-equal-buttons',
                            confirmButton: 'btn btn-primary fw-bold swal-btn',
                            cancelButton: 'btn btn-secondary fw-bold swal-btn'
                        },
                        buttonsStyling: false,
                        backdrop: true,
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown animate__faster'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp animate__faster'
                        },
                        preConfirm: () => {
                            const selectedOption = document.querySelector('input[name="tipoAssinatura"]:checked');
                            if (!selectedOption) {
                                Swal.showValidationMessage('Por favor, selecione um tipo de assinatura');
                                return false;
                            }
                            return selectedOption.value;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            processarConfirmacao(plano, result.value);
                        }
                    });
                }

                // Função auxiliar para processar a confirmação
                function processarConfirmacao(plano, acaoSelecionada) {
                    // Fechar modal atual e abrir novo com spinner
                    Swal.close();

                    // Abrir novo modal com spinner
                    Swal.fire({
                        title: 'Processando...',
                        html: `
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="fas fa-spinner fa-spin fa-4x text-primary"></i>
                                </div>
                                <p class="h4 fw-semibold text-body-emphasis mb-3">Processando sua ${acaoSelecionada === 'upgrade' ? 'upgrade' : acaoSelecionada === 'renovar' ? 'renovação' : 'assinatura'}</p>
                                <p class="text-muted">Aguarde enquanto redirecionamos você para o pagamento...</p>
                            </div>
                        `,
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        backdrop: true,
                        customClass: {
                            popup: 'swal-wide-modal'
                        }
                    });

                    // Disparar o evento Livewire com a ação selecionada
                    Livewire.dispatch('confirmarPacote', { id: plano.id, acao: acaoSelecionada });
                }
            });

        


            Livewire.on('pagamento-sucesso', (data) => {
                // Assume-se que o Livewire está passando um array, acessamos o primeiro item
                const pagamento = data[0];


                Swal.fire({
                    // Título mais limpo
                    title: '<span class="fw-bold text-dark">Transação Concluída</span>',
                    // Usamos o ícone padrão do Swal para consistência, mas o HTML personalizado define a aparência
                    icon: 'success',

                    html: `
                        <div class="text-center">
                            <!-- Ícone Principal com Wrapper Estilizado (Estrutura do V2) -->
                            <div class="d-inline-flex justify-content-center align-items-center p-3 rounded-circle bg-success-subtle mb-3 border border-success border-2">
                                <i class="fas fa-handshake fa-2x text-success"></i>
                            </div>

                            <p class="h5 fw-semibold text-body-emphasis mb-2">Seu comprovativo foi recebido!</p>
                            <p class="text-muted small mb-4">A sua solicitação de pagamento está em análise e será processada em breve.</p>

                            <!-- Detalhes da Transação -->
                            <div class="row g-2 justify-content-center text-center">
                                <!-- Cartão de Referência (Agora usando a classe 'detail-card' para o estilo V2) -->
                                <div class="col-6">
                                    <div class="border rounded-3 p-3 bg-light shadow-sm detail-card">
                                        <h6 class="text-info mb-1 small fw-bold text-uppercase">Referência</h6>
                                        <strong class="fs-6 text-dark">${pagamento.referencia}</strong>
                                    </div>
                                </div>
                                <!-- Cartão de Status (Agora usando a classe 'detail-card' para o estilo V2) -->
                                <div class="col-6">
                                    <div class="border rounded-3 p-3 bg-light shadow-sm detail-card">
                                        <h6 class="text-warning mb-1 small fw-bold text-uppercase">Status Atual</h6>
                                        <span class="badge bg-warning fs-6 py-1 px-3 fw-bold">${pagamento.status}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Alerta de Próximos Passos (Compacto e com fundo suave) -->
                            <div class="alert mt-4 p-3 rounded-3 text-start border-info-subtle bg-info-subtle">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-hourglass-half me-3 text-info fs-4"></i>
                                    <div>
                                        <strong class="text-info">Próximo Passo:</strong>
                                        <div class="small text-dark">
                                            Sua análise é feita em até <strong class="fw-bold">24 horas úteis</strong>.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    // Custom Classes que dependem do CSS incluído no seu projeto (como no preview HTML)
                    customClass: {
                        popup: 'swal2-responsive-modal shadow-lg', // Define o container principal (tamanho/sombra)
                        confirmButton: 'btn btn-success btn-lg px-4 fw-bold' // Define o botão (cor, animação, tamanho)
                    },
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fas fa-home me-2"></i> Ir para o Dashboard',
                    // Desativa o estilo padrão do SweetAlert para usar o Bootstrap e o estilo customizado
                    buttonsStyling: false,
                    backdrop: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirecionar para dashboard da igreja
                        window.location.href = '/e-commerce/payments-assignatures';
                    }
                });
            });

            // Modal de login para usuários não autenticados
            Livewire.on('abrirModalLogin', () => {
                Swal.fire({
                    title: '<i class="fas fa-exclamation-triangle text-warning me-2"></i>Login Necessário',
                    html: `
                        <div class="text-center">
                            <div class="mb-4">
                                <i class="fas fa-user-lock fa-4x text-primary"></i>
                            </div>
                            <p class="h5 fw-semibold text-body-emphasis mb-3">Você precisa estar logado para continuar</p>
                            <p class="text-muted mb-4">Para assinar um plano e acessar todas as funcionalidades, faça login em sua conta.</p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-sign-in-alt me-2"></i>Fazer Login',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal-equal-buttons',
                        confirmButton: 'btn btn-primary fw-bold swal-btn',
                        cancelButton: 'btn btn-secondary fw-bold swal-btn'
                    },
                    buttonsStyling: false,
                    backdrop: true,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirecionar para a página de login com transição suave
                        window.location.href = '/login';
                    }
                });
            });

            // Alert específico para solicitação pendente
            Livewire.on('solicitacao-pendente', () => {
                Swal.fire({
                    title: 'Pedido Pendente',
                    text: 'Você já possui uma solicitação de período de teste pendente de aprovação. Nossa equipe irá analisar e você receberá um email com a decisão.',
                    icon: 'warning',
                    confirmButtonText: 'Entendi',
                    confirmButtonColor: '#ffc107',
                    customClass: {
                        popup: 'swal-equal-buttons',
                        confirmButton: 'btn btn-warning fw-bold swal-btn'
                    },
                    buttonsStyling: false,
                    backdrop: true,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    }
                });
            });

            // Alert específico para limite atingido
            Livewire.on('limite-atingido', () => {
                Swal.fire({
                    title: 'Limite Atingido',
                    text: 'Você já utilizou o limite máximo de 2 períodos de teste. Cada usuário tem direito a apenas 2 solicitações aprovadas. Entre em contacto com o suporte técnico.',
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
                    }
                });
            });

            // Alert genérico para outros casos
            Livewire.on('swal:alert', (params) => {
                Swal.fire({
                    title: params.title,
                    text: params.text,
                    icon: params.icon,
                    confirmButtonText: params.confirmButtonText || 'OK',
                    confirmButtonColor: params.icon === 'error' ? '#dc3545' : '#007bff',
                    customClass: {
                        popup: 'swal-equal-buttons',
                        confirmButton: 'btn btn-primary fw-bold swal-btn'
                    },
                    buttonsStyling: false,
                    backdrop: true,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    }
                });
            });


            
        });
    </script>

   


   <script src="<?php echo e(asset('system/js/js_pages.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/core/libs.min.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/core/external.min.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/charts/widgetcharts.js')); ?>" data-navigate-once></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   
   <script src="<?php echo e(asset('assets/js/charts/vectore-chart.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/charts/dashboard.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/plugins/fslightbox.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/plugins/setting.js')); ?>" ></script>
   <script src="<?php echo e(asset('assets/js/plugins/slider-tabs.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/plugins/form-wizard.js')); ?>" data-navigate-once></script>
   <script src="<?php echo e(asset('assets/vendor/aos/dist/aos.js')); ?>" data-navigate-once></script>

   <script src="<?php echo e(asset('assets/js/hope-ui.js')); ?>"  data-navigate-once></script>
   <script src="<?php echo e(asset('assets/js/font-define.js')); ?>" data-navigate-once ></script>
   <script src="https://cdn.jsdelivr.net/npm/flatpickr" once-navigate-once></script>
</body>
</html><?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/components/layouts/subscription.blade.php ENDPATH**/ ?>