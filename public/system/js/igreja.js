/**
 * Igreja Management JavaScript - Livewire 3 Compatible
 * Gerenciamento de modais e funcionalidades da gestão de igrejas
 */

// Verificação simples para evitar redeclaração
if (typeof IgrejaManager !== 'undefined') {
    // console.log('IgrejaManager já existe, pulando...');
} else {

class IgrejaManager {
    constructor() {
        this.modal = null;
        this.modalElement = null;
        this.isLivewireReady = false;
        this.isSaving = false; // Controle manual do estado de salvamento
        this.init();
    }

    init() {
        // Aguardar DOM estar pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.setupModal();
                this.setupEventListeners();
            });
        } else {
            this.setupModal();
            this.setupEventListeners();
        }

        // Aguardar Livewire estar pronto
        if (typeof Livewire !== 'undefined') {
            this.setupLivewireListeners();
            this.isLivewireReady = true;
        } else {
            document.addEventListener('livewire:init', () => {
                this.setupLivewireListeners();
                this.isLivewireReady = true;
            });
        }
    }

    setupModal() {
        this.modalElement = document.getElementById('churchModal');
        if (this.modalElement && typeof bootstrap !== 'undefined') {
            try {
                this.modal = new bootstrap.Modal(this.modalElement, {
                    backdrop: 'static',
                    keyboard: false, // Desabilitar ESC do Bootstrap para controlar manualmente
                    focus: true
                });
                // console.log('Modal Bootstrap inicializado com sucesso');
            } catch (error) {
                console.error('Erro ao inicializar modal Bootstrap:', error);
                this.modal = null;
            }
        } else {
            console.warn('Modal element ou Bootstrap não encontrado');
            this.modal = null;
        }
    }

    setupEventListeners() {
        // Controlar visibilidade do campo sede
        this.setupSedeFieldListeners();

        // Controlar visibilidade do campo sede sem re-render das abas (fallback)
        document.addEventListener('change', (e) => {
            if (e.target.matches('select[wire\\:model\\.live="tipo"]')) {
                this.toggleSedeField(e.target.value);
            }
        });

        // Event listeners para botões de fechar (apenas para churchModal)
        document.addEventListener('click', (e) => {
            // Verificar se o clique é dentro do churchModal
            if (this.modalElement && this.modalElement.contains(e.target)) {
                // Botão X do modal
                if (e.target.matches('.btn-close') || e.target.closest('.btn-close')) {
                    // console.log('Church modal X button clicked');
                    e.preventDefault();
                    this.closeModal();
                }

                // Botão Cancelar
                if (e.target.matches('[data-bs-dismiss="modal"]') || e.target.closest('[data-bs-dismiss="modal"]')) {
                    // console.log('Church modal Cancel button clicked');
                    e.preventDefault();
                    this.closeModal();
                }
            }

            // Botão Salvar - marcar como salvando
            if (e.target.matches('[wire\\:click="saveChurch"]') || e.target.closest('[wire\\:click="saveChurch"]')) {
                // console.log('Iniciando salvamento...');
                this.isSaving = true;

                // Reset do estado após timeout (fallback)
                setTimeout(() => {
                    this.isSaving = false;
                    // console.log('Reset do estado de salvamento (timeout)');
                }, 10000); // 10 segundos
            }
        });

        // Eventos do Bootstrap Modal
        if (this.modalElement) {
            this.modalElement.addEventListener('shown.bs.modal', () => {
                this.onModalShown();
            });

            this.modalElement.addEventListener('hidden.bs.modal', () => {
                this.onModalHidden();
            });

            this.modalElement.addEventListener('hide.bs.modal', (event) => {
                return this.onModalHide(event);
            });

            // Fechar com ESC (controle manual) - apenas para churchModal
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.modalElement && this.modalElement.classList.contains('show')) {
                    // console.log('ESC pressionado no churchModal');
                    this.closeModal();
                }
            });
        }
    }

    setupLivewireListeners() {
        if (typeof Livewire === 'undefined') {
            console.warn('Livewire não está disponível');
            return;
        }

        // Listener para abrir modal
        Livewire.on('open-church-modal', () => {
            if (window.igrejaManager) {
                window.igrejaManager.openModal();
            }
        });

        // Listener para fechar modal
        Livewire.on('close-church-modal', () => {
            // console.log('Evento close-church-modal recebido');
            if (window.igrejaManager) {
                window.igrejaManager.isSaving = false; // Reset do estado de salvamento
                window.igrejaManager.closeModal();
            }
        });

        // Listener para mudança do tipo de igreja
        Livewire.on('tipo-changed', (data) => {
            const tipo = Array.isArray(data) ? data[0] : data;
            if (window.igrejaManager) {
                window.igrejaManager.toggleSedeField(tipo);
            }
        });

        // Listeners para estados de loading do Livewire
        document.addEventListener('livewire:request', () => {
            // console.log('Livewire request iniciado');
            // Não marcar como saving aqui, apenas para saveChurch
        });

        document.addEventListener('livewire:response', () => {
            // console.log('Livewire response recebido');
            this.isSaving = false; // Reset do estado de salvamento
        });

        // console.log('Livewire listeners configurados');
    }

    openModal() {
        // console.log('Tentando abrir modal...');

        // Verificar se o modal element ainda existe no DOM
        if (!this.modalElement || !document.body.contains(this.modalElement)) {
            // console.log('Modal element não encontrado no DOM, tentando localizar...');
            this.modalElement = document.getElementById('churchModal');
        }

        // Verificar se o modal Bootstrap ainda é válido
        if (!this.modal || !this.modalElement) {
            // console.log('Modal não inicializado ou element não encontrado, reinicializando...');
            this.setupModal();
        }

        if (this.modal && this.modalElement) {
            try {
                // Reset do estado de salvamento
                this.isSaving = false;

                // Limpar qualquer estado anterior
                this.modalElement.removeAttribute('aria-hidden');
                this.modalElement.setAttribute('aria-modal', 'true');

                // Mostrar modal
                this.modal.show();
                // console.log('Modal aberto com sucesso');

                // Notificar Livewire após abertura
                setTimeout(() => {
                    if (this.isLivewireReady && typeof Livewire !== 'undefined') {
                        Livewire.dispatch('modal-opened');
                        // console.log('Evento modal-opened enviado');
                    }
                }, 150);
            } catch (error) {
                console.error('Erro ao abrir modal:', error);
                // Tentar reinicializar e abrir novamente
                this.setupModal();
                if (this.modal) {
                    setTimeout(() => this.modal.show(), 100);
                }
            }
        } else {
            console.error('Modal ou elemento não encontrado após tentativas de reinicialização');
        }
    }

    closeModal() {
        // console.log('Fechando modal...');

        // Verificar se está salvando
        if (this.isSaving) {
            // console.log('Não é possível fechar durante salvamento');
            return;
        }

        if (this.modal && this.modalElement) {
            // Notificar Livewire antes de fechar
            if (this.isLivewireReady && typeof Livewire !== 'undefined') {
                Livewire.dispatch('modal-closed');
                // console.log('Evento modal-closed enviado para Livewire');
            }

            // Fechar modal via Bootstrap
            this.modal.hide();
            // console.log('Modal fechado via Bootstrap');
        } else {
            console.error('Modal não encontrado para fechar');
        }
    }

    onModalShown() {
        // console.log('Modal totalmente aberto');
        // Modal totalmente aberto
        this.modalElement.removeAttribute('aria-hidden');
        this.modalElement.setAttribute('aria-modal', 'true');

        // Reset do estado de salvamento
        this.isSaving = false;

        // Garantir que os event listeners estejam configurados (especialmente após navegação)
        this.setupSedeFieldListeners();

        // Verificar estado inicial do campo sede
        const tipoSelect = document.getElementById('tipo-select');
        if (tipoSelect) {
            this.toggleSedeField(tipoSelect.value);
        }

        // Focar no primeiro campo
        const firstInput = this.modalElement.querySelector('input[wire\\:model="nome"]');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }

        // Garantir que a primeira aba esteja ativa
        this.activateFirstTab();
    }

    onModalHidden() {
        // console.log('Modal totalmente fechado');
        // Modal totalmente fechado
        this.modalElement.setAttribute('aria-hidden', 'true');
        this.modalElement.removeAttribute('aria-modal');

        // Reset do estado de salvamento
        this.isSaving = false;

        // Limpar foco
        if (document.activeElement) {
            document.activeElement.blur();
        }

        // Reset das abas para a primeira
        this.activateFirstTab();

        // console.log('Modal cleanup completo');
    }

    onModalHide(event) {
        // console.log('Modal iniciando fechamento...');
        // console.log('Estado de salvamento:', this.isSaving);

        // Prevenir fechamento apenas se estiver realmente salvando
        if (this.isSaving) {
            // console.log('Prevenindo fechamento durante salvamento');
            event.preventDefault();
            return false;
        }

        // console.log('Permitindo fechamento do modal');
        return true;
    }

    setupSedeFieldListeners() {
        // Remover listeners antigos para evitar duplicação
        const tipoSelect = document.getElementById('tipo-select');
        if (tipoSelect) {
            // Clonar e substituir o elemento para remover todos os listeners antigos
            const newTipoSelect = tipoSelect.cloneNode(true);
            tipoSelect.parentNode.replaceChild(newTipoSelect, tipoSelect);

            // Adicionar novo listener
            newTipoSelect.addEventListener('change', (e) => {
                this.toggleSedeField(e.target.value);
            });

            // console.log('Event listener do tipo select configurado');
        }
    }

    toggleSedeField(tipo) {
        // console.log('Alterando visibilidade do campo sede para tipo:', tipo);
        const sedeField = document.getElementById('sede-field');
        if (sedeField) {
            sedeField.style.display = tipo === 'filial' ? 'block' : 'none';
        }
    }

    activateFirstTab() {
        // Ativar primeira aba
        const firstTab = document.getElementById('nav-basic-tab');
        const firstPane = document.getElementById('nav-basic');

        if (firstTab && firstPane) {
            // Remover active de todas as abas
            document.querySelectorAll('.nav-link').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });

            // Ativar primeira aba
            firstTab.classList.add('active');
            firstPane.classList.add('show', 'active');
            // console.log('Primeira aba ativada');
        }
    }

    // Função para debug
    debug() {



        // Testar botões de fechar
        const closeButtons = document.querySelectorAll('.btn-close, [data-bs-dismiss="modal"]');
        // console.log('Botões de fechar encontrados:', closeButtons.length);
    }

    // Função global para abrir modal (fallback)
    static openModal() {
        // console.log('Função estática openModal chamada');
        if (window.igrejaManager) {
            window.igrejaManager.openModal();
        } else {
         //   console.error('IgrejaManager não inicializado');
        }
    }

    // Função global para fechar modal (fallback)
    static closeModal() {
        // console.log('Função estática closeModal chamada');
        if (window.igrejaManager) {
            window.igrejaManager.closeModal();
        } else {
            console.error('IgrejaManager não inicializado');
        }
    }

    // Função para editar igreja - recebe ID e dispara evento para Livewire
    static editIgreja(igrejaId) {
       //  console.log('Editando igreja com ID:', igrejaId);
        if (typeof Livewire !== 'undefined') {
            Livewire.dispatch('edit-igreja', igrejaId);
        } else {
         //   console.error('Livewire não está disponível');
        }
    }
}

// Inicializar quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    if (!window.igrejaManager) {
        // console.log('Inicializando Igreja Manager...');
        window.igrejaManager = new IgrejaManager();
    }
});

// Listener para navegação Livewire (reinicializar após navegação)
document.addEventListener('livewire:navigated', function() {
    // console.log('Livewire navegou, reinicializando Igreja Manager...');
    setTimeout(() => {
        if (!window.igrejaManager) {
            // console.log('Recriando Igreja Manager após navegação...');
            window.igrejaManager = new IgrejaManager();
        } else {
            // Verificar se o modal ainda existe no DOM
            const modalElement = document.getElementById('churchModal');
            if (modalElement && (!window.igrejaManager.modalElement || !document.body.contains(window.igrejaManager.modalElement))) {
                // console.log('Reinicializando modal após navegação...');
                window.igrejaManager.setupModal();
            }

            // Sempre reinicializar os event listeners do campo sede após navegação
            // console.log('Reinicializando event listeners do campo sede...');
            window.igrejaManager.setupSedeFieldListeners();
        }
    }, 100);
});

// Funções globais para compatibilidade
window.openChurchModal = () => {
    // console.log('Função global openChurchModal chamada');
    IgrejaManager.openModal();
};

window.closeChurchModal = () => {
    // console.log('Função global closeChurchModal chamada');
    IgrejaManager.closeModal();
};

// Função global para editar igreja
function editIgreja(id) {
    if (typeof Livewire !== 'undefined') {
        Livewire.dispatch('edit-igreja', [id]);
    } else {
        console.error('Livewire não está disponível para editar igreja');
    }
}

// Debug global
window.debugIgrejaManager = () => {
    if (window.igrejaManager) {
        window.igrejaManager.debug();
    } else {
        // console.log('Igreja Manager não inicializado');
    }
};

// Fechar verificação condicional para data-navigate-once
}

// ===== SOLUÇÃO SIMPLES PARA CAMPO SEDE =====
// Função independente que funciona sempre
function initSedeFieldController() {
    // console.log('Inicializando controlador do campo sede...');

    function setupSedeField() {
        const tipoSelect = document.getElementById('tipo-select');
        const sedeField = document.getElementById('sede-field');

        if (!tipoSelect || !sedeField) {
            // console.log('Elementos do campo sede não encontrados, tentando novamente...');
            setTimeout(setupSedeField, 100);
            return;
        }

        function toggleSedeField() {
            const tipo = tipoSelect.value;
            sedeField.style.display = tipo === 'filial' ? 'block' : 'none';
            // console.log('Campo sede:', tipo === 'filial' ? 'visível' : 'oculto');
        }

        // Verificar valor inicial
        toggleSedeField();

        // Adicionar listener
        tipoSelect.addEventListener('change', toggleSedeField);
        // console.log('Listener do campo sede configurado');
    }

    // Executar quando DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupSedeField);
    } else {
        setupSedeField();
    }

    // Re-executar após navegação Livewire
    document.addEventListener('livewire:navigated', () => {
        // console.log('Livewire navegou, reinicializando campo sede...');
        setTimeout(setupSedeField, 200);
    });

    // Re-executar quando modal abrir
    document.addEventListener('shown.bs.modal', (e) => {
        if (e.target.id === 'churchModal') {
            // console.log('Modal aberto, verificando campo sede...');
            setTimeout(setupSedeField, 100);
        }
    });
}

// Inicializar controlador do campo sede
initSedeFieldController();
