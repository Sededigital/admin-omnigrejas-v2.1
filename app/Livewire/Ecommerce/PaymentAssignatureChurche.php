<?php

namespace App\Livewire\Ecommerce;

use App\Models\Billings\PagamentoAssinaturaIgreja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use SweetAlert2\Laravel\Traits\WithSweetAlert;
use App\Helpers\SupabaseHelper;


#[Title('Status dos Pagamentos - OmnIgrejas E-commerce')]
#[Layout('components.layouts.subscription')]
class PaymentAssignatureChurche extends Component
{
    use WithPagination;
    use WithSweetAlert;

    // Filtros e busca
    public $search = '';
    public $statusFilter = '';
    public $metodoFilter = '';
    public $perPage = 10;

    // Controle de igrejas (se múltiplas)
    public $igrejaSelecionada;
    public $igrejasDisponiveis;

    // Modal de detalhes
    public $showModal = false;
    public $pagamentoSelecionado;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        // Carregar igrejas disponíveis do usuário
        $this->carregarIgrejasDisponiveis();

        // Definir igreja padrão (igreja com pagamento mais recente)
        if ($this->igrejasDisponiveis && $this->igrejasDisponiveis->isNotEmpty()) {
            $this->selecionarIgrejaPadrao();
        }
    }

    private function carregarIgrejasDisponiveis()
    {
        $user = Auth::user();

        // Buscar todas as igrejas onde o usuário é membro ativo
        $this->igrejasDisponiveis = collect($user->membros()
            ->where('status', 'ativo')
            ->with('igreja')
            ->get()
            ->map(function($membro) {
                return [
                    'id' => $membro->igreja->id,
                    'nome' => $membro->igreja->nome,
                    'sigla' => $membro->igreja->sigla,
                    'categoria' => $membro->igreja->categoria->nome ?? 'Geral',
                    'cargo' => $membro->cargo,
                    'principal' => $membro->principal
                ];
            })
            ->sortByDesc('principal') // Igrejas principais primeiro
            ->values());
    }

    private function selecionarIgrejaPadrao()
    {
        // Primeiro, tentar encontrar a igreja com o pagamento mais recente
        $user = Auth::user();
        $igrejaIds = $user->membros()->where('status', 'ativo')->pluck('igreja_id');

        $pagamentoMaisRecente = PagamentoAssinaturaIgreja::whereIn('igreja_id', $igrejaIds)
            ->orderBy('data_pagamento', 'desc')
            ->first();

        if ($pagamentoMaisRecente) {
            $this->igrejaSelecionada = $pagamentoMaisRecente->igreja_id;
        } else {
            // Se não há pagamentos, selecionar a primeira igreja
            $this->igrejaSelecionada = $this->igrejasDisponiveis->first()['id'];
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedMetodoFilter()
    {
        $this->resetPage();
    }

    public function updatedIgrejaSelecionada()
    {
        $this->resetPage();
    }

    public function verDetalhes($pagamentoId)
    {
        $this->pagamentoSelecionado = PagamentoAssinaturaIgreja::with(['igreja', 'pacote', 'confirmadoPor', 'criadoPor'])
            ->find($pagamentoId);

        if ($this->pagamentoSelecionado) {
            $this->showModal = true;
        }
    }

    public function fecharModal()
    {
        $this->showModal = false;
        $this->pagamentoSelecionado = null;
    }

    public function getPagamentos()
    {
        $query = PagamentoAssinaturaIgreja::with(['igreja', 'pacote', 'confirmadoPor'])
            ->where('igreja_id', $this->igrejaSelecionada)
            ->orderBy('data_pagamento', 'desc'); // Ordenar por data de pagamento mais recente

        // Aplicar filtros
        if ($this->search) {
            $query->where(function($q) {
                $q->where('referencia', 'like', '%' . $this->search . '%')
                  ->orWhere('observacoes', 'like', '%' . $this->search . '%')
                  ->orWhereHas('pacote', function($pq) {
                      $pq->where('nome', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->metodoFilter) {
            $query->where('metodo_pagamento', $this->metodoFilter);
        }

        return $query->paginate($this->perPage);
    }

    public function getEstatisticas()
    {
        $pagamentos = PagamentoAssinaturaIgreja::where('igreja_id', $this->igrejaSelecionada);

        return [
            'total' => (clone $pagamentos)->count(),
            'pendentes' => (clone $pagamentos)->where('status', 'pendente')->count(),
            'confirmados' => (clone $pagamentos)->where('status', 'confirmado')->count(),
            'rejeitados' => (clone $pagamentos)->where('status', 'rejeitado')->count(),
            'expirados' => (clone $pagamentos)->where('status', 'expirado')->count(),
            'valor_total' => (clone $pagamentos)->sum('valor'),
            'valor_confirmado' => (clone $pagamentos)->where('status', 'confirmado')->sum('valor'),
        ];
    }

    public function getEstatisticasGerais()
    {
        // Estatísticas gerais de todas as igrejas do usuário
        $user = Auth::user();
        $igrejaIds = $user->membros()->where('status', 'ativo')->pluck('igreja_id');

        $pagamentos = PagamentoAssinaturaIgreja::whereIn('igreja_id', $igrejaIds);

        return [
            'total_geral' => $pagamentos->count(),
            'confirmados_geral' => $pagamentos->where('status', 'confirmado')->count(),
            'pendentes_geral' => $pagamentos->where('status', 'pendente')->count(),
            'valor_total_geral' => $pagamentos->sum('valor'),
        ];
    }

    public function verDetalhesPagamento($pagamentoId)
    {
        $pagamento = PagamentoAssinaturaIgreja::find($pagamentoId);

        if (!$pagamento) {
            return;
        }

        // Determinar cor do status
        $statusColorMap = [
            'confirmado' => 'success',
            'pendente' => 'warning',
            'rejeitado' => 'danger',
            'expirado' => 'secondary',
        ];
        $statusColor = $statusColorMap[$pagamento->status] ?? 'secondary';

        $this->swalFire([
            'title' => '<i class="fas fa-receipt text-info me-2"></i>Detalhes do Pagamento',
            'html' => '
                <div class="text-center">
                    <div class="card border-0 bg-light mb-0">
                        <div class="card-body p-4">
                            <!-- Pacote e Valor -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="text-center">
                                        <i class="fas fa-box text-info fa-2x mb-2"></i>
                                        <h6 class="text-info fw-bold mb-1">Pacote</h6>
                                        <p class="mb-0 fw-semibold">' . htmlspecialchars($pagamento->pacote_nome ?: $pagamento->pacote->nome) . '</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <i class="fas fa-money-bill-wave text-success fa-2x mb-2"></i>
                                        <h6 class="text-success fw-bold mb-1">Valor</h6>
                                        <p class="mb-0 fw-bold text-success">' . $pagamento->getValorFormatado() . '</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Método e Status -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="text-center">
                                        <i class="fas fa-credit-card text-info fa-2x mb-2"></i>
                                        <h6 class="text-info fw-bold mb-1">Método</h6>
                                        <p class="mb-0">' . $pagamento->getMetodoFormatado() . '</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <i class="fas fa-info-circle text-' . $statusColor . ' fa-2x mb-2"></i>
                                        <h6 class="text-' . $statusColor . ' fw-bold mb-1">Status</h6>
                                        <span class="badge bg-' . $statusColor . ' fs-6 px-3 py-2">' . $pagamento->getStatusFormatado() . '</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Referência -->
                            <div class="text-center">
                                <i class="fas fa-hashtag text-muted fa-lg mb-2"></i>
                                <h6 class="text-muted fw-bold mb-2">Referência</h6>
                                <div class="bg-white rounded p-2 border">
                                    <code class="text-dark small">' . htmlspecialchars($pagamento->referencia) . '</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ',
            'showConfirmButton' => false,
            'showCancelButton' => true,
            'cancelButtonText' => '<i class="fas fa-times me-2"></i>Fechar',
            'customClass' => [
                'popup' => 'swal-wide-modal',
                'cancelButton' => 'btn btn-secondary'
            ],
            'buttonsStyling' => false,
            'showClass' => [
                'popup' => 'animate__animated animate__fadeInDown animate__faster'
            ],
            'hideClass' => [
                'popup' => 'animate__animated animate__fadeOutUp animate__faster'
            ]
        ]);
    }


    public function verComprovativo($pagamentoId)
    {
        $pagamento = PagamentoAssinaturaIgreja::find($pagamentoId);

        if (!$pagamento || !$pagamento->temComprovativo()) {
            return;
        }

        $tipo = $pagamento->comprovativo_tipo;
        $isPdf = str_contains($tipo, 'pdf');

        $caminhoRelativo = parse_url($pagamento->comprovativo_url, PHP_URL_PATH);
        $caminhoRelativo = ltrim(str_replace('/storage/', '', $caminhoRelativo), '/');


        if (SupabaseHelper::supabaseAtivo()) {
            // Online Supabase
            $url = Storage::disk('supabase')->url($pagamento->comprovativo_url);
        } else {

         if (SupabaseHelper::supabaseAtivo()) {
                $url = Storage::disk('supabase')->url($caminhoRelativo);
            } else {
                $arquivoFisico = storage_path('app/public/' . $caminhoRelativo);
                if (!file_exists($arquivoFisico)) {
                    return $this->swalFire([
                        'title' => 'Erro',
                        'text' => 'Arquivo não encontrado.',
                        'icon' => 'error'
                    ]);
                }
                $url = rtrim(env('APP_URL'), '/') . '/storage/' . $caminhoRelativo;
            }
              
        }

        $html = '<div class="text-center">';

        if ($isPdf) {
            $html .= '<iframe src="' . htmlspecialchars($url) . '" width="100%" height="400px" style="border: none; border-radius: 8px;"></iframe>';
        } else {
            $html .= '<img src="' . htmlspecialchars($url) . '" class="img-fluid rounded shadow" style="max-height: 400px;" alt="Comprovativo">';
        }

        $html .= '</div>';

        $this->swalFire([
            'title' => '<i class="fas fa-file text-info me-2"></i>Comprovativo',
            'html' => $html,
            'showConfirmButton' => false,
            'showCloseButton' => true,
            'customClass' => [
                'popup' => 'swal-wide-modal'
            ],
            'showClass' => [
                'popup' => 'animate__animated animate__zoomIn animate__faster'
            ],
            'hideClass' => [
                'popup' => 'animate__animated animate__zoomOut animate__faster'
            ]
        ]);
    }


    public function render()
    {
        return view('ecommerce.payment-assignature-churche', [
            'pagamentos' => $this->getPagamentos(),
            'estatisticas' => $this->getEstatisticas(),
            'estatisticasGerais' => $this->getEstatisticasGerais(),
            'igrejasDisponiveis' => $this->igrejasDisponiveis,
        ]);
    }
}
