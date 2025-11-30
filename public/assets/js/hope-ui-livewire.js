// Hope UI - Versão otimizada para Livewire
// Namespace para evitar conflitos globais
window.HopeUI = window.HopeUI || {};

/*---------------------------------------------------------------------
              Circle Progress
-----------------------------------------------------------------------*/
HopeUI.initCircleProgress = function() {
  const progressBar = document.getElementsByClassName('circle-progress');

  if (typeof progressBar !== 'undefined' && progressBar.length > 0) {
    Array.from(progressBar, (elem) => {
      // Evita reinicializar elementos já processados
      if (elem.dataset.hopeInitialized === 'true') return;

      const minValue = elem.getAttribute('data-min-value');
      const maxValue = elem.getAttribute('data-max-value');
      const value = elem.getAttribute('data-value');
      const type = elem.getAttribute('data-type');

      if (elem.getAttribute('id') !== '' && elem.getAttribute('id') !== null) {
        new CircleProgress('#' + elem.getAttribute('id'), {
          min: minValue,
          max: maxValue,
          value: value,
          textFormat: type,
        });

        // Marca como inicializado
        elem.dataset.hopeInitialized = 'true';
      }
    });
  }
};

/*---------------------------------------------------------------------
              Progress Bar
-----------------------------------------------------------------------*/
HopeUI.progressBarInit = function(elem) {
  // Evita reinicializar elementos já processados
  if (elem.dataset.hopeInitialized === 'true') return;

  const currentValue = elem.getAttribute('aria-valuenow');
  elem.style.width = '0%';
  elem.style.transition = 'width 2s';

  if (typeof Waypoint !== 'undefined') {
    new Waypoint({
      element: elem,
      handler: function () {
        setTimeout(() => {
          elem.style.width = currentValue + '%';
        }, 100);
      },
      offset: 'bottom-in-view',
    });
  }

  // Marca como inicializado
  elem.dataset.hopeInitialized = 'true';
};

HopeUI.initProgressBars = function() {
  const customProgressBar = document.querySelectorAll('[data-toggle="progress-bar"]:not([data-hope-initialized="true"])');
  Array.from(customProgressBar, (elem) => {
    HopeUI.progressBarInit(elem);
  });
};

// Função principal de inicialização
HopeUI.init = function() {
  HopeUI.initCircleProgress();
  HopeUI.initProgressBars();
  // Adicione aqui outras inicializações do Hope UI
};

// Inicialização automática
document.addEventListener('DOMContentLoaded', HopeUI.init);

// Integração com Livewire
document.addEventListener('livewire:navigated', HopeUI.init);
document.addEventListener('livewire:updated', HopeUI.init);
