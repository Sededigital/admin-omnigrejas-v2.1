/**
 * JavaScript para a página de gestão de movimentos financeiros
 * Arquivo: financial-movements.js
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
                minDate: field.getAttribute('data-min-date') || "2020-01-01",
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
const initFinancialMovementsPage = () => {
    // console.log('Financial Movements page JavaScript loaded');

    // Inicializar Flatpickr imediatamente
    initializeFlatpickr();

    // Setup modal listeners
    const setupModalListeners = () => {
        const movementModal = document.getElementById('movementModal');
        if (movementModal && !movementModal.hasAttribute('data-listeners-attached')) {
            movementModal.setAttribute('data-listeners-attached', 'true');

            // Quando modal for aberto
            movementModal.addEventListener('shown.bs.modal', function () {
                // console.log('Movement modal opened');

                // Reinicializar Flatpickr após modal abrir
                setTimeout(() => {
                    initializeFlatpickr();

                    // Focar no primeiro campo
                    const firstInput = movementModal.querySelector('input:not(.date_flatpicker), select, textarea');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }, 100);
            });

            // Quando modal for fechado
            movementModal.addEventListener('hidden.bs.modal', function (event) {
                event.stopPropagation();
                if (typeof $wire !== 'undefined') {
                    $wire.call('closeModal');
                }
                // console.log('Movement modal closed and reset');
            });
        }
    };

    // Setup modal listeners
    setupModalListeners();

    // Validação adicional de datas
    const setupDateValidation = () => {
        const dataTransacaoInput = document.querySelector('input[name="data_transacao"]');

        if (dataTransacaoInput) {
            // Validação básica - data não pode ser futura
            dataTransacaoInput.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const today = new Date();

                if (selectedDate > today) {
                    alert('A data do movimento não pode ser futura.');
                    this.value = '';
                }
            });
        }
    };

    // Melhorar experiência dos selects
    const enhanceSelects = () => {
        const selects = document.querySelectorAll('#movementModal select');
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

    // console.log('All Financial Movements page JavaScript initialized successfully');
};

// Event listeners para Livewire e navegação SPA
document.addEventListener('livewire:loaded', initFinancialMovementsPage);
document.addEventListener('livewire:navigated', initFinancialMovementsPage);
document.addEventListener('DOMContentLoaded', initFinancialMovementsPage);

// Escutar eventos do Livewire
document.addEventListener('livewire:loaded', () => {
    // Escutar evento de refresh dos movimentos
    Livewire.on('refreshMovements', () => {
        // console.log('Movements refreshed');
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