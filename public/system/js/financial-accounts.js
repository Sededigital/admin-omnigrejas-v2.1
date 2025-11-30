/**
 * JavaScript para a página de gestão de contas financeiras
 * Arquivo: financial-accounts.js
 * Compatível com Livewire 3 e navegação SPA
 */

// Instâncias globais dos modais
let accountModalInstance = null;
let digitalChannelModalInstance = null;

// Inicialização global - executada imediatamente quando o script carrega
const initFinancialAccountsPage = () => {
    // console.log('Financial Accounts page JavaScript loaded');

    // Setup modal listeners
    const setupModalListeners = () => {
        const accountModal = document.getElementById('accountModal');
        const digitalChannelModal = document.getElementById('digitalChannelModal');

        // Setup Account Modal
        if (accountModal && !accountModal.hasAttribute('data-listeners-attached')) {
            accountModal.setAttribute('data-listeners-attached', 'true');

            // Quando modal for aberto
            accountModal.addEventListener('shown.bs.modal', function () {
                // console.log('Account modal opened');

                // Armazenar instância do modal
                accountModalInstance = bootstrap.Modal.getInstance(accountModal);

                // Focar no primeiro campo
                setTimeout(() => {
                    const firstInput = accountModal.querySelector('input:not(.date_flatpicker), select, textarea');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }, 100);
            });

            // Quando modal for fechado
            accountModal.addEventListener('hidden.bs.modal', function (event) {
                event.stopPropagation();
                if (typeof $wire !== 'undefined') {
                    $wire.call('closeModal');
                }
                // Limpar instância
                accountModalInstance = null;
                // console.log('Account modal closed and reset');
            });
        }

        // Setup Digital Channel Modal
        if (digitalChannelModal && !digitalChannelModal.hasAttribute('data-listeners-attached')) {
            digitalChannelModal.setAttribute('data-listeners-attached', 'true');

            // Quando modal for aberto
            digitalChannelModal.addEventListener('shown.bs.modal', function () {
                // console.log('Digital channel modal opened');

                // Armazenar instância do modal
                digitalChannelModalInstance = bootstrap.Modal.getInstance(digitalChannelModal);

                // Focar no primeiro campo
                setTimeout(() => {
                    const firstInput = digitalChannelModal.querySelector('input:not(.date_flatpicker), select, textarea');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }, 100);
            });

            // Quando modal for fechado
            digitalChannelModal.addEventListener('hidden.bs.modal', function (event) {
                event.stopPropagation();
                if (typeof $wire !== 'undefined') {
                    $wire.call('closeDigitalModal');
                }
                // Limpar instância
                digitalChannelModalInstance = null;
                // console.log('Digital channel modal closed and reset');
            });
        }
    };

    // Melhorar experiência dos selects
    const enhanceSelects = () => {
        const selects = document.querySelectorAll('#accountModal select, #digitalChannelModal select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                // console.log(`${this.name} changed to: ${this.value}`);
            });
        });
    };

    // Executar todas as inicializações
    setupModalListeners();
    enhanceSelects();

    // Escutar eventos do Livewire
    if (!window.livewireListenersAttached) {
        window.livewireListenersAttached = true;

        Livewire.on('refreshAccounts', () => {
            // console.log('Accounts refreshed');
            setTimeout(() => {
                // Re-inicializar se necessário
            }, 100);
        });

        Livewire.on('modalOpened', () => {
            // console.log('Modal opened via Livewire');
        });

        Livewire.on('closeModalEvent', () => {
            // console.log('Closing modal via Livewire');
            if (accountModalInstance) {
                accountModalInstance.hide();
            }
        });

        Livewire.on('closeDigitalModalEvent', () => {
            // console.log('Closing digital modal via Livewire');
            if (digitalChannelModalInstance) {
                digitalChannelModalInstance.hide();
            }
        });

        // Adicionar listener para abrir modal de conta
        Livewire.on('openAccountModal', () => {
            // console.log('JavaScript: Received openAccountModal event from Livewire');
            setTimeout(() => {
                const button = document.getElementById('openAccountModalBtn');
                if (button) {
                    // console.log('JavaScript: Clicking hidden button to open account modal');
                    button.click();
                } else {
                    // console.log('JavaScript: Hidden button openAccountModalBtn not found!');
                    setTimeout(() => {
                        const retryButton = document.getElementById('openAccountModalBtn');
                        if (retryButton) {
                            // console.log('JavaScript: Retry - Clicking hidden button to open account modal');
                            retryButton.click();
                        } else {
                            console.error('JavaScript: Hidden button still not found after retry!');
                        }
                    }, 500);
                }
            }, 100);
        });

        // Adicionar listener para abrir modal de canal digital
        Livewire.on('openDigitalChannelModal', () => {
            // console.log('JavaScript: Received openDigitalChannelModal event from Livewire');
            setTimeout(() => {
                const button = document.getElementById('openDigitalChannelModalBtn');
                if (button) {
                    // console.log('JavaScript: Clicking hidden button to open digital channel modal');
                    button.click();
                } else {
                    // console.log('JavaScript: Hidden button openDigitalChannelModalBtn not found!');
                    setTimeout(() => {
                        const retryButton = document.getElementById('openDigitalChannelModalBtn');
                        if (retryButton) {
                            // console.log('JavaScript: Retry - Clicking hidden button to open digital channel modal');
                            retryButton.click();
                        } else {
                            console.error('JavaScript: Hidden button still not found after retry!');
                        }
                    }, 500);
                }
            }, 100);
        });
    }

    // console.log('All Financial Accounts page JavaScript initialized successfully');
};

// Event listeners para Livewire e navegação SPA
document.addEventListener('livewire:loaded', initFinancialAccountsPage);
document.addEventListener('livewire:navigated', initFinancialAccountsPage);
document.addEventListener('DOMContentLoaded', initFinancialAccountsPage);

// Cleanup para SPA navigation
document.addEventListener('livewire:navigating', () => {
    // Limpar instâncias dos modais antes da navegação
    accountModalInstance = null;
    digitalChannelModalInstance = null;
});
