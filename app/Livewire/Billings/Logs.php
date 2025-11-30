<?php

namespace App\Livewire\Billings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Igrejas\Igreja;
use Livewire\Attributes\Title;
use App\Models\Billings\Pacote;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use App\Models\Billings\AssinaturaLog;

#[Title('Logs de Assinaturas')]
#[Layout('components.layouts.app')]
class Logs extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $acaoFilter = '';
    public $igrejaFilter = '';
    public $pacoteFilter = '';
    public $dataInicio = '';
    public $dataFim = '';

    protected $listeners = ['refreshLogs' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedAcaoFilter()
    {
        $this->resetPage();
    }

    public function updatedIgrejaFilter()
    {
        $this->resetPage();
    }

    public function updatedPacoteFilter()
    {
        $this->resetPage();
    }

    public function updatedDataInicio()
    {
        $this->resetPage();
    }

    public function updatedDataFim()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->acaoFilter = '';
        $this->igrejaFilter = '';
        $this->pacoteFilter = '';
        $this->dataInicio = '';
        $this->dataFim = '';
        $this->resetPage();
    }

    public function getLogs()
    {
        try {
            $query = AssinaturaLog::with(['igreja', 'pacote', 'usuario']);

            if ($this->search) {
                $query->where(function ($q) {
                    // Busca por igreja (se existir)
                    $q->whereHas('igreja', function ($subQ) {
                        $subQ->where('nome', 'like', '%' . $this->search . '%')
                             ->orWhere('nif', 'like', '%' . $this->search . '%');
                    })
                    // Busca por pacote (se existir)
                    ->orWhereHas('pacote', function ($subQ) {
                        $subQ->where('nome', 'like', '%' . $this->search . '%');
                    })
                    // Busca na descrição
                    ->orWhere('descricao', 'like', '%' . $this->search . '%')
                    // Busca nos detalhes JSON
                    ->orWhere('detalhes->motivo', 'like', '%' . $this->search . '%')
                    ->orWhere('detalhes->referencia', 'like', '%' . $this->search . '%');
                });
            }

            if ($this->acaoFilter) {
                $query->where('acao', $this->acaoFilter);
            }

            if ($this->igrejaFilter) {
                $query->where('igreja_id', $this->igrejaFilter);
            }

            if ($this->pacoteFilter) {
                $query->where('pacote_id', $this->pacoteFilter);
            }

            if ($this->dataInicio) {
                $query->whereDate('data_acao', '>=', $this->dataInicio);
            }

            if ($this->dataFim) {
                $query->whereDate('data_acao', '<=', $this->dataFim);
            }

            return $query->orderBy('data_acao', 'desc')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            // Log do erro para debug (usando logger do Laravel)
            logger('Erro ao carregar logs de assinaturas: ' . $e->getMessage());

            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao carregar logs. Tente novamente.'
            ]);

            // Retorna uma coleção vazia em caso de erro
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function getAcaoOptions()
    {
        return [
            'criado' => 'Criado',
            'upgrade' => 'Upgrade',
            'downgrade' => 'Downgrade',
            'cancelado' => 'Cancelado',
            'renovado' => 'Renovado',
            'pagamento' => 'Pagamento',
            'expirado' => 'Expirado',
        ];
    }

    public function getAcaoBadgeClass($acao)
    {
        return match($acao) {
            'criado', 'renovado' => 'success',
            'cancelado', 'expirado' => 'danger',
            'upgrade' => 'info',
            'downgrade' => 'warning',
            'pagamento' => 'primary',
            default => 'secondary'
        };
    }

    public function deleteLog($logId)
    {
        try {
            $log = AssinaturaLog::find($logId);
            if ($log) {
                $log->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Log excluído com sucesso!'
                ]);
                $this->dispatch('refreshLogs');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Log não encontrado!'
                ]);
            }
        } catch (\Exception $e) {
            logger('Erro ao excluir log: ' . $e->getMessage());
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir log. Tente novamente.'
            ]);
        }
    }

    public function render()
    {
        return view('billings.logs', [
            'logs' => $this->getLogs(),
            'igrejas' => Igreja::orderBy('nome')->get(),
            'pacotes' => Pacote::orderBy('nome')->get(),
            'acaoOptions' => $this->getAcaoOptions(),
        ]);
    }
}
