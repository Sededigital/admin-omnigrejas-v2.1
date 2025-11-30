<?php

namespace App\Livewire\Subscription;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\Igrejas\Igreja;
use Livewire\Attributes\Title;
use App\Models\Billings\Pacote;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Billings\AssinaturaAtual;
use App\Models\Billings\AssinaturaPagamento;
use App\Models\Billings\PagamentoAssinaturaIgreja;

#[Title('Pagamento de Assinatura - OmnIgrejas E-commerce')]
#[Layout('components.layouts.subscription')]
class PaymentPage extends Component
{
    use WithFileUploads;

    public $igreja;
    public $pacote;
    public $acao; // 'nova_assinatura', 'renovar', 'upgrade'
    public $assinaturaAtual;

    // Dados do pagamento
    public $metodoPagamento = '';
    public $referencia = '';
    public $comprovativo;
    public $observacoes = '';

    // Novos campos para duração e cálculo
    public $duracaoMeses = 1; // 1, 3, 6, 12 ou 'vitalicio'
    public $valorTotal = 0;
    public $valorMensal = 0;

    // Controle da UI (removido showSuccessModal pois agora usa SweetAlert2)

    // Controle de igrejas (se usuário tiver múltiplas)
    public $igrejaSelecionada;
    public $igrejasDisponiveis;

    // Mensagens de validação
    public $mensagensValidacao = [];

    protected $listeners = ['fileUploaded' => 'handleFileUpload'];

    public function mount()
    {
        // Obter parâmetros da rota manualmente
        $pacoteId = request()->route('pacote');
        $acao = request()->route('acao', 'nova_assinatura');

        $this->pacote = Pacote::findOrFail($pacoteId);
        $this->acao = $acao;

        // Carregar igrejas disponíveis do usuário
        $this->carregarIgrejasDisponiveis();

        // Definir igreja padrão (priorizar igrejas sem assinatura)
        if ($this->igrejasDisponiveis && $this->igrejasDisponiveis->isNotEmpty()) {
            $this->selecionarIgrejaPadrao();
        }

        // Verificar se o usuário tem permissão para esta igreja
        if ($this->igreja && !Auth::user()->membros()->where('igreja_id', $this->igreja->id)->exists()) {
            abort(403, 'Você não tem permissão para acessar esta igreja.');
        }

        // Carregar assinatura atual
        $this->assinaturaAtual = $this->igreja ? AssinaturaAtual::where('igreja_id', $this->igreja->id)->first() : null;

        // Gerar referência automática (UUID)
        $this->gerarReferenciaAutomatica();

        // Calcular valores iniciais
        $this->calcularValorTotal();

        // Validar se a ação é permitida
        $this->validarAcao();
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
                $assinaturaAtual = AssinaturaAtual::where('igreja_id', $membro->igreja->id)->first();
                return [
                    'id' => $membro->igreja->id,
                    'nome' => $membro->igreja->nome,
                    'sigla' => $membro->igreja->sigla,
                    'categoria' => $membro->igreja->categoria->nome ?? 'Geral',
                    'localizacao' => $membro->igreja->localizacao,
                    'cargo' => $membro->cargo,
                    'principal' => $membro->principal,
                    'tem_assinatura' => $assinaturaAtual ? true : false
                ];
            })
            ->sortByDesc('principal') // Igrejas principais primeiro
            ->values());
    }

    private function selecionarIgrejaPadrao()
    {
        // Verificar se há uma igreja na sessão (da UpgradePage)
        $igrejaSessao = session('igreja_atual');

        if ($igrejaSessao) {
            // Verificar se o usuário tem acesso a esta igreja
            $igrejaSessaoDisponivel = $this->igrejasDisponiveis->first(function($igreja) use ($igrejaSessao) {
                return $igreja['id'] == $igrejaSessao->id;
            });

            if ($igrejaSessaoDisponivel) {
                $this->igrejaSelecionada = $igrejaSessao->id;
                $this->igreja = $igrejaSessao;
                return;
            }
        }

        // Fallback: Primeiro, tentar encontrar uma igreja sem assinatura
        $igrejaSemAssinatura = $this->igrejasDisponiveis->first(function($igreja) {
            return !$igreja['tem_assinatura'];
        });

        if ($igrejaSemAssinatura) {
            $this->igrejaSelecionada = $igrejaSemAssinatura['id'];
        } else {
            // Se todas têm assinatura, selecionar a primeira (principal)
            $this->igrejaSelecionada = $this->igrejasDisponiveis->first()['id'];
        }

        $this->igreja = Igreja::find($this->igrejaSelecionada);
    }

    private function gerarReferenciaAutomatica()
    {
        // Gerar UUID único para a referência do pagamento
        $this->referencia = $this->gerarReferenciaUnica();
    }


    private function gerarReferenciaUnica()
    {
        do {
            // Gerar referência no formato: PAG + timestamp + 3 dígitos aleatórios
            $referencia = 'PAG' . time() . rand(100, 999);
        } while (AssinaturaPagamento::where('referencia', $referencia)->exists());

        return $referencia;
    }

    private function validarAcao()
    {
        if (!$this->igreja) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Nenhuma igreja disponível para assinatura.'
            ]);
            return redirect()->route('selecionar.igreja');
        }

        // Em vez de redirecionar, mostrar alertas informativos na própria página
        $this->validarCondicoesAcao();
    }

    private function validarCondicoesAcao()
    {
        $mensagens = [];

        // Sempre mostrar informações da assinatura atual se existir
        if ($this->assinaturaAtual) {
            $mensagens[] = $this->getMensagemAssinaturaAtual();
        } else {
            // Igreja sem assinatura - sempre mostrar nova assinatura
            $mensagens[] = [
                'tipo' => 'info',
                'titulo' => 'Nova Assinatura',
                'mensagem' => 'Esta igreja ainda não possui uma assinatura ativa.',
                'acao_sugerida' => 'nova_assinatura',
                'assinatura_info' => null
            ];
        }

        // Armazenar mensagens para exibir na view
        $this->mensagensValidacao = $mensagens;
    }

    private function getMensagemAssinaturaAtual(): array
    {
        $pacoteAtual = $this->assinaturaAtual->pacote;
        $isExpired = $this->assinaturaAtual->isExpired();
        $isMesmoPacote = $pacoteAtual->id === $this->pacote->id;

        $assinaturaInfo = [
            'pacote_nome' => $pacoteAtual->nome,
            'pacote_preco' => $pacoteAtual->getPrecoFormatado(),
            'data_fim' => $isExpired ? null : $this->assinaturaAtual->data_fim->format('d/m/Y'),
            'status' => $isExpired ? 'Expirada' : 'Ativa',
            'mesmo_pacote' => $isMesmoPacote
        ];

        if ($isExpired) {
            // Assinatura expirada - sempre renovar
            return [
                'tipo' => 'warning',
                'titulo' => 'Assinatura Expirada',
                'mensagem' => "A assinatura do pacote {$pacoteAtual->nome} expirou. Renove sua assinatura para continuar aproveitando os recursos.",
                'acao_sugerida' => 'renovar',
                'assinatura_info' => $assinaturaInfo
            ];
        } else {
            // Assinatura ativa
            if ($isMesmoPacote) {
                // Mesmo pacote - renovar
                return [
                    'tipo' => 'info',
                    'titulo' => 'Renovar Assinatura',
                    'mensagem' => "Sua assinatura do pacote {$pacoteAtual->nome} está ativa até {$this->assinaturaAtual->data_fim->format('d/m/Y')}. Você pode renovar antecipadamente.",
                    'acao_sugerida' => 'renovar',
                    'assinatura_info' => $assinaturaInfo
                ];
            } else {
                // Pacote diferente - upgrade
                $precoAtual = $pacoteAtual->preco;
                $precoNovo = $this->pacote->preco;
                $diferenca = $precoNovo - $precoAtual;

                $mensagem = "Sua assinatura do pacote {$pacoteAtual->nome} está ativa. ";
                if ($diferenca > 0) {
                    $mensagem .= "Faça upgrade para o pacote {$this->pacote->nome} por apenas " . number_format($diferenca, 2, ',', '.') . " AOA/mês a mais.";
                } elseif ($diferenca < 0) {
                    $mensagem .= "Você pode fazer downgrade para o pacote {$this->pacote->nome} e economizar " . number_format(abs($diferenca), 2, ',', '.') . " AOA/mês.";
                } else {
                    $mensagem .= "Você pode trocar para o pacote {$this->pacote->nome} mantendo o mesmo valor.";
                }

                return [
                    'tipo' => 'success',
                    'titulo' => 'Upgrade Disponível',
                    'mensagem' => $mensagem,
                    'acao_sugerida' => 'upgrade',
                    'assinatura_info' => $assinaturaInfo
                ];
            }
        }
    }

    public function updatedMetodoPagamento()
    {
        // Reset apenas campos específicos quando mudar método (exceto comprovativo e referência)
        // A referência deve permanecer a mesma durante toda a sessão

        // Limpar erros de validação relacionados (exceto comprovativo)
        $this->resetValidation(['metodoPagamento']);
    }

    public function updatedDuracaoMeses()
    {
        // Recalcular valor total quando mudar duração
        $this->calcularValorTotal();
    }

    public function updatedIgrejaSelecionada()
    {
        // Atualizar igreja quando selecionar outra
        $this->igreja = Igreja::find($this->igrejaSelecionada);

        // Recarregar assinatura atual da nova igreja
        $this->assinaturaAtual = $this->igreja ? AssinaturaAtual::where('igreja_id', $this->igreja->id)->first() : null;

        // Recalcular valores
        $this->calcularValorTotal();

        // Revalidar mensagens de validação para a nova igreja
        $this->validarCondicoesAcao();
    }

    public function submitPagamento()
    {
        $this->validate([
            'igrejaSelecionada' => 'required|exists:igrejas,id',
            'duracaoMeses' => 'required|in:1,3,6,12,vitalicio',
            'metodoPagamento' => 'required|in:deposito,multicaixa_express,transferencia',
            'referencia' => 'nullable|string|max:255',
            'comprovativo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'observacoes' => 'nullable|string|max:1000',
        ], [
            'igrejaSelecionada.required' => 'Selecione uma igreja.',
            'duracaoMeses.required' => 'Selecione a duração da assinatura.',
            'metodoPagamento.required' => 'Selecione o método de pagamento.',
            'referencia.max' => 'A referência não pode ter mais de 255 caracteres.',
            'comprovativo.required' => 'O comprovativo é obrigatório.',
            'comprovativo.mimes' => 'O comprovativo deve ser um arquivo PDF, JPG, JPEG ou PNG.',
            'comprovativo.max' => 'O comprovativo não pode ter mais de 5MB.',
        ]);

        try {
            
            

            // Verificar se a igreja selecionada permite a ação
            $this->validarAcaoParaIgrejaSelecionada();

            // Upload do arquivo para Supabase
            $comprovativoPath = $this->uploadComprovativo();

           

            // Criar registro de pagamento
            $pagamento = PagamentoAssinaturaIgreja::create([
                'igreja_id' => $this->igrejaSelecionada,
                'pacote_id' => $this->pacote->id,
                'valor' => $this->valorTotal,
                'preco_vitalicio' => $this->duracaoMeses === 'vitalicio' ? $this->pacote->preco_vitalicio : null,
                'duracao_meses' => $this->duracaoMeses === 'vitalicio' ? null : $this->duracaoMeses,
                'is_vitalicio' => $this->duracaoMeses === 'vitalicio',
                'pacote_nome' => $this->pacote->nome,
                'metodo_pagamento' => $this->metodoPagamento,
                'referencia' => $this->referencia,
                'comprovativo_url' => $comprovativoPath['url'],
                'comprovativo_nome' => $comprovativoPath['nome_original'],
                'comprovativo_tipo' => $comprovativoPath['tipo_mime'],
                'comprovativo_tamanho' => $comprovativoPath['tamanho_bytes'],
                'status' => 'pendente',
                'observacoes' => $this->observacoes,
                'created_by' => Auth::id(),
            ]);

          
            // Armazenar temporariamente para o SweetAlert2
            $pagamentoTemp = $pagamento;

            // Limpar formulário
            $this->reset(['metodoPagamento', 'referencia', 'comprovativo', 'observacoes']);

            $referencia = Str::limit($pagamentoTemp->referencia, 15, '/');
            
            // Disparar evento para SweetAlert2
            $this->dispatch('pagamento-sucesso', [
                'referencia' => $referencia,
                'status' => $pagamentoTemp->getStatusFormatado(),
                'igrejaId' => $this->igreja->id
            ]);

        } catch (\Exception $e) {

            Log::error('Erro ao processar pagamento de assinatura', [
                'igreja_id' => $this->igrejaSelecionada,
                'pacote_id' => $this->pacote->id,
                'erro' => $e->getMessage(),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao processar pagamento: ' . $e->getMessage()
            ]);
        }
    }

    private function uploadComprovativo()
    {
        if (!$this->comprovativo) {
            throw new \Exception('Arquivo de comprovativo não encontrado.');
        }

        // Usar o SupabaseHelper para fazer upload
        return \App\Helpers\SupabaseHelper::fazerUploadComprovativoAssinatura(
            $this->comprovativo,
            $this->igreja->nome,
            $this->pacote->nome
        );
    }

    // Método removido pois agora usa SweetAlert2

    public function getMetodosPagamento()
    {
        return [
            'deposito' => [
                'nome' => 'Depósito Bancário',
                'icone' => 'fas fa-university',
                'cor' => 'text-primary',
                'descricao' => 'Depósito direto na conta bancária',
                'requer_referencia' => true
            ],
            'multicaixa_express' => [
                'nome' => 'Multicaixa Express',
                'icone' => 'fas fa-mobile-alt',
                'cor' => 'text-success',
                'descricao' => 'Pagamento via Multicaixa Express',
                'requer_referencia' => true
            ],
            'transferencia' => [
                'nome' => 'Transferência Bancária',
                'icone' => 'fas fa-exchange-alt',
                'cor' => 'text-warning',
                'descricao' => 'Transferência bancária tradicional',
                'requer_referencia' => true
            ]
        ];
    }

    private function calcularValorTotal()
    {
        if (!$this->pacote) return;

        $this->valorMensal = $this->pacote->preco;

        if ($this->duracaoMeses === 'vitalicio') {
            // Usar preço vitalício se disponível, senão calcular baseado nos meses
            $this->valorTotal = $this->pacote->preco_vitalicio ?? ($this->valorMensal * 120); // 10 anos
        } else {
            $this->valorTotal = $this->valorMensal * $this->duracaoMeses;
        }
    }

    private function validarAcaoParaIgrejaSelecionada()
    {
        $assinaturaAtual = AssinaturaAtual::where('igreja_id', $this->igrejaSelecionada)->first();

        if ($this->acao === 'nova_assinatura' && $assinaturaAtual && !$assinaturaAtual->isExpired()) {
            throw new \Exception('Esta igreja já possui uma assinatura ativa. Use a opção de upgrade ou renovação.');
        }

        // Permitir renovação mesmo com assinatura ativa (renovação antecipada)
        // Só impedir se não houver assinatura alguma
        if ($this->acao === 'renovar' && !$assinaturaAtual) {
            throw new \Exception('Esta igreja não possui assinatura para renovar. Use a opção de nova assinatura.');
        }

        if ($this->acao === 'upgrade' && (!$assinaturaAtual || $assinaturaAtual->isExpired())) {
            throw new \Exception('Esta igreja não possui assinatura ativa. Use a opção de nova assinatura.');
        }

        // Para upgrade, verificar se o pacote é realmente superior
        if ($this->acao === 'upgrade' && $assinaturaAtual && $this->pacote->preco <= $assinaturaAtual->pacote->preco) {
            throw new \Exception('Para fazer upgrade, selecione um pacote superior ao atual.');
        }
    }

    public function getAcaoFormatada()
    {
        return match($this->acao) {
            'nova_assinatura' => 'Nova Assinatura',
            'renovar' => 'Renovar Assinatura',
            'upgrade' => 'Fazer Upgrade',
            default => 'Assinar'
        };
    }

    public function getDuracaoOpcoes()
    {
        return [
            '1' => '1 Mês',
            '3' => '3 Meses',
            '6' => '6 Meses',
            '12' => '1 Ano',
            'vitalicio' => 'Vitalício'
        ];
    }

    public function getIgrejasFormatadas()
    {
        return $this->igrejasDisponiveis ?? collect();
    }

    public function render()
    {
        return view('subscription.payment-page', [
            'metodosPagamento' => $this->getMetodosPagamento(),
            'acaoFormatada' => $this->getAcaoFormatada(),
            'duracaoOpcoes' => $this->getDuracaoOpcoes(),
            'igrejasDisponiveis' => $this->getIgrejasFormatadas(),
            'mensagensValidacao' => $this->mensagensValidacao,
        ]);
    }
}