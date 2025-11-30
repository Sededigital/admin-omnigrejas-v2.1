<?php

namespace App\Livewire\Church\Engagement;

use App\Models\Outros\EnqueteDenuncia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Inquéritos | Portal da Igreja')]
#[Layout('components.layouts.app')]
class Polls extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // ========================================
    // PROPRIEDADES PARA NAVEGAÇÃO ENTRE ABAS
    // ========================================
    public $abaAtiva = 'denuncias'; // denuncias
    public $abasDisponiveis = ['denuncias'];

    // ========================================
    // PROPRIEDADES PARA DENÚNCIAS
    // ========================================
    public $denunciaSelecionada = null;
    public $isEditingDenuncia = false;

    // Filtros para denúncias
    public $filtroDenunciaBusca = '';
    public $filtroDenunciaDataInicio = '';
    public $filtroDenunciaDataFim = '';

    // ========================================
    // PROPRIEDADES GERAIS
    // ========================================
    public $igrejaAtual;
    public $confirmacaoExclusao = false;
    public $itemParaExcluir = null;
    public $tipoExclusao = ''; // denuncia

    // Propriedades para modal de confirmação genérico
    public $confirmacaoAcao = false;
    public $acaoParaConfirmar = ''; // excluir_denuncia
    public $itemParaConfirmar = null;
    public $mensagemConfirmacao = '';

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'confirmarExclusao' => 'confirmarExclusao',
        'confirmarAcao' => 'confirmarAcao',
        'cancelarAcao' => 'cancelarAcao'
    ];

    public function mount()
    {
        $this->carregarIgrejaAtual();
    }

    protected function carregarIgrejaAtual()
    {
        $user = Auth::user();
        $this->igrejaAtual = $user->getIgreja();

        if (!$this->igrejaAtual) {
            abort(403, 'Usuário não está associado a nenhuma igreja.');
        }
    }

    public function carregarDadosAbaAtiva()
    {
        // Método mantido para compatibilidade, mas dados são carregados no render()
        $this->resetPage();
    }

    // ========================================
    // MÉTODOS PARA DENÚNCIAS
    // ========================================

    protected function getDenunciasQuery()
    {
        $query = EnqueteDenuncia::with(['igreja', 'criadoPor'])
            ->where('igreja_id', $this->igrejaAtual->id);

        if ($this->filtroDenunciaBusca) {
            $query->where(function($q) {
                $q->where('texto', 'ILIKE', '%' . $this->filtroDenunciaBusca . '%')
                  ->orWhereHas('criadoPor', function($userQuery) {
                      $userQuery->where('name', 'ILIKE', '%' . $this->filtroDenunciaBusca . '%');
                  });
            });
        }

        if ($this->filtroDenunciaDataInicio) {
            $query->whereDate('data', '>=', $this->filtroDenunciaDataInicio);
        }

        if ($this->filtroDenunciaDataFim) {
            $query->whereDate('data', '<=', $this->filtroDenunciaDataFim);
        }

        return $query->orderBy('data', 'desc');
    }

    public function abrirModalDenuncia($denunciaId = null)
    {
        if ($denunciaId) {
            $denuncia = EnqueteDenuncia::find($denunciaId);

            if (!$denuncia || $denuncia->igreja_id !== $this->igrejaAtual->id) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Denúncia não encontrada.'
                ]);
                return;
            }

            $this->denunciaSelecionada = $denuncia;
            $this->isEditingDenuncia = true;

            $this->dispatch('open-denuncia-modal');
        }
    }

    public function excluirDenuncia($denunciaId)
    {
        try {
            $denuncia = EnqueteDenuncia::find($denunciaId);

            if (!$denuncia || $denuncia->igreja_id !== $this->igrejaAtual->id) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Denúncia não encontrada.'
                ]);
                return;
            }

            $denuncia->delete();

            $this->dispatch('close-confirmacao-modal');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Denúncia excluída com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir denúncia: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'igreja_id' => $this->igrejaAtual->id ?? null,
                'denuncia_id' => $denunciaId
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir denúncia. Tente novamente.'
            ]);
        }
    }

    // ========================================
    // MÉTODOS PARA MODAL DE CONFIRMAÇÃO GENÉRICO
    // ========================================

    public function abrirModalConfirmacao($acao, $itemId, $mensagem = null)
    {
        $this->acaoParaConfirmar = $acao;
        $this->itemParaConfirmar = $itemId;

        // Definir mensagem padrão baseada na ação
        if (!$mensagem) {
            switch ($acao) {
                case 'excluir_denuncia':
                    $mensagem = 'Tem certeza que deseja excluir esta denúncia? Esta ação não pode ser desfeita.';
                    break;
                default:
                    $mensagem = 'Tem certeza que deseja executar esta ação?';
            }
        }

        $this->mensagemConfirmacao = $mensagem;
        $this->confirmacaoAcao = true;

        $this->dispatch('open-confirmacao-modal', $mensagem);
    }

    public function confirmarAcao()
    {
        if (!$this->acaoParaConfirmar || !$this->itemParaConfirmar) {
            return;
        }

        switch ($this->acaoParaConfirmar) {
            case 'excluir_denuncia':
                $this->excluirDenuncia($this->itemParaConfirmar);
                break;
        }

        $this->cancelarAcao();
    }

    public function cancelarAcao()
    {
        $this->acaoParaConfirmar = '';
        $this->itemParaConfirmar = null;
        $this->mensagemConfirmacao = '';
        $this->confirmacaoAcao = false;

        $this->dispatch('close-confirmacao-modal');
    }

    // ========================================
    // LISTENERS PARA FILTROS
    // ========================================

    public function updatedFiltroDenunciaBusca()
    {
        $this->resetPage();
    }

    public function updatedFiltroDenunciaDataInicio()
    {
        $this->resetPage();
    }

    public function updatedFiltroDenunciaDataFim()
    {
        $this->resetPage();
    }

    // ========================================
    // PROPRIEDADES COMPUTADAS
    // ========================================

    public function getAbasDisponiveisInfoProperty()
    {
        $abasInfo = [
            'denuncias' => [
                'titulo' => 'Denúncias',
                'icone' => 'fas fa-exclamation-triangle',
                'cor' => 'danger',
                'disponivel' => true
            ],
        ];

        return $abasInfo;
    }

    public function render()
    {
        // Buscar dados apenas da aba ativa
        $dados = [];

        if ($this->abaAtiva === 'denuncias') {
            $denunciasQuery = $this->getDenunciasQuery();
            $dados['denuncias'] = $denunciasQuery->paginate(15);
        }

        return view('church.engagement.polls', array_merge($dados, [
            'abasDisponiveisInfo' => $this->abasDisponiveisInfo,
        ]));
    }
}
