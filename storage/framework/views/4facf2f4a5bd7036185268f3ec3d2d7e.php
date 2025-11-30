<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

<title><?php echo e($title ?? 'Admin Dashnoard'); ?></title>
<!-- Favicon -->
<link rel="shortcut icon" href="<?php echo e(asset('system/img/logo-system/icon-admin-blue.png')); ?>">
<!-- Aos Animation Css -->
<link rel="stylesheet" href="<?php echo e(asset('assets/css/core/libs.min.css')); ?>">

<!-- Aos Animation Css -->
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/aos/dist/aos.css')); ?>">

<!-- Hope Ui Design System Css -->
<link rel="stylesheet" href="<?php echo e(asset('assets/css/hope-ui.min.css')); ?>">

<!-- Custom Css -->
<link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.min.css')); ?>">

<!-- Customizer Css -->
<link rel="stylesheet" href="<?php echo e(asset('assets/css/customizer.min.css')); ?>">

<!-- RTL Css -->
<link rel="stylesheet" href="<?php echo e(asset('assets/css/rtl.min.css')); ?>">

<!-- System Pages Styles -->
<link rel="stylesheet" href="<?php echo e(asset('system/css/css_pages.css')); ?>">

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="<?php echo e(asset('system/css/toast.css')); ?>">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js" data-navigate-once></script>


<script src="<?php echo e(asset('system/js/full_calendar.js')); ?>" data-navigate-once></script>

<script>
let calendar = null;

function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');

    // Verifica se o elemento existe e se o calendário ainda não foi inicializado
    if (calendarEl && !calendar) {
        calendar = new FullCalendar.Calendar(calendarEl, {
            // Configurações básicas
            initialView: 'dayGridMonth',
            locale: 'pt-br',

            // Configurações de cabeçalho
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },

            // Configurações de texto em português
            buttonText: {
                today: 'Hoje',
                month: 'Mês',
                week: 'Semana',
                day: 'Dia'
            },

            // Configurações de altura e responsividade
            height: 'auto',
            aspectRatio: 1.8,

            // Configurações de navegação
            navLinks: true,
            dayMaxEvents: true,

            // Configurações de horário
            firstDay: 0, // Domingo como primeiro dia da semana
            weekNumbers: false,

            // Eventos de exemplo (remova se não precisar)
            events: [
                // {
                //     title: 'Evento de Exemplo',
                //     date: '2024-01-01',
                //     backgroundColor: '#3b82f6',
                //     borderColor: '#1d4ed8'
                // }
            ],

            // Customizações de estilo e interação
            eventDisplay: 'block',
            dayHeaderFormat: { weekday: 'short' },

            // Callbacks úteis
            dateClick: function(info) {
                // console.log('Data clicada: ' + info.dateStr);
                // Aqui você pode adicionar lógica para criar eventos
            },

            eventClick: function(info) {
                // console.log('Evento clicado: ' + info.event.title);
                // Aqui você pode adicionar lógica para editar/visualizar eventos
            }
        });
        calendar.render();

        // Aplicar estilos customizados após renderização
        applyCustomStyles();
    }
}

function applyCustomStyles() {
    // Adiciona estilos customizados ao calendário
    const style = document.createElement('style');
    style.textContent = `
        /* Estilos gerais do calendário */
        .fc {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        /* Header do calendário */
        .fc-header-toolbar {
            padding: 1.5rem 1.5rem 1rem 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
        }

        .fc-toolbar-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-transform: capitalize;
        }

        /* Botões do header */
        .fc-button {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .fc-button:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-1px);
        }

        .fc-button:focus {
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
        }

        .fc-button-active {
            background: rgba(255, 255, 255, 0.4) !important;
            border-color: rgba(255, 255, 255, 0.6) !important;
        }

        /* Dias da semana */
        .fc-col-header {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .fc-col-header-cell {
            padding: 1rem 0.5rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        /* Células dos dias */
        .fc-daygrid-day {
            border: 1px solid #f1f5f9;
            transition: background-color 0.2s ease;
        }

        .fc-daygrid-day:hover {
            background-color: #f8fafc;
        }

        .fc-daygrid-day-number {
            padding: 0.5rem;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
        }

        /* Dia atual */
        .fc-day-today {
            background-color: rgba(59, 130, 246, 0.05) !important;
            border-color: #3b82f6 !important;
        }

        .fc-day-today .fc-daygrid-day-number {
            background: #3b82f6;
            color: white;
            border-radius: 50%;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0.25rem;
        }

        /* Outros meses */
        .fc-day-other .fc-daygrid-day-number {
            color: #9ca3af;
        }

        /* Eventos */
        .fc-event {
            border: none;
            border-radius: 6px;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            margin: 1px;
        }

        .fc-event:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Botão "mais eventos" */
        .fc-daygrid-more-link {
            color: #6366f1;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .fc-daygrid-more-link:hover {
            background-color: rgba(99, 102, 241, 0.1);
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .fc-header-toolbar {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .fc-toolbar-title {
                font-size: 1.25rem;
                order: -1;
            }

            .fc-button-group {
                display: flex;
                gap: 0.5rem;
            }

            .fc-button {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
        }

        /* Popover de eventos */
        .fc-popover {
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border: none;
        }

        .fc-popover-header {
            background: #f8fafc;
            border-radius: 8px 8px 0 0;
            padding: 0.75rem 1rem;
            font-weight: 600;
            color: #374151;
        }
    `;

    if (!document.getElementById('fullcalendar-custom-styles')) {
        style.id = 'fullcalendar-custom-styles';
        document.head.appendChild(style);
    }
}

function destroyCalendar() {
    if (calendar) {
        calendar.destroy();
        calendar = null;
    }
}

// Evento para quando o DOM está carregado (primeira carga)
document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
});

// Eventos do Livewire 3 para navegação SPA
document.addEventListener('livewire:navigated', function() {
    // Reinicializa o calendário após navegação SPA
    initializeCalendar();
});

document.addEventListener('livewire:navigating', function() {
    // Destrói o calendário antes de navegar para evitar vazamentos de memória
    destroyCalendar();
});

// Opcional: Se você estiver usando componentes Livewire que podem ser atualizados
document.addEventListener('livewire:updated', function() {
    // Se o calendário foi removido do DOM durante uma atualização do Livewire
    const calendarEl = document.getElementById('calendar');
    if (calendarEl && !calendar) {
        initializeCalendar();
    }
});

</script>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/components/layouts/head/head.blade.php ENDPATH**/ ?>