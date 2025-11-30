<?php

namespace App\Livewire\Church\Courses;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use App\Models\Cursos\CursoTurma;
use App\Models\Cursos\Curso;
use App\Models\Igrejas\Igreja;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;

#[Title('Turmas de Cursos | Portal da Igreja')]
#[Layout('components.layouts.app')]
class CourseClass extends Component
{
    use WithPagination, WithFileUploads, WithoutUrlPagination;

    // Propriedades para identificação
    public $courseId;
    public $course;
    public $membroAtual;

    // Propriedades para listagem
    public $classes = [];

    // Propriedades para métricas
    public $totalTurmas = 0;
    public $turmasAtivas = 0;
    public $turmasPlanejadas = 0;
    public $turmasConcluidas = 0;

    // Propriedades para modal
    public $showModal = false;
    public $editingClass = null;
    public $classSelecionada = null;
    public $isEditing = false;

    // Propriedades do formulário
    #[Rule('required|string|max:255')]
    public $nome = '';

    #[Rule('nullable|string|max:500')]
    public $codigo = '';

    #[Rule('nullable|date')]
    public $data_inicio = '';

    #[Rule('nullable|date|after_or_equal:data_inicio')]
    public $data_fim = '';

    #[Rule('nullable|integer|min:0|max:6')]
    public $dia_semana = '';

    #[Rule('nullable|date_format:H:i')]
    public $hora_inicio = '';

    #[Rule('nullable|date_format:H:i|after:hora_inicio')]
    public $hora_fim = '';

    #[Rule('nullable|string|max:255')]
    public $local = '';

    #[Rule('nullable|integer|min:1')]
    public $vagas_maximo = '';

    #[Rule('required|in:planejado,ativo,concluido,suspenso,cancelado')]
    public $status = 'planejado';

    #[Rule('nullable|exists:users,id')]
    public $instrutor_id = '';

    // Propriedades para filtros
    public $search = '';
    public $selectedStatus = '';
    public $selectedCourse = '';

    public function mount($course = null)
    {
        $this->courseId = $course;
        $this->carregarMembroAtual();
        $this->carregarCourse();
        $this->carregarClasses();
        $this->carregarMetricas();
    }

    protected function carregarMembroAtual()
    {
        $this->membroAtual = \App\Models\Igrejas\IgrejaMembro::where('user_id', Auth::id())
            ->where('status', 'ativo')
            ->where('cargo', '!=', 'membro')
            ->first();

        if (!$this->membroAtual) {
            abort(403, 'Acesso negado. Apenas líderes ativos podem acessar as turmas.');
        }
    }

    protected function carregarCourse()
    {
        if ($this->courseId) {
            $this->course = Curso::where('id', $this->courseId)
                ->where('igreja_id', Auth::user()->getIgrejaId())
                ->first();

            if (!$this->course) {
                abort(404, 'Curso não encontrado.');
            }
        }
    }

    protected function carregarClasses()
    {
        $query = CursoTurma::with(['instrutor', 'matriculasAtivas'])
            ->whereHas('curso', function($q) {
                $q->where('igreja_id', Auth::user()->getIgrejaId());
            });

        // Filtrar por curso específico se informado
        if ($this->courseId) {
            $query->where('curso_id', $this->courseId);
        }

        // Aplicar filtros
        if ($this->search) {
            $query->where('nome', 'ilike', '%' . $this->search . '%');
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        if ($this->selectedCourse) {
            $query->where('curso_id', $this->selectedCourse);
        }

        $this->classes = $query->orderBy('created_at', 'desc')->get();
    }

    protected function carregarMetricas()
    {
        $query = CursoTurma::whereHas('curso', function($q) {
            $q->where('igreja_id', Auth::user()->getIgrejaId());
        });

        // Filtrar por curso específico se informado
        if ($this->courseId) {
            $query->where('curso_id', $this->courseId);
        }

        $classes = $query->get();

        $this->totalTurmas = $classes->count();
        $this->turmasAtivas = $classes->where('status', 'ativo')->count();
        $this->turmasPlanejadas = $classes->where('status', 'planejado')->count();
        $this->turmasConcluidas = $classes->where('status', 'concluido')->count();
    }

    public function openModal($classId = null)
    {
        $this->resetModal();

        if ($classId) {
            $class = CursoTurma::find($classId);

            if (!$class || $class->curso->igreja_id !== Auth::user()->getIgrejaId()) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Turma não encontrada.'
                ]);
                return;
            }
            if ($class) {

                $this->classSelecionada = $class;
                $this->nome = $class->nome;
                $this->codigo = $class->codigo;
                $this->data_inicio = $class->data_inicio?->format('Y-m-d');
                $this->data_fim = $class->data_fim?->format('Y-m-d');
                $this->dia_semana = $class->dia_semana;
                $this->hora_inicio = $class->hora_inicio?->format('H:i');
                $this->hora_fim = $class->hora_fim?->format('H:i');
                $this->local = $class->local;
                $this->vagas_maximo = $class->vagas_maximo;
                $this->status = $class->status;
                $this->instrutor_id = $class->instrutor_id;
                $this->editingClass = $class;
                $this->isEditing = true;
            }

        } else {

            $this->resetModal();
            $this->editingClass = null;
            $this->isEditing = false;
        }

        $this->showModal = true;
        $this->dispatch('open-class-modal');
    }

    #[On('open-class-modal')]
    public function handleOpenModal($classId = null)
    {
        $this->openModal($classId);
    }

    public function salvarClass()
    {
        $this->validate();

        if ($this->isEditing) {
            $this->classSelecionada->update([
                'nome' => $this->nome,
                'codigo' => $this->codigo,
                'data_inicio' => $this->data_inicio,
                'data_fim' => $this->data_fim,
                'dia_semana' => $this->dia_semana,
                'hora_inicio' => $this->hora_inicio,
                'hora_fim' => $this->hora_fim,
                'local' => $this->local,
                'vagas_maximo' => $this->vagas_maximo,
                'status' => $this->status,
                'instrutor_id' => $this->instrutor_id,
            ]);

            $mensagem = 'Turma atualizada com sucesso!';
        } else {
            CursoTurma::create([
                'curso_id' => $this->courseId ?: $this->selectedCourse,
                'nome' => $this->nome,
                'codigo' => $this->codigo,
                'data_inicio' => $this->data_inicio,
                'data_fim' => $this->data_fim,
                'dia_semana' => $this->dia_semana,
                'hora_inicio' => $this->hora_inicio,
                'hora_fim' => $this->hora_fim,
                'local' => $this->local,
                'vagas_maximo' => $this->vagas_maximo,
                'vagas_ocupadas' => 0,
                'status' => $this->status,
                'instrutor_id' => $this->instrutor_id,
            ]);

            $mensagem = 'Turma criada com sucesso!';
        }

        $this->carregarClasses();
        $this->carregarMetricas();
        $this->closeModal();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $mensagem
        ]);
    }

    public function deleteClass($classId)
    {
        $class = CursoTurma::find($classId);

        if (!$class || $class->curso->igreja_id !== Auth::user()->getIgrejaId()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Turma não encontrada.'
            ]);
            return;
        }

        // Verificar se tem matrículas ativas
        if ($class->matriculasAtivas()->count() > 0) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Não é possível excluir uma turma que possui matrículas ativas.'
            ]);
            return;
        }

        $class->delete();
        $this->carregarClasses();
        $this->carregarMetricas();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Turma excluída com sucesso!'
        ]);
    }

    public function viewStudents($classId)
    {
        return redirect()->route('churches.courses.registration', ['class' => $classId]);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetModal();
    }

    public function viewSchedule($classId)
    {
        // Lógica para visualizar cronograma
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Funcionalidade de cronograma em desenvolvimento.'
        ]);
    }

    protected function resetModal()
    {
        $this->classSelecionada = null;
        $this->nome = '';
        $this->codigo = '';
        $this->data_inicio = '';
        $this->data_fim = '';
        $this->dia_semana = '';
        $this->hora_inicio = '';
        $this->hora_fim = '';
        $this->local = '';
        $this->vagas_maximo = '';
        $this->status = 'planejado';
        $this->instrutor_id = '';
        $this->resetValidation();
    }

    public function updatingSearch()
    {
        $this->carregarClasses();
    }

    public function updatingSelectedStatus()
    {
        $this->carregarClasses();
    }

    public function updatingSelectedCourse()
    {
        $this->carregarClasses();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedStatus = '';
        $this->selectedCourse = '';
        $this->carregarClasses();
    }

    public function setStatusFilter($status)
    {
        $this->selectedStatus = $status;
        $this->carregarClasses();
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'ativo' => 'success',
            'planejado' => 'warning',
            'concluido' => 'info',
            'suspenso' => 'secondary',
            'cancelado' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusLabel($status)
    {
        return match($status) {
            'planejado' => 'Planejada',
            'ativo' => 'Ativa',
            'concluido' => 'Concluída',
            'suspenso' => 'Suspensa',
            'cancelado' => 'Cancelada',
            default => 'Não definido'
        };
    }

    public function getDiaSemanaLabel($dia)
    {
        return CursoTurma::DIAS_SEMANA[$dia] ?? 'Não definido';
    }

    public function render()
    {
        return view('church.courses.course-class', [
            'totalTurmas' => $this->totalTurmas,
            'turmasAtivas' => $this->turmasAtivas,
            'turmasPlanejadas' => $this->turmasPlanejadas,
            'turmasConcluidas' => $this->turmasConcluidas,
            'courses' => $this->courseId ? collect([$this->course]) : Curso::where('igreja_id', Auth::user()->getIgrejaId())->get(),
        ]);
    }
}

