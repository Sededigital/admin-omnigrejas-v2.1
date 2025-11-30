/*
* Versão: 1.2.1 (Adaptado para Livewire 3 - Corrigido)
* Template: Hope-Ui - Responsive Bootstrap 5 Admin Dashboard Template
* Autor: iqonic.design
* Adaptado por: Lésio luis
* NOTA: Este arquivo foi reestruturado para funcionar corretamente com a navegação "wire:navigate" do Livewire 3.
* Todas as inicializações de plugins e listeners de eventos agora são tratadas por uma função central que é
* chamada em cada navegação do Livewire. Adicionadas verificações de segurança para evitar erros null.
*/

"use strict";

// Variáveis para armazenar referências a listeners de eventos
let stickyScrollHandler = null;
let backToTopScrollHandler = null;
let windowResizeHandler = null;
let sidebarObserver = null;

/**
 * Função principal que inicializa todos os scripts e plugins do template.
 * Ela é chamada na carga inicial da página e em cada evento de navegação do Livewire.
 */
const initializeHopeUI = () => {
  /*-----------------------------------------------------------------------
                Sticky-Nav
  -------------------------------------------------------------------------*/
  const navsSticky = document.querySelector(".navs-sticky");
  if (navsSticky) {
    // Remove listeners existentes para evitar duplicação
    if (stickyScrollHandler) {
        window.removeEventListener('scroll', stickyScrollHandler);
    }
    stickyScrollHandler = () => {
        let yOffset = window.scrollY;
        if (yOffset >= 100) {
            navsSticky.classList.add("menu-sticky");
        } else {
            navsSticky.classList.remove("menu-sticky");
        }
    };
    window.addEventListener('scroll', stickyScrollHandler);
  }

  /*-----------------------------------------------------------------------
                Popover
  -------------------------------------------------------------------------*/
  if (typeof bootstrap !== 'undefined' && bootstrap.Popover) {
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    if (popoverTriggerList.length > 0) {
      popoverTriggerList.forEach(function (popoverTriggerEl) {
        // Destroy existing popover if exists
        const existingPopover = bootstrap.Popover.getInstance(popoverTriggerEl);
        if (existingPopover) {
          existingPopover.dispose();
        }
        new bootstrap.Popover(popoverTriggerEl);
      });
    }
  }

  /*-----------------------------------------------------------------------
                  Tooltip
  -------------------------------------------------------------------------*/
  if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"], [data-sidebar-toggle="tooltip"]');
    if (tooltipTriggerList.length > 0) {
      tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        // Destroy existing tooltip if exists
        const existingTooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
        if (existingTooltip) {
          existingTooltip.dispose();
        }
        new bootstrap.Tooltip(tooltipTriggerEl);
      });
    }
  }

  /*-----------------------------------------------------------------------
                Circle Progress
  -------------------------------------------------------------------------*/
  const progressBar = document.querySelectorAll('.circle-progress');
  if (typeof CircleProgress !== 'undefined' && progressBar.length > 0) {
    progressBar.forEach((elem) => {
      const minValue = elem.getAttribute('data-min-value');
      const maxValue = elem.getAttribute('data-max-value');
      const value = elem.getAttribute('data-value');
      const type = elem.getAttribute('data-type');
      const elemId = elem.getAttribute('id');

      if (elemId && minValue && maxValue && value) {
        new CircleProgress('#' + elemId, {
          min: minValue,
          max: maxValue,
          value: value,
          textFormat: type,
        });
      }
    });
  }

  /*-----------------------------------------------------------------------
                Progress Bar
  -------------------------------------------------------------------------*/
  const progressBarInit = (elem) => {
    if (!elem) return;

    const currentValue = elem.getAttribute('aria-valuenow');
    if (!currentValue) return;

    elem.style.width = '0%';
    elem.style.transition = 'width 2s';

    if (typeof Waypoint !== 'undefined') {
      new Waypoint({
        element: elem,
        handler: function () {
          setTimeout(() => {
            if (elem) {
              elem.style.width = currentValue + '%';
            }
          }, 100);
          this.destroy();
        },
        offset: 'bottom-in-view',
      });
    }
  };

  const customProgressBar = document.querySelectorAll('[data-toggle="progress-bar"]');
  if (customProgressBar.length > 0) {
    customProgressBar.forEach(progressBarInit);
  }

  /*-----------------------------------------------------------------------
                 noUiSlider
  -------------------------------------------------------------------------*/
  if (typeof noUiSlider !== 'undefined') {
    const rangeSlider = document.querySelectorAll('.range-slider');
    rangeSlider.forEach((elem) => {
      // Destroy existing slider if exists
      if (elem.noUiSlider) {
        elem.noUiSlider.destroy();
      }

      noUiSlider.create(elem, {
        start: [20, 80],
        connect: true,
        range: {
          'min': 0,
          'max': 100
        }
      });
    });

    const slider = document.querySelectorAll('.slider');
    slider.forEach((elem) => {
      // Destroy existing slider if exists
      if (elem.noUiSlider) {
        elem.noUiSlider.destroy();
      }

      noUiSlider.create(elem, {
        start: 50,
        connect: [true, false],
        range: {
          'min': 0,
          'max': 100
        }
      });
    });
  }

  /*-----------------------------------------------------------------------
                Copy To Clipboard
  -------------------------------------------------------------------------*/
  const copy = document.querySelectorAll('[data-toggle="copy"]');
  if (copy.length > 0) {
    copy.forEach((elem) => {
      // Remove existing listeners to prevent duplicates
      elem.removeEventListener('click', handleCopyClick);
      elem.addEventListener('click', handleCopyClick);
    });
  }

  function handleCopyClick(e) {
    e.preventDefault();
    const elem = e.currentTarget;
    const target = elem.getAttribute("data-copy-target");
    let value = elem.getAttribute("data-copy-value");

    if (target) {
      const container = document.querySelector(target);
      if (container) {
        value = container.value || container.innerHTML;
      }
    }

    if (value) {
      if (navigator.clipboard && window.isSecureContext) {
        // Use modern clipboard API
        navigator.clipboard.writeText(value).catch(err => {
          console.error('Failed to copy: ', err);
          fallbackCopyTextToClipboard(value);
        });
      } else {
        // Fallback for older browsers
        fallbackCopyTextToClipboard(value);
      }
    }
  }

  function fallbackCopyTextToClipboard(text) {
    const tempInput = document.createElement("input");
    document.body.appendChild(tempInput);
    tempInput.value = text;
    tempInput.select();
    try {
      document.execCommand("copy");
    } catch (err) {
      console.error('Fallback copy failed: ', err);
    }
    tempInput.remove();
  }

  /*-----------------------------------------------------------------------
                CounterUp 2
  -------------------------------------------------------------------------*/
  if (typeof window.counterUp !== 'undefined') {
    const counterUp = window.counterUp.default || window.counterUp;
    const counterUp2 = document.querySelectorAll('.counter');

    counterUp2.forEach((el) => {
      if (typeof Waypoint !== 'undefined') {
        new Waypoint({
          element: el,
          handler: function () {
            if (el && counterUp) {
              counterUp(el, {
                duration: 1000,
                delay: 10,
              });
            }
            this.destroy();
          },
          offset: "bottom-in-view",
        });
      }
    });
  }

  /*-----------------------------------------------------------------------
                SliderTab
  -------------------------------------------------------------------------*/
  const sliderTabs = document.querySelectorAll('[data-toggle="slider-tab"]');
  sliderTabs.forEach((elem) => {
    if (typeof SliderTab !== 'undefined') {
      new SliderTab(elem);
    }
  });

  /*-----------------------------------------------------------------------
                Swiper
  -------------------------------------------------------------------------*/
  const swiperCards = document.querySelectorAll('.d-slider1');
  swiperCards.forEach((swiperCard) => {
    if (typeof Swiper !== 'undefined') {
      // Destroy existing swiper if exists
      if (swiperCard.swiper) {
        swiperCard.swiper.destroy();
      }

      new Swiper(swiperCard, {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        breakpoints: {
          320: {
            slidesPerView: 1
          },
          480: {
            slidesPerView: 2
          },
          768: {
            slidesPerView: 3
          },
          1024: {
            slidesPerView: 4
          },
        },
      });
    }
  });

  /*-----------------------------------------------------------------------
    Data tables
  -------------------------------------------------------------------------*/
  if (typeof $ !== 'undefined' && $.fn.DataTable) {
    const dataTables = $('[data-toggle="data-table"]');
    if (dataTables.length) {
      // Destroy existing DataTable if exists
      dataTables.each(function() {
        if ($.fn.DataTable.isDataTable(this)) {
          $(this).DataTable().destroy();
        }
      });

      dataTables.DataTable({
        "dom": '<"row align-items-center"<"col-md-6" l><"col-md-6" f>><"table-responsive border-bottom my-3" rt><"row align-items-center" <"col-md-6" i><"col-md-6" p>><"clear">',
      });
    }
  }

  /*-----------------------------------------------------------------------
    Active Class for Pricing Table
  -------------------------------------------------------------------------*/
  const table = document.getElementById('my-table');
  if (table) {
    const tableTh = table.querySelectorAll('tr th');
    const tableTd = table.querySelectorAll('td');

    tableTh.forEach((elem) => {
      // Remove existing listeners
      elem.removeEventListener('click', handleTableClick);
      elem.addEventListener('click', handleTableClick);
    });

    function handleTableClick(e) {
      const elem = e.currentTarget;

      tableTh.forEach((th) => {
        if (th.children.length) {
          th.children[0].classList.remove('active');
        }
      });

      if (elem.children.length) {
        elem.children[0].classList.add('active');
      }

      tableTd.forEach((td) => td.classList.remove('active'));

      const col = Array.prototype.indexOf.call(table.querySelector('tr').children, elem);
      const tdIcons = table.querySelectorAll("tr td:nth-child(" + parseInt(col + 1) + ")");
      tdIcons.forEach((td) => td.classList.add('active'));
    }
  }

  /*-----------------------------------------------------------------------
                AOS Animation Plugin
  -------------------------------------------------------------------------*/
  if (typeof AOS !== 'undefined') {
    AOS.init({
      startEvent: 'DOMContentLoaded',
      disable: function () {
        var maxWidth = 996;
        return window.innerWidth < maxWidth;
      },
      throttleDelay: 10,
      once: true,
      duration: 700,
      offset: 10
    });
    // Refresh AOS on each navigation
    AOS.refresh();
  }

  /*-----------------------------------------------------------------------
                Resize Plugins
  -------------------------------------------------------------------------*/
  const resizePlugins = () => {
    const tabs = document.querySelectorAll('.nav');
    const sidebarResponsive = document.querySelector('.sidebar-default');

    if (window.innerWidth < 1025) {
      tabs.forEach((elem) => {
        if (!elem.classList.contains('flex-column') &&
            elem.classList.contains('nav-tabs') &&
            elem.classList.contains('nav-pills')) {
          elem.classList.add('flex-column', 'on-resize');
        }
      });

      if (sidebarResponsive && !sidebarResponsive.classList.contains('sidebar-mini')) {
        sidebarResponsive.classList.add('sidebar-mini', 'on-resize');
      }
    } else {
      tabs.forEach((elem) => {
        if (elem.classList.contains('on-resize')) {
          elem.classList.remove('flex-column', 'on-resize');
        }
      });

      if (sidebarResponsive &&
          sidebarResponsive.classList.contains('sidebar-mini') &&
          sidebarResponsive.classList.contains('on-resize')) {
        sidebarResponsive.classList.remove('sidebar-mini', 'on-resize');
      }
    }
  };
  resizePlugins();

  // Remove listeners existentes para evitar duplicação
  if (windowResizeHandler) {
      window.removeEventListener('resize', windowResizeHandler);
  }
  windowResizeHandler = () => {
      resizePlugins();
  };
  window.addEventListener('resize', windowResizeHandler);

  /*-----------------------------------------------------------------------
                Sidebar Toggle
  -------------------------------------------------------------------------*/
  const sidebarToggleBtn = document.querySelectorAll('[data-toggle="sidebar"]');
  const sidebar = document.querySelector('.sidebar-default');

  const setupSidebar = () => {
    if (sidebar) {
      const sidebarActiveItem = sidebar.querySelectorAll('.active');
      sidebarActiveItem.forEach((elem) => {
        const closestUl = elem.closest('ul');
        if (closestUl && !closestUl.classList.contains('iq-main-menu')) {
          const childMenu = closestUl;
          childMenu.classList.add('show');
          const parentLi = childMenu.closest('li');
          if (parentLi) {
            const parentMenu = parentLi.querySelector('.nav-link');
            if (parentMenu) {
              parentMenu.classList.add('collapsed');
              parentMenu.setAttribute('aria-expanded', 'true');
            }
          }
        }
      });
    }
  };
  setupSidebar();

  // Adicionar listeners para scroll nos links do sidebar
  const sidebarLinks = document.querySelectorAll('.sidebar-body .nav-link');
  sidebarLinks.forEach(link => {
    link.removeEventListener('click', handleSidebarLinkClick);
    link.addEventListener('click', handleSidebarLinkClick);
  });

  // Configurar MutationObserver para detectar mudanças na classe 'active'
  const sidebarBody = document.querySelector('.sidebar-body');
  if (sidebarBody && typeof MutationObserver !== 'undefined') {
    // Desconectar observer anterior se existir
    if (sidebarObserver) {
      sidebarObserver.disconnect();
    }

    sidebarObserver = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          const target = mutation.target;
          if (target.classList.contains('nav-link') && target.classList.contains('active')) {
            // Link ativo mudou, fazer scroll
            setTimeout(() => {
              scrollSidebarToActive();
            }, 50);
          }
        }
      });
    });

    // Observar mudanças na classe de todos os links do sidebar
    sidebarObserver.observe(sidebarBody, {
      attributes: true,
      subtree: true,
      attributeFilter: ['class']
    });
  }

  sidebarToggleBtn.forEach((sidebarBtn) => {
    // Remove existing listeners
    sidebarBtn.removeEventListener('click', handleSidebarToggle);
    sidebarBtn.addEventListener('click', handleSidebarToggle);
  });

  function handleSidebarToggle() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
      sidebar.classList.toggle('sidebar-mini');
    }
  }

  /*---------------------------------------------------------------------------
                              Back To Top
  ----------------------------------------------------------------------------*/
  const backToTop = document.getElementById("back-to-top");
  if (backToTop) {
    backToTop.classList.add("animate__animated", "animate__fadeOut");

    // Remove existing scroll listener
    if (backToTopScrollHandler) {
        window.removeEventListener('scroll', backToTopScrollHandler);
    }
    backToTopScrollHandler = () => {
        if (document.documentElement.scrollTop > 250) {
            backToTop.classList.remove("animate__fadeOut");
            backToTop.classList.add("animate__fadeIn");
        } else {
            backToTop.classList.remove("animate__fadeIn");
            backToTop.classList.add("animate__fadeOut");
        }
    };
    window.addEventListener('scroll', backToTopScrollHandler);

    const topButton = document.querySelector('#top');
    if (topButton) {
      topButton.removeEventListener('click', handleTopClick);
      topButton.addEventListener('click', handleTopClick);
    }
  }

  function handleTopClick(e) {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  /*---------------------------------------------------------------------
  | | | | | DropDown
  -----------------------------------------------------------------------*/
  function darken_screen(yesno) {
    const screenDarken = document.querySelector('.screen-darken');
    if (!screenDarken) return;

    if (yesno) {
      screenDarken.classList.add('active');
    } else {
      screenDarken.classList.remove('active');
    }
  }

  function close_offcanvas() {
    darken_screen(false);
    const mobileOffcanvas = document.querySelector('.mobile-offcanvas.show');
    if (mobileOffcanvas) {
      mobileOffcanvas.classList.remove('show');
      document.body.classList.remove('offcanvas-active');
    }
  }

  function show_offcanvas(offcanvas_id) {
    if (!offcanvas_id) return;

    darken_screen(true);
    const offcanvas = document.getElementById(offcanvas_id);
    if (offcanvas) {
      offcanvas.classList.add('show');
      document.body.classList.add('offcanvas-active');
    }
  }

  // Setup offcanvas triggers
  const triggerElements = document.querySelectorAll('[data-trigger]');
  triggerElements.forEach(function (element) {
    const offcanvas_id = element.getAttribute('data-trigger');
    element.removeEventListener('click', handleTriggerClick);
    element.addEventListener('click', handleTriggerClick);
  });

  function handleTriggerClick(e) {
    e.preventDefault();
    const offcanvas_id = e.currentTarget.getAttribute('data-trigger');
    show_offcanvas(offcanvas_id);
  }

  // Setup close buttons
  const closeButtons = document.querySelectorAll('.btn-close');
  closeButtons.forEach(function (button) {
    button.removeEventListener('click', close_offcanvas);
    button.addEventListener('click', close_offcanvas);
  });

  // Setup screen darken click
  const screenDarken = document.querySelector('.screen-darken');
  if (screenDarken) {
    screenDarken.removeEventListener('click', close_offcanvas);
    screenDarken.addEventListener('click', close_offcanvas);
  }

  // Setup navbar collapse
  const navbarSideCollapse = document.querySelector('#navbarSideCollapse');
  if (navbarSideCollapse) {
    navbarSideCollapse.removeEventListener('click', handleNavbarCollapse);
    navbarSideCollapse.addEventListener('click', handleNavbarCollapse);
  }

  function handleNavbarCollapse() {
    const offcanvasCollapse = document.querySelector('.offcanvas-collapse');
    if (offcanvasCollapse) {
      offcanvasCollapse.classList.toggle('open');
    }
  }

  /*---------------------------------------------------------------------------
                                     Form Validation
  ---------------------------------------------------------------------------*/
  const validationForms = document.querySelectorAll('.needs-validation');
  validationForms.forEach(form => {
    form.removeEventListener('submit', handleFormValidation);
    form.addEventListener('submit', handleFormValidation, false);
  });

  function handleFormValidation(event) {
    const form = event.currentTarget;
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }
    form.classList.add('was-validated');
  }

  /*------------------------------------------------------------
                               Flatpickr
  -------------------------------------------------------------*/
  if (typeof flatpickr !== 'undefined') {
    const flatpickrSelectors = [
      { selector: '.date_flatpicker', config: { minDate: "today", dateFormat: "Y-m-d" } },
      { selector: '.range_flatpicker', config: { mode: "range", minDate: "today", dateFormat: "Y-m-d" } },
      { selector: '.wrap_flatpicker', config: { wrap: true, minDate: "today", dateFormat: "Y-m-d" } },
      { selector: '.time_flatpicker', config: { enableTime: true, noCalendar: true, dateFormat: "H:i" } },
      { selector: '.inline_flatpicker', config: { inline: true, minDate: "today", dateFormat: "Y-m-d" } }
    ];

    flatpickrSelectors.forEach(item => {
      const elements = document.querySelectorAll(item.selector);
      elements.forEach(elem => {
        // Destroy existing flatpickr instance if exists
        if (elem._flatpickr) {
          elem._flatpickr.destroy();
        }
        flatpickr(elem, item.config);
      });
    });
  }
};

// Global cleanup function para remover todos os event listeners
const cleanupHopeUI = () => {
  // Remove window event listeners usando as referências globais
  if (stickyScrollHandler) {
      window.removeEventListener('scroll', stickyScrollHandler);
      stickyScrollHandler = null;
  }
  if (backToTopScrollHandler) {
      window.removeEventListener('scroll', backToTopScrollHandler);
      backToTopScrollHandler = null;
  }
  if (windowResizeHandler) {
      window.removeEventListener('resize', windowResizeHandler);
      windowResizeHandler = null;
  }

  // Destroy Bootstrap components
  if (typeof bootstrap !== 'undefined') {
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
      const popover = bootstrap?.Popover?.getInstance(el);
      if (popover) popover.dispose();
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"], [data-sidebar-toggle="tooltip"]').forEach(el => {
      const tooltip = bootstrap?.Tooltip?.getInstance(el);
      if (tooltip) tooltip.dispose();
    });
  }

  // Destroy Swiper instances
  document.querySelectorAll('.d-slider1').forEach(el => {
    if (el.swiper) el.swiper.destroy();
  });

  // Destroy noUiSlider instances
  document.querySelectorAll('.range-slider, .slider').forEach(el => {
    if (el.noUiSlider) el.noUiSlider.destroy();
    });

  // Destroy DataTables
  if (typeof $ !== 'undefined' && $.fn.DataTable) {
    $('[data-toggle="data-table"]').each(function() {
      if ($.fn.DataTable.isDataTable(this)) {
        $(this).DataTable().destroy();
      }
    });
  }

  // Destroy Flatpickr instances
  document.querySelectorAll('.date_flatpicker, .range_flatpicker, .wrap_flatpicker, .time_flatpicker, .inline_flatpicker').forEach(el => {
    if (el._flatpickr) {
      el._flatpickr.destroy();
    }
  });

  // Remove sidebar link click listeners
  document.querySelectorAll('.sidebar-body .nav-link').forEach(link => {
    link.removeEventListener('click', handleSidebarLinkClick);
  });

  // Desconectar MutationObserver
  if (sidebarObserver) {
    sidebarObserver.disconnect();
    sidebarObserver = null;
  }
};

const scrollSidebarToActive = () => {
    const sidebarBody = document.querySelector('.sidebar-body');
    if (!sidebarBody) return;

    const activeLink = sidebarBody.querySelector('.nav-link.active');
    if (activeLink) {
      const rect = activeLink.getBoundingClientRect();
      const sidebarRect = sidebarBody.getBoundingClientRect();
      const scrollTop = sidebarBody.scrollTop + rect.top - sidebarRect.top - sidebarBody.clientHeight / 2;
      sidebarBody.scrollTo({
        top: scrollTop,
        behavior: 'smooth'
      });
    }
  };

  // Função para scroll no clique do link (placeholder para futuras expansões)
  const handleSidebarLinkClick = (e) => {
    // Pode ser usado para lógica adicional no futuro
  };

  document.addEventListener('livewire:navigated', () => {
    initializeHopeUI();
    requestAnimationFrame(() => {
      scrollSidebarToActive();
    });
  });



  document.addEventListener('DOMContentLoaded', () => {
    initializeHopeUI();
    scrollSidebarToActive();
  });


// Listener para o evento inicial de carregamento da página
document.addEventListener('DOMContentLoaded', () => {
  initializeHopeUI();
});

