<?php

namespace App\Models\Billings\Trial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrialDadosCriados extends Model
{
    use HasFactory;

    protected $table = 'trial_dados_criados';

    protected $fillable = [
        'trial_user_id',
        'tabela',
        'registro_id',
        'tipo_dado',
        'criado_em',
        'soft_deleted',
        'deleted_em',
    ];

    protected $casts = [
        'criado_em' => 'datetime',
        'deleted_em' => 'datetime',
        'soft_deleted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function trialUser(): BelongsTo
    {
        return $this->belongsTo(TrialUser::class, 'trial_user_id');
    }

    // 🔗 SCOPES
    public function scopeAtivos($query)
    {
        return $query->where('soft_deleted', false);
    }

    public function scopeDeletados($query)
    {
        return $query->where('soft_deleted', true);
    }

    public function scopePorTabela($query, $tabela)
    {
        return $query->where('tabela', $tabela);
    }

    public function scopePorTipoDado($query, $tipo)
    {
        return $query->where('tipo_dado', $tipo);
    }

    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    public function scopeCriadosHoje($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeCriadosOntem($query)
    {
        return $query->whereDate('created_at', today()->subDay());
    }

    public function scopeCriadosEstaSemana($query)
    {
        return $query->where('created_at', '>=', now()->startOfWeek());
    }

    public function scopeCriadosEsteMes($query)
    {
        return $query->where('created_at', '>=', now()->startOfMonth());
    }

    // 🔗 HELPERS
    public function foiDeletado(): bool
    {
        return $this->soft_deleted;
    }

    public function estaAtivo(): bool
    {
        return !$this->soft_deleted;
    }

    public function foiDeletadoRecentemente(): bool
    {
        return $this->soft_deleted && $this->deleted_em &&
               $this->deleted_em->diffInHours(now()) < 24;
    }

    public function getTabelaFormatada(): string
    {
        return match($this->tabela) {
            'users' => 'Usuários',
            'igrejas' => 'Igrejas',
            'igreja_membros' => 'Membros',
            'posts' => 'Posts',
            'eventos' => 'Eventos',
            'ministerios' => 'Ministérios',
            'cursos' => 'Cursos',
            'curso_turmas' => 'Turmas',
            'curso_matriculas' => 'Matrículas',
            'marketplace_produtos' => 'Produtos',
            'marketplace_pedidos' => 'Pedidos',
            'doacoes_online' => 'Doações',
            'voluntarios' => 'Voluntários',
            'atendimentos_pastorais' => 'Atendimentos',
            'pedidos_especiais' => 'Pedidos Especiais',
            'recursos' => 'Recursos',
            'agendamentos' => 'Agendamentos',
            default => ucfirst(str_replace('_', ' ', $this->tabela))
        };
    }

    public function getTipoDadoFormatado(): string
    {
        return match($this->tipo_dado) {
            'membro' => 'Membro',
            'post' => 'Post',
            'evento' => 'Evento',
            'ministerio' => 'Ministério',
            'curso' => 'Curso',
            'turma' => 'Turma',
            'matricula' => 'Matrícula',
            'produto' => 'Produto',
            'pedido' => 'Pedido',
            'doacao' => 'Doação',
            'voluntario' => 'Voluntário',
            'atendimento' => 'Atendimento',
            'pedido_especial' => 'Pedido Especial',
            'recurso' => 'Recurso',
            'agendamento' => 'Agendamento',
            default => ucfirst($this->tipo_dado ?? 'Desconhecido')
        };
    }

    public function getIcone(): string
    {
        return match($this->tipo_dado) {
            'membro' => 'fas fa-user',
            'post' => 'fas fa-newspaper',
            'evento' => 'fas fa-calendar',
            'ministerio' => 'fas fa-users',
            'curso' => 'fas fa-graduation-cap',
            'turma' => 'fas fa-users-cog',
            'matricula' => 'fas fa-user-check',
            'produto' => 'fas fa-shopping-bag',
            'pedido' => 'fas fa-shopping-cart',
            'doacao' => 'fas fa-hand-holding-heart',
            'voluntario' => 'fas fa-hands-helping',
            'atendimento' => 'fas fa-user-md',
            'pedido_especial' => 'fas fa-star',
            'recurso' => 'fas fa-tools',
            'agendamento' => 'fas fa-clock',
            default => 'fas fa-circle'
        };
    }

    public function getCriadoEmFormatado(): string
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    public function getCriadoEmRelativo(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getDeletedEmFormatado(): string
    {
        return $this->deleted_em ? $this->deleted_em->format('d/m/Y H:i') : 'Não deletado';
    }

    public function getDeletedEmRelativo(): string
    {
        return $this->deleted_em ? $this->deleted_em->diffForHumans() : 'Não deletado';
    }

    public function getDiasDesdeCriacao(): int
    {
        return $this->created_at->diffInDays(now());
    }

    public function getDiasDesdeDelecao(): ?int
    {
        return $this->deleted_em ? $this->deleted_em->diffInDays(now()) : null;
    }

    public function podeSerRestaurado(): bool
    {
        // Só pode restaurar se foi deletado há menos de 30 dias
        return $this->soft_deleted &&
               $this->deleted_em &&
               $this->deleted_em->diffInDays(now()) <= 30;
    }

    public function deletar(): void
    {
        $this->update([
            'soft_deleted' => true,
            'deleted_em' => now(),
        ]);
    }

    public function restaurar(): void
    {
        if ($this->podeSerRestaurado()) {
            $this->update([
                'soft_deleted' => false,
                'deleted_em' => null,
            ]);
        }
    }

    public function getRegistroUrl(): ?string
    {
        // Retorna URL para visualizar o registro (se aplicável)
        return match($this->tabela) {
            'igreja_membros' => route('churches.church-members') . '?id=' . $this->registro_id,
            'posts' => route('churches.only-posts') . '?id=' . $this->registro_id,
            'eventos' => route('churches.church-events') . '?id=' . $this->registro_id,
            'ministerios' => route('churches.church-ministries') . '?id=' . $this->registro_id,
            'cursos' => route('churches.church-courses.courses') . '?id=' . $this->registro_id,
            'marketplace_produtos' => route('churches.church-marketplace.products') . '?id=' . $this->registro_id,
            default => null
        };
    }

    public function getRegistroNome(): string
    {
        // Tenta buscar o nome do registro (implementação básica)
        try {
            // Aqui seria implementada a lógica para buscar o nome real do registro
            // Por enquanto retorna um placeholder
            return $this->getTipoDadoFormatado() . ' #' . $this->registro_id;
        } catch (\Exception $e) {
            return $this->getTipoDadoFormatado() . ' #' . $this->registro_id;
        }
    }
}