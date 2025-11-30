(function() {
    "use strict";

    // Objeto para armazenar instâncias dos gráficos
    let dashboardCharts = {};

    // Função para inicializar todos os gráficos do dashboard
    const initializeDashboardCharts = () => {
        // Verifica se ApexCharts está disponível
        if (typeof ApexCharts === 'undefined') {
            // console.warn('ApexCharts não está carregado');
            return;
        }

        // Destrói gráficos existentes para evitar conflitos
        for (const chartId in dashboardCharts) {
            if (dashboardCharts[chartId]) {
                dashboardCharts[chartId].destroy();
                delete dashboardCharts[chartId];
            }
        }

        // Gráfico de Vendas Brutas (Gross Sales)
        if (document.querySelectorAll('#d-main').length) {
            const grossSalesData = window.grossSalesData || [10, 41, 35, 51, 49, 62, 69, 91, 148];
            const options = {
                series: [{
                    name: 'Vendas Brutas',
                    data: grossSalesData
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                colors: ['#3a57e8'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.2,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set']
                },
                yaxis: {
                    title: {
                        text: 'Valor (Kz)'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "Kz " + val.toLocaleString('pt-BR');
                        }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#d-main"), options);
            chart.render();
            dashboardCharts['gross-sales'] = chart;
        }

        // Gráfico de Ganhos (Earnings)
        if (document.querySelectorAll('#ganhosChart').length) {
            const earningsData = window.earningsData || [44, 55, 57, 56, 61, 58];
            const options = {
                series: [{
                    name: 'Ganhos',
                    data: earningsData
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                colors: ['#1aa053'],
                xaxis: {
                    categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun']
                },
                yaxis: {
                    title: {
                        text: 'Valor (Kz)'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "Kz " + val.toLocaleString('pt-BR');
                        }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#ganhosChart"), options);
            chart.render();
            dashboardCharts['earnings'] = chart;
        }

        // Gráfico de Conversões
        if (document.querySelectorAll('#conversoesChart').length) {
            const conversionsData = window.conversionsData || [35, 41, 36, 26, 45, 48];
            const options = {
                series: [{
                    name: 'Conversões',
                    data: conversionsData
                }],
                chart: {
                    type: 'line',
                    height: 350
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                colors: ['#c03221'],
                xaxis: {
                    categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun']
                },
                yaxis: {
                    title: {
                        text: 'Taxa (%)'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + "%";
                        }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#conversoesChart"), options);
            chart.render();
            dashboardCharts['conversions'] = chart;
        }

        // Gráfico de Crescimento de Usuários
        if (document.querySelectorAll('#crescimentoUsuariosChart').length) {
            const usersData = window.usersData || [10, 15, 20, 25, 30, 35];
            const options = {
                series: [{
                    name: 'Usuários',
                    data: usersData
                }],
                chart: {
                    type: 'area',
                    height: 350
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                colors: ['#d48918'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.2,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun']
                },
                yaxis: {
                    title: {
                        text: 'Número de Usuários'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " usuários";
                        }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#crescimentoUsuariosChart"), options);
            chart.render();
            dashboardCharts['users-growth'] = chart;
        }
    };

    // Função para atualizar dados dos gráficos
    const updateChartData = (chartId, newData) => {
        if (dashboardCharts[chartId]) {
            dashboardCharts[chartId].updateSeries([{
                name: dashboardCharts[chartId].config.series[0].name,
                data: newData
            }]);
        }
    };

    // Função para atualizar todos os gráficos com novos dados
    const updateAllCharts = (data) => {
        if (data.grossSales) {
            updateChartData('gross-sales', data.grossSales);
        }
        if (data.earnings) {
            updateChartData('earnings', data.earnings);
        }
        if (data.conversions) {
            updateChartData('conversions', data.conversions);
        }
        if (data.usersGrowth) {
            updateChartData('users-growth', data.usersGrowth);
        }
    };

    // Inicializar gráficos na primeira carga
    initializeDashboardCharts();

    // Listener para o evento de navegação do Livewire
    document.addEventListener('livewire:navigated', () => {
        setTimeout(initializeDashboardCharts, 100);
    });

    // Listener para eventos personalizados do Livewire
    document.addEventListener('periodoAlterado', (event) => {
        // console.log('Período alterado:', event.detail);

        // Atualizar dados dos gráficos com as novas informações
        if (event.detail && event.detail.dados) {
            const dados = event.detail.dados;

            // Converter dados para arrays se necessário
            if (dados.grossSales) {
                const grossSalesArray = Array.isArray(dados.grossSales) ? dados.grossSales : Object.values(dados.grossSales);
                updateChartData('gross-sales', grossSalesArray);
            }

            if (dados.earnings) {
                const earningsArray = Array.isArray(dados.earnings) ? dados.earnings : Object.values(dados.earnings);
                updateChartData('earnings', earningsArray);
            }

            if (dados.conversions) {
                const conversionsArray = Array.isArray(dados.conversions) ? dados.conversions : Object.values(dados.conversions);
                updateChartData('conversions', conversionsArray);
            }

            if (dados.usersGrowth) {
                const usersArray = Array.isArray(dados.usersGrowth) ? dados.usersGrowth : Object.values(dados.usersGrowth);
                updateChartData('users-growth', usersArray);
            }
        }
    });

    // Listener para mudanças de página do Livewire
    document.addEventListener('livewire:load', () => {
        initializeDashboardCharts();
    });

    // Listener para atualizações do Livewire
    document.addEventListener('livewire:update', () => {
        setTimeout(initializeDashboardCharts, 100);
    });

    // Expor funções globalmente para uso externo
    window.dashboardCharts = {
        initialize: initializeDashboardCharts,
        update: updateAllCharts,
        updateChart: updateChartData
    };

})();
