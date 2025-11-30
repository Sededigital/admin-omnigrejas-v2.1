<?php

namespace App\Livewire\Church\Courses;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use App\Models\Cursos\Curso;
use App\Models\Igrejas\Igreja;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;

#[Title('Cursos | Portal da Igreja')]
#[Layout('components.layouts.app')]
class Courses extends Component
{
    use WithPagination, WithFileUploads, WithoutUrlPagination;

    // Propriedades para identificação
    public $membroAtual;

    // Propriedades para métricas
    public $totalCursos = 0;
    public $cursosAtivos = 0;
    public $cursosPlanejados = 0;
    public $cursosConcluidos = 0;

    public function getCoursesProperty()
    {
        return $this->getCoursesQuery()->paginate(10);
    }

    // Propriedades para modal
    public $showModal = false;
    public $editingCourseId = null;
    public $editingCourse = null;
    public $courseSelecionado = null;

    // Propriedades do formulário
    #[Rule('required|string|max:255')]
    public $nome = '';

    #[Rule('required|in:escola_dominical,preparacao_batismo,curso_membros,lideranca,ministerial,casais,jovens,outro')]
    public $tipo = '';

    #[Rule('nullable|string|max:1000')]
    public $descricao = '';

    #[Rule('nullable|string|max:1000')]
    public $objetivo = '';

    #[Rule('nullable|integer|min:1')]
    public $carga_horaria_total = '';

    #[Rule('nullable|integer|min:1')]
    public $duracao_semanas = '';

    #[Rule('required|in:planejado,ativo,concluido,suspenso,cancelado')]
    public $status = 'planejado';

    #[Rule('nullable|date')]
    public $data_inicio = '';

    #[Rule('nullable|date|after_or_equal:data_inicio')]
    public $data_fim = '';

    #[Rule('nullable|exists:users,id')]
    public $instrutor_principal = '';

    #[Rule('nullable|exists:users,id')]
    public $coordenador = '';

    #[Rule('nullable|integer|min:1')]
    public $vagas_maximo = '';

    #[Rule('boolean')]
    public $certificado_obrigatorio = false;

    #[Rule('nullable|integer|min:0|max:100')]
    public $frequencia_minima = 75;

    // Propriedades para filtros
    public $search = '';
    public $selectedStatus = '';
    public $selectedType = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedStatus' => ['except' => ''],
        'selectedType' => ['except' => '']
    ];

    public function mount()
    {
        $this->carregarMembroAtual();
        $this->carregarMetricas();
    }

    protected function carregarMembroAtual()
    {
        $this->membroAtual = \App\Models\Igrejas\IgrejaMembro::where('user_id', Auth::id())
            ->where('status', 'ativo')
            ->where('cargo', '!=', 'membro')
            ->first();

        if (!$this->membroAtual) {
            abort(403, 'Acesso negado. Apenas líderes ativos podem acessar os cursos.');
        }
    }

    protected function carregarMetricas()
    {
        // Total de cursos
        $this->totalCursos = Curso::where('igreja_id', Auth::user()->getIgrejaId())->count();

        // Cursos ativos
        $this->cursosAtivos = Curso::where('igreja_id', Auth::user()->getIgrejaId())
            ->where('status', 'ativo')
            ->count();

        // Cursos planejados
        $this->cursosPlanejados = Curso::where('igreja_id', Auth::user()->getIgrejaId())
            ->where('status', 'planejado')
            ->count();

        // Cursos concluídos
        $this->cursosConcluidos = Curso::where('igreja_id', Auth::user()->getIgrejaId())
            ->where('status', 'concluido')
            ->count();
    }

    protected function getCoursesQuery()
    {
        $query = Curso::with(['instrutorPrincipal', 'coordenadorUser'])
            ->where('igreja_id', Auth::user()->getIgrejaId());

        // Aplicar filtros
        if ($this->search) {
            $query->where('nome', 'ilike', '%' . $this->search . '%');
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        if ($this->selectedType) {
            $query->where('tipo', $this->selectedType);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus()
    {
        $this->resetPage();
    }

    public function updatingSelectedType()
    {
        $this->resetPage();
    }

    public function openModal($courseId = null)
    {
        $this->resetModal();

        if ($courseId) {
            $course = Curso::find($courseId);

            if (!$course || $course->igreja_id !== Auth::user()->getIgrejaId()) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Curso não encontrado.'
                ]);
                return;
            }

            $this->editingCourseId = $courseId;
            $this->editingCourse = $course;

            // Preencher os campos com os dados do curso
            $this->nome = $course->nome;
            $this->tipo = $course->tipo;
            $this->descricao = $course->descricao;
            $this->objetivo = $course->objetivo;
            $this->carga_horaria_total = $course->carga_horaria_total;
            $this->duracao_semanas = $course->duracao_semanas;
            $this->status = $course->status;
            $this->data_inicio = $course->data_inicio?->format('Y-m-d');
            $this->data_fim = $course->data_fim?->format('Y-m-d');
            $this->instrutor_principal = $course->instrutor_principal;
            $this->coordenador = $course->coordenador;
            $this->vagas_maximo = $course->vagas_maximo;
            $this->certificado_obrigatorio = $course->certificado_obrigatorio;
            $this->frequencia_minima = $course->frequencia_minima;
        } else {
            $this->editingCourseId = null;
            $this->editingCourse = null;
            $this->status = 'planejado';
            $this->certificado_obrigatorio = false;
            $this->frequencia_minima = 75;
        }

        $this->showModal = true;
        $this->dispatch('open-course-modal');
    }
    #[On('open-course-modal')]
    public function handleOpenModal($courseId = null)
    {
        $this->openModal($courseId);
    }

    #[On('edit-course')]
    public function handleEditCourse($courseId)
    {
        $this->openModal($courseId);
    }

    public function salvarCourse()
    {
        $this->validate();

        try {
            if ($this->editingCourseId) {
                // Atualizar curso existente
                $course = Curso::findOrFail($this->editingCourseId);

                $course->update([
                    'nome' => $this->nome,
                    'tipo' => $this->tipo,
                    'descricao' => $this->descricao,
                    'objetivo' => $this->objetivo,
                    'carga_horaria_total' => $this->carga_horaria_total,
                    'duracao_semanas' => $this->duracao_semanas,
                    'status' => $this->status,
                    'data_inicio' => $this->data_inicio,
                    'data_fim' => $this->data_fim,
                    'instrutor_principal' => $this->instrutor_principal,
                    'coordenador' => $this->coordenador,
                    'vagas_maximo' => $this->vagas_maximo,
                    'certificado_obrigatorio' => $this->certificado_obrigatorio,
                    'frequencia_minima' => $this->frequencia_minima,
                ]);

                $mensagem = 'Curso atualizado com sucesso!';
            } else {
                // Criar novo curso
                Curso::create([
                    'igreja_id' => Auth::user()->getIgrejaId(),
                    'nome' => $this->nome,
                    'tipo' => $this->tipo,
                    'descricao' => $this->descricao,
                    'objetivo' => $this->objetivo,
                    'carga_horaria_total' => $this->carga_horaria_total,
                    'duracao_semanas' => $this->duracao_semanas,
                    'status' => $this->status,
                    'data_inicio' => $this->data_inicio,
                    'data_fim' => $this->data_fim,
                    'instrutor_principal' => $this->instrutor_principal,
                    'coordenador' => $this->coordenador,
                    'vagas_maximo' => $this->vagas_maximo,
                    'certificado_obrigatorio' => $this->certificado_obrigatorio,
                    'frequencia_minima' => $this->frequencia_minima,
                    'created_by' => Auth::id(),
                ]);

                $mensagem = 'Curso cadastrado com sucesso!';
            }

            // Recarregar métricas
            $this->carregarMetricas();

            // Fechar modal e recarregar
            $this->dispatch('close-course-modal');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => $mensagem
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao salvar curso: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteCourse($courseId)
    {
        $course = Curso::find($courseId);

        if (!$course || $course->igreja_id !== Auth::user()->getIgrejaId()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Curso não encontrado.'
            ]);
            return;
        }

        // Verificar se tem turmas ativas
        if ($course->turmasAtivas()->count() > 0) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Não é possível excluir um curso que possui turmas ativas.'
            ]);
            return;
        }

        $course->delete();

        // Recarregar métricas
        $this->carregarMetricas();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Curso excluído com sucesso!'
        ]);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetModal();
    }

    public function viewCourse($courseId)
    {
        return redirect()->route('churches.courses.class', ['course' => $courseId]);
    }

    protected function resetModal()
    {
        $this->editingCourseId = null;
        $this->editingCourse = null;
        $this->courseSelecionado = null;
        $this->nome = '';
        $this->tipo = '';
        $this->descricao = '';
        $this->objetivo = '';
        $this->carga_horaria_total = '';
        $this->duracao_semanas = '';
        $this->status = 'planejado';
        $this->data_inicio = '';
        $this->data_fim = '';
        $this->instrutor_principal = '';
        $this->coordenador = '';
        $this->vagas_maximo = '';
        $this->certificado_obrigatorio = false;
        $this->frequencia_minima = 75;
        $this->resetValidation();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedStatus = '';
        $this->selectedType = '';
    }

    public function setStatusFilter($status)
    {
        $this->selectedStatus = $status;
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

    public function render()
    {
        return view('church.courses.courses', [
            'totalCursos' => $this->totalCursos,
            'cursosAtivos' => $this->cursosAtivos,
            'cursosPlanejados' => $this->cursosPlanejados,
            'cursosConcluidos' => $this->cursosConcluidos,
        ]);
    }
}
