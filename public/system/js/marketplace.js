/**
 * JavaScript para as páginas do marketplace
 * Arquivo: marketplace.js
 */

// Função para inicializar a página de produtos
const initProductsPage = () => {
    // console.log('Products page JavaScript loaded');

    // Setup modal listeners
    const setupModalListeners = () => {
        const productModal = document.getElementById('productModal');
        if (productModal && !productModal.hasAttribute('data-listeners-attached')) {
            productModal.setAttribute('data-listeners-attached', 'true');

            // Quando modal for aberto
            productModal.addEventListener('shown.bs.modal', function () {
                // console.log('Product modal opened');

                // Focar no primeiro campo
                const firstInput = productModal.querySelector('input:not([type="radio"]), select, textarea');
                if (firstInput) {
                    firstInput.focus();
                }
            });

            // Quando modal for fechado
            productModal.addEventListener('hidden.bs.modal', function (event) {
                event.stopPropagation();
                if (typeof $wire !== 'undefined') {
                    $wire.call('closeModal');
                }
                // console.log('Product modal closed and reset');
            });
        }
    };

    // Setup modal listeners
    setupModalListeners();

    // console.log('All Products page JavaScript initialized successfully');
};

// Função para inicializar a página de pedidos
const initOrdersPage = () => {
    // console.log('Orders page JavaScript loaded');
    // console.log('All Orders page JavaScript initialized successfully');
};

// Função para inicializar a página de pagamentos
const initPaymentsPage = () => {
    // console.log('Payments page JavaScript loaded');
    // console.log('All Payments page JavaScript initialized successfully');
};

// Inicialização global - executada imediatamente quando o script carrega
const initMarketplacePages = () => {
    // Detectar qual página estamos
    const currentPath = window.location.pathname;

    if (currentPath.includes('/products')) {
        initProductsPage();
    } else if (currentPath.includes('/orders')) {
        initOrdersPage();
    } else if (currentPath.includes('/payments')) {
        initPaymentsPage();
    }
};

// Event listeners para Livewire e navegação SPA
document.addEventListener('livewire:loaded', initMarketplacePages);
document.addEventListener('livewire:navigated', initMarketplacePages);
document.addEventListener('DOMContentLoaded', initMarketplacePages);

// Escutar eventos do Livewire
document.addEventListener('livewire:loaded', () => {
    // Escutar evento de refresh dos produtos
    Livewire.on('refreshProducts', () => {
        // console.log('Products refreshed');
    });

    // Reinicializar quando modal for aberto via Livewire
    Livewire.on('modalOpened', () => {
        // console.log('Modal opened via Livewire');
    });

    // Escutar evento para abrir modal de produto
    Livewire.on('open-product-modal', () => {
        // console.log('Opening product modal');
        const productModal = new bootstrap.Modal(document.getElementById('productModal'));
        productModal.show();
    });

    // Escutar evento para fechar modal de produto
    Livewire.on('close-product-modal', () => {
        // console.log('Closing product modal');
        const productModal = bootstrap.Modal.getInstance(document.getElementById('productModal'));
        if (productModal) {
            productModal.hide();
        }
    });
});

// Cleanup para SPA navigation
document.addEventListener('livewire:navigating', () => {
    // Cleanup se necessário
});
