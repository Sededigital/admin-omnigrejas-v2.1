<div class="row g-4 mb-5"
id="pricingCards">
@foreach($pacotes as $pacote)
   <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
       <div class="card h-100 border-0 shadow-lg card-pricing
            {{ $pacote->isPopular() ? 'border-primary-thick sparkle' : '' }}"
            wire:click.prevent="abrirModalConfirmacao({{ $pacote->id }})"
            style="cursor: pointer !important;">

           <!-- Badge Popular -->
           @if($pacote->isPopular())
               <div class="position-absolute top-0 end-0 mt-3 me-3" style="z-index: 2;">
                   <span class="badge badge-popular">⭐ Mais Popular</span>
               </div>
           @endif

           <!-- Badge Pacote Atual -->
           @if($pacoteAtual && $pacoteAtual->id == $pacote->id)
               <div class="position-absolute top-0 start-0 mt-3 ms-3" style="z-index: 2;">
                   <span class="badge bg-success">SEU PLANO ATUAL</span>
               </div>
           @endif

           <div class="card-body text-center p-4 position-relative">
               <div class="mb-4">
                   @if($pacote->nome === 'Bronze')
                       <i class="fas fa-crown fa-4x" style="color: #cd7f32;"></i>
                   @elseif($pacote->nome === 'Prata')
                       <i class="fas fa-gem fa-4x" style="color: #C0C0C0;"></i>
                   @elseif($pacote->nome === 'Ouro')
                       <i class="fas fa-star fa-4x" style="color: var(--gold);"></i>
                   @else
                       <i class="fas fa-church fa-4x text-primary"></i>
                   @endif
               </div>

               <h4 class="card-title fw-bold mb-3 fs-3">{{ $pacote->nome }}</h4>
               <div class="mb-4">
                   <div class="h1 price-tag mb-2">
                       <span class="gradient-text">{{ $pacote->getPrecoFormatado() }}</span>
                       <small class="text-muted fs-6 ms-1">/mês</small>
                   </div>
                   @if($pacote->preco_vitalicio)
                       <small class="text-muted">
                           Ou {{ $pacote->getPrecoVitalicioFormatado() }} vitalício
                       </small>
                   @endif
               </div>

               <p class="text-muted mb-4 px-2">{{ $pacote->descricao ?: 'Plano completo para sua igreja' }}</p>

               <!-- Recursos do Pacote -->
               <div class="text-start mb-4">
                   @foreach($pacote->recursos->where('ativo', true)->take(4) as $recurso)
                       <div class="feature-item d-flex align-items-start" style="margin-bottom: 0.25rem;">
                           <i class="fas fa-check-circle text-success feature-icon"></i>
                           <span class="flex-grow-1" style="margin-left: 0.125rem;">
                               @if($recurso->isIlimitado())
                                   <strong>{{ $recurso->getTipoFormatado() }}</strong> Ilimitado
                               @else
                                   <strong>{{ $recurso->limite_valor }}</strong> {{ $recurso->getTipoFormatado() }}
                               @endif
                           </span>
                       </div>
                   @endforeach
                   @if($pacote->recursos->where('ativo', true)->count() > 4)
                       <div class="feature-item d-flex align-items-start">
                           <i class="fas fa-plus text-primary feature-icon"></i>
                           <span class="text-primary flex-grow-1" style="margin-left: 0.125rem;">E muito mais...</span>
                       </div>
                   @endif
               </div>

               <!-- Botão de Ação -->
               @php
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
               @endphp

               <!-- Botão de Ação -->
               @php
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
               @endphp

               <button
                   class="btn {{ $classeBotao }} w-100 fw-bold btn-glow py-3"
                   wire:click.prevent="abrirModalConfirmacao({{ $pacote->id }})"
                   wire:loading.attr="disabled"
                   wire:target="abrirModalConfirmacao({{ $pacote->id }})"
                   @if(!$podeSelecionar) disabled @endif
               >
                   <span wire:loading.remove wire:target="abrirModalConfirmacao({{ $pacote->id }})">
                       <i class="{{ $iconeBotao }} me-2"></i> {{ $mensagemBotao }}
                   </span>
                   <span wire:loading wire:target="abrirModalConfirmacao({{ $pacote->id }})">
                       <i class="fas fa-spinner fa-spin me-2"></i> Processando...
                   </span>
               </button>



           </div>
       </div>
   </div>
@endforeach
</div>