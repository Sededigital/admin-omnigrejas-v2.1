<?php

namespace App\Livewire\Church\Courses;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use App\Models\Cursos\CursoCertificado;
use App\Models\Cursos\Curso;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;

#[Title('Certificados de Cursos | Portal da Igreja')]
#[Layout('components.layouts.app')]
class Certificates extends Component
{
    use WithPagination, WithFileUploads, WithoutUrlPagination;

    // Propriedades para identificação
    public $membroAtual;

    // Propriedades para métricas
    public $totalCertificados = 0;
    public $certificadosEmitidos = 0;
    public $certificadosPendentes = 0;
    public $certificadosCancelados = 0;

    // Propriedades para listagem
    public $certificates = [];

    // Propriedades para modal
    public $isEditing = false;
    public $certificateSelecionado = null;

    // Propriedades do formulário
    #[Rule('required|exists:curso_matriculas,id')]
    public $matricula_id = '';

    #[Rule('nullable|string|max:100')]
    public $numero_certificado = '';

    #[Rule('nullable|date')]
    public $data_emissao = '';

    #[Rule('nullable|date')]
    public $data_conclusao = '';

    #[Rule('nullable|numeric|min:0|max:100')]
    public $frequencia_final = '';

    #[Rule('nullable|string|max:255')]
    public $template_usado = '';

    #[Rule('nullable|string|max:255')]
    public $codigo_verificacao = '';

    #[Rule('nullable|date')]
    public $valido_ate = '';

    // Propriedades para filtros
    public $search = '';
    public $selectedStatus = '';
    public $selectedCourse = '';
    public $validationCode = '';
    public $validationResult = null;

    public function mount()
    {
        $this->carregarMembroAtual();
        $this->carregarCertificates();
        $this->carregarMetricas();
    }

    protected function carregarMembroAtual()
    {
        $this->membroAtual = \App\Models\Igrejas\IgrejaMembro::where('user_id', Auth::id())
            ->where('status', 'ativo')
            ->where('cargo', '!=', 'membro')
            ->first();

        if (!$this->membroAtual) {
            abort(403, 'Acesso negado. Apenas líderes ativos podem acessar os certificados.');
        }
    }

    protected function carregarCertificates()
    {
        $query = CursoCertificado::with(['matricula.membro.user', 'matricula.turma.curso'])
            ->whereHas('matricula.turma.curso', function($q) {
                $q->where('igreja_id', Auth::user()->getIgrejaId());
            });

        // Aplicar filtros
        if ($this->search) {
            $query->whereHas('matricula.membro.user', function($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->selectedStatus) {
            if ($this->selectedStatus === 'emitido') {
                $query->whereNotNull('data_emissao');
            } elseif ($this->selectedStatus === 'pendente') {
                $query->whereNull('data_emissao');
            } elseif ($this->selectedStatus === 'cancelado') {
                $query->whereNotNull('valido_ate')
                      ->where('valido_ate', '<', now());
            }
        }

        if ($this->selectedCourse) {
            $query->whereHas('matricula.turma', function($q) {
                $q->where('curso_id', $this->selectedCourse);
            });
        }

        $this->certificates = $query->orderBy('created_at', 'desc')->get();
    }

    protected function carregarMetricas()
    {
        $query = CursoCertificado::whereHas('matricula.turma.curso', function($q) {
            $q->where('igreja_id', Auth::user()->getIgrejaId());
        });

        $certificates = $query->get();

        $this->totalCertificados = $certificates->count();
        $this->certificadosEmitidos = $certificates->whereNotNull('data_emissao')->count();
        $this->certificadosPendentes = $certificates->whereNull('data_emissao')->count();
        $this->certificadosCancelados = $certificates->filter(function($cert) {
            return $cert->valido_ate && $cert->valido_ate->isPast();
        })->count();
    }

    public function openModal($certificateId = null)
    {
        $this->resetModal();

        if ($certificateId) {
            $certificate = CursoCertificado::find($certificateId);

            if (!$certificate || $certificate->matricula->turma->curso->igreja_id !== Auth::user()->getIgrejaId()) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Certificado não encontrado.'
                ]);
                return;
            }

            $this->certificateSelecionado = $certificate;
            $this->matricula_id = $certificate->matricula_id;
            $this->numero_certificado = $certificate->numero_certificado;
            $this->data_emissao = $certificate->data_emissao?->format('Y-m-d');
            $this->data_conclusao = $certificate->data_conclusao?->format('Y-m-d');
            $this->frequencia_final = $certificate->frequencia_final;
            $this->template_usado = $certificate->template_usado;
            $this->codigo_verificacao = $certificate->codigo_verificacao;
            $this->valido_ate = $certificate->valido_ate?->format('Y-m-d');
            $this->isEditing = true;
        } else {
            $this->data_emissao = now()->format('Y-m-d');
            $this->isEditing = false;
        }

        $this->dispatch('open-certificate-modal');
    }

    #[On('open-certificate-modal')]
    public function handleOpenModal($certificateId = null)
    {
        $this->openModal($certificateId);
    }

    public function salvarCertificate()
    {
        $this->validate();

        if ($this->isEditing) {
            $this->certificateSelecionado->update([
                'matricula_id' => $this->matricula_id,
                'numero_certificado' => $this->numero_certificado,
                'data_emissao' => $this->data_emissao,
                'data_conclusao' => $this->data_conclusao,
                'frequencia_final' => $this->frequencia_final,
                'template_usado' => $this->template_usado,
                'codigo_verificacao' => $this->codigo_verificacao,
                'valido_ate' => $this->valido_ate,
            ]);

            $mensagem = 'Certificado atualizado com sucesso!';
        } else {
            // Verificar se já existe certificado para esta matrícula
            $existing = CursoCertificado::where('matricula_id', $this->matricula_id)->first();

            if ($existing) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Já existe um certificado para esta matrícula.'
                ]);
                return;
            }

            // Verificar se a matrícula está apta
            $matricula = \App\Models\Cursos\CursoMatricula::find($this->matricula_id);
            if (!$matricula || !$matricula->isApto()) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'A matrícula deve estar apta para emitir certificado.'
                ]);
                return;
            }

            CursoCertificado::create([
                'matricula_id' => $this->matricula_id,
                'numero_certificado' => $this->numero_certificado,
                'data_emissao' => $this->data_emissao,
                'data_conclusao' => $this->data_conclusao,
                'frequencia_final' => $this->frequencia_final,
                'template_usado' => $this->template_usado,
                'codigo_verificacao' => $this->codigo_verificacao,
                'valido_ate' => $this->valido_ate,
            ]);

            // Marcar certificado como emitido na matrícula
            $matricula->emitirCertificado();

            $mensagem = 'Certificado emitido com sucesso!';
        }

        $this->carregarCertificates();
        $this->carregarMetricas();
        $this->dispatch('close-certificate-modal');

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $mensagem
        ]);
    }

    public function revokeCertificate($certificateId)
    {
        $certificate = CursoCertificado::find($certificateId);

        if (!$certificate || $certificate->matricula->turma->curso->igreja_id !== Auth::user()->getIgrejaId()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Certificado não encontrado.'
            ]);
            return;
        }

        $certificate->revogar();
        $this->carregarCertificates();
        $this->carregarMetricas();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Certificado revogado com sucesso!'
        ]);
    }

    public function downloadCertificate($certificateId)
    {
        // Lógica para download do PDF
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Funcionalidade de download em desenvolvimento.'
        ]);
    }

    public function previewCertificate($certificateId)
    {
        // Lógica para preview
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Funcionalidade de preview em desenvolvimento.'
        ]);
    }

    public function validateCertificate()
    {
        $this->validate([
            'validationCode' => 'required|string'
        ]);

        $certificate = CursoCertificado::where('codigo_verificacao', $this->validationCode)->first();

        if ($certificate) {
            $this->validationResult = [
                'valid' => $certificate->isValido(),
                'member' => $certificate->membro->user->name,
                'course' => $certificate->curso->nome,
                'date' => $certificate->data_emissao->format('d/m/Y'),
                'message' => $certificate->isValido() ? 'Certificado válido' : 'Certificado expirado ou inválido'
            ];
        } else {
            $this->validationResult = [
                'valid' => false,
                'message' => 'Código de verificação não encontrado'
            ];
        }
    }

    protected function resetModal()
    {
        $this->certificateSelecionado = null;
        $this->matricula_id = '';
        $this->numero_certificado = '';
        $this->data_emissao = '';
        $this->data_conclusao = '';
        $this->frequencia_final = '';
        $this->template_usado = '';
        $this->codigo_verificacao = '';
        $this->valido_ate = '';
        $this->resetValidation();
    }

    public function updatingSearch()
    {
        $this->carregarCertificates();
    }

    public function updatingSelectedStatus()
    {
        $this->carregarCertificates();
    }

    public function updatingSelectedCourse()
    {
        $this->carregarCertificates();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedStatus = '';
        $this->selectedCourse = '';
        $this->carregarCertificates();
    }

    public function setStatusFilter($status)
    {
        $this->selectedStatus = $status;
        $this->carregarCertificates();
    }

    public function getStatusBadgeClass($status)
    {
        if ($status === 'emitido') {
            return 'success';
        } elseif ($status === 'pendente') {
            return 'warning';
        } elseif ($status === 'cancelado') {
            return 'danger';
        }
        return 'secondary';
    }

    public function getStatusLabel($status)
    {
        if ($status === 'emitido') {
            return 'Emitido';
        } elseif ($status === 'pendente') {
            return 'Pendente';
        } elseif ($status === 'cancelado') {
            return 'Cancelado';
        }
        return 'Não definido';
    }

    public function render()
    {
        return view('church.courses.certificates', [
            'totalCertificados' => $this->totalCertificados,
            'certificadosEmitidos' => $this->certificadosEmitidos,
            'certificadosPendentes' => $this->certificadosPendentes,
            'certificadosCancelados' => $this->certificadosCancelados,
            'courses' => Curso::where('igreja_id', Auth::user()->getIgrejaId())->get(),
        ]);
    }
}

