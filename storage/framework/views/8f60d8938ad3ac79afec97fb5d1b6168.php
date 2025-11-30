<div class="modal fade" id="meetingModal" tabindex="-1" aria-labelledby="meetingModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-primary text-white border-bottom">
                <h5 class="modal-title fw-bold" id="meetingModalLabel">
                    <i class="fas fa-calendar-plus me-2"></i>
                    <span><?php echo e($isEditing ? 'Editar Reunião' : 'Agendar Nova Reunião'); ?></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-4">
                <form wire:submit.prevent="salvarReuniao">

                    <!-- Navegação por Abas -->
                    <nav class="mb-4" wire:ignore>
                        <div class="nav nav-tabs border-bottom-0" id="meeting-nav-tab" role="tablist">
                            <button class="nav-link active border-0 bg-transparent fw-semibold"
                                    id="meeting-nav-basic-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#meeting-nav-basic"
                                    type="button" role="tab">
                                <i class="fas fa-info-circle text-primary me-1"></i>Informações Básicas
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold"
                                    id="meeting-nav-details-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#meeting-nav-details"
                                    type="button" role="tab">
                                <i class="fas fa-cogs text-primary me-1"></i>Detalhes
                            </button>
                        </div>
                    </nav>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content" id="meeting-nav-tabContent">

                        <!-- Aba: Informações Básicas -->
                        <div class="tab-pane fade show active" id="meeting-nav-basic" role="tabpanel"  wire:ignore.self>
                            <div class="row g-3">
                                <!-- Título da Reunião -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password"  autocomplete="new-password"  class="form-control <?php $__errorArgs = ['titulo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model="titulo" placeholder="Digite o título da reunião" required>
                                        <label><i class="fas fa-heading text-primary me-1"></i>Título da Reunião *</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['titulo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- Data e Hora -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password"  autocomplete="new-password"
                                               class="form-control date_flatpicker <?php $__errorArgs = ['data_agendamento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model.defer="data_agendamento"
                                               placeholder="dd/mm/aaaa"
                                               data-min-date="<?php echo e(date('Y-m-d')); ?>"
                                               data-max-date=""
                                               autocomplete="off"
                                               readonly
                                               style="border: 2px solid #007bff; border-radius: 0.375rem; cursor: pointer;">
                                        <label><i class="fas fa-calendar text-primary me-1"></i>Data *</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['data_agendamento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-floating mb-3">
                                        <input type="time" class="form-control <?php $__errorArgs = ['hora_inicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model="hora_inicio" required>
                                        <label><i class="fas fa-clock text-primary me-1"></i>Início *</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['hora_inicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-floating mb-3">
                                        <input type="time" class="form-control <?php $__errorArgs = ['hora_fim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model="hora_fim">
                                        <label><i class="fas fa-clock text-primary me-1"></i>Fim</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['hora_fim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- Tipo de Reunião -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                wire:model="tipo" required>
                                            <option value="reuniao">Reunião Geral</option>
                                            <option value="consulta">Consulta</option>
                                            <option value="acompanhamento">Acompanhamento</option>
                                            <option value="outro">Outro</option>
                                        </select>
                                        <label><i class="fas fa-tag text-primary me-1"></i>Tipo *</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- Modalidade -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select <?php $__errorArgs = ['modalidade'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                wire:model="modalidade" required>
                                            <option value="presencial">Presencial</option>
                                            <option value="online">Online</option>
                                            <option value="hibrido">Híbrido</option>
                                        </select>
                                        <label><i class="fas fa-globe text-primary me-1"></i>Modalidade *</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['modalidade'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- Local (condicional) -->
                                <div class="col-12" wire:ignore>
                                    <div class="form-floating mb-3" id="localField" style="display: <?php echo e(in_array($modalidade, ['presencial', 'hibrido']) ? 'block' : 'none'); ?>">
                                        <input type="text"  autocomplete="new-password"  autocomplete="new-password"  class="form-control <?php $__errorArgs = ['local'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model="local" placeholder="Digite o local da reunião">
                                        <label><i class="fas fa-map-marker-alt text-primary me-1"></i>Local</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['local'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- Link da Reunião (condicional) -->
                                <div class="col-12" wire:ignore>
                                    <div class="form-floating mb-3" id="linkField" style="display: <?php echo e(in_array($modalidade, ['online', 'hibrido']) ? 'block' : 'none'); ?>">
                                        <input type="url" class="form-control <?php $__errorArgs = ['link_reuniao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model="link_reuniao" placeholder="https://meet.google.com/...">
                                        <label><i class="fas fa-link text-primary me-1"></i>Link da Reunião</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['link_reuniao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Detalhes -->
                        <div class="tab-pane fade" id="meeting-nav-details" role="tabpanel"  wire:ignore.self>
                            <div class="row g-3">
                                <!-- Descrição -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control <?php $__errorArgs = ['descricao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                  wire:model="descricao" rows="4"
                                                  placeholder="Descreva os objetivos e tópicos da reunião"></textarea>
                                        <label><i class="fas fa-align-left text-primary me-1"></i>Descrição</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['descricao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- Aliança (obrigatório) -->
                                <div class="col-12" wire:ignore.self>
                                    <div class="form-floating mb-3" >
                                        <select class="form-select <?php $__errorArgs = ['aliancaSelecionada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                wire:model.live="aliancaSelecionada" required>
                                            <option value="">Selecione uma aliança *</option>
                                            <!--[if BLOCK]><![endif]--><?php if(isset($aliancasDisponiveis) && !empty($aliancasDisponiveis)): ?>
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $aliancasDisponiveis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alianca): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($alianca['id']); ?>">
                                                        <?php echo e($alianca['nome']); ?>

                                                        <!--[if BLOCK]><![endif]--><?php if($alianca['sigla']): ?> (<?php echo e($alianca['sigla']); ?>) <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        <!--[if BLOCK]><![endif]--><?php if(isset($alianca['tipo']) && $alianca['tipo'] === 'criada'): ?>
                                                            <span class="text-primary">★</span>
                                                        <?php else: ?>
                                                            <span class="text-muted">●</span>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            <?php else: ?>
                                                <option value="" disabled>Nenhuma aliança disponível</option>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                        <label><i class="fas fa-handshake text-primary me-1"></i>Aliança *</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['aliancaSelecionada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        <div class="form-text">
                                            <small class="text-muted">Selecione uma aliança para carregar líderes e membros específicos</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Responsável -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select <?php $__errorArgs = ['responsavel_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                wire:model="responsavel_id">
                                            <option value="">Selecione um responsável</option>
                                            <!--[if BLOCK]><![endif]--><?php if(isset($lideresDisponiveis) && !empty($lideresDisponiveis)): ?>
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $lideresDisponiveis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <!--[if BLOCK]><![endif]--><?php if($lider->membro && $lider->membro->user): ?>
                                                        <option value="<?php echo e($lider->membro->user_id); ?>">
                                                            <?php echo e($lider->membro->user->name); ?> (<?php echo e(ucfirst($lider->cargo_na_alianca)); ?>)
                                                        </option>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                        <label><i class="fas fa-user-tie text-primary me-1"></i>Responsável</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['responsavel_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- Convidado Especial-->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select <?php $__errorArgs = ['convidado_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                wire:model="convidado_id">
                                            <option value="">Selecione um convidado (opcional)</option>
                                            <!--[if BLOCK]><![endif]--><?php if(isset($membrosDisponiveis) && !empty($membrosDisponiveis)): ?>
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $membrosDisponiveis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $membro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($membro->user_id); ?>">
                                                        <?php echo e($membro->user->name); ?> (<?php echo e(ucfirst($membro->cargo)); ?>)
                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                        <label><i class="fas fa-user text-primary me-1"></i>Convidado Especial</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['convidado_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        <div class="form-text">
                                            <small class="text-muted">Campo opcional - deixe vazio para reunião geral</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Observações -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                  wire:model="observacoes" rows="3"
                                                  placeholder="Observações adicionais"></textarea>
                                        <label><i class="fas fa-sticky-note text-primary me-1"></i>Observações</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- Status Visual -->
                                <div class="col-12">
                                    <div class="alert alert-light border">
                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                        <strong>Status:</strong>
                                        <span class="text-muted">
                                            <?php echo e($isEditing ? 'Editando Reunião' : 'Nova Reunião'); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer do Modal -->
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" wire:click="salvarReuniao" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarReuniao">
                        <i class="fas fa-save me-1"></i><?php echo e($isEditing ? 'Atualizar Reunião' : 'Agendar Reunião'); ?>

                    </span>
                    <span wire:loading wire:target="salvarReuniao">
                        <i class="fas fa-spinner fa-spin me-1"></i><?php echo e($isEditing ? 'Atualizando...' : 'Agendando...'); ?>

                    </span>
                </button>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('livewire:init', () => {
    // Atualizar campos condicionais quando modalidade mudar
    Livewire.on('modalidadeChanged', (modalidade) => {
        const localField = document.getElementById('localField');
        const linkField = document.getElementById('linkField');

        if (localField && linkField) {
            if (modalidade === 'presencial' || modalidade === 'hibrido') {
                localField.style.display = 'block';
            } else {
                localField.style.display = 'none';
            }

            if (modalidade === 'online' || modalidade === 'hibrido') {
                linkField.style.display = 'block';
            } else {
                linkField.style.display = 'none';
            }
        }
    });
});

</script>

<!-- Estilos para Flatpickr - Forçar sempre desktop -->
<style>
    /* Forçar Flatpickr sempre visível */
    .flatpickr-calendar {
        z-index: 10000 !important;
        display: none;
    }

    .flatpickr-calendar.open {
        display: block !important;
    }

    /* Overlay para mobile */
    .flatpickr-overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: rgba(0,0,0,0.5) !important;
        z-index: 9999 !important;
    }

    /* Responsivo para telas pequenas */
    @media (max-width: 768px) {
        .flatpickr-calendar {
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        width: 320px !important;
        max-width: 90vw !important;
    }

    .flatpickr-calendar .flatpickr-month {
        height: 40px !important;
    }

    .flatpickr-calendar .flatpickr-day {
        height: 35px !important;
        line-height: 35px !important;
    }
    }

    /* Melhorar inputs de data */
    .date_flatpicker {
        cursor: pointer !important;
        background-color: white !important;
    }

    .date_flatpicker:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }
</style>

<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/church/alliance/modals/meeting-modal.blade.php ENDPATH**/ ?>