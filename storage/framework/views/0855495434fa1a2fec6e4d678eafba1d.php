<div>
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Calendário de Assinaturas</h1>
                            <p>Gerencie as datas importantes das assinaturas das igrejas</p>
                        </div>

                        <!-- Filtros -->
                        <div class="d-flex gap-2 align-items-center">
                            <select wire:model.live="filterType" class="form-select">
                                <option value="all">Todos os Eventos</option>
                                <option value="start_dates">Datas de Início</option>
                                <option value="end_dates">Datas de Fim</option>
                                <option value="payments">Pagamentos</option>
                                <option value="new_contracts">Novos Contratos</option>
                            </select>

                            <div class="btn-group">
                                <button wire:click="changeMonth('prev')" class="btn btn-outline-primary">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="btn btn-outline-primary disabled">
                                    <?php echo e(\Carbon\Carbon::create($selectedYear, $selectedMonth)->format('F Y')); ?>

                                </button>
                                <button wire:click="changeMonth('next')" class="btn btn-outline-primary">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="iq-header-img">
            <img src="<?php echo e(asset('assets/images/dashboard/top-header.png')); ?>" alt="header" class="theme-color-default-img img-fluid w-100 h-100 animated-scaleX">
            <img src="<?php echo e(asset('assets/images/dashboard/top-header1.png')); ?>" alt="header" class="theme-color-purple-img img-fluid w-100 h-100 animated-scaleX">
            <img src="<?php echo e(asset('assets/images/dashboard/top-header2.png')); ?>" alt="header" class="theme-color-blue-img img-fluid w-100 h-100 animated-scaleX">
            <img src="<?php echo e(asset('assets/images/dashboard/top-header3.png')); ?>" alt="header" class="theme-color-green-img img-fluid w-100 h-100 animated-scaleX">
            <img src="<?php echo e(asset('assets/images/dashboard/top-header4.png')); ?>" alt="header" class="theme-color-yellow-img img-fluid w-100 h-100 animated-scaleX">
            <img src="<?php echo e(asset('assets/images/dashboard/top-header5.png')); ?>" alt="header" class="theme-color-pink-img img-fluid w-100 h-100 animated-scaleX">
        </div>
    </div>

    <!-- Legenda -->
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-3">Legenda</h6>
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-success me-2" style="width: 20px; height: 20px;"></div>
                                    <small>Início de Assinatura</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-warning me-2" style="width: 20px; height: 20px;"></div>
                                    <small>Fim de Assinatura (Ativa)</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-danger me-2" style="width: 20px; height: 20px;"></div>
                                    <small>Fim de Assinatura (Expirada)</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-info me-2" style="width: 20px; height: 20px;"></div>
                                    <small>Pagamento Confirmado</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-purple me-2" style="width: 20px; height: 20px;"></div>
                                    <small>Novo Contrato</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendário com Double Buffering System -->
    <div class="container-fluid" wire:ignore>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Container principal do calendário -->
                        <div class="calendar-container" style="position: relative; height: 800px;">
                            <!-- Calendário principal (visível) -->
                            <div id='calendar-billings' class="calendar-main" style="height: 100%; position: relative;"></div>

                            <!-- Calendário buffer (invisível para preparação) -->
                            <div id='calendar-buffer' class="calendar-buffer" style="height: 100%; position: absolute; top: 0; left: 0; visibility: hidden; opacity: 0; pointer-events: none; z-index: -1;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalhes do Evento -->
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalTitle">Detalhes do Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="eventModalBody">
                    <!-- Conteúdo será preenchido via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
    <style>
        /* CONGELAMENTO VISUAL ABSOLUTO - SEM QUALQUER SINAL VISUAL */
        #calendar-billings.frozen {
            position: relative !important;
            transform: none !important;
            transition: none !important;
            animation: none !important;
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: none !important;
            user-select: none !important;
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
        }

        /* Congelar TODOS os elementos do calendário */
        #calendar-billings.frozen *,
        #calendar-billings.frozen .fc *,
        #calendar-billings.frozen .fc-view-harness,
        #calendar-billings.frozen .fc-scroller,
        #calendar-billings.frozen .fc-daygrid-body,
        #calendar-billings.frozen .fc-event,
        #calendar-billings.frozen .fc-daygrid-event,
        #calendar-billings.frozen .fc-timegrid-event,
        #calendar-billings.frozen .fc-col-header,
        #calendar-billings.frozen .fc-daygrid-day,
        #calendar-billings.frozen .fc-button,
        #calendar-billings.frozen .fc-header-toolbar,
        #calendar-billings.frozen .fc-toolbar,
        #calendar-billings.frozen .fc-title,
        #calendar-billings.frozen .fc-button-group,
        #calendar-billings.frozen .fc-daygrid-day-top,
        #calendar-billings.frozen .fc-daygrid-day-number,
        #calendar-billings.frozen .fc-event-title,
        #calendar-billings.frozen .fc-event-time,
        #calendar-billings.frozen .fc-daygrid-day-frame,
        #calendar-billings.frozen .fc-scrollgrid,
        #calendar-billings.frozen .fc-scrollgrid-section,
        #calendar-billings.frozen .fc-col-header-cell {
            transition: none !important;
            animation: none !important;
            transform: none !important;
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: none !important;
            user-select: none !important;
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            position: static !important;
            left: auto !important;
            top: auto !important;
            right: auto !important;
            bottom: auto !important;
            width: auto !important;
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            text-shadow: none !important;
        }

        /* Garantir que o calendário principal esteja sempre estável */
        #calendar-billings {
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            transform: none !important;
            transition: none !important;
            animation: none !important;
        }

        /* Congelamento durante troca de buffers */
        .calendar-container.swapping {
            position: relative !important;
            transform: none !important;
            transition: none !important;
            animation: none !important;
            pointer-events: none !important;
            user-select: none !important;
        }

        .calendar-container.swapping *,
        .calendar-container.swapping .calendar-main,
        .calendar-container.swapping .calendar-buffer {
            transition: none !important;
            animation: none !important;
            transform: none !important;
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: none !important;
            user-select: none !important;
        }

        /* Otimização máxima de performance */
        #calendar-billings .fc-event {
            will-change: auto;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
            contain: layout style paint;
            -webkit-contain: layout style paint;
        }

        #calendar-billings .fc-view-harness {
            contain: layout style paint;
            -webkit-contain: layout style paint;
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
        }
    </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script>
        let billingsCalendarInstance = null;
        let calendarInitialized = false;

        // Inicializar sistema dual de calendários
        function initializeBillingsCalendar() {
            const calendarEl = document.getElementById('calendar-billings');

            if (!calendarEl) {
                console.log('Elemento calendar-billings não encontrado');
                return false;
            }

            if (typeof FullCalendar === 'undefined') {
                console.log('FullCalendar não está carregado, tentando novamente...');
                setTimeout(initializeBillingsCalendar, 200);
                return false;
            }

            // Evitar inicialização duplicada
            if (calendarInitialized && mainCalendarInstance) {
                console.log('Sistema já inicializado');
                return true;
            }

            try {
                // Inicializar buffer calendar primeiro
                initializeBufferCalendar();

                // Destruir instâncias anteriores se existirem
                if (mainCalendarInstance) {
                    mainCalendarInstance.destroy();
                    mainCalendarInstance = null;
                }

                // Inicializar calendário principal
                mainCalendarInstance = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    initialDate: '<?php echo e($selectedYear); ?>-<?php echo e(str_pad($selectedMonth, 2, "0", STR_PAD_LEFT)); ?>-01',
                    locale: 'pt-br',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: <?php echo json_encode($events, 15, 512) ?>,
                    eventClick: function(info) {
                        info.jsEvent.preventDefault();
                        showEventDetails(info.event);
                    },
                    eventDidMount: function(info) {
                        // Adicionar tooltip
                        info.el.setAttribute('title', getEventTooltip(info.event));
                        info.el.style.cursor = 'pointer';
                    },
                    dayMaxEvents: 3,
                    moreLinkClick: 'popover',
                    height: 800,
                    aspectRatio: 1.5,
                    editable: false,
                    selectable: false,
                    dayMaxEventRows: 3,
                    eventDisplay: 'block',
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        meridiem: false
                    }
                });

                mainCalendarInstance.render();
                calendarInitialized = true;
                console.log('Sistema de double buffering inicializado com sucesso');
                return true;

            } catch (error) {
                console.error('Erro ao inicializar sistema de double buffering:', error);
                calendarInitialized = false;
                return false;
            }
        }

        // SISTEMA DE DOUBLE BUFFERING - ATUALIZAÇÃO 100% INVISÍVEL
        let lastEventsHash = '';
        let isUpdating = false;
        let bufferCalendarInstance = null;
        let mainCalendarInstance = null;
        let currentBuffer = 'main'; // 'main' ou 'buffer'

        // Calcular hash dos eventos
        function calculateEventsHash(events) {
            return btoa(JSON.stringify(events)).substring(0, 16);
        }

        // Inicializar buffer calendar
        function initializeBufferCalendar() {
            const bufferEl = document.getElementById('calendar-buffer');
            if (!bufferEl || typeof FullCalendar === 'undefined') return;

            try {
                bufferCalendarInstance = new FullCalendar.Calendar(bufferEl, {
                    initialView: 'dayGridMonth',
                    initialDate: '<?php echo e($selectedYear); ?>-<?php echo e(str_pad($selectedMonth, 2, "0", STR_PAD_LEFT)); ?>-01',
                    locale: 'pt-br',
                    headerToolbar: false, // Sem header no buffer
                    height: '100%',
                    editable: false,
                    selectable: false,
                    dayMaxEvents: 3,
                    eventDisplay: 'block',
                    eventClick: function(info) {
                        info.jsEvent.preventDefault();
                        showEventDetails(info.event);
                    },
                    eventDidMount: function(info) {
                        info.el.setAttribute('title', getEventTooltip(info.event));
                        info.el.style.cursor = 'pointer';
                    },
                    // Sincronizar com o calendário principal
                    dateClick: function(info) {
                        // Opcional: sincronizar cliques de data se necessário
                    }
                });
            } catch (error) {
                console.error('Erro ao inicializar buffer calendar:', error);
            }
        }

        // ATUALIZAÇÃO USANDO DOUBLE BUFFERING - 100% INVISÍVEL
        function updateCalendarEvents() {
            if (!mainCalendarInstance || isUpdating) {
                return;
            }

            try {
                const newEvents = <?php echo json_encode($events, 15, 512) ?>;
                const newEventsHash = calculateEventsHash(newEvents);

                // Se não mudou, não fazer nada
                if (lastEventsHash === newEventsHash) {
                    return;
                }

                isUpdating = true;

                // Preparar buffer com novos eventos
                prepareBuffer(newEvents, () => {
                    // Troca instantânea entre buffers
                    performInstantBufferSwap(() => {
                        lastEventsHash = newEventsHash;
                        isUpdating = false;
                    });
                });

            } catch (error) {
                console.error('Erro na atualização double buffering:', error);
                isUpdating = false;
            }
        }

        // Preparar buffer com novos eventos
        function prepareBuffer(newEvents, callback) {
            if (!bufferCalendarInstance) {
                initializeBufferCalendar();
            }

            if (!bufferCalendarInstance) {
                callback();
                return;
            }

            try {
                // Limpar buffer
                bufferCalendarInstance.removeAllEvents();

                // Adicionar novos eventos ao buffer
                if (newEvents && newEvents.length > 0) {
                    bufferCalendarInstance.addEventSource(newEvents);
                }

                // Aguardar renderização completa no buffer
                setTimeout(callback, 50);

            } catch (error) {
                console.error('Erro ao preparar buffer:', error);
                callback();
            }
        }

        // Troca instantânea entre buffers
        function performInstantBufferSwap(callback) {
            const mainEl = document.getElementById('calendar-billings');
            const bufferEl = document.getElementById('calendar-buffer');
            const containerEl = document.querySelector('.calendar-container');

            if (!mainEl || !bufferEl || !containerEl) {
                callback();
                return;
            }

            try {
                // Congelar container completamente
                containerEl.classList.add('swapping');

                // Troca instantânea de conteúdo DOM
                const mainContent = mainEl.innerHTML;
                const bufferContent = bufferEl.innerHTML;

                // Aplicar conteúdo do buffer no main instantaneamente
                mainEl.innerHTML = bufferContent;

                // Re-inicializar instância do calendário principal
                if (mainCalendarInstance) {
                    const newEvents = <?php echo json_encode($events, 15, 512) ?>;
                    mainCalendarInstance.removeAllEvents();
                    if (newEvents && newEvents.length > 0) {
                        mainCalendarInstance.addEventSource(newEvents);
                    }
                }

            } catch (error) {
                console.error('Erro na troca de buffers:', error);
            } finally {
                // Descongelar imediatamente
                queueMicrotask(() => {
                    containerEl.classList.remove('swapping');
                    callback();
                });
            }
        }


        function showEventDetails(event) {
            const props = event.extendedProps;
            let content = '';

            switch (props.type) {
                case 'start_date':
                    content = `
                        <div class="event-details">
                            <h6><i class="fas fa-calendar-plus text-success"></i> Início de Assinatura</h6>
                            <p><strong>Igreja:</strong> ${props.igreja}</p>
                            <p><strong>Pacote:</strong> ${props.pacote}</p>
                            <p><strong>Valor:</strong> Kz ${props.valor.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</p>
                            <p><strong>Vitalício:</strong> ${props.vitalicio ? '<span class="badge bg-warning">Sim</span>' : '<span class="badge bg-secondary">Não</span>'}</p>
                        </div>
                    `;
                    break;

                case 'end_date':
                    content = `
                        <div class="event-details">
                            <h6><i class="fas fa-calendar-times ${props.status === 'expirado' ? 'text-danger' : 'text-warning'}"></i> Fim de Assinatura</h6>
                            <p><strong>Igreja:</strong> ${props.igreja}</p>
                            <p><strong>Pacote:</strong> ${props.pacote}</p>
                            <p><strong>Status:</strong> <span class="badge bg-${props.status === 'expirado' ? 'danger' : 'warning'}">${props.status}</span></p>
                            ${props.dias_restantes > 0 ? `<p><strong>Dias restantes:</strong> ${props.dias_restantes}</p>` : ''}
                        </div>
                    `;
                    break;

                case 'payment':
                    content = `
                        <div class="event-details">
                            <h6><i class="fas fa-money-bill-wave text-info"></i> Pagamento Confirmado</h6>
                            <p><strong>Igreja:</strong> ${props.igreja}</p>
                            <p><strong>Valor:</strong> Kz ${props.valor.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</p>
                            <p><strong>Método:</strong> ${props.metodo}</p>
                            ${props.referencia ? `<p><strong>Referência:</strong> ${props.referencia}</p>` : ''}
                        </div>
                    `;
                    break;

                case 'new_contract':
                    content = `
                        <div class="event-details">
                            <h6><i class="fas fa-handshake text-purple"></i> Novo Contrato</h6>
                            <p><strong>Igreja:</strong> ${props.igreja}</p>
                            <p><strong>Pacote:</strong> ${props.pacote}</p>
                            <p><strong>Criado por:</strong> ${props.usuario}</p>
                        </div>
                    `;
                    break;
            }

            const modalTitle = document.getElementById('eventModalTitle');
            const modalBody = document.getElementById('eventModalBody');

            if (modalTitle && modalBody) {
                modalTitle.innerHTML = event.title;
                modalBody.innerHTML = content;

                const modal = new bootstrap.Modal(document.getElementById('eventModal'));
                modal.show();
            }
        }

        function getEventTooltip(event) {
            const props = event.extendedProps;

            switch (props.type) {
                case 'start_date':
                    return `Início: ${props.igreja} - ${props.pacote}`;
                case 'end_date':
                    return `Fim: ${props.igreja} (${props.status})`;
                case 'payment':
                    return `Pagamento: Kz ${props.valor.toLocaleString('pt-BR')} - ${props.igreja}`;
                case 'new_contract':
                    return `Novo: ${props.igreja} - ${props.pacote}`;
                default:
                    return event.title;
            }
        }

        // Cleanup function
        function destroyBillingsCalendar() {
            if (billingsCalendarInstance) {
                try {
                    billingsCalendarInstance.destroy();
                    billingsCalendarInstance = null;
                    calendarInitialized = false;
                    console.log('Calendário destruído');
                } catch (error) {
                    console.error('Erro ao destruir calendário:', error);
                }
            }
        }

        // ========== LIVEWIRE 3 COMPATIBILITY ==========

        // Inicialização inicial
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM carregado, inicializando calendário...');
            // Calcular hash inicial dos eventos
            const initialEvents = <?php echo json_encode($events, 15, 512) ?>;
            lastEventsHash = calculateEventsHash(initialEvents);
            // Inicializar calendário
            setTimeout(initializeBillingsCalendar, 100);

            // Log inicial do mês atual
            console.log('Mês inicial:', <?php echo e($selectedMonth); ?>, 'Ano inicial:', <?php echo e($selectedYear); ?>);
        });


        // Livewire 3: Quando o componente é atualizado
        document.addEventListener('livewire:updated', function(event) {
            console.log('🔄 Livewire updated, verificando se calendário precisa ser atualizado...');
            setTimeout(function() {
                if (!calendarInitialized) {
                    console.log('📅 Calendário não inicializado, inicializando...');
                    initializeBillingsCalendar();
                } else {
                    // Verificar se o mês mudou comparando com o estado atual
                    const currentMonth = mainCalendarInstance ? mainCalendarInstance.getDate().getMonth() + 1 : null;
                    const currentYear = mainCalendarInstance ? mainCalendarInstance.getDate().getFullYear() : null;
                    const newMonth = <?php echo e($selectedMonth); ?>;
                    const newYear = <?php echo e($selectedYear); ?>;

                    console.log(`📊 Estado atual: ${currentMonth}/${currentYear}, Novo estado: ${newMonth}/${newYear}`);

                    if (currentMonth !== newMonth || currentYear !== newYear) {
                        console.log(`📆 MÊS MUDOU! De ${currentMonth}/${currentYear} para ${newMonth}/${newYear}`);
                        console.log('🔄 Chamando updateCalendarForNewMonth...');
                        updateCalendarForNewMonth();
                    } else {
                        console.log('✅ Mês não mudou, apenas atualizando eventos...');
                        updateCalendarEvents();
                    }
                }
            }, 100);
        });

        // Livewire 3: Quando a navegação SPA começa
        document.addEventListener('livewire:navigating', function() {
            console.log('Navegando, destruindo calendário...');
            destroyBillingsCalendar();
        });

        // Livewire 3: Quando a navegação SPA termina
        document.addEventListener('livewire:navigated', function() {
            console.log('Navegação completa, reinicializando calendário...');
            setTimeout(initializeBillingsCalendar, 200);
        });

        // Eventos customizados do componente Livewire - Otimizados para invisibilidade
        Livewire.on('calendar-filter-changed', function() {
            // Aguardar o mínimo possível para DOM estar pronto
            requestAnimationFrame(() => {
                updateCalendarEvents();
            });
        });

        Livewire.on('calendar-month-changed', function() {
            console.log('📡 Evento calendar-month-changed RECEBIDO!');
            console.log('📅 Mês/ano atual no PHP:', <?php echo e($selectedMonth); ?>, '/', <?php echo e($selectedYear); ?>);

            // Aguardar um pouco para garantir que o DOM esteja atualizado
            setTimeout(() => {
                console.log('🚀 Executando updateCalendarForNewMonth...');
                updateCalendarForNewMonth();
            }, 10);
        });

        Livewire.on('calendar-refreshed', function() {
            requestAnimationFrame(() => {
                updateCalendarEvents();
            });
        });

        // SOLUÇÃO ROBUSTA: Forçar reinicialização completa do calendário
        function updateCalendarForNewMonth() {
            console.log('🔄 Iniciando atualização forçada do calendário...');

            try {
                // Obter o novo mês e ano do componente Livewire
                const newMonth = <?php echo e($selectedMonth); ?>;
                const newYear = <?php echo e($selectedYear); ?>;

                console.log(`📅 Novo mês/ano: ${newMonth}/${newYear}`);

                // DESTRUIR instâncias existentes completamente
                if (mainCalendarInstance) {
                    console.log('🗑️ Destruindo calendário principal...');
                    mainCalendarInstance.destroy();
                    mainCalendarInstance = null;
                }

                if (bufferCalendarInstance) {
                    console.log('🗑️ Destruindo calendário buffer...');
                    bufferCalendarInstance.destroy();
                    bufferCalendarInstance = null;
                }

                // Marcar como não inicializado para forçar reinicialização
                calendarInitialized = false;

                // LIMPAR elementos DOM completamente
                const mainEl = document.getElementById('calendar-billings');
                const bufferEl = document.getElementById('calendar-buffer');

                if (mainEl) {
                    mainEl.innerHTML = '';
                    console.log('🧹 Calendário principal limpo');
                }

                if (bufferEl) {
                    bufferEl.innerHTML = '';
                    console.log('🧹 Calendário buffer limpo');
                }

                // AGUARDAR um momento para garantir limpeza completa
                setTimeout(() => {
                    console.log('🚀 Reinicializando calendário com novo mês...');
                    initializeBillingsCalendar();

                    // Verificar se foi inicializado corretamente
                    setTimeout(() => {
                        if (mainCalendarInstance) {
                            const currentDate = mainCalendarInstance.getDate();
                            const currentMonth = currentDate.getMonth() + 1;
                            const currentYear = currentDate.getFullYear();

                            console.log(`✅ Calendário reinicializado - Mês atual: ${currentMonth}/${currentYear}`);

                            if (currentMonth !== newMonth || currentYear !== newYear) {
                                console.warn(`⚠️ Mês não corresponde! Esperado: ${newMonth}/${newYear}, Atual: ${currentMonth}/${currentYear}`);
                            } else {
                                console.log('🎉 Calendário atualizado com sucesso!');
                            }
                        } else {
                            console.error('❌ Falha na reinicialização do calendário');
                        }
                    }, 200);
                }, 100);

            } catch (error) {
                console.error('❌ Erro crítico na atualização do calendário:', error);
                // Último recurso: recarregar a página
                console.log('🔄 Último recurso: recarregando página...');
                window.location.reload();
            }
        }

        // Fallback: verificar periodicamente se o calendário precisa ser reinicializado
        let calendarCheckInterval = setInterval(function() {
            const calendarEl = document.getElementById('calendar-billings');
            if (calendarEl && !calendarEl.querySelector('.fc-view-harness') && !calendarEl.querySelector('.fc')) {
                console.log('Calendário parece quebrado, reinicializando...');
                calendarInitialized = false;
                initializeBillingsCalendar();
            }
        }, 3000);

        // Cleanup quando a página é fechada
        window.addEventListener('beforeunload', function() {
            destroyBillingsCalendar();
            if (calendarCheckInterval) {
                clearInterval(calendarCheckInterval);
            }
        });

        // Função para forçar atualização (pode ser chamada manualmente se necessário)
        window.forceCalendarUpdate = function() {
            console.log('Forçando atualização do calendário...');
            calendarInitialized = false;
            setTimeout(initializeBillingsCalendar, 100);
        };

        // Função de debug para verificar estado do calendário
        window.debugCalendarState = function() {
            console.log('=== DEBUG CALENDAR STATE ===');
            console.log('calendarInitialized:', calendarInitialized);
            console.log('mainCalendarInstance exists:', !!mainCalendarInstance);
            console.log('bufferCalendarInstance exists:', !!bufferCalendarInstance);

            if (mainCalendarInstance) {
                const date = mainCalendarInstance.getDate();
                console.log('Current calendar date:', date);
                console.log('Current month (JS):', date.getMonth() + 1);
                console.log('Current year (JS):', date.getFullYear());
            }

            console.log('PHP selectedMonth:', <?php echo e($selectedMonth); ?>);
            console.log('PHP selectedYear:', <?php echo e($selectedYear); ?>);
            console.log('Events count:', <?php echo json_encode($events, 15, 512) ?>.length);
            console.log('===========================');
        };

        // Verificação periódica para detectar problemas
        setInterval(() => {
            if (calendarInitialized && mainCalendarInstance) {
                const currentMonth = mainCalendarInstance.getDate().getMonth() + 1;
                const currentYear = mainCalendarInstance.getDate().getFullYear();
                const phpMonth = <?php echo e($selectedMonth); ?>;
                const phpYear = <?php echo e($selectedYear); ?>;

                if (currentMonth !== phpMonth || currentYear !== phpYear) {
                    console.warn(`🔴 DESINCRONIZAÇÃO DETECTADA!`);
                    console.warn(`Calendário: ${currentMonth}/${currentYear}`);
                    console.warn(`PHP: ${phpMonth}/${phpYear}`);
                    console.log('🔄 Corrigindo desincronização...');
                    updateCalendarForNewMonth();
                }
            }
        }, 2000);
    </script>
    <?php $__env->stopPush(); ?>
</div>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/billings/calendar.blade.php ENDPATH**/ ?>