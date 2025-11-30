<?php

namespace App\Livewire\Billings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Igrejas\Igreja;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Log;
use App\Models\Billings\AssinaturaCiclo;
use App\Models\Billings\AssinaturaHistorico;
use App\Models\Billings\AssinaturaPagamento;
use App\Models\Billings\AssinaturaPagamentoFalha;

#[Title('Pagamentos de Assinaturas')]
#[Layout('components.layouts.app')]
class Pagamentos extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $statusFilter = '';
    public $metodoFilter = '';
    public $igrejaFilter = '';

    // Abas
    public $activeTab = 'pagamentos';

    // Controle para abrir modal automaticamente
    public $autoOpenModal = false;
    public $modalOpened = false;

    // Controle de campos desabilitados
    public $camposDesabilitados = false;

    // Controle de redirecionamento
    public $veioComParametroUrl = false;


    // Modal properties - Pagamentos
    public $showModal = false;
    public $editingPagamento = null;
    public $assinatura_id = '';
    public $igreja_id = '';
    public $valor = '';
    public $metodo_pagamento = '';
    public $referencia = '';
    public $status = 'pendente';
    public $data_pagamento = '';

    // Ciclos de Cobrança
    public $searchCiclos = '';
    public $statusCicloFilter = '';
    public $showModalCiclo = false;
    public $editingCiclo = null;
    public $ciclo_assinatura_id = '';
    public $ciclo_inicio = '';
    public $ciclo_fim = '';
    public $ciclo_valor = '';
    public $ciclo_status = 'pendente';

    // Falhas de Pagamento
    public $searchFalhas = '';
    public $statusFalhaFilter = '';
    public $showModalFalha = false;
    public $editingFalha = null;
    public $falha_pagamento_id = '';
    public $falha_motivo = '';
    public $falha_resolvido = false;

    protected $rules = [
        'assinatura_id' => 'required|exists:assinatura_historico,id',
        'igreja_id' => 'required|exists:igrejas,id',
        'valor' => 'required|numeric|min:0',
        'metodo_pagamento' => 'required|in:deposito,multicaixa_express,tpa,transferencia,outro',
        'referencia' => 'nullable|string|max:255',
        'status' => 'required|in:pendente,confirmado,falhou,estornado',
        'data_pagamento' => 'nullable|date',
    ];

    protected $messages = [
        'metodo_pagamento.in' => 'O método de pagamento selecionado não é válido.',
        'metodo_pagamento.required' => 'O método de pagamento é obrigatório.',
    ];

    protected $listeners = ['refreshPagamentos' => '$refresh'];

    public function mount($assinatura_id = null)
    {
        Log::info('Pagamentos mount called with assinatura_id: ' . ($assinatura_id ?? 'null'));
        $this->assinatura_id = $assinatura_id;

        // Se recebeu ID da assinatura, preparar para abrir modal automaticamente
        if ($assinatura_id) {
            Log::info('Setting autoOpenModal to true for assinatura_id: ' . $assinatura_id);
            $this->autoOpenModal = true;
            $this->camposDesabilitados = true; // Desabilitar campos quando há parâmetro na URL
            $this->veioComParametroUrl = true; // Marcar que veio com parâmetro na URL
            $this->openModalWithAssinatura($assinatura_id);
        } else {
            Log::info('No assinatura_id provided, modal will not auto-open');
            $this->camposDesabilitados = false; // Campos habilitados em condições normais
            $this->veioComParametroUrl = false; // Não veio com parâmetro
        }
    }

    public function updatingSearch()
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

    public function updatedIgrejaFilter()
    {
        $this->resetPage();
    }

    // Método para atualizar campos automaticamente quando assinatura for selecionada
    public function updatedCicloAssinaturaId()
    {
        if ($this->ciclo_assinatura_id) {
            try {
                $assinatura = AssinaturaHistorico::with(['igreja', 'pacote'])->find($this->ciclo_assinatura_id);

                if ($assinatura) {
                    // Preencher automaticamente as datas de início e fim do plano
                    $this->ciclo_inicio = $assinatura->data_inicio ? $assinatura->data_inicio->format('Y-m-d') : '';
                    $this->ciclo_fim = $assinatura->data_fim ? $assinatura->data_fim->format('Y-m-d') : '';

                    // Preencher o valor da assinatura
                    $this->ciclo_valor = $assinatura->valor ?? 0;

                    // Status padrão para novo ciclo
                    $this->ciclo_status = 'pendente';

                    Log::info('Campos do ciclo preenchidos automaticamente', [
                        'assinatura_id' => $this->ciclo_assinatura_id,
                        'inicio' => $this->ciclo_inicio,
                        'fim' => $this->ciclo_fim,
                        'valor' => $this->ciclo_valor
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Erro ao preencher campos do ciclo automaticamente: ' . $e->getMessage());
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Erro ao carregar dados da assinatura!'
                ]);
            }
        } else {
            // Limpar campos se nenhuma assinatura for selecionada
            $this->ciclo_inicio = '';
            $this->ciclo_fim = '';
            $this->ciclo_valor = '';
            $this->ciclo_status = 'pendente';
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->metodoFilter = '';
        $this->igrejaFilter = '';
        $this->resetPage();
    }

    public function openModal($pagamentoId = null)
    {
        try {


            if ($pagamentoId) {
                $pagamento = AssinaturaPagamento::find($pagamentoId);
                if ($pagamento) {
                    $this->editingPagamento = $pagamento;
                    $this->assinatura_id = $pagamento->assinatura_id;
                    $this->igreja_id = $pagamento->igreja_id;
                    $this->valor = $pagamento->valor;
                    $this->metodo_pagamento = $pagamento->metodo_pagamento;
                    $this->referencia = $pagamento->referencia;
                    $this->status = $pagamento->status;
                    $this->data_pagamento = $pagamento->data_pagamento ? $pagamento->data_pagamento->format('Y-m-d') : '';

                    // Campos sempre desabilitados ao editar
                    $this->camposDesabilitados = true;
                } else {
                    $this->dispatch('toast', [
                        'type' => 'danger',
                        'message' => 'Pagamento não encontrado!'
                    ]);
                    return;
                }
            } else {
                $this->resetModal();
                // Preenche automaticamente se veio da rota
                if ($this->assinatura_id) {
                    $this->assinatura_id = $this->assinatura_id;
                    // Campos desabilitados quando há parâmetro na URL
                    $this->camposDesabilitados = true;
                } else {
                    // Campos habilitados em condições normais
                    $this->camposDesabilitados = false;
                }
            }

            $this->showModal = true;
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao abrir modal: ' . $e->getMessage()
            ]);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetModal();
        $this->dispatch('modalClosed');
    }

    private function resetModal()
    {
        $this->editingPagamento = null;
        $this->assinatura_id = '';
        $this->igreja_id = '';
        $this->valor = '';
        $this->metodo_pagamento = '';
        $this->referencia = $this->gerarReferenciaUnica(); // Gerar referência única
        $this->status = 'pendente';
        $this->data_pagamento = now()->format('Y-m-d'); // Inicializar com data atual
        $this->camposDesabilitados = false; // Reset para habilitado
        $this->resetValidation();
    }

    public function savePagamento()
    {
        // Debug log para verificar se o método está sendo chamado
        Log::info('savePagamento chamado', [
            'metodo_pagamento' => $this->metodo_pagamento,
            'assinatura_id' => $this->assinatura_id,
            'valor' => $this->valor,
            'status' => $this->status
        ]);

        $this->validate();

        // Log após validação bem-sucedida
        Log::info('Validação passou para savePagamento', [
            'metodo_pagamento' => $this->metodo_pagamento,
            'assinatura_id' => $this->assinatura_id,
            'valor' => $this->valor,
            'metodo_pagamento_type' => gettype($this->metodo_pagamento)
        ]);

        // Verificação adicional para método depósito
        if ($this->metodo_pagamento === 'deposito') {
            Log::info('Método depósito selecionado - processando normalmente');
        }

        try {

            $data = [
                'assinatura_id' => $this->assinatura_id,
                'igreja_id' => $this->igreja_id,
                'valor' => $this->valor,
                'metodo_pagamento' => $this->metodo_pagamento,
                'referencia' => $this->referencia ?: null,
                'status' => $this->status,
                'data_pagamento' => $this->data_pagamento ?: now(),
            ];

            if ($this->editingPagamento) {
                $this->editingPagamento->update($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Pagamento atualizado com sucesso!'
                ]);
            } else {
                $pagamento = AssinaturaPagamento::create($data);

                // Atualizar assinatura_historico com informações do pagamento
                if ($pagamento->status === 'confirmado') {
                    AssinaturaHistorico::where('id', $pagamento->assinatura_id)
                        ->update([
                            'forma_pagamento' => $pagamento->metodo_pagamento,
                            'transacao_id' => null, // Não salvar referência como transacao_id (campo UUID)
                            'valor' => $this->valor,
                        ]);
                }

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Pagamento criado com sucesso!'
                ]);
            }

            $this->closeModal();
            $this->dispatch('refreshPagamentos');

            // Redirecionar para a rota limpa se veio com parâmetro na URL
            if ($this->veioComParametroUrl) {
                Log::info('Redirecting to clean URL after save (replace history)');
                // Usar JavaScript direto para redirecionamento com replace
                $this->js("window.location.replace('" . route('admin.assignatures.pagamentos') . "')");
            }

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar pagamento: ' . $e->getMessage()
            ]);
        }

    }

    public function deletePagamento($pagamentoId)
    {
        try {
            $pagamento = AssinaturaPagamento::find($pagamentoId);
            if ($pagamento) {
                $pagamento->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Pagamento excluído com sucesso!'
                ]);
                $this->dispatch('refreshPagamentos');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Pagamento não encontrado!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir pagamento: ' . $e->getMessage()
            ]);
        }
    }

    public function getPagamentos()
    {
        try {
            $query = AssinaturaPagamento::with(['igreja', 'assinatura.pacote']);

            if ($this->search) {
                $query->where(function ($q) {
                    $q->whereHas('igreja', function ($subQ) {
                        $subQ->where('nome', 'like', '%' . $this->search . '%')
                             ->orWhere('nif', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('referencia', 'like', '%' . $this->search . '%')
                    ->orWhere('transacao_id', 'like', '%' . $this->search . '%');
                });
            }

            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }

            if ($this->metodoFilter) {
                $query->where('metodo_pagamento', $this->metodoFilter);
            }

            if ($this->igrejaFilter) {
                $query->where('igreja_id', $this->igrejaFilter);
            }

            return $query->orderBy('data_pagamento', 'desc')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao carregar pagamentos: ' . $e->getMessage()
            ]);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function getStatusOptions()
    {
        return [
            'pendente' => 'Pendente',
            'confirmado' => 'Confirmado',
            'falhou' => 'Falhou',
            'estornado' => 'Estornado',
        ];
    }

    public function getMetodoOptions()
    {
        return [
            'deposito' => 'Depósito',
            'multicaixa_express' => 'Multicaixa Express',
            'tpa' => 'TPA',
            'transferencia' => 'Transferência',
            'outro' => 'Outro',
        ];
    }

    public function closeModalFromJS()
    {
        $this->closeModal();
    }

    // Método para gerar referência única
    private function gerarReferenciaUnica()
    {
        do {
            // Gerar referência no formato: PAG + timestamp + 3 dígitos aleatórios
            $referencia = 'PAG' . time() . rand(100, 999);
        } while (AssinaturaPagamento::where('referencia', $referencia)->exists());

        return $referencia;
    }

    // Método para gerar nova referência
    public function gerarNovaReferencia()
    {
        $this->referencia = $this->gerarReferenciaUnica();
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Nova referência gerada com sucesso!'
        ]);
    }

    // Método para abrir modal automaticamente com dados da assinatura
    public function openModalWithAssinatura($assinaturaId)
    {
        Log::info('openModalWithAssinatura called with assinaturaId: ' . $assinaturaId);
        try {
            $assinatura = AssinaturaHistorico::with(['igreja', 'pacote'])->find($assinaturaId);

            if ($assinatura) {
                Log::info('Assinatura found: ' . $assinatura->id . ', igreja: ' . ($assinatura->igreja ? $assinatura->igreja->nome : 'null'));
                $this->assinatura_id = $assinatura->id;
                $this->igreja_id = $assinatura->igreja_id;

                // Extrair valor diretamente da tabela assinatura_historico
                $this->valor = $assinatura->valor ?? 0;
                Log::info('Valor extraído da assinatura_historico: ' . $this->valor);

                // Inicializar campos que não vêm da assinatura
                $this->referencia = $this->gerarReferenciaUnica();
                $this->data_pagamento = now()->format('Y-m-d');
                $this->metodo_pagamento = '';
                $this->status = 'pendente';

                // Abrir modal
                $this->showModal = true;
                Log::info('showModal set to true');

                // Disparar evento para abrir modal via JavaScript
                $this->dispatch('openPagamentoModal');
                Log::info('Dispatched openPagamentoModal event');

            } else {
                Log::error('Assinatura not found for ID: ' . $assinaturaId);
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Assinatura não encontrada!'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in openModalWithAssinatura: ' . $e->getMessage());
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao abrir modal: ' . $e->getMessage()
            ]);
        }
    }


    // Método para sincronizar aba ativa (chamado pelo JavaScript)
    public function setActiveTab($tab)
    {
        // Este método pode ser usado para sincronizar estado se necessário
        // Por enquanto, não precisamos fazer nada aqui pois o JS controla tudo
    }

    // ============ MÉTODOS PARA CICLOS DE COBRANÇA ============

    public function openModalCiclo($cicloId = null)
    {
        try {
            if ($cicloId) {
                $ciclo = AssinaturaCiclo::find($cicloId);
                if ($ciclo) {
                    $this->editingCiclo = $ciclo;
                    $this->ciclo_assinatura_id = $ciclo->assinatura_id;
                    $this->ciclo_inicio = $ciclo->inicio ? $ciclo->inicio->format('Y-m-d') : '';
                    $this->ciclo_fim = $ciclo->fim ? $ciclo->fim->format('Y-m-d') : '';
                    $this->ciclo_valor = $ciclo->valor;
                    $this->ciclo_status = $ciclo->status;
                } else {
                    $this->dispatch('toast', [
                        'type' => 'danger',
                        'message' => 'Ciclo não encontrado!'
                    ]);
                    return;
                }
            } else {
                $this->resetModalCiclo();
            }

            $this->showModalCiclo = true;
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao abrir modal: ' . $e->getMessage()
            ]);
        }
    }

    public function closeModalCiclo()
    {
        $this->showModalCiclo = false;
        $this->resetModalCiclo();
    }

    private function resetModalCiclo()
    {
        $this->editingCiclo = null;
        $this->ciclo_assinatura_id = '';
        $this->ciclo_inicio = '';
        $this->ciclo_fim = '';
        $this->ciclo_valor = '';
        $this->ciclo_status = 'pendente';
    }

    public function saveCiclo()
    {
        $this->validate([
            'ciclo_assinatura_id' => 'required|exists:assinatura_historico,id',
            'ciclo_inicio' => 'required|date',
            'ciclo_fim' => 'required|date|after:ciclo_inicio',
            'ciclo_valor' => 'required|numeric|min:0',
            'ciclo_status' => 'required|in:pendente,pago,atrasado,falhou',
        ]);

        try {
            $data = [
                'assinatura_id' => $this->ciclo_assinatura_id,
                'inicio' => $this->ciclo_inicio,
                'fim' => $this->ciclo_fim,
                'valor' => $this->ciclo_valor,
                'status' => $this->ciclo_status,
            ];

            if ($this->editingCiclo) {
                $this->editingCiclo->update($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Ciclo atualizado com sucesso!'
                ]);
            } else {
                AssinaturaCiclo::create($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Ciclo criado com sucesso!'
                ]);
            }

            $this->closeModalCiclo();
            $this->dispatch('refreshCiclos');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar ciclo: ' . $e->getMessage()
            ]);
        }
    }

    public function getCiclos()
    {
        try {
            $query = AssinaturaCiclo::with(['assinatura.pacote', 'assinatura.igreja']);

            if ($this->searchCiclos) {
                $query->where(function ($q) {
                    $q->whereHas('assinatura.igreja', function ($subQ) {
                        $subQ->where('nome', 'like', '%' . $this->searchCiclos . '%');
                    })
                    ->orWhereHas('assinatura.pacote', function ($subQ) {
                        $subQ->where('nome', 'like', '%' . $this->searchCiclos . '%');
                    });
                });
            }

            if ($this->statusCicloFilter) {
                $query->where('status', $this->statusCicloFilter);
            }

            return $query->orderBy('inicio', 'desc')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao carregar ciclos: ' . $e->getMessage()
            ]);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    // ============ MÉTODOS PARA FALHAS DE PAGAMENTO ============

    public function openModalFalha($falhaId = null)
    {
        try {
            if ($falhaId) {
                $falha = AssinaturaPagamentoFalha::find($falhaId);
                if ($falha) {
                    $this->editingFalha = $falha;
                    $this->falha_pagamento_id = $falha->pagamento_id;
                    $this->falha_motivo = $falha->motivo;
                    $this->falha_resolvido = $falha->resolvido;
                } else {
                    $this->dispatch('toast', [
                        'type' => 'danger',
                        'message' => 'Falha não encontrada!'
                    ]);
                    return;
                }
            } else {
                $this->resetModalFalha();
            }

            $this->showModalFalha = true;
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao abrir modal: ' . $e->getMessage()
            ]);
        }
    }

    public function closeModalFalha()
    {
        $this->showModalFalha = false;
        $this->resetModalFalha();
    }

    private function resetModalFalha()
    {
        $this->editingFalha = null;
        $this->falha_pagamento_id = '';
        $this->falha_motivo = '';
        $this->falha_resolvido = false;
    }

    public function saveFalha()
    {
        $this->validate([
            'falha_pagamento_id' => 'required|exists:assinatura_pagamentos,id',
            'falha_motivo' => 'required|string|max:500',
            'falha_resolvido' => 'boolean',
        ]);

        try {
            $data = [
                'pagamento_id' => $this->falha_pagamento_id,
                'igreja_id' => AssinaturaPagamento::find($this->falha_pagamento_id)->igreja_id,
                'motivo' => $this->falha_motivo,
                'resolvido' => $this->falha_resolvido,
            ];

            if ($this->editingFalha) {
                $this->editingFalha->update($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Falha atualizada com sucesso!'
                ]);
            } else {
                AssinaturaPagamentoFalha::create($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Falha registrada com sucesso!'
                ]);
            }

            $this->closeModalFalha();
            $this->dispatch('refreshFalhas');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar falha: ' . $e->getMessage()
            ]);
        }
    }

    public function getFalhas()
    {
        try {
            $query = AssinaturaPagamentoFalha::with(['pagamento.assinaturaHistorico.pacote', 'pagamento.igreja']);

            if ($this->searchFalhas) {
                $query->where(function ($q) {
                    $q->whereHas('pagamento.igreja', function ($subQ) {
                        $subQ->where('nome', 'like', '%' . $this->searchFalhas . '%');
                    })
                    ->orWhere('motivo', 'like', '%' . $this->searchFalhas . '%');
                });
            }

            if ($this->statusFalhaFilter !== '') {
                $query->where('resolvido', $this->statusFalhaFilter === 'resolvido');
            }

            return $query->orderBy('data', 'desc')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao carregar falhas: ' . $e->getMessage()
            ]);
            return collect()->paginate($this->perPage);
        }
    }

    public function deleteCiclo($cicloId)
    {
        try {
            $ciclo = AssinaturaCiclo::find($cicloId);
            if ($ciclo) {
                $ciclo->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Ciclo excluído com sucesso!'
                ]);
                $this->dispatch('refreshCiclos');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Ciclo não encontrado!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir ciclo: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteFalha($falhaId)
    {
        try {
            $falha = AssinaturaPagamentoFalha::find($falhaId);
            if ($falha) {
                $falha->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Falha excluída com sucesso!'
                ]);
                $this->dispatch('refreshFalhas');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Falha não encontrada!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir falha: ' . $e->getMessage()
            ]);
        }
    }

    public function getAssinaturasDisponiveisParaCiclos()
    {
        try {
            // Buscar IDs das assinaturas que já têm ciclos
            $assinaturasComCiclos =AssinaturaCiclo::pluck('assinatura_id')->toArray();

            // Retornar apenas assinaturas que NÃO têm ciclos
            return AssinaturaHistorico::with('pacote')
                ->whereNotIn('id', $assinaturasComCiclos)
                ->orderBy('created_at', 'desc')
                ->get();

        } catch (\Exception $e) {
            Log::error('Erro ao buscar assinaturas disponíveis para ciclos: ' . $e->getMessage());
            return collect(); // Retornar coleção vazia em caso de erro
        }
    }

    public function render()
    {
        return view('billings.pagamentos', [
            'pagamentos' => $this->getPagamentos(),
            'ciclos' => $this->getCiclos(),
            'falhas' => $this->getFalhas(),
            'igrejas' => Igreja::orderBy('nome')->get(),
            'assinaturas' => $this->getAssinaturasDisponiveisParaCiclos(),
            'statusOptions' => $this->getStatusOptions(),
            'metodoOptions' => $this->getMetodoOptions(),
        ]);
    }
}
