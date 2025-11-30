/**
 * Courses Management JavaScript - Livewire 3 Compatible
 * Gerenciamento de modais e funcionalidades da gestão de cursos
 */

// Verificação simples para evitar redeclaração
if (typeof CoursesManager !== 'undefined') {
    // console.log('CoursesManager já existe, pulando...');
} else {

class CoursesManager {
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
        this.modalElement = document.getElementById('courseModal');
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
        // Event listeners para botões de fechar (apenas para courseModal)
        document.addEventListener('click', (e) => {
            // Verificar se o clique é dentro do courseModal
            if (this.modalElement && this.modalElement.contains(e.target)) {
                // Botão X do modal
                if (e.target.matches('.btn-close') || e.target.closest('.btn-close')) {
                    // console.log('Course modal X button clicked');
                    e.preventDefault();
                    this.closeModal();
                }

                // Botão Cancelar
                if (e.target.matches('[data-bs-dismiss="modal"]') || e.target.closest('[data-bs-dismiss="modal"]')) {
                    // console.log('Course modal Cancel button clicked');
                    e.preventDefault();
                    this.closeModal();
                }
            }

            // Botão Salvar - marcar como salvando
            if (e.target.matches('[wire\\:click="salvarCourse"]') || e.target.closest('[wire\\:click="salvarCourse"]')) {
                // console.log('Iniciando salvamento...');
                this.isSaving = true;

                // Reset do estado após timeout (fallback)
                setTimeout(() => {
                    this.isSaving = false;
                    // console.log('Reset do estado de salvamento (timeout)');
                }, 10000); // 10 segundos
            }
        });

        // Configurar event listeners para modais com abas
        this.setupTabEventListeners();

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

            // Fechar com ESC (controle manual) - apenas para courseModal
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.modalElement && this.modalElement.classList.contains('show')) {
                    // console.log('ESC pressionado no courseModal');
                    this.closeModal();
                }
            });
        }
    }

    setupTabEventListeners() {
        // Configurar event listeners para manter o estado das abas nos modais
        // Registration Modal
        const registrationModal = document.getElementById('registrationModal');
        if (registrationModal) {
            this.setupModalTabState(registrationModal, 'registration');
        }

        // Certificate Modal
        const certificateModal = document.getElementById('certificateModal');
        if (certificateModal) {
            this.setupModalTabState(certificateModal, 'certificate');
        }

        // Class Modal
        const classModal = document.getElementById('classModal');
        if (classModal) {
            this.setupModalTabState(classModal, 'class');
        }

        // Course Modal (já existente)
        if (this.modalElement) {
            this.setupModalTabState(this.modalElement, 'course');
        }
    }

    setupModalTabState(modalElement, modalType) {
        // Salvar estado da aba quando ela muda
        modalElement.addEventListener('shown.bs.modal', () => {
            // console.log(`Modal ${modalType} aberto, configurando abas...`);
            this.activateFirstTabForModal(modalElement, modalType);
        });

        // Listener para mudanças de aba
        const tabButtons = modalElement.querySelectorAll('[data-bs-toggle="tab"]');
        tabButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                // console.log(`Aba clicada no modal ${modalType}:`, e.target.id);
                // O Bootstrap cuida da mudança de aba automaticamente
            });
        });
    }

    activateFirstTabForModal(modalElement, modalType) {
        // Ativar primeira aba específica para cada modal
        let firstTabId, firstPaneId;

        switch(modalType) {
            case 'registration':
                firstTabId = 'nav-basic-registration-tab';
                firstPaneId = 'nav-basic-registration';
                break;
            case 'certificate':
                firstTabId = 'nav-basic-certificate-tab';
                firstPaneId = 'nav-basic-certificate';
                break;
            case 'class':
                firstTabId = 'nav-basic-class-tab';
                firstPaneId = 'nav-basic-class';
                break;
            case 'course':
                firstTabId = 'nav-basic-tab';
                firstPaneId = 'nav-basic';
                break;
            default:
                return;
        }

        const firstTab = modalElement.querySelector(`#${firstTabId}`);
        const firstPane = modalElement.querySelector(`#${firstPaneId}`);

        if (firstTab && firstPane) {
            // Remover active de todas as abas do modal
            modalElement.querySelectorAll('.nav-link').forEach(tab => {
                tab.classList.remove('active');
            });
            modalElement.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });

            // Ativar primeira aba
            firstTab.classList.add('active');
            firstPane.classList.add('show', 'active');
            // console.log(`Primeira aba ativada para modal ${modalType}`);
        }
    }

    setupLivewireListeners() {
        if (typeof Livewire === 'undefined') {
            console.warn('Livewire não está disponível');
            return;
        }

        // Listener para abrir modal
        Livewire.on('open-course-modal', () => {
            if (window.coursesManager) {
                window.coursesManager.openModal();
            }
        });

        // Listener para fechar modal
        Livewire.on('close-course-modal', () => {
            // console.log('Evento close-course-modal recebido');
            if (window.coursesManager) {
                window.coursesManager.isSaving = false; // Reset do estado de salvamento
                window.coursesManager.closeModal();
            }
        });

        // Listeners para estados de loading do Livewire
        document.addEventListener('livewire:request', () => {
            // console.log('Livewire request iniciado');
            // Não marcar como saving aqui, apenas para salvarCourse
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
            this.modalElement = document.getElementById('courseModal');
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
            return false;
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
        this.setupEventListeners();

        // Verificar estado inicial dos campos
        // Focar no primeiro campo
        const firstInput = this.modalElement.querySelector('#nome-input');
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

    activateFirstTab() {
        // Ativar primeira aba do courseModal (padrão)
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
        // console.log('=== DEBUG CoursesManager ===');
        // console.log('Modal element:', this.modalElement);
        // console.log('Modal Bootstrap:', this.modal);
        // console.log('Is saving:', this.isSaving);
        // console.log('Is Livewire ready:', this.isLivewireReady);

        // Testar botões de fechar
        const closeButtons = document.querySelectorAll('.btn-close, [data-bs-dismiss="modal"]');
        // console.log('Botões de fechar encontrados:', closeButtons.length);
    }

    // Função global para abrir modal (fallback)
    static openModal() {
        // console.log('Função estática openModal chamada');
        if (window.coursesManager) {
            window.coursesManager.openModal();
        } else {
            console.error('CoursesManager não inicializado');
        }
    }

    // Função global para fechar modal (fallback)
    static closeModal() {
        // console.log('Função estática closeModal chamada');
        if (window.coursesManager) {
            window.coursesManager.closeModal();
        } else {
            console.error('CoursesManager não inicializado');
        }
    }

    // Função para editar curso - recebe ID e dispara evento para Livewire
    static editCourse(courseId) {
        // console.log('Editando curso com ID:', courseId);
        if (typeof Livewire !== 'undefined') {
            Livewire.dispatch('edit-course', courseId);
        } else {
            console.error('Livewire não está disponível');
        }
    }
}

// Inicializar quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    if (!window.coursesManager) {
        // console.log('Inicializando Courses Manager...');
        window.coursesManager = new CoursesManager();
    }
});

// Listener para navegação Livewire (reinicializar após navegação)
document.addEventListener('livewire:navigated', function() {
    // console.log('Livewire navegou, reinicializando Courses Manager...');
    setTimeout(() => {
        if (!window.coursesManager) {
            // console.log('Recriando Courses Manager após navegação...');
            window.coursesManager = new CoursesManager();
        } else {
            // Verificar se o modal ainda existe no DOM
            const modalElement = document.getElementById('courseModal');
            if (modalElement && (!window.coursesManager.modalElement || !document.body.contains(window.coursesManager.modalElement))) {
                // console.log('Reinicializando modal após navegação...');
                window.coursesManager.setupModal();
            }

            // Sempre reinicializar os event listeners após navegação
            // console.log('Reinicializando event listeners após navegação...');
            window.coursesManager.setupEventListeners();
        }
    }, 100);
});

// Funções globais para compatibilidade
window.openCourseModal = () => {
    // console.log('Função global openCourseModal chamada');
    CoursesManager.openModal();
};

window.closeCourseModal = () => {
    // console.log('Função global closeCourseModal chamada');
    CoursesManager.closeModal();
};

// Função global para editar curso
function editCourse(id) {
    if (typeof Livewire !== 'undefined') {
        Livewire.dispatch('edit-course', [id]);
    } else {
        console.error('Livewire não está disponível para editar curso');
    }
}

// Debug global
window.debugCoursesManager = () => {
    if (window.coursesManager) {
        window.coursesManager.debug();
    } else {
        // console.log('Courses Manager não inicializado');
    }
};

// Fechar verificação condicional para data-navigate-once
}
