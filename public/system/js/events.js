/**
 * JavaScript para a página de gestão de eventos
 * Arquivo: events.js
 */

// Função para inicializar Flatpickr - movida para fora do event listener
const initializeFlatpickrEvents = () => {
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
                minDate: field.getAttribute('data-min-date') || "today",
                maxDate: field.getAttribute('data-max-date') || "2030-12-31",
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
const initEventsPage = () => {
  //  console.log('Events page JavaScript loaded');

    // Inicializar Flatpickr imediatamente
    initializeFlatpickrEvents();

    // Setup modal listeners
    const setupModalListeners = () => {
        const eventModal = document.getElementById('eventModal');
        if (eventModal && !eventModal.hasAttribute('data-listeners-attached')) {
            eventModal.setAttribute('data-listeners-attached', 'true');

            // Quando modal for aberto
            eventModal.addEventListener('shown.bs.modal', function () {
             //   console.log('Event modal opened');

                // Reinicializar Flatpickr após modal abrir
                setTimeout(() => {
                    initializeFlatpickrEvents();

                    // Focar no primeiro campo
                    const firstInput = eventModal.querySelector('input:not(.date_flatpicker), select, textarea');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }, 100);
            });

            // Quando modal for fechado
            eventModal.addEventListener('hidden.bs.modal', function (event) {
                event.stopPropagation();
                if (typeof $wire !== 'undefined') {
                    $wire.call('closeModal');
                }
           //     console.log('Event modal closed and reset');
            });
        }
    };

    // Setup modal listeners
    setupModalListeners();

    // Validação de datas e horários
    const setupDateValidation = () => {
        const dataInicioInput = document.querySelector('input[name="data_inicio"]');
        const dataFimInput = document.querySelector('input[name="data_fim"]');
        const horaInicioInput = document.querySelector('input[name="hora_inicio"]');
        const horaFimInput = document.querySelector('input[name="hora_fim"]');

        if (dataInicioInput && dataFimInput) {
            dataFimInput.addEventListener('change', function() {
                const inicio = new Date(dataInicioInput.value);
                const fim = new Date(this.value);

                if (fim < inicio) {
                    alert('A data de fim não pode ser anterior à data de início.');
                    this.value = '';
                }
            });
        }

        if (horaInicioInput && horaFimInput) {
            horaFimInput.addEventListener('change', function() {
                const inicio = horaInicioInput.value;
                const fim = this.value;

                if (inicio && fim && fim <= inicio) {
                    alert('A hora de fim deve ser posterior à hora de início.');
                    this.value = '';
                }
            });
        }
    };

    // Melhorar experiência dos selects
    const enhanceSelects = () => {
        const selects = document.querySelectorAll('#eventModal select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
            //    console.log(`${this.name} changed to: ${this.value}`);
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

  //  console.log('All Events page JavaScript initialized successfully');
};

// Event listeners para Livewire e navegação SPA
document.addEventListener('livewire:loaded', initEventsPage);
document.addEventListener('livewire:navigated', initEventsPage);
document.addEventListener('DOMContentLoaded', initEventsPage);

// Escutar eventos do Livewire
document.addEventListener('livewire:loaded', () => {
    // Escutar evento de refresh dos eventos
    Livewire.on('refreshEvents', () => {
      //  console.log('Events refreshed');
        setTimeout(() => {
            initializeFlatpickrEvents();
        }, 100);
    });

    // Reinicializar quando modal for aberto via Livewire
    Livewire.on('modalOpened', () => {
      //  console.log('Modal opened via Livewire');
        setTimeout(() => {
            initializeFlatpickrEvents();
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
