<div class="row g-4 mb-5"
id="pricingCards">
<!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pacotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pacote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
   <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
       <div class="card h-100 border-0 shadow-lg card-pricing
            <?php echo e($pacote->isPopular() ? 'border-primary-thick sparkle' : ''); ?>"
            wire:click.prevent="abrirModalConfirmacao(<?php echo e($pacote->id); ?>)"
            style="cursor: pointer !important;">

           <!-- Badge Popular -->
           <!--[if BLOCK]><![endif]--><?php if($pacote->isPopular()): ?>
               <div class="position-absolute top-0 end-0 mt-3 me-3" style="z-index: 2;">
                   <span class="badge badge-popular">⭐ Mais Popular</span>
               </div>
           <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

           <!-- Badge Pacote Atual -->
           <!--[if BLOCK]><![endif]--><?php if($pacoteAtual && $pacoteAtual->id == $pacote->id): ?>
               <div class="position-absolute top-0 start-0 mt-3 ms-3" style="z-index: 2;">
                   <span class="badge bg-success">SEU PLANO ATUAL</span>
               </div>
           <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

           <div class="card-body text-center p-4 position-relative">
               <div class="mb-4">
                   <!--[if BLOCK]><![endif]--><?php if($pacote->nome === 'Bronze'): ?>
                       <i class="fas fa-crown fa-4x" style="color: #cd7f32;"></i>
                   <?php elseif($pacote->nome === 'Prata'): ?>
                       <i class="fas fa-gem fa-4x" style="color: #C0C0C0;"></i>
                   <?php elseif($pacote->nome === 'Ouro'): ?>
                       <i class="fas fa-star fa-4x" style="color: var(--gold);"></i>
                   <?php else: ?>
                       <i class="fas fa-church fa-4x text-primary"></i>
                   <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
               </div>

               <h4 class="card-title fw-bold mb-3 fs-3"><?php echo e($pacote->nome); ?></h4>
               <div class="mb-4">
                   <div class="h1 price-tag mb-2">
                       <span class="gradient-text"><?php echo e($pacote->getPrecoFormatado()); ?></span>
                       <small class="text-muted fs-6 ms-1">/mês</small>
                   </div>
                   <!--[if BLOCK]><![endif]--><?php if($pacote->preco_vitalicio): ?>
                       <small class="text-muted">
                           Ou <?php echo e($pacote->getPrecoVitalicioFormatado()); ?> vitalício
                       </small>
                   <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
               </div>

               <p class="text-muted mb-4 px-2"><?php echo e($pacote->descricao ?: 'Plano completo para sua igreja'); ?></p>

               <!-- Recursos do Pacote -->
               <div class="text-start mb-4">
                   <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pacote->recursos->where('ativo', true)->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recurso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                       <div class="feature-item d-flex align-items-start" style="margin-bottom: 0.25rem;">
                           <i class="fas fa-check-circle text-success feature-icon"></i>
                           <span class="flex-grow-1" style="margin-left: 0.125rem;">
                               <!--[if BLOCK]><![endif]--><?php if($recurso->isIlimitado()): ?>
                                   <strong><?php echo e($recurso->getTipoFormatado()); ?></strong> Ilimitado
                               <?php else: ?>
                                   <strong><?php echo e($recurso->limite_valor); ?></strong> <?php echo e($recurso->getTipoFormatado()); ?>

                               <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                           </span>
                       </div>
                   <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                   <!--[if BLOCK]><![endif]--><?php if($pacote->recursos->where('ativo', true)->count() > 4): ?>
                       <div class="feature-item d-flex align-items-start">
                           <i class="fas fa-plus text-primary feature-icon"></i>
                           <span class="text-primary flex-grow-1" style="margin-left: 0.125rem;">E muito mais...</span>
                       </div>
                   <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
               </div>

               <!-- Botão de Ação -->
               <?php
                   $podeSelecionar = true;
                   $mensagemBotao = 'Selecionar Plano';
                   $iconeBotao = 'fas fa-hand-pointer';
                   $classeBotao = 'btn-outline-primary';

                   // Verificar se é o pacote atual
                   if (isset($assinaturaAtual) && $assinaturaAtual && $assinaturaAtual->pacote->id === $pacote->id) {
                       if ($assinaturaAtual->isExpired()) {
                           $mensagemBotao = 'Renovar Plano';
                           $iconeBotao = 'fas fa-refresh';
                           $classeBotao = 'btn-success';
                       } else {
                           // Mesmo pacote ativo - permitir renovação antecipada
                           $mensagemBotao = 'Renovar Plano';
                           $iconeBotao = 'fas fa-refresh';
                           $classeBotao = 'btn-success';
                       }
                   }
                   // Verificar se pode fazer upgrade (pacote superior)
                   elseif (isset($assinaturaAtual) && $assinaturaAtual && !$assinaturaAtual->isExpired() && $pacote->preco > $assinaturaAtual->pacote->preco) {
                       $mensagemBotao = 'Fazer Upgrade';
                       $iconeBotao = 'fas fa-arrow-up';
                       $classeBotao = 'btn-warning';
                   }
                   // Verificar se pode fazer downgrade (pacote inferior)
                   elseif (isset($assinaturaAtual) && $assinaturaAtual && !$assinaturaAtual->isExpired() && $pacote->preco < $assinaturaAtual->pacote->preco) {
                       $mensagemBotao = 'Fazer Downgrade';
                       $iconeBotao = 'fas fa-arrow-down';
                       $classeBotao = 'btn-info';
                   }
               ?>

               <!-- Botão de Ação -->
               <?php
                   $podeSelecionar = true;
                   $mensagemBotao = 'Selecionar Plano';
                   $iconeBotao = 'fas fa-hand-pointer';
                   $classeBotao = 'btn-outline-primary';

                   // Verificar se é o pacote atual
                   if (isset($assinaturaAtual) && $assinaturaAtual && $assinaturaAtual->pacote->id === $pacote->id) {
                       if ($assinaturaAtual->isExpired()) {
                           $mensagemBotao = 'Renovar Plano';
                           $iconeBotao = 'fas fa-refresh';
                           $classeBotao = 'btn-success';
                       } else {
                           // Mesmo pacote ativo - permitir renovação antecipada
                           $mensagemBotao = 'Renovar Plano';
                           $iconeBotao = 'fas fa-refresh';
                           $classeBotao = 'btn-success';
                       }
                   }
                   // Verificar se pode fazer upgrade (pacote superior)
                   elseif (isset($assinaturaAtual) && $assinaturaAtual && !$assinaturaAtual->isExpired() && $pacote->preco > $assinaturaAtual->pacote->preco) {
                       $mensagemBotao = 'Fazer Upgrade';
                       $iconeBotao = 'fas fa-arrow-up';
                       $classeBotao = 'btn-warning';
                   }
                   // Verificar se pode fazer downgrade (pacote inferior)
                   elseif (isset($assinaturaAtual) && $assinaturaAtual && !$assinaturaAtual->isExpired() && $pacote->preco < $assinaturaAtual->pacote->preco) {
                       $mensagemBotao = 'Fazer Downgrade';
                       $iconeBotao = 'fas fa-arrow-down';
                       $classeBotao = 'btn-info';
                   }
               ?>

               <button
                   class="btn <?php echo e($classeBotao); ?> w-100 fw-bold btn-glow py-3"
                   wire:click.prevent="abrirModalConfirmacao(<?php echo e($pacote->id); ?>)"
                   wire:loading.attr="disabled"
                   wire:target="abrirModalConfirmacao(<?php echo e($pacote->id); ?>)"
                   <?php if(!$podeSelecionar): ?> disabled <?php endif; ?>
               >
                   <span wire:loading.remove wire:target="abrirModalConfirmacao(<?php echo e($pacote->id); ?>)">
                       <i class="<?php echo e($iconeBotao); ?> me-2"></i> <?php echo e($mensagemBotao); ?>

                   </span>
                   <span wire:loading wire:target="abrirModalConfirmacao(<?php echo e($pacote->id); ?>)">
                       <i class="fas fa-spinner fa-spin me-2"></i> Processando...
                   </span>
               </button>



           </div>
       </div>
   </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/subscription/pacote-cards.blade.php ENDPATH**/ ?>