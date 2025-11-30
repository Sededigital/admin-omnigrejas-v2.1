/**
 * CSRF Token Handler
 * Trata automaticamente erros de token CSRF expirado
 * Compatível com SPA e Livewire 3
 * Usando IIFE para evitar conflitos globais
 */

(function() {
    'use strict';

    // Prevenir múltiplas inicializações
    if (window.csrfHandlerInitialized) return;
    window.csrfHandlerInitialized = true;

    // Estado privado para controlar múltiplas execuções
    const csrfHandlerState = {
        handlingInProgress: false,
        initialized: false
    };

    // Função para interceptar XMLHttpRequest
    const setupXMLHttpRequestInterceptor = () => {
        const originalOpen = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
            this.addEventListener('load', function() {
                if (this.status === 419) {
                    try {
                        const data = JSON.parse(this.responseText);
                        if (data.csrf_expired) {
                            handleCsrfExpired(data);
                        }
                    } catch (e) {
                        // Se não conseguir parsear JSON, ainda trata como CSRF
                        handleCsrfExpired();
                    }
                }
            });
            return originalOpen.call(this, method, url, async, user, password);
        };
    };

    // Função para interceptar fetch
    const setupFetchInterceptor = () => {
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            return originalFetch.apply(this, args)
                .then(response => {
                    if (response.status === 419) {
                        return response.clone().json().then(data => {
                            if (data.csrf_expired) {
                                handleCsrfExpired(data);
                                return Promise.reject(new Error('CSRF token expired'));
                            }
                            return response;
                        }).catch(() => {
                            // Se não conseguir fazer parse, ainda trata como CSRF
                            handleCsrfExpired();
                            return Promise.reject(new Error('CSRF token expired'));
                        });
                    }
                    return response;
                })
                .catch(error => {
                    if (error.message !== 'CSRF token expired') {
                        throw error;
                    }
                });
        };
    };

    // Função para lidar com CSRF expirado
    const handleCsrfExpired = (data = null) => {
        // Evitar múltiplas execuções
        if (csrfHandlerState.handlingInProgress) {
            return;
        }
        csrfHandlerState.handlingInProgress = true;

        const message = data?.message || 'Sua sessão expirou. Recarregando página...';

        // Mostrar notificação se disponível
        if (window.showToast) {
            window.showToast(message, 'warning');
        } else if (window.toastr) {
            window.toastr.warning(message);
        } else {
            alert(message);
        }

        // Delay de 5 segundos para todos os casos
        setTimeout(() => {
            window.location.reload();
        }, 5000);
    };

    // Função para configurar interceptores do Livewire
    const setupLivewireInterceptors = () => {
        // Override do método de envio do Livewire para interceptar CSRF
        if (typeof Livewire !== 'undefined' && Livewire.request) {
            const originalRequest = Livewire.request;

            Livewire.request = function(component, url, method, payload, headers) {
                return originalRequest.call(this, component, url, method, payload, headers)
                    .catch(error => {
                        // Verificar se é erro CSRF
                        if (error.response && error.response.status === 419) {
                            if (!csrfHandlerState.handlingInProgress) {
                                csrfHandlerState.handlingInProgress = true;

                                const message = 'Sessão expirada. Recarregando página...';

                                if (window.showToast) {
                                    window.showToast(message, 'warning');
                                }

                                // Recarregar imediatamente
                                window.location.reload();
                            }
                            return; // Não relançar o erro
                        }

                        // Relançar outros erros
                        throw error;
                    });
            };
        }

        // Backup: interceptar eventos do Livewire
        if (typeof Livewire !== 'undefined') {
            Livewire.on('csrf-expired', (data) => {
                if (!csrfHandlerState.handlingInProgress) {
                    csrfHandlerState.handlingInProgress = true;
                    window.location.reload();
                }
            });
        }
    };

    // Função principal de inicialização
    const initCsrfHandler = () => {
        // Evitar inicialização múltipla
        if (csrfHandlerState.initialized) {
            return;
        }

        csrfHandlerState.initialized = true;

        // Configurar interceptores
        setupXMLHttpRequestInterceptor();
        setupFetchInterceptor();
        setupLivewireInterceptors();

        // console.log('CSRF Handler initialized successfully');
    };

    // Cleanup para navegação SPA
    const cleanupCsrfHandler = () => {
        // Resetar estado para nova página
        csrfHandlerState.handlingInProgress = false;
        // Manter initialized como true para evitar re-inicialização desnecessária
    };

    // Event listeners para Livewire e navegação SPA
    document.addEventListener('livewire:loaded', initCsrfHandler);
    document.addEventListener('livewire:navigated', initCsrfHandler);
    document.addEventListener('DOMContentLoaded', initCsrfHandler);

    // Cleanup para navegação SPA
    document.addEventListener('livewire:navigating', cleanupCsrfHandler);

    // Inicialização imediata (fallback)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCsrfHandler);
    } else {
        // DOM já carregado
        initCsrfHandler();
    }

    // Expor função de cleanup globalmente (opcional)
    window.csrfHandlerCleanup = cleanupCsrfHandler;

})();
