/**
 * JavaScript para a página de gestão de relatórios de culto
 * Arquivo: reports.js
 */

// Função para inicializar Flatpickr - movida para fora do event listener
const initializeFlatpickrReports = () => {
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
const initReportsPage = () => {
    // console.log('Reports page JavaScript loaded');

    // Inicializar Flatpickr imediatamente
    initializeFlatpickrReports();

    // Setup modal listeners
    const setupModalListeners = () => {
        const reportModal = document.getElementById('reportModal');
        if (reportModal && !reportModal.hasAttribute('data-listeners-attached')) {
            reportModal.setAttribute('data-listeners-attached', 'true');

            // Quando modal for aberto
            reportModal.addEventListener('shown.bs.modal', function () {
                // console.log('Report modal opened');

                // Restaurar aba ativa (se existir)
                const activeTab = localStorage.getItem('reportModalActiveTab');
                if (activeTab) {
                    const tabElement = reportModal.querySelector(`button[data-bs-target="${activeTab}"]`);
                    if (tabElement) {
                        const tab = new bootstrap.Tab(tabElement);
                        tab.show();
                    }
                }

                // Reinicializar Flatpickr após modal abrir
                setTimeout(() => {
                    initializeFlatpickrReports();

                    // Focar no primeiro campo da aba ativa
                    const activeTabContent = reportModal.querySelector('.tab-pane.show.active');
                    if (activeTabContent) {
                        const firstInput = activeTabContent.querySelector('input:not(.date_flatpicker), select, textarea');
                        if (firstInput) {
                            firstInput.focus();
                        }
                    }
                }, 100);
            });

            // Quando modal for fechado
            reportModal.addEventListener('hidden.bs.modal', function (event) {
                event.stopPropagation();
                // Limpar aba ativa ao fechar
                localStorage.removeItem('reportModalActiveTab');
                if (typeof $wire !== 'undefined') {
                    $wire.call('fecharModal');
                }
                // console.log('Report modal closed and reset');
            });
        }
    };

    // Setup tabs persistence
    const setupTabsPersistence = () => {
        // Escutar mudanças de aba e salvar no localStorage
        document.addEventListener('shown.bs.tab', function (event) {
            if (event.target.closest('#reportModal')) {
                const target = event.target.getAttribute('data-bs-target');
                localStorage.setItem('reportModalActiveTab', target);
                // console.log('Active tab saved:', target);
            }
        });
    };

    // Setup modal listeners
    setupModalListeners();
    setupTabsPersistence();

    // Validação adicional de datas
    const setupDateValidation = () => {
        const dataRelatorioInput = document.querySelector('input[name="data_relatorio"]');

        if (dataRelatorioInput) {
            dataRelatorioInput.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const today = new Date();

                if (selectedDate > today) {
                    alert('A data do relatório não pode ser no futuro.');
                    this.value = '';
                }
            });
        }
    };

    // Melhorar experiência dos selects
    const enhanceSelects = () => {
        const selects = document.querySelectorAll('#reportModal select');
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

    // Setup para preview do conteúdo
    const setupContentPreview = () => {
        const contentTextarea = document.querySelector('textarea[name="conteudo"]');
        const previewDiv = document.querySelector('.content-preview');

        if (contentTextarea && previewDiv) {
            contentTextarea.addEventListener('input', function() {
                if (this.value.trim()) {
                    previewDiv.innerHTML = this.value.replace(/\n/g, '<br>');
                } else {
                    previewDiv.innerHTML = '<span class="text-muted">O conteúdo aparecerá aqui conforme você digita...</span>';
                }
            });
        }
    };

    // Executar todas as inicializações
    setupDateValidation();
    enhanceSelects();
    setupCardAnimations();
    setupContentPreview();

    // console.log('All Reports page JavaScript initialized successfully');
};

// Event listeners para Livewire e navegação SPA
document.addEventListener('livewire:loaded', initReportsPage);
document.addEventListener('livewire:navigated', initReportsPage);
document.addEventListener('DOMContentLoaded', initReportsPage);

// Escutar eventos do Livewire
document.addEventListener('livewire:loaded', () => {
    // Escutar evento de refresh dos relatórios
    Livewire.on('refreshReports', () => {
        // console.log('Reports refreshed');
        setTimeout(() => {
            initializeFlatpickrReports();
        }, 100);
    });

    // Reinicializar quando modal for aberto via Livewire
    Livewire.on('modalOpened', () => {
        // console.log('Modal opened via Livewire');
        setTimeout(() => {
            initializeFlatpickrReports();
        }, 200);
    });

    // Escutar evento para abrir modal de relatório
    Livewire.on('openModal', (reportId) => {
        // console.log('Opening report modal for ID:', reportId);
        const modal = new bootstrap.Modal(document.getElementById('reportModal'));
        modal.show();
    });

    // Escutar evento para fechar modal de relatório
    Livewire.on('closeModal', () => {
        // console.log('Closing report modal');
        const modal = bootstrap.Modal.getInstance(document.getElementById('reportModal'));
        if (modal) {
            modal.hide();
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
