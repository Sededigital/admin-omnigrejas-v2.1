/**
 * JavaScript para a página de comunidade das alianças
 * Arquivo: alliance.js
 */

// Função para inicializar Flatpickr - movida para fora do event listener
if (typeof initializeFlatpickr === 'undefined') {
const initializeFlatpickr = () => {
    if (typeof flatpickr !== 'undefined') {
        // Destruir instâncias existentes primeiro
        document.querySelectorAll('.date_flatpicker').forEach(field => {
            if (field._flatpickr) {
                field._flatpickr.destroy();
            }
        });

        // Reinicializar todos os campos de data
        const dateFields = document.querySelectorAll('.date_flatpicker');
        dateFields.forEach(function(field) {
            flatpickr(field, {
                dateFormat: "Y-m-d",
                minDate: field.getAttribute('data-min-date') || "1900-01-01",
                maxDate: field.getAttribute('data-max-date') || "today",
                disableMobile: true, // Forçar sempre desktop mode
                position: "auto",
                appendTo: document.body,
                allowInput: false,
                clickOpens: true,
                onReady: function(selectedDates, dateStr, instance) {
                    instance.calendarContainer.style.zIndex = 10050;
                },
                onOpen: function(selectedDates, dateStr, instance) {
                    instance.calendarContainer.style.zIndex = 10050;
                    // Garantir posicionamento correto
                    setTimeout(() => {
                        if (window.innerWidth <= 768) {
                            const calendar = instance.calendarContainer;
                            calendar.style.position = 'fixed';
                            calendar.style.top = '50%';
                            calendar.style.left = '50%';
                            calendar.style.transform = 'translate(-50%, -50%)';
                        }
                    }, 10);
                }
            });
        });
    }
};

// Inicialização global - executada imediatamente quando o script carrega
if (typeof initAlliancePage === 'undefined') {
const initAlliancePage = () => {
    // Inicializar Flatpickr imediatamente
    initializeFlatpickr();

    // Setup modal listeners
    const setupModalListeners = () => {
        const meetingModal = document.getElementById('meetingModal');
        if (meetingModal && !meetingModal.hasAttribute('data-listeners-attached')) {
            meetingModal.setAttribute('data-listeners-attached', 'true');

            // Quando modal for aberto
            meetingModal.addEventListener('shown.bs.modal', function () {
                // Reinicializar Flatpickr após modal abrir
                setTimeout(() => {
                    initializeFlatpickr();

                    // Focar no primeiro campo
                    const firstInput = meetingModal.querySelector('input:not(.date_flatpicker), select, textarea');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }, 100);
            });

            // Quando modal for fechado
            meetingModal.addEventListener('hidden.bs.modal', function (event) {
                event.stopPropagation();
                if (typeof $wire !== 'undefined') {
                    $wire.call('closeModal');
                }
            });
        }

        // Listener específico para o modal de confirmação
        const confirmModal = document.getElementById('confirmModal');
        if (confirmModal && !confirmModal.hasAttribute('data-confirm-listeners-attached')) {
            confirmModal.setAttribute('data-confirm-listeners-attached', 'true');

            // Quando o modal de confirmação for completamente fechado (por qualquer método)
            confirmModal.addEventListener('hidden.bs.modal', function (event) {
                // Aplicar limpeza completa
                setTimeout(() => {
                    // Garantir que todas as classes sejam removidas
                    confirmModal.classList.remove('show', 'fade', 'in');
                    confirmModal.setAttribute('aria-hidden', 'true');
                    confirmModal.style.display = 'none';

                    // Remover todos os backdrops
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());

                    // Restaurar body completamente
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';

                    // Verificar se há outros modais abertos
                    const outrosModais = document.querySelectorAll('.modal.show');

                    // Só restaurar foco se não houver outros modais
                    if (outrosModais.length === 0) {
                        // Restaurar foco para o botão que abriu o modal
                        const focusElement = document.querySelector('[data-bs-target="#confirmModal"]');
                        if (focusElement) {
                            focusElement.focus();
                        }
                    }
                }, 100); // Delay menor para limpeza imediata
            });

            // Listener adicional para o botão de cancelar (segurança extra)
            const cancelButton = confirmModal.querySelector('button[data-bs-dismiss="modal"]');
            if (cancelButton && !cancelButton.hasAttribute('data-cancel-listener-attached')) {
                cancelButton.setAttribute('data-cancel-listener-attached', 'true');

                cancelButton.addEventListener('click', function() {
                    // Pequeno delay para garantir que o Bootstrap inicie o fechamento
                    setTimeout(() => {
                        // Verificar se ainda há problemas após o clique no cancelar
                        const backdropsAtivos = document.querySelectorAll('.modal-backdrop').length;
                        const bodyComModal = document.body.classList.contains('modal-open');

                        if (backdropsAtivos > 0 || bodyComModal) {
                            // Forçar limpeza adicional
                            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                        }
                    }, 200);
                });
            }

            // Listener para o botão X (fechar) do modal
            const closeButton = confirmModal.querySelector('.btn-close');
            if (closeButton && !closeButton.hasAttribute('data-close-listener-attached')) {
                closeButton.setAttribute('data-close-listener-attached', 'true');

                closeButton.addEventListener('click', function() {
                    // Mesmo tratamento que o botão cancelar
                    setTimeout(() => {
                        const backdropsAtivos = document.querySelectorAll('.modal-backdrop').length;
                        const bodyComModal = document.body.classList.contains('modal-open');

                        if (backdropsAtivos > 0 || bodyComModal) {
                            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                        }
                    }, 200);
                });
            }
        }
    };

    // Setup modal listeners
    setupModalListeners();

    // Setup Livewire listeners
    const setupLivewireListeners = () => {
        // Escutar evento de refresh das reuniões
        Livewire.on('refreshMeetings', () => {
            setTimeout(() => {
                initializeFlatpickr();
            }, 100);
        });

        // Reinicializar quando modal for aberto via Livewire
        Livewire.on('modalOpened', () => {
            setTimeout(() => {
                initializeFlatpickr();
            }, 200);
        });

        // Abrir modal de confirmação
        Livewire.on('showConfirmModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        });

        // Fechar modal de confirmação após entrada bem-sucedida
        Livewire.on('closeConfirmModal', () => {
            const confirmModal = document.getElementById('confirmModal');

            if (confirmModal) {
                const modal = bootstrap.Modal.getInstance(confirmModal);

                if (modal) {
                    // Método robusto para fechar modal completamente
                    try {
                        // 1. Esconder o modal usando Bootstrap
                        modal.hide();

                        // 2. Forçar remoção do backdrop após um pequeno delay
                        setTimeout(() => {
                            // Remover classes do modal
                            confirmModal.classList.remove('show');
                            confirmModal.setAttribute('aria-hidden', 'true');
                            confirmModal.style.display = 'none';

                            // Remover backdrop manualmente se ainda existir
                            const backdrops = document.querySelectorAll('.modal-backdrop');
                            backdrops.forEach(backdrop => backdrop.remove());

                            // Restaurar scroll do body
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';

                            // Verificar se há outros modais abertos
                            const outrosModais = document.querySelectorAll('.modal.show');

                            // Só restaurar foco se não houver outros modais
                            if (outrosModais.length === 0) {
                                // Restaurar foco para o botão que abriu o modal
                                const focusElement = document.querySelector('[data-bs-target="#confirmModal"]');
                                if (focusElement) {
                                    focusElement.focus();
                                }
                            }

                            // Verificação final após mais um delay
                            setTimeout(() => {
                                const modalAindaAberto = confirmModal.classList.contains('show');
                                const bodyAindaComModal = document.body.classList.contains('modal-open');
                                const backdropsRestantes = document.querySelectorAll('.modal-backdrop').length;

                                if (modalAindaAberto || bodyAindaComModal || backdropsRestantes > 0) {
                                    confirmModal.classList.remove('show');
                                    confirmModal.style.display = 'none';
                                    document.body.classList.remove('modal-open');
                                    document.body.style.overflow = '';
                                    document.body.style.paddingRight = '';
                                    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                                }
                            }, 500);

                        }, 300); // Delay para garantir que o Bootstrap termine sua animação

                    } catch (error) {
                        // Fallback: fechar modal manualmente
                        confirmModal.classList.remove('show');
                        confirmModal.setAttribute('aria-hidden', 'true');
                        confirmModal.style.display = 'none';

                        // Remover backdrops
                        const backdrops = document.querySelectorAll('.modal-backdrop');
                        backdrops.forEach(backdrop => backdrop.remove());

                        // Restaurar body
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    }
                } else {
                    // Fallback: fechar modal manualmente
                    confirmModal.classList.remove('show');
                    confirmModal.setAttribute('aria-hidden', 'true');
                    confirmModal.style.display = 'none';

                    // Remover backdrops
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());

                    // Restaurar body
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }
            }
        });
    };

    // Setup Livewire listeners
    setupLivewireListeners();

    // Validação adicional de datas
    const setupDateValidation = () => {
        const dataInicioInput = document.querySelector('input[name="hora_inicio"]');
        const dataFimInput = document.querySelector('input[name="hora_fim"]');

        if (dataInicioInput && dataFimInput) {
            dataFimInput.addEventListener('change', function() {
                const inicio = dataInicioInput.value;
                const fim = this.value;

                if (inicio && fim && fim <= inicio) {
                    alert('A hora de fim deve ser posterior à hora de início.');
                    this.value = '';
                }
            });
        }
    };
    }

    // Melhorar experiência dos selects
    const enhanceSelects = () => {
        const selects = document.querySelectorAll('#meetingModal select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                // console.log(`${this.name} changed to: ${this.value}`);
            });
        });
    };

    // Animações para cards
    const setupCardAnimations = () => {
        const cards = document.querySelectorAll('.meeting-card, .alliance-item');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    };

    // Executar todas as inicializações
    setupDateValidation();
    enhanceSelects();
    setupCardAnimations();
};
}

// Event listeners para Livewire e navegação SPA
document.addEventListener('livewire:loaded', initAlliancePage);
document.addEventListener('livewire:navigated', initAlliancePage);
document.addEventListener('DOMContentLoaded', initAlliancePage);

// Cleanup para SPA navigation
document.addEventListener('livewire:navigating', () => {
    // Destruir instâncias do Flatpickr antes da navegação
    document.querySelectorAll('.date_flatpicker').forEach(field => {
        if (field._flatpickr) {
            field._flatpickr.destroy();
        }
    });
});
