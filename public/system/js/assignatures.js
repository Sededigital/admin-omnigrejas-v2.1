/**
* JavaScript para a página de gestão de assinaturas
* Arquivo: assignatures.js
*/

// Instância global do modal
let assinaturaModalInstance = null;

// Função para inicializar Flatpickr - movida para fora do event listener
const initializeFlatpickrAssignatures = () => {
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
                minDate: "today", // Não permite datas passadas
                maxDate: field.getAttribute('data-max-date') || null,
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
        // console.log('Flatpickr initialized for', dateFields.length, 'fields in assignatures');
    }
};

// Inicialização global - executada imediatamente quando o script carrega
const initAssignaturesPage = () => {
    // console.log('Assignatures page JavaScript loaded');

    // Inicializar Flatpickr imediatamente
    initializeFlatpickrAssignatures();

    // Setup modal listeners
    const setupModalListeners = () => {
        const assinaturaModal = document.getElementById('assinaturaModal');
        if (assinaturaModal && !assinaturaModal.hasAttribute('data-listeners-attached')) {
            assinaturaModal.setAttribute('data-listeners-attached', 'true');

            // Quando modal for aberto
            assinaturaModal.addEventListener('shown.bs.modal', function () {
                // console.log('Assinatura modal opened');

                // Armazenar instância do modal
                assinaturaModalInstance = bootstrap.Modal.getInstance(assinaturaModal);

                // Reinicializar Flatpickr após modal abrir
                setTimeout(() => {
                    initializeFlatpickrAssignatures();

                    // Focar no primeiro campo
                    const firstInput = assinaturaModal.querySelector('input:not(.date_flatpicker), select, textarea');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }, 100);
            });

            // Quando modal for fechado
            assinaturaModal.addEventListener('hidden.bs.modal', function (event) {
                event.stopPropagation();
                if (typeof $wire !== 'undefined') {
                    $wire.call('closeModal');
                }
                // Limpar instância
                assinaturaModalInstance = null;
                // console.log('Assinatura modal closed and reset');
            });
        }
    };

    // Setup modal listeners
    setupModalListeners();

    // Validação adicional de datas
    const setupDateValidation = () => {
        const dataInicioInput = document.querySelector('input[name="data_inicio"]');
        const dataFimInput = document.querySelector('input[name="data_fim"]');

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
    };

    // Melhorar experiência dos selects
    const enhanceSelects = () => {
        const selects = document.querySelectorAll('#assinaturaModal select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                // console.log(`${this.name} changed to: ${this.value}`);
            });
        });
    };

    // Executar todas as inicializações
    setupDateValidation();
    enhanceSelects();

    // Escutar eventos do Livewire
    if (!window.livewireListenersAttached) {
        window.livewireListenersAttached = true;
        Livewire.on('refreshAssinaturas', () => {
            // console.log('Assinaturas refreshed');
            setTimeout(() => {
                initializeFlatpickrAssignatures();
            }, 100);
        });

        Livewire.on('modalOpened', () => {
            // console.log('Modal opened via Livewire');
            setTimeout(() => {
                initializeFlatpickrAssignatures();
            }, 200);
        });

        Livewire.on('closeModalEvent', () => {
            // console.log('Closing modal via Livewire');
            if (assinaturaModalInstance) {
                assinaturaModalInstance.hide();
            }
        });

        // Adicionar listener para abrir modal de pagamentos
        Livewire.on('openPagamentoModal', () => {
            // console.log('JavaScript: Received openPagamentoModal event from Livewire');
            // Pequeno delay para garantir que o DOM está pronto
            setTimeout(() => {
                const button = document.getElementById('openPagamentoModalBtn');
                if (button) {
                    // console.log('JavaScript: Clicking hidden button to open pagamento modal');
                    button.click();
                } else {
                    // console.log('JavaScript: Hidden button openPagamentoModalBtn not found!');
                    // Tentar novamente após mais tempo
                    setTimeout(() => {
                        const retryButton = document.getElementById('openPagamentoModalBtn');
                        if (retryButton) {
                            // console.log('JavaScript: Retry - Clicking hidden button to open pagamento modal');
                            retryButton.click();
                        } else {
                            console.error('JavaScript: Hidden button still not found after retry!');
                        }
                    }, 500);
                }
            }, 100);
        });


Livewire.on('showDeleteModal', () => {
   // console.log('Opening delete modal');
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
});

Livewire.on('showStatusModal', () => {
   // console.log('Opening status modal');
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
});

Livewire.on('closeStatusModal', () => {
    const modal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
    if (modal) modal.hide();
});

Livewire.on('closeDeleteModal', () => {
    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
    if (modal) modal.hide();
});

}

    // console.log('All Assignatures page JavaScript initialized successfully');
};

// Event listeners para Livewire e navegação SPA
document.addEventListener('livewire:loaded', initAssignaturesPage);
document.addEventListener('livewire:navigated', initAssignaturesPage);
document.addEventListener('DOMContentLoaded', initAssignaturesPage);

// Escutar eventos do Livewire - movidos para dentro do init

// Cleanup para SPA navigation
document.addEventListener('livewire:navigating', () => {
    // Destruir instâncias do Flatpickr antes da navegação
    document.querySelectorAll('.date_flatpicker').forEach(field => {
        if (field._flatpickr) {
            field._flatpickr.destroy();
        }
    });
});
