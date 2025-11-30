(function (jQuery) {
    "use strict";

    /**
     * Objeto que armazena as instâncias dos gráficos ApexCharts para
     * que possamos destruí-las e recriá-las corretamente.
     */
    let charts = {};

    /**
     * Função para inicializar todos os gráficos do ApexCharts.
     * É chamada na carga inicial e em cada navegação do Livewire.
     * Para evitar erros, primeiro removemos qualquer gráfico existente
     * antes de criar um novo.
     */
    const initializeApexCharts = () => {
        // Destrói todas as instâncias de gráficos existentes para evitar conflitos.
        for (const chartId in charts) {
            if (charts[chartId]) {
                charts[chartId].destroy();
                delete charts[chartId];
            }
        }

        // Verifica se ApexCharts está disponível
        if (typeof ApexCharts === 'undefined') {
            console.warn('ApexCharts não está carregado');
            return;
        }

        // apex-basic
        if (document.querySelectorAll("#apex-basic").length) {
            const options = {
                chart: {
                    height: 350,
                    type: "line",
                    zoom: {
                        enabled: false
                    }
                },
                colors: ["#3a57e8"],
                series: [{
                    name: "Desktops",
                    data: [10, 41, 35, 51, 49, 62, 69, 91, 148]
                }],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: "straight"
                },
                title: {
                    text: "Product Trends by Month",
                    align: "left"
                },
                xaxis: {
                    categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep"]
                }
            };
            const chart = new ApexCharts(document.querySelector("#apex-basic"), options);
            chart.render();
            charts['apex-basic'] = chart;
        }

        // apex-line-area
        if (document.querySelectorAll("#apex-line-area").length) {
            const options = {
                chart: {
                    height: 350,
                    type: "area"
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: "smooth"
                },
                colors: ["#3a57e8", "#c03221"],
                series: [{
                    name: "series1",
                    data: [31, 40, 28, 51, 42, 109, 100]
                }, {
                    name: "series2",
                    data: [11, 32, 45, 32, 34, 52, 41]
                }],
                xaxis: {
                    type: "datetime",
                    categories: ["2018-09-19T00:00:00", "2018-09-19T01:30:00", "2018-09-19T02:30:00", "2018-09-19T03:30:00", "2018-09-19T04:30:00", "2018-09-19T05:30:00", "2018-09-19T06:30:00"]
                },
                tooltip: {
                    x: {
                        format: "dd/MM/yy HH:mm"
                    }
                }
            };
            const chart = new ApexCharts(document.querySelector("#apex-line-area"), options);
            chart.render();
            charts['apex-line-area'] = chart;
        }

        // apex-column
        if (document.querySelectorAll("#apex-column").length) {
            const options = {
                chart: {
                    height: 350,
                    type: "bar"
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: "55%",
                        endingShape: "rounded"
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ["transparent"]
                },
                colors: ["#3a57e8", "#1aa053", "#c03221"],
                series: [{
                    name: "Net Profit",
                    data: [44, 55, 57, 56, 61, 58]
                }, {
                    name: "Revenue",
                    data: [76, 85, 101, 98, 87, 105]
                }, {
                    name: "Free Cash Flow",
                    data: [35, 41, 36, 26, 45, 48]
                }],
                xaxis: {
                    categories: ["Feb", "Mar", "Apr", "May", "Jun", "Jul"]
                },
                yaxis: {
                    title: {
                        text: "$ (thousands)"
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (e) {
                            return "$ " + e + " thousands"
                        }
                    }
                }
            };
            const chart = new ApexCharts(document.querySelector("#apex-column"), options);
            chart.render();
            charts['apex-column'] = chart;
        }

        // apex-bar
        if (document.querySelectorAll("#apex-bar").length) {
            const options = {
                chart: {
                    height: 350,
                    type: "bar"
                },
                plotOptions: {
                    bar: {
                        horizontal: true
                    }
                },
                dataLabels: {
                    enabled: false
                },
                colors: ["#3a57e8"],
                series: [{
                    data: [470, 540, 580, 690, 1100, 1200, 1380]
                }],
                xaxis: {
                    categories: ["Netherlands", "Italy", "France", "Japan", "United States", "China", "Germany"]
                }
            };
            const chart = new ApexCharts(document.querySelector("#apex-bar"), options);
            chart.render();
            charts['apex-bar'] = chart;
        }

        // apex-mixed-chart
        if (document.querySelectorAll("#apex-mixed-chart").length) {
            const options = {
                chart: {
                    height: 350,
                    type: "line",
                    stacked: false
                },
                stroke: {
                    width: [0, 2, 5],
                    curve: "smooth"
                },
                plotOptions: {
                    bar: {
                        columnWidth: "50%"
                    }
                },
                colors: ["#c03221", "#1aa053", "#3a57e8"],
                series: [{
                    name: "Facebook",
                    type: "column",
                    data: [23, 11, 22, 27, 13, 22, 37, 21, 44, 22, 30]
                }, {
                    name: "Vine",
                    type: "area",
                    data: [44, 55, 41, 67, 22, 43, 21, 41, 56, 27, 43]
                }, {
                    name: "Dribbble",
                    type: "line",
                    data: [30, 25, 36, 30, 45, 35, 64, 52, 59, 36, 39]
                }],
                fill: {
                    opacity: [0.85, 0.25, 1],
                    gradient: {
                        inverseColors: false,
                        shade: "light",
                        type: "vertical",
                        opacityFrom: 0.85,
                        opacityTo: 0.55,
                        stops: [0, 100, 100, 100]
                    }
                },
                labels: ["01/01/2003", "02/01/2003", "03/01/2003", "04/01/2003", "05/01/2003", "06/01/2003", "07/01/2003", "08/01/2003", "09/01/2003", "10/01/2003", "11/01/2003"],
                markers: {
                    size: 0
                },
                xaxis: {
                    type: "datetime"
                },
                yaxis: {
                    min: 0
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (e) {
                            return e !== undefined ? e.toFixed(0) + " views" : e;
                        }
                    }
                },
                legend: {
                    labels: {
                        useSeriesColors: true
                    },
                    markers: {
                        customHTML: [function () {
                            return "";
                        }, function () {
                            return "";
                        }, function () {
                            return "";
                        }]
                    }
                }
            };
            const chart = new ApexCharts(document.querySelector("#apex-mixed-chart"), options);
            chart.render();
            charts['apex-mixed-chart'] = chart;
        }

        // apex-candlestick-chart
        if (document.querySelectorAll("#apex-candlestick-chart").length) {
            const options = {
                chart: {
                    height: 350,
                    type: "candlestick"
                },
                colors: ["#3a57e8", "#1aa053"],
                series: [{
                    data: [{
                        x: new Date(15387786e5),
                        y: [6629.81, 6650.5, 6623.04, 6633.33]
                    }, {
                        x: new Date(15387804e5),
                        y: [6632.01, 6643.59, 6620, 6630.11]
                    }, {
                        x: new Date(15387822e5),
                        y: [6630.71, 6648.95, 6623.34, 6635.65]
                    }, {
                        x: new Date(1538784e6),
                        y: [6635.65, 6651, 6629.67, 6638.24]
                    }, {
                        x: new Date(15387858e5),
                        y: [6638.24, 6640, 6620, 6624.47]
                    }, {
                        x: new Date(15387876e5),
                        y: [6624.53, 6636.03, 6621.68, 6624.31]
                    }, {
                        x: new Date(15387894e5),
                        y: [6624.61, 6632.2, 6617, 6626.02]
                    }, {
                        x: new Date(15387912e5),
                        y: [6627, 6627.62, 6584.22, 6603.02]
                    }, {
                        x: new Date(1538793e6),
                        y: [6605, 6608.03, 6598.95, 6604.01]
                    }, {
                        x: new Date(15387948e5),
                        y: [6604.5, 6614.4, 6602.26, 6608.02]
                    }, {
                        x: new Date(15387966e5),
                        y: [6608.02, 6610.68, 6601.99, 6608.91]
                    }, {
                        x: new Date(15387984e5),
                        y: [6608.91, 6618.99, 6608.01, 6612]
                    }, {
                        x: new Date(15388002e5),
                        y: [6612, 6615.13, 6605.09, 6612]
                    }, {
                        x: new Date(1538802e6),
                        y: [6612, 6624.12, 6608.43, 6622.95]
                    }, {
                        x: new Date(15388038e5),
                        y: [6623.91, 6623.91, 6615, 6615.67]
                    }]
                }],
                title: {
                    text: 'CandleStick Chart',
                    align: 'left'
                },
                xaxis: {
                    type: 'datetime'
                },
                yaxis: {
                    tooltip: {
                        enabled: true
                    }
                }
            };
            const chart = new ApexCharts(document.querySelector("#apex-candlestick-chart"), options);
            chart.render();
            charts['apex-candlestick-chart'] = chart;
        }
    };

    // Chamada inicial para renderizar os gráficos na primeira carga da página.
    initializeApexCharts();

    // Listener para o evento de navegação do Livewire, que acionará a reinicialização.
    document.addEventListener('livewire:navigated', () => {
        // Um pequeno atraso garante que o DOM foi completamente substituído antes de renderizar os gráficos.
        setTimeout(initializeApexCharts, 100);
    });

    // Listener para eventos personalizados do Livewire
    document.addEventListener('periodoAlterado', () => {
        // Quando o período é alterado, reinicializa os gráficos
        setTimeout(initializeApexCharts, 100);
    });

    // Listener para mudanças de página do Livewire
    document.addEventListener('livewire:load', () => {
        initializeApexCharts();
    });

    // Listener para atualizações do Livewire
    document.addEventListener('livewire:update', () => {
        setTimeout(initializeApexCharts, 100);
    });

})(jQuery);
