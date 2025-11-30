<?php

namespace App\Livewire\Church\Courses;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use App\Models\Cursos\CursoMatricula;
use App\Models\Cursos\CursoTurma;
use App\Models\Cursos\Curso;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;

#[Title('Matrículas de Cursos | Portal da Igreja')]
#[Layout('components.layouts.app')]
class CourseRegistered extends Component
{
    use WithPagination, WithFileUploads, WithoutUrlPagination;

    // Propriedades para identificação
    public $classId;
    public $class;
    public $membroAtual;

    // Propriedades para métricas
    public $totalMatriculas = 0;
    public $matriculasAtivas = 0;
    public $matriculasConcluidas = 0;
    public $matriculasDesistentes = 0;

    // Propriedades para listagem
    public $registrations = [];

    // Propriedades para modal
    public $isEditing = false;
    public $registrationSelecionada = null;

    // Propriedades do formulário
    #[Rule('required|exists:igreja_membros,id')]
    public $membro_id = '';

    #[Rule('nullable|date')]
    public $data_matricula = '';

    #[Rule('required|in:ativo,concluido,desistente,transferido,suspenso')]
    public $status = 'ativo';

    #[Rule('boolean')]
    public $apto = false;

    #[Rule('nullable|date')]
    public $data_apto = '';

    #[Rule('boolean')]
    public $certificado_emitido = false;

    #[Rule('nullable|date')]
    public $data_certificado = '';

    #[Rule('nullable|string|max:1000')]
    public $observacoes = '';

    // Propriedades para filtros
    public $search = '';
    public $selectedStatus = '';
    public $selectedCourse = '';

    public function mount($class = null)
    {
        $this->classId = $class;
        $this->carregarMembroAtual();
        $this->carregarClass();
        $this->carregarRegistrations();
        $this->carregarMetricas();
    }

    protected function carregarMembroAtual()
    {
        $this->membroAtual = \App\Models\Igrejas\IgrejaMembro::where('user_id', Auth::id())
            ->where('status', 'ativo')
            ->where('cargo', '!=', 'membro')
            ->first();

        if (!$this->membroAtual) {
            abort(403, 'Acesso negado. Apenas líderes ativos podem acessar as matrículas.');
        }
    }

    protected function carregarClass()
    {
        if ($this->classId) {
            $this->class = CursoTurma::with('curso')
                ->where('id', $this->classId)
                ->whereHas('curso', function($q) {
                    $q->where('igreja_id', Auth::user()->getIgrejaId());
                })
                ->first();

            if (!$this->class) {
                abort(404, 'Turma não encontrada.');
            }
        }
    }

    protected function carregarRegistrations()
    {
        $query = CursoMatricula::with(['membro.user', 'turma.curso'])
            ->whereHas('turma.curso', function($q) {
                $q->where('igreja_id', Auth::user()->getIgrejaId());
            });

        // Filtrar por turma específica se informado
        if ($this->classId) {
            $query->where('turma_id', $this->classId);
        }

        // Aplicar filtros
        if ($this->search) {
            $query->whereHas('membro.user', function($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        if ($this->selectedCourse) {
            $query->whereHas('turma', function($q) {
                $q->where('curso_id', $this->selectedCourse);
            });
        }

        $this->registrations = $query->orderBy('created_at', 'desc')->get();
    }

    protected function carregarMetricas()
    {
        $query = CursoMatricula::whereHas('turma.curso', function($q) {
            $q->where('igreja_id', Auth::user()->getIgrejaId());
        });

        // Filtrar por turma específica se informado
        if ($this->classId) {
            $query->where('turma_id', $this->classId);
        }

        $matriculas = $query->get();

        $this->totalMatriculas = $matriculas->count();
        $this->matriculasAtivas = $matriculas->where('status', 'ativo')->count();
        $this->matriculasConcluidas = $matriculas->where('status', 'concluido')->count();
        $this->matriculasDesistentes = $matriculas->where('status', 'desistente')->count();
    }

    public function openModal($registrationId = null)
    {
        $this->resetModal();

        if ($registrationId) {
            $registration = CursoMatricula::find($registrationId);

            if (!$registration || $registration->turma->curso->igreja_id !== Auth::user()->getIgrejaId()) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Matrícula não encontrada.'
                ]);
                return;
            }

            $this->registrationSelecionada = $registration;
            $this->membro_id = $registration->membro_id;
            $this->data_matricula = $registration->data_matricula?->format('Y-m-d');
            $this->status = $registration->status;
            $this->apto = $registration->apto;
            $this->data_apto = $registration->data_apto?->format('Y-m-d');
            $this->certificado_emitido = $registration->certificado_emitido;
            $this->data_certificado = $registration->data_certificado?->format('Y-m-d');
            $this->observacoes = $registration->observacoes;
            $this->isEditing = true;
        } else {
            $this->status = 'ativo';
            $this->apto = false;
            $this->certificado_emitido = false;
            $this->data_matricula = now()->format('Y-m-d');
            $this->isEditing = false;
        }

        $this->dispatch('open-registration-modal');
    }

    #[On('open-registration-modal')]
    public function handleOpenModal($registrationId = null)
    {
        $this->openModal($registrationId);
    }

    public function salvarRegistration()
    {
        $this->validate();

        // Verificar se o membro já está matriculado nesta turma
        if (!$this->isEditing) {
            $existing = CursoMatricula::where('turma_id', $this->classId ?: $this->registrationSelecionada->turma_id)
                ->where('membro_id', $this->membro_id)
                ->where('status', 'ativo')
                ->first();

            if ($existing) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Este membro já está matriculado nesta turma.'
                ]);
                return;
            }
        }

        if ($this->isEditing) {
            $this->registrationSelecionada->update([
                'membro_id' => $this->membro_id,
                'data_matricula' => $this->data_matricula,
                'status' => $this->status,
                'apto' => $this->apto,
                'data_apto' => $this->data_apto,
                'certificado_emitido' => $this->certificado_emitido,
                'data_certificado' => $this->data_certificado,
                'observacoes' => $this->observacoes,
            ]);

            $mensagem = 'Matrícula atualizada com sucesso!';
        } else {
            $matricula = CursoMatricula::create([
                'turma_id' => $this->classId,
                'membro_id' => $this->membro_id,
                'data_matricula' => $this->data_matricula,
                'status' => $this->status,
                'apto' => $this->apto,
                'data_apto' => $this->data_apto,
                'certificado_emitido' => $this->certificado_emitido,
                'data_certificado' => $this->data_certificado,
                'observacoes' => $this->observacoes,
            ]);

            // Atualizar vagas da turma
            $matricula->turma->adicionarMatricula();

            $mensagem = 'Matrícula criada com sucesso!';
        }

        $this->carregarRegistrations();
        $this->carregarMetricas();
        $this->dispatch('close-registration-modal');

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $mensagem
        ]);
    }

    public function cancelRegistration($registrationId)
    {
        $registration = CursoMatricula::find($registrationId);

        if (!$registration || $registration->turma->curso->igreja_id !== Auth::user()->getIgrejaId()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Matrícula não encontrada.'
            ]);
            return;
        }

        $registration->marcarDesistencia('Cancelamento via sistema');
        $this->carregarRegistrations();
        $this->carregarMetricas();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Matrícula cancelada com sucesso!'
        ]);
    }

    public function viewProgress($registrationId)
    {
        // Lógica para visualizar progresso
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Funcionalidade de progresso em desenvolvimento.'
        ]);
    }

    protected function resetModal()
    {
        $this->registrationSelecionada = null;
        $this->membro_id = '';
        $this->data_matricula = '';
        $this->status = 'ativo';
        $this->apto = false;
        $this->data_apto = '';
        $this->certificado_emitido = false;
        $this->data_certificado = '';
        $this->observacoes = '';
        $this->resetValidation();
    }

    public function updatingSearch()
    {
        $this->carregarRegistrations();
    }

    public function updatingSelectedStatus()
    {
        $this->carregarRegistrations();
    }

    public function updatingSelectedCourse()
    {
        $this->carregarRegistrations();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedStatus = '';
        $this->selectedCourse = '';
        $this->carregarRegistrations();
    }

    public function setStatusFilter($status)
    {
        $this->selectedStatus = $status;
        $this->carregarRegistrations();
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'ativo' => 'success',
            'concluido' => 'primary',
            'desistente' => 'danger',
            'transferido' => 'warning',
            'suspenso' => 'secondary',
            default => 'secondary'
        };
    }

    public function getStatusLabel($status)
    {
        return match($status) {
            'ativo' => 'Ativa',
            'concluido' => 'Concluída',
            'desistente' => 'Desistente',
            'transferido' => 'Transferida',
            'suspenso' => 'Suspensa',
            default => 'Não definido'
        };
    }

    public function render()
    {
        return view('church.courses.course-registered', [
            'totalMatriculas' => $this->totalMatriculas,
            'matriculasAtivas' => $this->matriculasAtivas,
            'matriculasConcluidas' => $this->matriculasConcluidas,
            'matriculasDesistentes' => $this->matriculasDesistentes,
            'courses' => $this->classId ? collect([$this->class->curso]) : Curso::where('igreja_id', Auth::user()->getIgrejaId())->get(),
            'membrosDisponiveis' => $this->classId ? IgrejaMembro::where('igreja_id', Auth::user()->getIgrejaId())
                ->where('status', 'ativo')
                ->with('user')
                ->get() : collect(),
        ]);
    }
}

