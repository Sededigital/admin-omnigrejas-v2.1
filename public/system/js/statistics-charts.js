/**
 * Statistics Charts Manager for Livewire 3 and SPA
 * Gerenciador de gráficos para estatísticas com Livewire 3 e SPA
 * Compatível com navegação SPA e Livewire 3
 * Usando IIFE para evitar conflitos globais
 */

(function() {
    'use strict';

    // Prevenir múltiplas inicializações
    if (window.statisticsChartsInitialized) return;
    window.statisticsChartsInitialized = true;

    // Estado privado para controlar múltiplas execuções
    const statisticsChartsState = {
        handlingInProgress: false,
        initialized: false,
        charts: {}
    };

    class StatisticsCharts {
        constructor() {
            this.charts = statisticsChartsState.charts;
        }

        init() {
                // Evitar inicialização múltipla
                if (statisticsChartsState.initialized) {
                    console.log('[Init] StatisticsCharts already initialized, skipping');
                    return;
                }
                statisticsChartsState.initialized = true;

                console.log('[Init] StatisticsCharts initializing...');

                // Aguardar mais tempo para garantir que o DOM esteja completamente pronto
                setTimeout(() => {
                    console.log('[Init] DOM timeout reached, creating charts');
                    this.createCharts();
                }, 500);

                // Event listeners para Livewire 3 e SPA
                document.addEventListener('livewire:updated', () => {
                    console.log('[Init] Livewire updated, refreshing charts');
                    setTimeout(() => this.updateCharts(), 200);
                });

                document.addEventListener('livewire:navigated', () => {
                    console.log('[Init] Livewire navigated, creating charts');
                    setTimeout(() => this.createCharts(), 200);
                });

                document.addEventListener('livewire:loaded', () => {
                    console.log('[Init] Livewire loaded, creating charts');
                    setTimeout(() => this.createCharts(), 200);
                });

                // Listeners para eventos do Livewire
                if (typeof Livewire !== 'undefined') {
                    Livewire.on('refreshCharts', () => {
                        console.log('[Init] Refresh charts event received');
                        setTimeout(() => this.updateCharts(), 200);
                    });

                    Livewire.on('updateChartData', (newData) => {
                        console.log('[Init] Update chart data event received:', newData);
                        window.chartData = newData;
                        setTimeout(() => this.updateCharts(), 200);
                    });
                }

                // Cleanup para navegação SPA
                document.addEventListener('livewire:navigating', () => {
                    this.destroyCharts();
                });

                window.addEventListener('beforeunload', () => {
                    this.destroyCharts();
                });
            }

        createCharts() {
        this.destroyCharts();

        const chartData = this.getChartData();

        if (!chartData || Object.keys(chartData).length === 0) {
            console.log('[CreateCharts] No chart data available');
            return;
        }

        console.log('[CreateCharts] Creating charts with data:', chartData);

        // Criar TODOS os gráficos diretamente, seguindo o mesmo padrão dos que funcionam
        console.log('[CreateCharts] Creating all charts...');
        this.createMembersChart(chartData);
        this.createRolesChart(chartData);
        this.createEventsChart(chartData);
        this.createMinistriesChart(chartData);
        this.createGenderChart(chartData);
        this.createAgeGroupsChart(chartData);
        this.createChatActivityChart(chartData);
        this.createFinancialCategoriesChart(chartData);

        console.log('[CreateCharts] All charts created successfully');
    }

    // Método removido - agora todos os gráficos são criados diretamente

    // Método removido - não é mais necessário

    updateCharts() {
        setTimeout(() => {
            this.createCharts();
        }, 100);
    }

    destroyCharts() {
        Object.values(this.charts).forEach(chart => {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        });
        this.charts = {};
    }

    getChartData() {
        if (window.StatisticsManager && window.StatisticsManager.chartData &&
            Object.keys(window.StatisticsManager.chartData).length > 0) {
            return window.StatisticsManager.chartData;
        }

        if (window.chartData && Object.keys(window.chartData).length > 0) {
            return window.chartData;
        }

        if (window.livewire && window.livewire.components) {
            const statsComponent = Object.values(window.livewire.components).find(component =>
                component.name === 'church.members.statistics'
            );

            if (statsComponent && statsComponent.data && statsComponent.data.chartData) {
                return statsComponent.data.chartData;
            }
        }

        const chartDataElement = document.querySelector('[data-chart-data]');
        if (chartDataElement) {
            try {
                return JSON.parse(chartDataElement.dataset.chartData);
            } catch (e) {
                // Silently handle error
            }
        }

        return {};
    }

    createMembersChart(chartData) {
        const membersCtx = document.getElementById('membersGrowthChart');
        if (membersCtx && chartData.members) {
            this.charts.members = new Chart(membersCtx, {
                type: 'line',
                data: {
                    labels: chartData.members.labels || [],
                    datasets: [{
                        label: 'Novos Membros',
                        data: chartData.members.data || [],
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#007bff',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: '#6c757d'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#6c757d'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    }
                }
            });
        }
    }

    createRolesChart(chartData) {
        const rolesCtx = document.getElementById('rolesChart');
        if (rolesCtx && chartData.roles) {
            this.charts.roles = new Chart(rolesCtx, {
                type: 'doughnut',
                data: {
                    labels: chartData.roles.labels || [],
                    datasets: [{
                        data: chartData.roles.data || [],
                        backgroundColor: [
                            '#007bff',
                            '#28a745',
                            '#ffc107',
                            '#dc3545',
                            '#6f42c1',
                            '#fd7e14',
                            '#20c997',
                            '#e83e8c'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverBorderWidth: 3,
                        hoverBorderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }
    }

    createEventsChart(chartData) {
        const eventsCtx = document.getElementById('eventsChart');
        if (eventsCtx && chartData.events) {
            this.charts.events = new Chart(eventsCtx, {
                type: 'bar',
                data: {
                    labels: chartData.events.labels || [],
                    datasets: [{
                        label: 'Eventos',
                        data: chartData.events.data || [],
                        backgroundColor: '#ffc107',
                        borderColor: '#e0a800',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                        hoverBackgroundColor: '#e0a800'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: '#6c757d'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#6c757d'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    }
                }
            });
        }
    }

    createMinistriesChart(chartData) {
        const ministriesCtx = document.getElementById('ministriesChart');
        if (ministriesCtx && chartData.ministries) {
            this.charts.ministries = new Chart(ministriesCtx, {
                type: 'bar',
                data: {
                    labels: chartData.ministries.labels || [],
                    datasets: [{
                        label: 'Membros',
                        data: chartData.ministries.data || [],
                        backgroundColor: '#28a745',
                        borderColor: '#1e7e34',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                        hoverBackgroundColor: '#1e7e34'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: '#6c757d'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        y: {
                            ticks: {
                                color: '#6c757d'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    }
                }
            });
        }
    }

    createGenderChart(chartData) {
        const genderCtx = document.getElementById('genderChart');
        if (genderCtx && chartData.gender) {
            this.charts.gender = new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: chartData.gender.labels || [],
                    datasets: [{
                        data: chartData.gender.data || [],
                        backgroundColor: [
                            '#007bff',
                            '#dc3545',
                            '#6c757d'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverBorderWidth: 3,
                        hoverBorderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }
    }

    createAgeGroupsChart(chartData) {
        const ageGroupsCtx = document.getElementById('ageGroupsChart');
        if (ageGroupsCtx && chartData.age_groups) {
            this.charts.age_groups = new Chart(ageGroupsCtx, {
                type: 'bar',
                data: {
                    labels: chartData.age_groups.labels || [],
                    datasets: [{
                        label: 'Membros',
                        data: chartData.age_groups.data || [],
                        backgroundColor: '#17a2b8',
                        borderColor: '#138496',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                        hoverBackgroundColor: '#138496'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: '#6c757d'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#6c757d'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    }
                }
            });
        }
    }

    createChatActivityChart(chartData) {
        const chatActivityCtx = document.getElementById('chatActivityChart');
        if (chatActivityCtx && chartData.chat_activity) {
            this.charts.chat_activity = new Chart(chatActivityCtx, {
                type: 'bar',
                data: {
                    labels: chartData.chat_activity.labels || [],
                    datasets: [{
                        label: 'Mensagens',
                        data: chartData.chat_activity.data || [],
                        backgroundColor: '#6f42c1',
                        borderColor: '#5a32a3',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                        hoverBackgroundColor: '#5a32a3'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: '#6c757d'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        y: {
                            ticks: {
                                color: '#6c757d'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    }
                }
            });
        }
    }

    createFinancialCategoriesChart(chartData) {
        const financialCategoriesCtx = document.getElementById('financialCategoriesChart');
        if (financialCategoriesCtx && chartData.financial_categories) {
            this.charts.financial_categories = new Chart(financialCategoriesCtx, {
                type: 'bar',
                data: {
                    labels: chartData.financial_categories.labels || [],
                    datasets: [{
                        label: 'Entradas',
                        data: chartData.financial_categories.income || [],
                        backgroundColor: '#28a745',
                        borderColor: '#1e7e34',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                        hoverBackgroundColor: '#1e7e34'
                    }, {
                        label: 'Saídas',
                        data: chartData.financial_categories.expenses || [],
                        backgroundColor: '#dc3545',
                        borderColor: '#bd2130',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                        hoverBackgroundColor: '#bd2130'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 8,
                            displayColors: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: '#6c757d',
                                callback: function(value) {
                                    return 'AOA ' + value.toLocaleString();
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#6c757d'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    }
                }
            });
        }
    }
}

// Sistema completo de gerenciamento de gráficos para estatísticas
window.StatisticsManager = {
    chartData: {},

    initChartData: function(chartDataJson) {
        try {
            this.chartData = JSON.parse(chartDataJson);
            return true;
        } catch (error) {
            this.chartData = {};
            return false;
        }
    },

    updateChartData: function(newData) {
        this.chartData = newData;

        if (this.chartsInstance) {
            this.chartsInstance.updateCharts();
        }
    },

    init: function() {
        if (typeof Chart === 'undefined') {
            this.loadChartJS();
            return;
        }

        this.initializeCharts();
    },

    loadChartJS: function() {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        script.onload = () => {
            this.initializeCharts();
        };
        script.onerror = () => {
            // Silently handle error
        };
        document.head.appendChild(script);
    },

    initializeCharts: function() {
        try {
            this.chartsInstance = new StatisticsCharts();
            this.chartsInstance.init();
        } catch (error) {
            // Silently handle error
        }
    }
};

// Funções globais para compatibilidade
window.initChartData = function(chartDataJson) {
    return window.StatisticsManager.initChartData(chartDataJson);
};

window.updateChartData = function(newData) {
    return window.StatisticsManager.updateChartData(newData);
};

// Inicializar automaticamente quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    const chartDataElement = document.getElementById('chart-data-container');
    if (chartDataElement && chartDataElement.dataset.chartData) {
        window.StatisticsManager.initChartData(chartDataElement.dataset.chartData);
    }

    window.StatisticsManager.init();
});

// Event listeners para Livewire e navegação SPA
document.addEventListener('livewire:loaded', function() {
    window.StatisticsManager.init();
});

document.addEventListener('livewire:navigated', function() {
    window.StatisticsManager.init();
});

// Cleanup para navegação SPA
document.addEventListener('livewire:navigating', function() {
    if (window.StatisticsManager && window.StatisticsManager.chartsInstance) {
        window.StatisticsManager.chartsInstance.destroyCharts();
    }
});

// Exportar para uso global
window.StatisticsCharts = StatisticsCharts;

})();
