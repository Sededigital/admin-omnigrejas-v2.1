(function (jQuery) {
    "use strict";

    /**
     * Função para inicializar o gráfico 'myChart' (RadialBar).
     * Esta função é chamada após a navegação do Livewire para recriar o gráfico.
     */
    const initMyChart = () => {
        if (document.querySelectorAll('#myChart').length) {
            const options = {
                series: [55, 75],
                chart: {
                    height: 230,
                    type: 'radialBar',
                },
                colors: ["#4bc7d2", "#3a57e8"],
                plotOptions: {
                    radialBar: {
                        hollow: {
                            margin: 10,
                            size: "50%",
                        },
                        track: {
                            margin: 10,
                            strokeWidth: '50%',
                        },
                        dataLabels: {
                            show: false,
                        }
                    }
                },
                labels: ['Apples', 'Oranges'],
            };
            if (typeof ApexCharts !== undefined) {
                const chart = new ApexCharts(document.querySelector("#myChart"), options);
                chart.render();
                document.addEventListener('ColorChange', (e) => {
                    const newOpt = {
                        colors: [e.detail.detail2, e.detail.detail1],
                    }
                    chart.updateOptions(newOpt)

                })
            }
        }
    };

    /**
     * Função para inicializar o gráfico 'd-activity' (Bar).
     * Esta função é chamada após a navegação do Livewire.
     */
    const initDActivityChart = () => {
        if (document.querySelectorAll('#d-activity').length) {
            const options = {
                series: [{
                    name: 'Successful deals',
                    data: [30, 50, 35, 60, 40, 60, 60, 30, 50, 35,]
                }, {
                    name: 'Failed deals',
                    data: [40, 50, 55, 50, 30, 80, 30, 40, 50, 55]
                }],
                chart: {
                    type: 'bar',
                    height: 230,
                    stacked: true,
                    toolbar: {
                        show: false
                    }
                },
                colors: ["#3a57e8", "#4bc7d2"],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '28%',
                        endingShape: 'rounded',
                        borderRadius: 5,
                    },
                },
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['S', 'M', 'T', 'W', 'T', 'F', 'S', 'M', 'T', 'W'],
                    labels: {
                        minHeight: 20,
                        maxHeight: 20,
                        style: {
                            colors: "#8A92A6",
                        },
                    }
                },
                yaxis: {
                    title: {
                        text: ''
                    },
                    labels: {
                        minWidth: 19,
                        maxWidth: 19,
                        style: {
                            colors: "#8A92A6",
                        },
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return "$ " + val + " thousands"
                        }
                    }
                }
            };
            const chart = new ApexCharts(document.querySelector("#d-activity"), options);
            chart.render();
        }
    };

    /**
     * Função para inicializar o gráfico 'd-main' (Bar).
     * Adicionada para resolver o erro de renderização após a navegação.
     */
    const initDMainChart = () => {
        if (document.querySelectorAll('#d-main').length) {
            const options = {
                series: [{
                    name: 'Successful deals',
                    data: [30, 50, 35, 60, 40, 60, 60, 30, 50, 35,]
                }, {
                    name: 'Failed deals',
                    data: [40, 50, 55, 50, 30, 80, 30, 40, 50, 55]
                }],
                chart: {
                    type: 'bar',
                    height: 230,
                    stacked: true,
                    toolbar: {
                        show: false
                    }
                },
                colors: ["#3a57e8", "#4bc7d2"],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '28%',
                        endingShape: 'rounded',
                        borderRadius: 5,
                    },
                },
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['S', 'M', 'T', 'W', 'T', 'F', 'S', 'M', 'T', 'W'],
                    labels: {
                        minHeight: 20,
                        maxHeight: 20,
                        style: {
                            colors: "#8A92A6",
                        },
                    }
                },
                yaxis: {
                    title: {
                        text: ''
                    },
                    labels: {
                        minWidth: 19,
                        maxWidth: 19,
                        style: {
                            colors: "#8A92A6",
                        },
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return "$ " + val + " thousands"
                        }
                    }
                }
            };
            const chart = new ApexCharts(document.querySelector("#d-main"), options);
            chart.render();
        }
    };

    /**
     * Função para inicializar o slider 'd-slider1'.
     * O Swiper.js também precisa ser reinicializado em cada navegação.
     */
    const initDSlider = () => {
        if ($('.d-slider1').length > 0) {
            const options = {
                centeredSlides: false,
                loop: false,
                slidesPerView: 4,
                autoplay: false,
                spaceBetween: 32,
                breakpoints: {
                    320: {
                        slidesPerView: 1
                    },
                    550: {
                        slidesPerView: 2
                    },
                    991: {
                        slidesPerView: 3
                    },
                    1400: {
                        slidesPerView: 3
                    },
                    1500: {
                        slidesPerView: 4
                    },
                    1920: {
                        slidesPerView: 6
                    },
                    2040: {
                        slidesPerView: 7
                    },
                    2440: {
                        slidesPerView: 8
                    }
                },
                pagination: {
                    el: '.swiper-pagination'
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev'
                },

                // And if we need scrollbar
                scrollbar: {
                    el: '.swiper-scrollbar'
                }
            }
            let swiper = new Swiper('.d-slider1', options);
        }
    };

    /**
     * Função principal para inicializar todos os componentes do dashboard.
     * Esta função será chamada pelo hope-ui.js após a navegação.
     */
    const initializeDashboard = () => {
        initMyChart();
        initDActivityChart();
        initDMainChart();
        initDSlider();
    };

    // Chamada inicial para renderizar os gráficos e sliders na primeira carga da página.
    initializeDashboard();

    // Listener para o evento de navegação do Livewire, que acionará a reinicialização.
    document.addEventListener('livewire:navigated', () => {
        // Um pequeno atraso garante que o DOM foi completamente substituído antes de renderizar os gráficos.
        setTimeout(initializeDashboard, 50);
    });

})(jQuery);
