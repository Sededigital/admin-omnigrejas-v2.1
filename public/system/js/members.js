/**
 * JavaScript para a página de gestão de membros
 * Arquivo: members.js
 */

// Função para inicializar Flatpickr - movida para fora do event listener
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
        // console.log('Flatpickr initialized for', dateFields.length, 'fields');
    }
};

// Inicialização global - executada imediatamente quando o script carrega
const initMembersPage = () => {
    // console.log('Members page JavaScript loaded');

    // Inicializar Flatpickr imediatamente
    initializeFlatpickr();

    // Setup modal listeners
    const setupModalListeners = () => {
        const memberModal = document.getElementById('memberModal');
        if (memberModal && !memberModal.hasAttribute('data-listeners-attached')) {
            memberModal.setAttribute('data-listeners-attached', 'true');

            // Quando modal for aberto
            memberModal.addEventListener('shown.bs.modal', function () {
                // console.log('Member modal opened');

                // Reinicializar Flatpickr após modal abrir
                setTimeout(() => {
                    initializeFlatpickr();

                    // Focar no primeiro campo
                    const firstInput = memberModal.querySelector('input:not(.date_flatpicker), select, textarea');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }, 100);
            });

            // Quando modal for fechado
            memberModal.addEventListener('hidden.bs.modal', function (event) {
                event.stopPropagation();
                if (typeof $wire !== 'undefined') {
                    $wire.call('closeModal');
                }
                // console.log('Member modal closed and reset');
            });
        }
    };

    // Setup modal listeners
    setupModalListeners();

    // Validação adicional de datas
    const setupDateValidation = () => {
        const dataEntradaInput = document.querySelector('input[name="data_entrada"]');
        const dataSaidaInput = document.querySelector('input[name="data_saida"]');

        if (dataEntradaInput && dataSaidaInput) {
            dataSaidaInput.addEventListener('change', function() {
                const entrada = new Date(dataEntradaInput.value);
                const saida = new Date(this.value);

                if (saida < entrada) {
                    alert('A data de saída não pode ser anterior à data de entrada.');
                    this.value = '';
                }
            });
        }
    };

    // Melhorar experiência dos selects
    const enhanceSelects = () => {
        const selects = document.querySelectorAll('#memberModal select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                // console.log(`${this.name} changed to: ${this.value}`);
            });
        });
    };

    // Animações para cards
    const setupCardAnimations = () => {
        const cards = document.querySelectorAll('.card-hover');
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

    // console.log('All Members page JavaScript initialized successfully');
};

// Event listeners para Livewire e navegação SPA
document.addEventListener('livewire:loaded', initMembersPage);
document.addEventListener('livewire:navigated', initMembersPage);
document.addEventListener('DOMContentLoaded', initMembersPage);

// Escutar eventos do Livewire
document.addEventListener('livewire:loaded', () => {
    // Escutar evento de refresh dos membros
    Livewire.on('refreshMembers', () => {
        // console.log('Members refreshed');
        setTimeout(() => {
            initializeFlatpickr();
        }, 100);
    });

    // Reinicializar quando modal for aberto via Livewire
    Livewire.on('modalOpened', () => {
        // console.log('Modal opened via Livewire');
        setTimeout(() => {
            initializeFlatpickr();
        }, 200);
    });

    // Escutar evento para abrir modal de configuração
    Livewire.on('openConfigModal', () => {
        // console.log('Opening config modal');
        const configModal = new bootstrap.Modal(document.getElementById('cardConfigModal'));
        configModal.show();
    });

    // Escutar evento para fechar modal de configuração
    Livewire.on('closeConfigModal', () => {
        // console.log('Closing config modal');
        const configModal = bootstrap.Modal.getInstance(document.getElementById('cardConfigModal'));
        if (configModal) {
            configModal.hide();
        }
    });
});

// Cleanup para SPA navigation
document.addEventListener('livewire:navigating', () => {
    // Destruir instâncias do Flatpickr antes da navegação
    document.querySelectorAll('.date_flatpicker').forEach(field => {
        if (field._flatpickr) {
            field._flatpickr.destroy();
        }
    });
});
