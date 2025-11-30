<?php

namespace App\Livewire\Billings;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Igrejas\Igreja;
use Livewire\Attributes\Title;
use App\Models\Billings\Pacote;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use App\Models\Billings\AssinaturaAtual;
use App\Models\Billings\AssinaturaHistorico;
use App\Models\Billings\IgrejaAssinada;

#[Title('Assinaturas Atuais')]
#[Layout('components.layouts.app')]
class AssinaturasAtuais extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $statusFilter = '';

    // Modal properties
    public $showModal = false;
    public $editingAssinatura = null;
    public $igreja_id = '';
    public $igreja_nome = '';
    public $pacote_id = '';
    public $data_inicio = '';
    public $data_fim = '';
    public $status = 'Ativo';
    public $trial_fim = '';
    public $duracao_meses_custom = '';
    public $vitalicio = false;


    protected function rules()
    {
        $rules = [
            'igreja_id' => 'required|exists:igrejas,id',
            'pacote_id' => 'required|exists:pacote,id',
            'data_inicio' => 'required|date',
            'status' => 'required|in:Ativo,Cancelado,Expirado',
            'trial_fim' => 'nullable|date|after_or_equal:data_inicio',
            'duracao_meses_custom' => 'nullable|integer|min:0',
            'vitalicio' => 'boolean',
        ];

        // Validação condicional para data_fim
        if (!$this->vitalicio) {
            $rules['data_fim'] = 'required|date|after:data_inicio';
        } else {
            $rules['data_fim'] = 'nullable|date';
        }

        // Validação condicional para trial_fim
        if ($this->trial_fim && !$this->vitalicio) {
            $rules['trial_fim'] = 'nullable|date|after_or_equal:data_inicio|before_or_equal:data_fim';
        }

        return $rules;
    }

    protected function messages(){
        $messages = [
            'pacote_id.required'=>'O campo pacote é obrigatório',
            'igreja_id.required'=>'O campo igreja é obrigatório',
        ];

        return $messages;
    }

    protected $listeners = ['refreshAssinaturas' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedVitalicio()
    {
        if ($this->vitalicio && $this->trial_fim) {
            $this->trial_fim = '';
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Não é possível selecionar Vitalício e Trial ao mesmo tempo. O Trial foi desmarcado.'
            ]);
        }

        // Se for vitalício, limpar data_fim
        if ($this->vitalicio) {
            $this->data_fim = '';
            $this->resetValidation('data_fim');
        }

        $this->calcularDuracao();

        // Lógica de auto-seleção do pacote
        $this->autoSelecionarPacote();

        // Forçar re-renderização completa dos pacotes
        $this->dispatch('refresh-pacotes-select');
    }

    // Método para auto-selecionar pacote baseado na disponibilidade
    private function autoSelecionarPacote()
    {
        if (!$this->igreja_id) {
            return; // Não há igreja selecionada
        }

        $pacotes = $this->getPacotes($this->igreja_id);

        if ($pacotes->isEmpty()) {
            $this->pacote_id = '';
            $this->resetValidation('pacote_id');
            return;
        }

        // Se já tem um pacote selecionado, verificar se ainda está disponível
        if ($this->pacote_id) {
            $pacoteSelecionado = $pacotes->firstWhere('id', $this->pacote_id);
            if ($pacoteSelecionado) {
                // Pacote ainda disponível, manter seleção
                return;
            }
        }

        // Auto-selecionar o primeiro pacote disponível
        $primeiroPacote = $pacotes->first();
        if ($primeiroPacote) {
            $this->pacote_id = $primeiroPacote->id;
            $this->resetValidation('pacote_id');
        } else {
            $this->pacote_id = '';
            $this->resetValidation('pacote_id');
        }
    }

    public function updatedTrialFim()
    {
        if ($this->trial_fim && $this->vitalicio) {
            $this->vitalicio = false;
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Não é possível selecionar Trial e Vitalício ao mesmo tempo. O Vitalício foi desmarcado.'
            ]);
        }
        $this->calcularDuracao();
    }

    // Métodos para limpar campos de data individualmente
    public function clearDataInicio()
    {
        $this->data_inicio = '';
        $this->resetValidation('data_inicio');
        $this->calcularDuracao();
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Data de início limpa com sucesso!'
        ]);
    }

    public function clearDataFim()
    {
        $this->data_fim = '';
        $this->resetValidation('data_fim');
        $this->calcularDuracao();
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Data de fim limpa com sucesso!'
        ]);
    }

    public function clearTrialFim()
    {
        $this->trial_fim = '';
        $this->resetValidation('trial_fim');
        $this->calcularDuracao();
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Data de trial limpa com sucesso!'
        ]);
    }

    public function updatedDataInicio()
    {
        $this->calcularDuracao();
    }

    public function updatedDataFim()
    {
        $this->calcularDuracao();
    }

    public function updatedIgrejaId()
    {
        // Limpar seleção de pacote quando igreja muda
        $this->pacote_id = '';

        // Resetar validação do campo pacote
        $this->resetValidation('pacote_id');

        // Atualizar nome da igreja baseado no ID
        if ($this->igreja_id) {
            $igreja = Igreja::find($this->igreja_id);
            if ($igreja) {
                $this->igreja_nome = $igreja->nome . ' (' . $igreja->nif . ')';
            }
        } else {
            $this->igreja_nome = '';
        }

        // Forçar re-renderização dos pacotes disponíveis
        $this->dispatch('$refresh');
    }

    public function updatedPacoteId()
    {
        // Resetar validação quando pacote é selecionado
        $this->resetValidation('pacote_id');
    }

    public function validateIgrejaSelection()
    {
        if ($this->igreja_nome) {
            $igreja = Igreja::where('status_aprovacao', 'aprovado')
                ->where(DB::raw("CONCAT(nome, ' (', nif, ')')"), '=', trim($this->igreja_nome))
                ->orWhere('nome', 'like', '%' . trim($this->igreja_nome) . '%')
                ->first();

            if ($igreja) {
                $this->igreja_id = $igreja->id;
                $this->resetValidation('igreja_id');
            } else {
                $this->igreja_id = '';
                $this->addError('igreja_id', 'Igreja não encontrada.');
            }
        } else {
            $this->igreja_id = '';
            $this->resetValidation('igreja_id');
        }

        $this->pacote_id = '';
    }

    public function clearIgrejaSelection()
    {
        $this->igreja_id = '';
        $this->igreja_nome = '';
        $this->pacote_id = '';
        $this->resetValidation(['igreja_id', 'pacote_id']);
        $this->dispatch('$refresh');
    }

    public function updatedIgrejaNome()
    {
        // Quando o usuário digita, tentar encontrar a igreja correspondente
        if ($this->igreja_nome) {
            $igreja = Igreja::where('nome', 'like', '%' . trim(explode('(', $this->igreja_nome)[0]) . '%')
                           ->orWhere('nif', 'like', '%' . $this->igreja_nome . '%')
                           ->first();

            if ($igreja) {
                $this->igreja_id = $igreja->id;
                $this->igreja_nome = $igreja->nome . ' (' . $igreja->nif . ')';
            } else {
                $this->igreja_id = '';
            }
        } else {
            $this->igreja_id = '';
        }
    }

    public function setIgrejaFromDatalist($igrejaId, $igrejaNome)
    {
        $this->igreja_id = $igrejaId;
        $this->igreja_nome = $igrejaNome;

        // O método updatedIgrejaId será chamado automaticamente
        // devido à mudança na propriedade igreja_id
        // Mas vamos garantir que seja chamado explicitamente também
        $this->updatedIgrejaId();
    }


    private function calcularDuracao()
    {
        if ($this->data_inicio && $this->data_fim && !$this->vitalicio) {
            try {
                $inicio = \Carbon\Carbon::parse($this->data_inicio);
                $fim = \Carbon\Carbon::parse($this->data_fim);

                // Calcula diferença em meses completos
                $anos = $fim->year - $inicio->year;
                $meses = $fim->month - $inicio->month;
                $totalMeses = ($anos * 12) + $meses;

                // Se os dias são diferentes no mesmo mês, retorna 0
                if ($totalMeses === 0 && $fim->day !== $inicio->day) {
                    $this->duracao_meses_custom = 0;
                } elseif ($totalMeses < 0) {
                    // Se data fim é anterior à data início, retorna 0
                    $this->duracao_meses_custom = 0;
                } else {
                    $this->duracao_meses_custom = $totalMeses;
                }
            } catch (\Exception $e) {
                $this->duracao_meses_custom = 0;
            }
        } elseif ($this->vitalicio) {
            $this->duracao_meses_custom = '';
        } else {
            $this->duracao_meses_custom = '';
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function openModal($igrejaId = null)
    {
        try {
            if ($igrejaId) {
                $assinatura = AssinaturaAtual::with(['igreja', 'pacote'])->where('igreja_id', $igrejaId)->first();
                if ($assinatura) {
                    $this->editingAssinatura = $assinatura;
                    $this->igreja_id = $assinatura->igreja_id;
                    $this->igreja_nome = $assinatura->igreja ? $assinatura->igreja->nome . ' (' . $assinatura->igreja->nif . ')' : '';
                    $this->pacote_id = $assinatura->pacote_id;
                    $this->data_inicio = $assinatura->data_inicio->format('Y-m-d');
                    $this->data_fim = $assinatura->data_fim ? $assinatura->data_fim->format('Y-m-d') : '';
                    $this->status = $assinatura->status;
                    $this->trial_fim = $assinatura->trial_fim ? $assinatura->trial_fim->format('Y-m-d') : '';
                    $this->duracao_meses_custom = $assinatura->duracao_meses_custom ?? '';
                    $this->vitalicio = $assinatura->vitalicio ?? false;
                } else {
                    $this->dispatch('toast', [
                        'type' => 'danger',
                        'message' => 'Assinatura não encontrada!'
                    ]);
                    return;
                }
            } else {
                $this->resetModal();
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
        $this->dispatch('closeModalEvent');
    }

    private function resetModal()
    {
        $this->editingAssinatura = null;
        $this->igreja_id = '';
        $this->igreja_nome = '';
        $this->pacote_id = '';
        $this->data_inicio = '';
        $this->data_fim = '';
        $this->status = 'Ativo';
        $this->trial_fim = '';
        $this->duracao_meses_custom = '';
        $this->vitalicio = false;
        $this->resetValidation();
    }

    public function saveAssinatura()
    {
        // Validação adicional para combinação Trial e Vitalício
        if ($this->trial_fim && $this->vitalicio) {
            $this->addError('vitalicio', 'Não é possível selecionar Trial e Vitalício ao mesmo tempo.');
            return;
        }

        $this->validate();

        try {

            $data = [
                'igreja_id' => $this->igreja_id,
                'pacote_id' => $this->pacote_id,
                'data_inicio' => $this->data_inicio,
                'data_fim' => $this->data_fim ?: null,
                'status' => $this->status,
                'trial_fim' => $this->trial_fim ?: null,
                'duracao_meses_custom' => $this->duracao_meses_custom ?: null,
                'vitalicio' => $this->vitalicio,
            ];

            if ($this->editingAssinatura) {
                $this->editingAssinatura->update($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Assinatura atualizada com sucesso!'
                ]);
            } else {



                AssinaturaAtual::create($data);

                // Criar registro no histórico
                $pacote = Pacote::find($this->pacote_id);

                // Calcular valor baseado nas regras específicas
                $valorCalculado = $this->calcularValorAssinatura($pacote);

                $historicoData = [
                    'igreja_id' => $this->igreja_id,
                    'pacote_id' => $this->pacote_id,
                    'data_inicio' => $this->data_inicio,
                    'data_fim' => $this->vitalicio ? null : $this->data_fim,
                    'valor' => $valorCalculado,
                    'status' => $this->status,
                    'trial_fim' => $this->trial_fim ?: null,
                    'duracao_meses_custom' => $this->duracao_meses_custom ?: null,
                    'vitalicio' => $this->vitalicio,
                ];

                $assinaturaHistorico = AssinaturaHistorico::create($historicoData);

                // Criar registro na tabela igrejas_assinadas
                IgrejaAssinada::create([
                    'igreja_id' => $this->igreja_id,
                    'pacote_id' => $this->pacote_id,
                    'ativo' => true,
                    'data_adesao' => now(),
                    'observacoes' => 'Assinatura criada automaticamente',
                ]);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Assinatura criada com sucesso!'
                ]);

                // Redirecionar para pagamentos com o ID da assinatura histórica
                return $this->redirect(route('admin.assignatures.pagamentos', ['assinatura_id' => $assinaturaHistorico->id]), navigate: true);
            }

            $this->closeModal();
            $this->dispatch('refreshAssinaturas');

        } catch (\Illuminate\Database\QueryException $e) {

            $errorMessage = 'Erro ao salvar assinatura.';

            // Tratamento específico para erros de banco de dados
            if (str_contains($e->getMessage(), 'duplicate key value violates unique constraint "assinatura_atual_pkey"')) {
                $igreja = Igreja::find($this->igreja_id);
                $errorMessage = 'Esta igreja já possui uma assinatura ativa. Cada igreja pode ter apenas uma assinatura por vez.';
            } elseif (str_contains($e->getMessage(), 'violates foreign key constraint')) {
                $errorMessage = 'Dados inválidos. Verifique se a igreja e o pacote selecionados existem.';
            } elseif (str_contains($e->getMessage(), 'violates check constraint')) {
                $errorMessage = 'Dados inválidos. Verifique os valores informados.';
            }

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => $errorMessage
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro inesperado ao salvar assinatura. Tente novamente.'
            ]);
        }
    }

    public function deleteAssinatura($igrejaId)
    {
        try {
            $assinatura = AssinaturaAtual::where('igreja_id', $igrejaId)->first();
            if ($assinatura) {
                $assinatura->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Assinatura excluída com sucesso!'
                ]);
                $this->dispatch('refreshAssinaturas');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Assinatura não encontrada!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir assinatura: ' . $e->getMessage()
            ]);
        }
    }

    public function getAssinaturas()
    {
        try {
            $query = AssinaturaAtual::with(['igreja', 'pacote']);

            if ($this->search) {
                $query->whereHas('igreja', function ($q) {
                    $q->where('nome', 'like', '%' . $this->search . '%')
                      ->orWhere('nif', 'like', '%' . $this->search . '%');
                })->orWhereHas('pacote', function ($q) {
                    $q->where('nome', 'like', '%' . $this->search . '%');
                });
            }

            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }

            return $query->orderBy('data_fim')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao carregar assinaturas: ' . $e->getMessage()
            ]);

            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function getIgrejas()
    {
        return Igreja::where('status_aprovacao', 'aprovado')
                    ->orWhere('status_aprovacao', 'pendente')
                    ->orderBy('nome')
                    ->get();
    }

    public function getPacotes($igrejaId = null)
    {
        $query = Pacote::orderBy('nome');

        if ($igrejaId && !$this->editingAssinatura) {
            // Filtrar pacotes que a igreja já tem assinatura ativa (apenas ao criar nova)
            $pacotesComAssinatura = AssinaturaAtual::where('igreja_id', $igrejaId)
                ->where('status', 'Ativo')
                ->pluck('pacote_id')
                ->toArray();

            if (!empty($pacotesComAssinatura)) {
                $query->whereNotIn('id', $pacotesComAssinatura);
            }
        }

        return $query->get();
    }

    // Computed property para pacotes disponíveis (mais reativo)
    public function getPacotesDisponiveisProperty()
    {
        $pacotes = $this->getPacotes($this->igreja_id);

        // Adicionar preço formatado baseado no vitalicio
        foreach ($pacotes as $pacote) {
            $pacote->preco_formatado = $this->formatarPrecoPacote($pacote);
        }

        return $pacotes;
    }

    // Método auxiliar para formatar preço baseado no vitalicio
    private function formatarPrecoPacote($pacote)
    {
        if ($this->vitalicio && $pacote->preco_vitalicio) {
            return $pacote->preco_vitalicio;
        }
        return $pacote->preco;
    }

    // Método para calcular valor da assinatura baseado nas regras específicas
    private function calcularValorAssinatura($pacote)
    {
        try {
            // Se for vitalício, usar preco_vitalicio se existir, senão usar preco normal
            if ($this->vitalicio) {
                return $pacote->preco_vitalicio ?? $pacote->preco;
            }

            // Se não for vitalício, calcular baseado em duração
            $precoBase = $pacote->preco;

            // Primeiro, verificar se tem duracao_meses_custom definida
            if ($this->duracao_meses_custom !== '' && $this->duracao_meses_custom !== null) {
                $duracao = (int) $this->duracao_meses_custom;

                // Se duração for 0, usar apenas o preço base
                if ($duracao === 0) {
                    return $precoBase;
                }

                // Se duração for 1 ou mais, multiplicar pelo preço base
                return $precoBase * $duracao;
            }

            // Se não tem duracao_meses_custom, calcular baseado nas datas
            if ($this->data_inicio && $this->data_fim) {
                $inicio = \Carbon\Carbon::parse($this->data_inicio);
                $fim = \Carbon\Carbon::parse($this->data_fim);

                $anos = $fim->year - $inicio->year;
                $meses = $fim->month - $inicio->month;
                $totalMeses = ($anos * 12) + $meses;

                // Garantir pelo menos 1 mês
                if ($totalMeses <= 0) {
                    $totalMeses = 1;
                }

                return $precoBase * $totalMeses;
            }

            // Fallback: usar preço base
            return $precoBase;

        } catch (\Exception $e) {
            // Em caso de erro, retornar preço base
            return $pacote->preco ?? 0;
        }
    }

    public function render()
    {
        return view('billings.assinaturas-atuais', [
            'assinaturas' => $this->getAssinaturas(),
            'igrejas' => $this->getIgrejas(),
            'pacotes' => $this->pacotesDisponiveis,
        ]);
    }
}
