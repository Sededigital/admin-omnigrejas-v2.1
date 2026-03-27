<!-- Header com informações do post ou geral -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-1">
                    <i class="fas fa-heart text-danger me-2"></i>
                    @if($postParaReacoes)
                        Reações do Post
                    @else
                        Todas as Reações
                    @endif
                </h5>
                <p class="mb-0 text-muted">
                    @if($postParaReacoes)
                        <strong>"{{ Str::limit($postParaReacoes->titulo, 60) }}"</strong>
                    @else
                        Visualizando todas as reações da igreja
                    @endif
                </p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-outline-secondary btn-sm" wire:click="voltarParaPosts">
                    <i class="fas fa-arrow-left me-1"></i>Voltar para Posts
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filtros para Reações -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tipo de Reação</label>
                <select class="form-select" wire:model.live="filtroReacaoTipo">
                    <option value="">Todas as reações</option>
                    @foreach($reacaoTipos as $tipo => $label)
                        <option value="{{ $tipo }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold">Estatísticas</label>
                <div class="row text-center">
                    <div class="col-12">
                        <div class="p-2 bg-success bg-opacity-10 rounded">
                            <div class="text-success fw-bold">
                                @if($postParaReacoes)
                                    {{ $postParaReacoes->likes_count }}
                                @else
                                    {{ \App\Models\Chats\PostReaction::whereHas('post', function($q) { $q->where('igreja_id', auth()->user()->getIgreja()->id); })->count() }}
                                @endif
                            </div>
                            <small class="text-muted">
                                @if($postParaReacoes)
                                    Total de Reações do Post
                                @else
                                    Total de Reações da Igreja
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Total</label>
                <div class="form-control-plaintext pt-2">
                    <strong>{{ $reacoes->total() }}</strong> reações encontradas
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Reações -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-heart text-danger me-2"></i>Reações
        </h5>
    </div>
    <div class="card-body">
        @if($reacoes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Usuário</th>
                            @if(!$postParaReacoes)
                                <th>Post</th>
                            @endif
                            <th>Tipo de Reação</th>
                            <th>Data/Hora</th>
                            <th width="100">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reacoes as $reacao)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            @if($reacao->user->photo_url)
                                            <img src="{{ Storage::disk('supabase')->url($reacao->user->photo_url) }}" alt="Avatar" class="rounded-circle">
                                            @else
                                                <div class="bg-info text-light text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <span class="fw-bold">{{ substr($reacao->user->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $reacao->user->name }}</div>
                                            <small class="text-muted">{{ $reacao->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                @if(!$postParaReacoes)
                                    <td>
                                        <div class="fw-semibold">{{ Str::limit($reacao->post->titulo, 40) }}</div>
                                        <small class="text-muted">{{ $reacao->post->autor->name }}</small>
                                    </td>
                                @endif
                                <td>
                                    @php
                                        $reacaoClasses = [
                                            'like' => 'success',
                                            'love' => 'danger',
                                            'haha' => 'warning',
                                            'wow' => 'info',
                                            'sad' => 'secondary',
                                            'angry' => 'dark'
                                        ];
                                        $reacaoIcons = [
                                            'like' => 'thumbs-up',
                                            'love' => 'heart',
                                            'haha' => 'laugh',
                                            'wow' => 'surprise',
                                            'sad' => 'sad-tear',
                                            'angry' => 'angry'
                                        ];
                                        $reacaoLabels = [
                                            'like' => 'Curtida',
                                            'love' => 'Amei',
                                            'haha' => 'Engraçado',
                                            'wow' => 'Uau',
                                            'sad' => 'Triste',
                                            'angry' => 'Raiva'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $reacaoClasses[$reacao->reaction] ?? 'secondary' }}">
                                        <i class="fas fa-{{ $reacaoIcons[$reacao->reaction] ?? 'question' }} me-1"></i>
                                        {{ $reacaoLabels[$reacao->reaction] ?? ucfirst($reacao->reaction) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $reacao->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" wire:click="excluirReacao({{ $reacao->post_id }}, '{{ $reacao->user_id }}')" title="Excluir Reação">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-center mt-4">
                {{ $reacoes->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-heart text-muted mb-4" style="font-size: 4rem;"></i>
                <h4 class="text-muted">Nenhuma reação encontrada</h4>
                <p class="text-muted mb-4">
                    @if($filtroReacaoTipo)
                        Nenhuma reação do tipo selecionado foi encontrada.
                    @else
                        @if($postParaReacoes)
                            Este post ainda não recebeu nenhuma reação.
                        @else
                            Nenhum post da igreja recebeu reações ainda.
                        @endif
                    @endif
                </p>
                @if($filtroReacaoTipo)
                    <button class="btn btn-outline-secondary me-2" wire:click="$set('filtroReacaoTipo', '')">
                        <i class="fas fa-times me-1"></i>Limpar Filtro
                    </button>
                @endif
                <button class="btn btn-outline-secondary" wire:click="voltarParaPosts">
                    <i class="fas fa-arrow-left me-1"></i>Voltar para Posts
                </button>
            </div>
        @endif
    </div>
</div>
