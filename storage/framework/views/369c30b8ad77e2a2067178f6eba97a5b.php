<div>
    <div class="wrapper">
        <section class="login-content">
            <div class="row m-0 align-items-center bg-white vh-100">
                <div class="col-md-6">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
                                <div class="card-body z-3 px-md-0 px-lg-4">
                                    <a href="<?php echo e(url('/')); ?>" class="navbar-brand d-flex align-items-center mb-3">
                                        <!--Logo start-->
                                        <div class="logo-main">
                                            <div class="logo-mini">
                                                <img src="<?php echo e(asset('system/img/logo-system/icon.png')); ?>" alt="logo">
                                            </div>
                                            <div class="logo-mini">
                                                <img src="<?php echo e(asset('system/img/logo-system/icon.png')); ?>" alt="logo">
                                            </div>
                                        </div>
                                        <!--logo End-->
                                        <h1 class="logo-title fw-bold">
                                            <span class="text-primary">Omn</span><span class="text-success">Igrejas</span>
                                        </h1>
                                    </a>
                                    <h2 class="mb-2 text-center">Selecionar Igreja</h2>
                                    <p class="text-center">Escolha qual igreja você deseja acessar</p>

                                    <!--[if BLOCK]><![endif]--><?php if(session()->has('warning')): ?>
                                        <div class="alert alert-warning">
                                            <?php echo e(session('warning')); ?>

                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <form wire:submit.prevent='selectChurch' id="select-church-form">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="selectedIgrejaId" class="form-label">Selecione sua Igreja</label>

                                                    <!--[if BLOCK]><![endif]--><?php if($igrejas): ?>
                                                        <div class="church-selection-container" style="max-height: 400px; overflow-y: auto; border: 1px solid #e3e3e0; border-radius: 8px; padding: 15px;">
                                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $igrejas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $igreja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <div class="church-card mb-3 p-3 border rounded-lg cursor-pointer transition-all hover:shadow-md"
                                                                     style="border: 2px solid <?php echo e($selectedIgrejaId == $igreja['id'] ? '#007bff' : '#e3e3e0'); ?> !important; background: <?php echo e($selectedIgrejaId == $igreja['id'] ? '#f8f9ff' : 'white'); ?>;"
                                                                     wire:click="$set('selectedIgrejaId', <?php echo e($igreja['id']); ?>)">

                                                                    <div class="d-flex align-items-center">
                                                                        <!-- Logo ou Avatar -->
                                                                        <div class="me-3">
                                                                            <!--[if BLOCK]><![endif]--><?php if($igreja['has_logo']): ?>
                                                                                <img src="<?php echo e($igreja['logo']); ?>"
                                                                                     alt="Logo <?php echo e($igreja['nome']); ?>"
                                                                                     class="rounded-circle"
                                                                                     style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #e3e3e0;">
                                                                            <?php else: ?>
                                                                                <div class="avatar avatar-md">
                                                                                    <div class="avatar-initial rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                                                         style="width: 50px; height: 50px; font-size: 18px; font-weight: bold;">
                                                                                        <?php echo e(substr($igreja['nome'], 0, 2)); ?>

                                                                                    </div>
                                                                                </div>
                                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                        </div>

                                                                        <!-- Informações da Igreja -->
                                                                        <div class="flex-grow-1">
                                                                            <div class="d-flex align-items-center mb-1">
                                                                                <h6 class="mb-0 me-2"><?php echo e($igreja['nome']); ?></h6>
                                                                                <!--[if BLOCK]><![endif]--><?php if($igreja['sigla']): ?>
                                                                                    <small class="badge bg-secondary"><?php echo e($igreja['sigla']); ?></small>
                                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                            </div>

                                                                            <div class="d-flex flex-wrap gap-1 mb-1">
                                                                                <span class="badge bg-light text-dark"><?php echo e($igreja['categoria']); ?></span>
                                                                                <span class="badge bg-info"><?php echo e(ucfirst($igreja['cargo'])); ?></span>
                                                                            </div>

                                                                            <div class="text-muted small">
                                                                                <div>Membro desde: <?php echo e($igreja['membro_desde']); ?></div>
                                                                                <!--[if BLOCK]><![endif]--><?php if($igreja['localizacao']): ?>
                                                                                    <div>📍 <?php echo e($igreja['localizacao']); ?></div>
                                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                            </div>
                                                                        </div>

                                                                        <!-- Radio Button -->
                                                                        <div class="ms-3">
                                                                            <div class="form-check">
                                                                                <input
                                                                                    class="form-check-input"
                                                                                    type="radio"
                                                                                    wire:model.live="selectedIgrejaId"
                                                                                    id="igreja-<?php echo e($igreja['id']); ?>"
                                                                                    value="<?php echo e($igreja['id']); ?>"
                                                                                    style="transform: scale(1.2);"
                                                                                >
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>

                                                        <div class="text-center mt-3">
                                                            <small class="text-muted">
                                                                <?php echo e(count($igrejas)); ?> igreja<?php echo e(count($igrejas) > 1 ? 's' : ''); ?> encontrada<?php echo e(count($igrejas) > 1 ? 's' : ''); ?>

                                                            </small>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="text-center py-5">
                                                            <div class="mb-3">
                                                                <i class="fas fa-church text-muted" style="font-size: 4rem;"></i>
                                                            </div>
                                                            <h5 class="text-muted mb-2">Nenhuma igreja encontrada</h5>
                                                            <p class="text-muted mb-0">Você não está associado a nenhuma igreja ativa no momento.</p>
                                                        </div>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedIgrejaId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <div class="alert alert-danger mt-3">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                            <?php echo e($message); ?>

                                                        </div>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-center">
                                            <button
                                                id="select-church-button"
                                                class="btn btn-primary bg-primary border-0 d-flex align-items-center"
                                                type="submit"
                                                wire:loading.attr="disabled"
                                                wire:loading.class="disabled"
                                            >
                                                <span wire:loading wire:target="selectChurch" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                                Continuar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sign-bg">
                        <img src="<?php echo e(asset('system/img/logo-system/icon.png')); ?>"
                             alt="logo"
                             class="img-fluid opacity-75"
                             width="400" height="330"
                             style="max-width: 200px; max-height: 200px;" >
                    </div>
                </div>
                <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
                    <img src="<?php echo e(asset('assets/images/auth/01.png')); ?>" class="img-fluid gradient-main animated-scaleX" alt="images">
                </div>
            </div>
        </section>
    </div>

    
    <div class="modal fade" id="accessCodeModal" tabindex="-1" wire:ignore.self data-bs-backdrop="false" data-bs-keyboard="false" data-bs-focus="false">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title mb-0">
                        <i class="fas fa-key me-2"></i>Código de Acesso
                    </h6>

                        <button type="button"  class="btn-close btn-close-dark" data-bs-dismiss="modal"></button>

                </div>
                <form wire:submit.prevent="validateAccessCode" id="access-code-form">
                    <div class="modal-body py-3">
                        <div id="access-code-content">
                            <div class="text-center mb-2">
                                <i class="fas fa-shield-alt text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <p class="text-muted text-center small mb-3">
                                Digite o código de acesso da igreja
                            </p>
                            <div class="mb-2">
                                <input
                                    type="password"
                                    class="form-control form-control-lg border-rounded border-primary text-center <?php $__errorArgs = ['accessCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    wire:model="accessCode"
                                    placeholder="Código de acesso"
                                    maxlength="20"
                                    autocomplete="new_password"
                                    id="access-code-input"
                                >
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['accessCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback text-center"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <div class="text-center mb-3">
                                <a href="#" class="text-decoration-none small text-primary" id="forgot-code-link">
                                    <i class="fas fa-key me-1"></i>Esqueci código de acesso
                                </a>
                            </div>
                        </div>

                        <div id="connecting-spinner" class="d-none">
                            <div class="text-center py-4 d-flex flex-column align-items-center justify-content-center" style="min-height: 180px;">
                                <i class="fas fa-spinner fa-spin text-primary mb-3" style="font-size: 3rem;"></i>
                                <p class="text-muted">Conectando...</p>
                            </div>
                        </div>

                        <div id="forgot-code-section" class="d-none">
                            <div class="text-center">
                                <i class="fas fa-envelope text-info mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted small mb-3">
                                    Enviaremos o código de acesso<br>
                                    para seu email cadastrado
                                </p>
                            </div>
                        </div>

                        <div id="email-sent-success" class="d-none">
                            <div class="text-center py-3 d-flex flex-column align-items-center justify-content-center" style="min-height: 150px;">
                                <i class="fas fa-check-circle text-success mb-2" style="font-size: 3rem;"></i>
                                <h5 class="text-success mb-2">Email enviado com sucesso!</h5>
                                <p class="text-muted small">Verifique sua caixa de entrada</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center py-2">
                        <button type="button" class="btn btn-sm btn-secondary bg-secondary d-flex align-items-center justify-content-center" style="min-width: 100px; min-height: 36px;" data-bs-dismiss="modal" id="cancel-button">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary bg-primary d-flex align-items-center justify-content-center" style="min-width: 100px; min-height: 36px;" id="validate-button">
                            <span id="validate-text">
                                <i class="fas fa-check me-1"></i>Validar
                            </span>
                            <span id="validating-text" class="d-none">
                                <i class="fas fa-spinner fa-spin me-1"></i>Validando...
                            </span>
                        </button>
                        <button type="button" class="btn btn-sm btn-info bg-info d-flex align-items-center justify-content-center d-none" style="min-width: 100px; min-height: 36px;" id="send-email-button" wire:click="sendAccessCodeByEmail" wire:loading.attr="disabled" wire:target="sendAccessCodeByEmail">
                            <span wire:loading.remove wire:target="sendAccessCodeByEmail">
                                <i class="fas fa-paper-plane me-1"></i>Enviar
                            </span>
                            <span wire:loading wire:target="sendAccessCodeByEmail">
                                <i class="fas fa-spinner fa-spin me-1"></i>Enviando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        // Aguardar Livewire estar disponível
        function waitForLivewire(callback) {
            if (typeof Livewire !== 'undefined') {
                callback();
            } else {
                setTimeout(() => waitForLivewire(callback), 100);
            }
        }

        document.addEventListener('livewire:navigated', function () {
            // Re-inicializar listeners após navegação SPA
            waitForLivewire(() => initAccessCodeModalListeners());
        });

        document.addEventListener('livewire:updated', function () {
            // Re-inicializar listeners após atualização do componente
            waitForLivewire(() => initAccessCodeModalListeners());
        });

        function initAccessCodeModalListeners() {
            Livewire.on('open-access-code-modal', () => {
                const modal = new bootstrap.Modal(document.getElementById('accessCodeModal'), {
                    backdrop: false,
                    keyboard: false,
                    focus: false
                });
                modal.show();
            });

            // Listener para quando o modal é fechado (qualquer forma)
            document.getElementById('accessCodeModal').addEventListener('hidden.bs.modal', function () {
                // Resetar estado quando modal fecha
                setTimeout(() => {
                    hideConnectingState();
                    // Forçar remoção de classes do modal que podem causar problemas de foco
                    const modal = document.getElementById('accessCodeModal');
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    // Remover todos os backdrops possíveis
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                }, 300);
            });

            // Listener para link "Esqueci código de acesso"
            document.addEventListener('click', function(e) {
                if (e.target && e.target.id === 'forgot-code-link') {
                    e.preventDefault();
                    showForgotCodeState();
                }
            });

            // Listener para iniciar conexão
            Livewire.on('start-connection', () => {
                showConnectingState();
            });

            // Listener para parar conexão (em caso de erro)
            Livewire.on('stop-connection', () => {
                hideConnectingState();
            });

            // Listener para email enviado com sucesso
            Livewire.on('email-sent-success', (message) => {
                console.log('Email enviado com sucesso, mostrando tela de sucesso');
                // Pequeno delay para garantir que o DOM foi atualizado
                setTimeout(() => {
                    showEmailSentSuccess();
                }, 100);
            });

            // Listener para erro no envio de email
            Livewire.on('email-sent-error', (message) => {
                showErrorToast(message);
                hideSendingState();
            });

            // Listener para mostrar estado de envio
            Livewire.on('show-sending-state', () => {
                showSendingState();
            });

            // Listener para esconder estado de envio
            Livewire.on('hide-sending-state', () => {
                hideSendingState();
            });
        }

        function showConnectingState() {
            // Esconder conteúdo normal
            document.getElementById('access-code-content').classList.add('d-none');

            // Mostrar spinner
            document.getElementById('connecting-spinner').classList.remove('d-none');

            // Desabilitar input
            document.getElementById('access-code-input').disabled = true;

            // Esconder botão cancelar
            document.getElementById('cancel-button').classList.add('d-none');

            // Mostrar spinner no botão
            document.getElementById('validate-text').classList.add('d-none');
            document.getElementById('validating-text').classList.remove('d-none');

            // Desabilitar botão
            document.getElementById('validate-button').disabled = true;

            // Impedir fechamento do modal
            const modal = document.getElementById('accessCodeModal');
            modal.setAttribute('data-bs-backdrop', 'static');
            modal.setAttribute('data-bs-keyboard', 'false');
        }

        function hideConnectingState() {
            // Mostrar conteúdo normal
            document.getElementById('access-code-content').classList.remove('d-none');

            // Esconder spinner
            document.getElementById('connecting-spinner').classList.add('d-none');

            // Esconder seção de forgot code
            document.getElementById('forgot-code-section').classList.add('d-none');

            // Esconder seção de sucesso
            document.getElementById('email-sent-success').classList.add('d-none');

            // Habilitar input
            document.getElementById('access-code-input').disabled = false;

            // Mostrar botão cancelar
            document.getElementById('cancel-button').classList.remove('d-none');

            // Esconder botão enviar
            document.getElementById('send-email-button').classList.add('d-none');

            // Mostrar botão validar
            document.getElementById('validate-button').classList.remove('d-none');

            // Resetar footer do modal
            const modalFooter = document.querySelector('#accessCodeModal .modal-footer');
            if (modalFooter) {
                modalFooter.innerHTML = `
                    <button type="button" class="btn btn-sm btn-secondary bg-secondary" data-bs-dismiss="modal" id="cancel-button">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary bg-primary d-flex align-items-center justify-content-center" style="min-width: 100px; min-height: 36px;" id="validate-button">
                        <span id="validate-text">
                            <i class="fas fa-check me-1"></i>Validar
                        </span>
                        <span id="validating-text" class="d-none">
                            <i class="fas fa-spinner fa-spin me-1"></i>Validando...
                        </span>
                    </button>
                    <button type="button" class="btn btn-sm btn-info bg-info d-flex align-items-center justify-content-center d-none" style="min-width: 100px; min-height: 36px;" id="send-email-button" wire:click="sendAccessCodeByEmail" wire:loading.attr="disabled" wire:target="sendAccessCodeByEmail">
                        <span wire:loading.remove wire:target="sendAccessCodeByEmail">
                            <i class="fas fa-paper-plane me-1"></i>Enviar
                        </span>
                        <span wire:loading wire:target="sendAccessCodeByEmail">
                            <i class="fas fa-spinner fa-spin me-1"></i>Enviando...
                        </span>
                    </button>
                `;
            }

            // Esconder spinner no botão
            document.getElementById('validate-text').classList.remove('d-none');
            document.getElementById('validating-text').classList.add('d-none');

            // Habilitar botão
            document.getElementById('validate-button').disabled = false;

            // Permitir fechamento do modal novamente
            const modal = document.getElementById('accessCodeModal');
            modal.setAttribute('data-bs-backdrop', 'true');
            modal.setAttribute('data-bs-keyboard', 'true');
        }

        function showForgotCodeState() {
            // Esconder conteúdo normal
            document.getElementById('access-code-content').classList.add('d-none');

            // Esconder spinner de conexão
            document.getElementById('connecting-spinner').classList.add('d-none');

            // Mostrar seção de forgot code
            document.getElementById('forgot-code-section').classList.remove('d-none');

            // Esconder botão cancelar
            document.getElementById('cancel-button').classList.add('d-none');

            // Esconder botão validar
            document.getElementById('validate-button').classList.add('d-none');

            // Mostrar botão enviar
            document.getElementById('send-email-button').classList.remove('d-none');
        }

        function hideForgotCodeState() {
            // Mostrar conteúdo normal
            document.getElementById('access-code-content').classList.remove('d-none');

            // Esconder seção de forgot code
            document.getElementById('forgot-code-section').classList.add('d-none');

            // Mostrar botão cancelar
            document.getElementById('cancel-button').classList.remove('d-none');

            // Mostrar botão validar
            document.getElementById('validate-button').classList.remove('d-none');

            // Esconder botão enviar
            document.getElementById('send-email-button').classList.add('d-none');
        }

        function showSendingState() {
            const sendText = document.getElementById('send-text');
            const sendingText = document.getElementById('sending-text');
            const sendButton = document.getElementById('send-email-button');

            if (sendText) sendText.classList.add('d-none');
            if (sendingText) sendingText.classList.remove('d-none');
            if (sendButton) sendButton.disabled = true;
        }

        function hideSendingState() {
            // Esconder spinner no botão enviar
            document.getElementById('send-text').classList.remove('d-none');
            document.getElementById('sending-text').classList.add('d-none');

            // Habilitar botão
            document.getElementById('send-email-button').disabled = false;
        }

        function showEmailSentSuccess() {
            console.log('Executando showEmailSentSuccess');

            // Esconder todas as seções
            const accessCodeContent = document.getElementById('access-code-content');
            const connectingSpinner = document.getElementById('connecting-spinner');
            const forgotCodeSection = document.getElementById('forgot-code-section');
            const emailSentSuccess = document.getElementById('email-sent-success');

            if (accessCodeContent) accessCodeContent.classList.add('d-none');
            if (connectingSpinner) connectingSpinner.classList.add('d-none');
            if (forgotCodeSection) forgotCodeSection.classList.add('d-none');

            // Mostrar seção de sucesso
            if (emailSentSuccess) {
                emailSentSuccess.classList.remove('d-none');
                console.log('Seção de sucesso mostrada');
                console.log('Conteúdo da seção:', emailSentSuccess.innerHTML);
            } else {
                console.log('Elemento email-sent-success não encontrado!');
            }

            // Esconder todos os botões
            const cancelButton = document.getElementById('cancel-button');
            const validateButton = document.getElementById('validate-button');
            const sendEmailButton = document.getElementById('send-email-button');

            if (cancelButton) cancelButton.classList.add('d-none');
            if (validateButton) validateButton.classList.add('d-none');
            if (sendEmailButton) sendEmailButton.classList.add('d-none');

            // Adicionar botão OK para fechar manualmente
            const modalFooter = document.querySelector('#accessCodeModal .modal-footer');
            if (modalFooter) {
                modalFooter.innerHTML = `
                    <button type="button" class="btn btn-success" onclick="closeSuccessModal()">
                        <i class="fas fa-check me-1"></i>OK
                    </button>
                `;

            } else {

            }
        }

        function closeSuccessModal() {
            const modal = document.getElementById('accessCodeModal');
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                }
            }
            // O reset será feito pelo listener 'hidden.bs.modal'
        }

        function showErrorToast(message) {
            // Usar toaster se disponível, senão alert simples
            if (typeof window.toastr !== 'undefined') {
                toastr.error(message);
            } else {
                alert(message);
            }
        }

        // Inicializar listeners na primeira carga
        waitForLivewire(() => initAccessCodeModalListeners());
    </script>
</div>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/components/layouts/auth/select-church.blade.php ENDPATH**/ ?>