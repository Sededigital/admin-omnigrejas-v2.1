<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-star me-2"></i>Mapa de Talentos
                        </h1>
                        <p class="mb-0 text-muted">Descubra e gerencie as habilidades dos membros da igreja</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn bg-info text-light btn-md" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#talentModal">
                            <i class="fas fa-plus-circle me-2"></i>Adicionar Habilidade
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <div class="row g-3 mb-4">
            <!-- Card Principal -->
            <div class="col-12 col-lg-8 mb-3">
                <div class="card h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="card-body p-4">
                        <div class="row align-items-center h-100">
                            <div class="col-md-6">
                                <h2 class="h1 mb-2 fw-bold text-light">{{ $stats['members_with_skills'] }}</h2>
                                <p class="mb-0 opacity-75">Membros com Habilidades</p>
                                <small class="opacity-50">de {{ $stats['total_members'] }} membros totais</small>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <div class="p-2 bg-white bg-opacity-20 rounded">
                                            <div class="fw-bold h5">{{ $stats['beginner_count'] }}</div>
                                            <small>Iniciantes</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-white bg-opacity-20 rounded">
                                            <div class="fw-bold h5">{{ $stats['intermediate_count'] }}</div>
                                            <small>Interm.</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-white bg-opacity-20 rounded">
                                            <div class="fw-bold h5">{{ $stats['advanced_count'] }}</div>
                                            <small>Avançados</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cards de Estatísticas --}}
            <div class="col-12 col-lg-4">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card text-center border border-info h-100">
                            <div class="card-body">
                                <i class="fas fa-lightbulb text-info display-6 mb-2"></i>
                                <div class="fw-bold h4 mb-1 text-info">{{ $stats['total_skills'] }}</div>
                                <div class="text-muted small">Habilidades Totais</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card text-center border border-success h-100">
                            <div class="card-body">
                                <i class="fas fa-users text-success display-6 mb-2"></i>
                                <div class="fw-bold h4 mb-1 text-success">
                                    {{ $stats['total_members'] > 0 ? round(($stats['members_with_skills'] / $stats['total_members']) * 100) : 0 }}%
                                </div>
                                <div class="text-muted small">Membros Ativos</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Buscar Membro</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nome do membro">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Filtrar por Nível</label>
                        <select class="form-select" wire:model.live="selectedLevel">
                            <option value="">Todos os níveis</option>
                            <option value="iniciante">Iniciante</option>
                            <option value="intermediario">Intermediário</option>
                            <option value="avancado">Avançado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button class="btn bg-info text-light flex-fill" wire:click="clearFilters">
                                <i class="fas fa-filter me-1"></i>Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Talentos - Design Diferente -->
        <div class="row g-4">
            @forelse($members as $member)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-4">
                <div class="card h-100 shadow-sm border-0 talent-card" style="border-radius: 15px; overflow: hidden;">
                    <!-- Header com gradiente -->
                    <div class="card-header bg-gradient-primary text-white d-flex align-items-center position-relative" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 80px;">
                        <div class="user-avatar bg-white text-info me-3 rounded-circle d-flex align-items-center justify-content-center fw-bold shadow" style="width: 50px; height: 50px; border: 3px solid rgba(255,255,255,0.3);">
                            {{ strtoupper(substr($member->user->name ?? 'M', 0, 2)) }}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold text-white">{{ $member->user->name ?? 'N/A' }}</h6>
                            <small class="text-white-50">{{ $member->cargo ?? 'Membro' }}</small>
                        </div>
                        <button class="btn btn-light btn-sm rounded-pill shadow-sm" wire:click="openModal('{{ $member->id }}')" data-bs-toggle="modal" data-bs-target="#talentModal" style="border: none;">
                            <i class="fas fa-plus text-info"></i>
                        </button>
                    </div>

                    <div class="card-body p-3">
                        @php
                            $skills = DB::table('habilidades_membros')->where('membro_id', $member->id)->get();
                        @endphp

                        @if($skills->count() > 0)
                            <div class="skills-container" style="max-height: 180px; overflow-y: auto;">
                                @foreach($skills as $skill)
                                <div class="skill-item mb-3 p-2 rounded bg-light border-start border-4" style="border-left-color: {{ $skill->nivel === 'iniciante' ? '#6c757d' : ($skill->nivel === 'intermediario' ? '#ffc107' : '#28a745') }} !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge {{ $this->getSkillBadgeClass($skill->nivel) }} badge-sm">
                                            {{ $this->getSkillLevelText($skill->nivel) }}
                                        </span>
                                        <small class="text-muted">{{ $skill->habilidade }}</small>
                                    </div>
                                    <div class="btn-group btn-group-xs w-100">
                                        <button class="btn btn-outline-secondary btn-xs flex-fill"
                                                wire:click="updateSkillLevel('{{ $member->id }}', '{{ $skill->habilidade }}', 'iniciante')"
                                                @if($skill->nivel === 'iniciante') disabled @endif
                                                title="Iniciante">
                                            <i class="fas fa-star-half-alt"></i>
                                        </button>
                                        <button class="btn btn-outline-warning btn-xs flex-fill"
                                                wire:click="updateSkillLevel('{{ $member->id }}', '{{ $skill->habilidade }}', 'intermediario')"
                                                @if($skill->nivel === 'intermediario') disabled @endif
                                                title="Intermediário">
                                            <i class="fas fa-star"></i>
                                        </button>
                                        <button class="btn btn-outline-success btn-xs flex-fill"
                                                wire:click="updateSkillLevel('{{ $member->id }}', '{{ $skill->habilidade }}', 'avancado')"
                                                @if($skill->nivel === 'avancado') disabled @endif
                                                title="Avançado">
                                            <i class="fas fa-star"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-xs"
                                                wire:click="openDeleteModal('{{ $member->id }}', '{{ $skill->habilidade }}')"
                                                data-bs-toggle="modal" data-bs-target="#deleteSkillModal"
                                                title="Remover">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <i class="fas fa-lightbulb text-warning" style="font-size: 3rem;"></i>
                                </div>
                                <h6 class="text-muted mb-3">Nenhuma habilidade cadastrada</h6>
                                <button class="btn bg-info text-light btn-sm rounded-pill px-3" wire:click="openModal('{{ $member->id }}')" data-bs-toggle="modal" data-bs-target="#talentModal">
                                    <i class="fas fa-plus me-1"></i>Adicionar Habilidade
                                </button>
                            </div>
                        @endif
                    </div>

                    @if($skills->count() > 0)
                    <div class="card-footer bg-white border-0">
                        <div class="row text-center g-2">
                            <div class="col-4">
                                <div class="p-2 rounded bg-light">
                                    <div class="fw-bold text-info">{{ $skills->where('nivel', 'iniciante')->count() }}</div>
                                    <small class="text-muted">Iniciante</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded bg-light">
                                    <div class="fw-bold text-warning">{{ $skills->where('nivel', 'intermediario')->count() }}</div>
                                    <small class="text-muted">Interm.</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded bg-light">
                                    <div class="fw-bold text-success">{{ $skills->where('nivel', 'avancado')->count() }}</div>
                                    <small class="text-muted">Avançado</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-users text-muted display-4 mb-3"></i>
                        <h5 class="text-muted">Nenhum membro encontrado</h5>
                        <p class="text-muted">Tente ajustar os filtros de busca</p>
                        @if($search || $selectedLevel)
                            <button class="btn btn-outline-primary" wire:click="$set('search', '')">
                                <i class="fas fa-times me-1"></i>Limpar filtros
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Paginação -->
        @if($members->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $members->links() }}
        </div>
        @endif
    </div>

    <!-- Modal para Adicionar Habilidade -->
    @include('church.members.modals.talent-modal')

    <!-- Modal para Confirmar Exclusão de Habilidade -->
    <div class="modal fade" id="deleteSkillModal" tabindex="-1" aria-labelledby="deleteSkillModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title fw-bold" id="deleteSkillModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Exclusão
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar" wire:click="closeDeleteModal"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body text-center p-4">
                    <i class="fas fa-trash-alt text-danger display-4 mb-3"></i>
                    <h6 class="mb-2">Tem certeza que deseja remover esta habilidade?</h6>
                    <p class="text-muted small mb-0">Esta ação não pode ser desfeita.</p>
                </div>

                <!-- Footer do Modal -->
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal" wire:click="closeDeleteModal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" wire:click="confirmDelete" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="confirmDelete">
                            <i class="fas fa-trash me-1"></i>Remover
                        </span>
                        <span wire:loading wire:target="confirmDelete">
                            <i class="fas fa-spinner fa-spin me-1"></i>Removendo...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para Talent Map -->
    <script src="{{ asset('system/js/members.js') }}"></script>


</div>
