<!-- Filtros para Logs -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label fw-semibold">Ação</label>
                <select class="form-select" wire:model.live="filtroLogAcao">
                    <option value="">Todas</option>
                    @foreach($acoesLog as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Usuário</label>
                <select class="form-select" wire:model.live="filtroLogUsuario">
                    <option value="">Todos os usuários</option>
                    @foreach($usuariosDisponiveis as $usuario)
                        <option value="{{ $usuario['id'] }}">{{ $usuario['nome'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Data Inicial</label>
                <input type="date" class="form-control" wire:model.live="filtroLogDataInicio">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Data Final</label>
                <input type="date" class="form-control" wire:model.live="filtroLogDataFinal">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Total</label>
                <div class="form-control-plaintext pt-2">
                    <strong>{{ $logs->total() }}</strong> registros encontrados
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Logs -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-history text-info me-2"></i>Logs de Auditoria
        </h5>
    </div>
    <div class="card-body">
        @if($logs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Data/Hora</th>
                            <th>Ação</th>
                            <th>Usuário</th>
                            <th>Detalhes</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>
                                    <small class="text-muted">
                                        {{ $log->realizado_em->format('d/m/Y H:i:s') }}
                                    </small>
                                </td>
                                <td>
                                    @php
                                        $acaoClasses = [
                                            'criar_permissao' => 'success',
                                            'atualizar_permissao' => 'warning',
                                            'excluir_permissao' => 'danger',
                                            'criar_funcao' => 'success',
                                            'atualizar_funcao' => 'warning',
                                            'excluir_funcao' => 'danger',
                                            'atribuir_funcao' => 'primary',
                                            'revogar_funcao' => 'secondary',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $acaoClasses[$log->acao] ?? 'secondary' }}">
                                        {{ $acoesLog[$log->acao] ?? ucfirst(str_replace('_', ' ', $log->acao)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->realizadoPor)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                <img src="{{ $log->realizadoPor->photo_url ? Storage::disk('supabase')->url($log->realizadoPor->photo_url) : asset('system/img/logo-system/icon.png') }}" alt="Avatar" class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">
                                            </div>
                                            <small>{{ $log->realizadoPor->name }}</small>
                                        </div>
                                    @else
                                        <small class="text-muted">Sistema</small>
                                    @endif
                                </td>
                                <td>
                                    @if($log->detalhes)
                                        <div>
                                            @if(isset($log->detalhes['permissao_nome']))
                                                <strong>Permissão:</strong> {{ $log->detalhes['permissao_nome'] }}
                                                @if(isset($log->detalhes['permissao_codigo']))
                                                    <br><code class="text-primary">{{ $log->detalhes['permissao_codigo'] }}</code>
                                                @endif
                                            @elseif(isset($log->detalhes['nome']))
                                                <strong>{{ isset($log->detalhes['codigo']) ? 'Permissão' : 'Função' }}:</strong> {{ $log->detalhes['nome'] }}
                                                @if(isset($log->detalhes['codigo']))
                                                    <br><code class="text-primary">{{ $log->detalhes['codigo'] }}</code>
                                                @endif
                                            @elseif(isset($log->detalhes['motivo']))
                                                <strong>Motivo:</strong> {{ $log->detalhes['motivo'] }}
                                            @else
                                                <small class="text-muted">Detalhes disponíveis</small>
                                            @endif
                                        </div>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $log->detalhes['ip'] ?? '-' }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-center mt-4">
                {{ $logs->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-history text-muted mb-4" style="font-size: 4rem;"></i>
                <h4 class="text-muted">Nenhum log encontrado</h4>
                <p class="text-muted mb-4">
                    @if($filtroLogAcao || $filtroLogUsuario || $filtroLogDataInicio || $filtroLogDataFim)
                        Nenhum log encontrado com os filtros aplicados.
                    @else
                        Ainda não há registros de auditoria.
                    @endif
                </p>
                @if($filtroLogAcao || $filtroLogUsuario || $filtroLogDataInicio || $filtroLogDataFim)
                    <button class="btn btn-outline-secondary" wire:click="$set('filtroLogAcao', '')" wire:click="$set('filtroLogUsuario', '')" wire:click="$set('filtroLogDataInicio', '')" wire:click="$set('filtroLogDataFim', '')">
                        <i class="fas fa-times me-1"></i>Limpar Filtros
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
