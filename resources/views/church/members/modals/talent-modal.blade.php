<div class="modal fade" id="talentModal" tabindex="-1" aria-labelledby="talentModalLabel" aria-hidden="true"
data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
<div class="modal-dialog modal-dialog-centered">
   <div class="modal-content">
       <!-- Header do Modal -->
       <div class="modal-header bg-light border-bottom">
           <h5 class="modal-title fw-bold" id="talentModalLabel">
               <i class="fas fa-star text-primary me-2"></i>
               <span id="modal-title">{{ $editingMember ? 'Adicionar Habilidade - ' . $editingMember->user->name : 'Adicionar Habilidade' }}</span>
           </h5>
           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
       </div>

       <!-- Corpo do Modal -->
       <div class="modal-body p-4">
           <form wire:submit.prevent="addSkill">

               <!-- Seleção do Membro (apenas se não estiver editando) -->
               @if(!$editingMember)
               <div class="mb-3">
                   <label class="form-label fw-semibold">Selecionar Membro</label>
                   <select class="form-select @error('selectedMember') is-invalid @enderror"
                           wire:model="selectedMember">
                       <option value="">Escolha um membro</option>
                       @foreach(\App\Models\Igrejas\IgrejaMembro::with('user')->where('status', 'ativo')->get() as $member)
                           <option value="{{ $member->id }}">{{ $member->user->name }} - {{ $member->cargo }}</option>
                       @endforeach
                   </select>
                   @error('selectedMember')
                       <div class="invalid-feedback">{{ $message }}</div>
                   @enderror
               </div>
               @endif

               <!-- Habilidade -->
               <div class="mb-3">
                   <label class="form-label fw-semibold">Habilidade</label>
                   <input type="text"  autocomplete="new-password" class="form-control @error('newSkill') is-invalid @enderror"
                          wire:model="newSkill" placeholder="Ex: Música, Ensino, Liderança..."
                          list="skills-list">
                   <datalist id="skills-list">
                       <option value="Música">
                       <option value="Canto">
                       <option value="Instrumento">
                       <option value="Ensino">
                       <option value="Liderança">
                       <option value="Comunicação">
                       <option value="Organização">
                       <option value="Tecnologia">
                       <option value="Culinária">
                       <option value="Artesanato">
                   </datalist>
                   @error('newSkill')
                       <div class="invalid-feedback">{{ $message }}</div>
                   @enderror
               </div>

               <!-- Nível -->
               <div class="mb-3">
                   <label class="form-label fw-semibold">Nível de Proficiência</label>
                   <div class="row g-2">
                       <div class="col-4">
                           <div class="form-check">
                               <input class="form-check-input" type="radio" name="skillLevel" id="level-iniciante" value="iniciante" wire:model="newSkillLevel">
                               <label class="form-check-label" for="level-iniciante">
                                   <i class="fas fa-star-half-alt text-info me-1"></i>Iniciante
                               </label>
                           </div>
                       </div>
                       <div class="col-4">
                           <div class="form-check">
                               <input class="form-check-input" type="radio" name="skillLevel" id="level-intermediario" value="intermediario" wire:model="newSkillLevel">
                               <label class="form-check-label" for="level-intermediario">
                                   <i class="fas fa-star text-warning me-1"></i>Intermediário
                               </label>
                           </div>
                       </div>
                       <div class="col-4">
                           <div class="form-check">
                               <input class="form-check-input" type="radio" name="skillLevel" id="level-avancado" value="avancado" wire:model="newSkillLevel">
                               <label class="form-check-label" for="level-avancado">
                                   <i class="fas fa-star text-success me-1"></i><i class="fas fa-star text-success me-1"></i>Avançado
                               </label>
                           </div>
                       </div>
                   </div>
                   @error('newSkillLevel')
                       <div class="invalid-feedback d-block">{{ $message }}</div>
                   @enderror
               </div>

               <!-- Status Visual -->
               <div class="alert alert-light border">
                   <i class="fas fa-info-circle text-primary me-2"></i>
                   <strong>Status:</strong>
                   <span class="text-muted">
                       Adicionando nova habilidade
                   </span>
               </div>
           </form>
       </div>

       <!-- Footer do Modal -->
       <div class="modal-footer border-top bg-light">
           <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
               <i class="fas fa-times me-1"></i>Cancelar
           </button>
           <button type="button" class="btn btn-primary" wire:click="addSkill" wire:loading.attr="disabled">
               <span wire:loading.remove wire:target="addSkill">
                   <i class="fas fa-save me-1"></i>Salvar Habilidade
               </span>
               <span wire:loading wire:target="addSkill">
                   <i class="fas fa-spinner fa-spin me-1"></i>Salvando...
               </span>
           </button>
       </div>
   </div>
</div>
</div>
