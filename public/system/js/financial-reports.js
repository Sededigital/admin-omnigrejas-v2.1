/**
 * JavaScript para a página de gestão de relatórios financeiros
 * Arquivo: financial-reports.js
 */

// Verificação para evitar re-execução desnecessária
if (typeof window.financialReportsInitialized !== 'undefined') {
    console.log('Financial Reports JS já inicializado, pulando...');
} else {
    window.financialReportsInitialized = true;

// Função para inicializar Flatpickr - configuração aprimorada para compatibilidade com Livewire 3 e SPA
const initializeFlatpickr = () => {
    if (typeof flatpickr !== 'undefined') {
        // Destruir instâncias existentes
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
                    // Garantir posicionamento correto em mobile
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

// Variáveis globais para armazenar instâncias dos gráficos
let entriesVsExitsChart = null;
let categoryChart = null;

// Função para inicializar gráficos
const initializeCharts = () => {
    console.log('Chart.js loaded:', typeof Chart !== 'undefined');
    console.log('Financial data available:', !!window.financialData);

    if (typeof Chart === 'undefined') {
        console.log('Chart.js not loaded, retrying...');
        setTimeout(initializeCharts, 100);
        return;
    }

    if (!window.financialData) {
        // Definir dados padrão se não estiver disponível
        console.log('Setting default financial data');
        window.financialData = {
            totalEntradas: 0,
            totalSaidas: 0,
            categoryNames: [],
            categorySaldos: []
        };
    }

    console.log('Financial data:', window.financialData);

    // Gráfico de Entradas vs Saídas
    const entriesVsExitsChartEl = document.getElementById('entriesVsExitsChart');
    console.log('Entries chart element found:', !!entriesVsExitsChartEl);

    if (entriesVsExitsChartEl) {
        // Destruir gráfico existente se houver
        if (entriesVsExitsChart) {
            console.log('Destroying existing entries chart');
            entriesVsExitsChart.destroy();
        }

        const entriesVsExitsCtx = entriesVsExitsChartEl.getContext('2d');
        const entradasData = parseFloat(window.financialData.totalEntradas) || 0;
        const saidasData = parseFloat(window.financialData.totalSaidas) || 0;

        console.log('Creating entries chart with data:', entradasData, saidasData);

        // Sempre criar gráfico, mesmo com dados zero
        try {
            entriesVsExitsChart = new Chart(entriesVsExitsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Entradas', 'Saídas'],
                    datasets: [{
                        data: [entradasData, saidasData],
                        backgroundColor: ['#28a745', '#dc3545'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
            console.log('Entries chart created successfully');
        } catch (error) {
            console.error('Error creating entries chart:', error);
        }
    }

    // Gráfico por Categoria
    const categoryChartEl = document.getElementById('categoryChart');
    console.log('Category chart element found:', !!categoryChartEl);

    if (categoryChartEl) {
        // Destruir gráfico existente se houver
        if (categoryChart) {
            console.log('Destroying existing category chart');
            categoryChart.destroy();
        }

        const categoryCtx = categoryChartEl.getContext('2d');
        const categoryNames = window.financialData.categoryNames || [];
        const categorySaldos = window.financialData.categorySaldos || [];

        console.log('Creating category chart with data:', categoryNames, categorySaldos);

        // Sempre criar gráfico, mesmo com arrays vazios
        try {
            categoryChart = new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: categoryNames,
                    datasets: [{
                        label: 'Saldo por Categoria',
                        data: categorySaldos,
                        backgroundColor: '#007bff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            console.log('Category chart created successfully');
        } catch (error) {
            console.error('Error creating category chart:', error);
        }
    }

    console.log('Charts initialization completed');
};

// Inicialização global - executada imediatamente quando o script carrega
const initFinancialReportsPage = () => {
    // Verificar se já foi inicializado para evitar duplicação
    if (document.body.hasAttribute('data-financial-reports-initialized')) {
        console.log('Financial Reports page already initialized, skipping...');
        return;
    }
    document.body.setAttribute('data-financial-reports-initialized', 'true');

    console.log('Initializing Financial Reports page...');
    // Inicializar Flatpickr imediatamente
    initializeFlatpickr();

    // Setup listeners para filtros
    const setupFilterListeners = () => {
        // Observar mudanças nos filtros para reinicializar Flatpickr se necessário
        const filterSection = document.querySelector('.filter-section');
        if (filterSection && !filterSection.hasAttribute('data-listeners-attached')) {
            filterSection.setAttribute('data-listeners-attached', 'true');

            // Reinicializar Flatpickr após mudanças nos filtros
            const observer = new MutationObserver(function(mutations) {
                let shouldReinitialize = false;
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                if (node.classList && node.classList.contains('date_flatpicker')) {
                                    shouldReinitialize = true;
                                }
                            }
                        });
                    }
                });

                if (shouldReinitialize) {
                    setTimeout(() => {
                        initializeFlatpickr();
                    }, 100);
                }
            });

            observer.observe(filterSection, {
                childList: true,
                subtree: true
            });
        }
    };

    // Setup filter listeners
    setupFilterListeners();

    // Validação adicional de datas
    const setupDateValidation = () => {
        const dateInputs = document.querySelectorAll('.date_flatpicker');
        dateInputs.forEach(input => {
            input.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const today = new Date();

                if (selectedDate > today) {
                    alert('A data não pode ser futura.');
                    this.value = '';
                }
            });
        });

        // Validação específica para datas de início e fim
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
        const selects = document.querySelectorAll('.filter-section select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                // Melhorar experiência do usuário
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

    // Inicializar gráficos com pequeno delay para garantir que os dados estejam disponíveis
    setTimeout(() => {
        initializeCharts();
    }, 100);

    // Executar todas as inicializações
    setupDateValidation();
    enhanceSelects();
    setupCardAnimations();

    console.log('Financial Reports page JavaScript initialized successfully');
};

// Event listeners para Livewire e navegação SPA
document.addEventListener('livewire:loaded', initFinancialReportsPage);
document.addEventListener('livewire:navigated', initFinancialReportsPage);
document.addEventListener('DOMContentLoaded', initFinancialReportsPage);

// Garantir que os gráficos sejam inicializados após tudo estar carregado
window.addEventListener('load', () => {
    setTimeout(() => {
        if (!entriesVsExitsChart || !categoryChart) {
            initializeCharts();
        }
    }, 500);
});

// Escutar eventos do Livewire
document.addEventListener('livewire:loaded', () => {
    // Escutar evento de refresh dos relatórios
    Livewire.on('refreshReports', () => {
        console.log('Refreshing financial reports...');
        setTimeout(() => {
            initializeFlatpickr();
            initializeCharts();
        }, 100);
    });

    // Reinicializar quando filtros forem aplicados
    Livewire.on('filtersApplied', () => {
        console.log('Filters applied, reinitializing...');
        setTimeout(() => {
            initializeFlatpickr();
            initializeCharts();
        }, 200);
    });

    // Reinicializar quando modal for aberto (se houver)
    Livewire.on('modalOpened', () => {
        console.log('Modal opened, reinitializing components...');
        setTimeout(() => {
            initializeFlatpickr();
        }, 200);
    });
});

// Cleanup para SPA navigation
document.addEventListener('livewire:navigating', () => {
    console.log('Navigating, cleaning up financial reports...');

    // Reset da flag de inicialização para permitir reinicialização na próxima página
    document.body.removeAttribute('data-financial-reports-initialized');

    // Destruir instâncias do Flatpickr antes da navegação
    document.querySelectorAll('.date_flatpicker').forEach(field => {
        if (field._flatpickr) {
            field._flatpickr.destroy();
        }
    });

    // Destruir instâncias dos gráficos antes da navegação
    if (entriesVsExitsChart) {
        entriesVsExitsChart.destroy();
        entriesVsExitsChart = null;
    }
    if (categoryChart) {
        categoryChart.destroy();
        categoryChart = null;
    }
});

// Fechar verificação condicional para evitar re-execução
}
