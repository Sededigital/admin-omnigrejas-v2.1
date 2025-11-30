<?php

namespace App\Livewire\Church\Members;

use BaconQrCode\Writer;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;
use BaconQrCode\Renderer\ImageRenderer;
use App\Models\CartaoMembro\CartaoMembro;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use App\Models\CartaoMembro\CartaoMembroHistorico;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;


#[Title('Cartões de Membro')]
#[Layout('components.layouts.app')]
class MemberCards extends Component
{
    use WithPagination, WithFileUploads, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // Propriedades básicas
    public $igreja;
    public $cartaoSelecionado = null;
    public $mostrarModal = false;
    public $modoEdicao = false;

    // Filtros e busca
    public $filtroStatus = '';
    public $busca = '';
    public $filtroData = '';

    // Propriedades do formulário
    public $membro_id = '';
    public $numero_cartao = '';
    public $data_emissao = '';
    public $data_validade = '';
    public $status = 'ativo';
    public $foto_arquivo; // Arquivo de upload da foto
    public $foto_url = '';
    public $assinatura_digital = '';
    public $qr_code = '';
    public $qr_code_imagem = ''; // Base64 da imagem QR
    public $custo_producao = 0;
    public $custo_entrega = 0;
    public $observacoes = '';
    public $motivo_inativacao = '';

    // Controle de geração automática
    public $gerar_automaticamente = false;

    // Propriedades do histórico
    public $mostrarHistorico = false;
    public $historicoCartao = [];

    // Propriedades de paginação
    public $perPage = 10;

    // Propriedades para configuração de cores
    public $cor_fundo_header = '#8B5CF6';
    public $cor_texto_header = '#FFFFFF';
    public $cor_texto_principal = '#1F2937';
    public $cor_texto_secundario = '#6B7280';
    public $cor_acento = '#8B5CF6';
    public $cor_status_ativo = '#10B981';
    public $cor_status_inativo = '#DC3545';
    public $cor_status_perdido = '#FD7E14';
    public $cor_status_danificado = '#6F42C1';
    public $cor_status_renovado = '#20C997';
    public $cor_status_cancelado = '#6C757D';

    protected $listeners = [
        'refreshCards' => '$refresh',
        'closeModal' => 'fecharModal',
        'openModal' => 'abrirModalEditar'
    ];

    public function mount($cartaoId = null)
    {
        $this->igreja = Auth::user()->getIgreja();

        if (!$this->igreja) {
            return redirect()->route('dashboard-admin.church')->with('error', 'Igreja não encontrada.');
        }

        // Carregar configurações de cores
        $this->carregarConfiguracao();

        // Se recebeu um ID, carrega o cartão
        if ($cartaoId) {
            $this->carregarCartao($cartaoId);
        }
    }

    public function carregarCartao($cartaoId)
    {
        $cartao = CartaoMembro::where('id', $cartaoId)
            ->where('igreja_id', $this->igreja->id)
            ->first();

        if ($cartao) {
            $this->cartaoSelecionado = $cartao;
            $this->preencherFormulario($cartao);
            $this->modoEdicao = true;
        }
    }

    public function preencherFormulario($cartao)
    {
        $this->membro_id = $cartao->membro_id;
        $this->numero_cartao = $cartao->numero_cartao;
        $this->data_emissao = $cartao->data_emissao ? $cartao->data_emissao->format('Y-m-d') : '';
        $this->data_validade = $cartao->data_validade ? $cartao->data_validade->format('Y-m-d') : '';
        $this->status = $cartao->status;
        $this->foto_url = $cartao->foto_url ?? '';
        $this->assinatura_digital = $cartao->assinatura_digital ?? '';
        $this->qr_code = $cartao->qr_code ?? '';
        $this->custo_producao = $cartao->custo_producao ?? 0;
        $this->custo_entrega = $cartao->custo_entrega ?? 0;
        $this->observacoes = $cartao->observacoes ?? '';
        $this->motivo_inativacao = $cartao->motivo_inativacao ?? '';
    }

    public function abrirModalNovo()
    {
        $this->resetFormulario();
        $this->modoEdicao = false;
        $this->dispatch('openModal', null);
    }

    public function abrirModalEditar($cartaoId = null)
    {
        if ($cartaoId) {
            $cartao = CartaoMembro::find($cartaoId);
            if ($cartao && $cartao->igreja_id === $this->igreja->id) {
                $this->cartaoSelecionado = $cartao;
                $this->preencherFormulario($cartao);
                $this->modoEdicao = true;
            }
        } else {

            if (empty($this->membro_id)) {
                $this->resetFormulario();
            }
            $this->modoEdicao = false;
        }
        $this->dispatch('openModal', $cartaoId);
    }

    public function openModal($cartaoId = null)
    {
        $this->abrirModalEditar($cartaoId);
    }

    public function fecharModal()
    {
        $this->mostrarModal = false;
        $this->resetFormulario();
        $this->cartaoSelecionado = null;
        $this->modoEdicao = false;
    }

    public function resetFormulario()
    {
        $this->membro_id = '';
        $this->numero_cartao = '';
        $this->data_emissao = date('Y-m-d'); // Data atual automática
        $this->data_validade = '';
        $this->status = 'ativo';
        $this->foto_arquivo = null;
        $this->foto_url = '';
        $this->assinatura_digital = '';
        $this->qr_code = '';
        $this->custo_producao = 0;
        $this->custo_entrega = 0;
        $this->observacoes = '';
        $this->motivo_inativacao = '';
    }

    public function regrasValidacao()
    {
        $regras = [
            'status' => 'required|in:ativo,inativo,perdido,danificado,renovado,cancelado',
            'foto_arquivo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ];


        // Em criação, membro_id e data_emissao são obrigatórios
        if (!$this->modoEdicao) {
            $regras['membro_id'] = 'required|exists:igreja_membros,id';
            $regras['data_emissao'] = 'required|date';

            $mensagens['membro_id.required'] = 'Selecione um membro para criar o cartão.';
            $mensagens['membro_id.exists'] = 'O membro selecionado não foi encontrado.';
        } else {
            // Em edição, só valida se foram preenchidos
            $regras['membro_id'] = 'nullable|exists:igreja_membros,id';
            $regras['data_emissao'] = 'nullable|date';

            $mensagens['membro_id.exists'] = 'O membro selecionado não foi encontrado.';
        }

        return [$regras, $mensagens];
    }

    public function salvarCartao()
    {
        [$regras, $mensagens] = $this->regrasValidacao();
        $this->validate($regras, $mensagens);



        DB::beginTransaction();
        try {
            // Faz upload da foto se foi enviada
            $caminhoFoto = $this->fazerUploadFoto();

            if ($this->modoEdicao && $this->cartaoSelecionado) {
                // Atualizar cartão existente
                $dadosAtualizacao = [
                    'membro_id' => $this->membro_id,
                    'numero_cartao' => $this->numero_cartao,
                    'data_emissao' => $this->data_emissao,
                    'data_validade' => $this->data_validade ?: $this->calcularDataValidade(),
                    'status' => $this->status,
                    'assinatura_digital' => $this->assinatura_digital,
                    'qr_code' => $this->qr_code ?: $this->gerarQRCode($this->numero_cartao),
                    'custo_producao' => $this->custo_producao,
                    'custo_entrega' => $this->custo_entrega,
                    'observacoes' => $this->observacoes,
                    'motivo_inativacao' => $this->motivo_inativacao,
                ];

                // Só atualiza a foto se uma nova foi enviada
                if ($caminhoFoto) {
                    $dadosAtualizacao['foto_url'] = $caminhoFoto;
                }

                $this->cartaoSelecionado->update($dadosAtualizacao);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Cartão atualizado com sucesso!'
                ]);

            } else {
                // Verificar se o usuário atual é admin
                $usuarioAtual = Auth::user();
                $isAdmin = $usuarioAtual->role === 'admin' || $usuarioAtual->role === 'super_admin';

                // Buscar o user_id do membro
                $membro = IgrejaMembro::find($this->membro_id);
                $userIdMembro = $membro ? $membro->user_id : null;

                // Preparar dados do cartão
                $dadosCartao = [
                    'id' => (string) Str::uuid(), // Gerar UUID
                    'membro_id' => $this->membro_id,
                    'igreja_id' => $this->igreja->id,
                    'numero_cartao' => $this->numero_cartao ?: $this->gerarNumeroCartaoUnico(),
                    'data_emissao' => $this->data_emissao,
                    'data_validade' => $this->data_validade ?: $this->calcularDataValidade(),
                    'status' => $this->status,
                    'foto_url' => $caminhoFoto ?: $this->foto_url,
                    'assinatura_digital' => $this->assinatura_digital,
                    'qr_code' => $this->qr_code ?: $this->gerarQRCode($this->numero_cartao),
                    'custo_producao' => $this->custo_producao,
                    'custo_entrega' => $this->custo_entrega,
                    'observacoes' => $this->observacoes,
                    'motivo_inativacao' => $this->motivo_inativacao,
                    'solicitado_por' => $userIdMembro, // ID do usuário do membro solicitante
                    'created_by' => Auth::id(),
                ];

                // Se for admin, aprovar automaticamente
                if ($isAdmin) {
                    $dadosCartao['aprovado_por'] = Auth::id();
                    $dadosCartao['aprovado_em'] = now();
                }

                // Criar novo cartão
                $cartao = CartaoMembro::create($dadosCartao);

                // Debug: verificar se o cartão foi criado com ID
                if (!$cartao->id) {
                    throw new \Exception('Falha ao gerar ID do cartão');
                }

                // Registrar no histórico após a criação do cartão
                CartaoMembroHistorico::registrarAcao(
                    $cartao,
                    CartaoMembroHistorico::ACAO_SOLICITADO,
                    'Cartão solicitado para o membro',
                    $usuarioAtual
                );

                // Se for admin, registrar também a aprovação
                if ($isAdmin) {
                    CartaoMembroHistorico::registrarAcao(
                        $cartao,
                        CartaoMembroHistorico::ACAO_APROVADO,
                        'Cartão aprovado automaticamente pelo administrador',
                        $usuarioAtual
                    );
                }

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Cartão criado com sucesso!'
                ]);
            }

            DB::commit();
            $this->fecharModal();
            $this->dispatch('refreshCards');

        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar cartão: ' . $e->getMessage()
            ]);
        }
    }

    public function excluirCartao($cartaoId)
    {
        $cartao = CartaoMembro::find($cartaoId);
        if ($cartao && $cartao->igreja_id === $this->igreja->id) {
            $cartao->delete();
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Cartão excluído com sucesso!'
            ]);
            $this->dispatch('refreshCards');
        }
    }

    public function aprovarCartao($cartaoId)
    {
        $cartao = CartaoMembro::find($cartaoId);
        if ($cartao && $cartao->igreja_id === $this->igreja->id) {
            $cartao->aprovar(Auth::user());
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Cartão aprovado com sucesso!'
            ]);
            $this->dispatch('refreshCards');
        }
    }

    public function marcarComoImpresso($cartaoId)
    {
        $cartao = CartaoMembro::find($cartaoId);
        if ($cartao && $cartao->igreja_id === $this->igreja->id) {
            $cartao->marcarComoImpresso(Auth::user());
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Cartão marcado como impresso!'
            ]);
            $this->dispatch('refreshCards');
        }
    }

    public function marcarComoEntregue($cartaoId)
    {
        $cartao = CartaoMembro::find($cartaoId);
        if ($cartao && $cartao->igreja_id === $this->igreja->id) {
            $cartao->marcarComoEntregue(Auth::user());
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Cartão marcado como entregue!'
            ]);
            $this->dispatch('refreshCards');
        }
    }

    public function renovarCartao($cartaoId)
    {
        $cartao = CartaoMembro::find($cartaoId);
        if ($cartao && $cartao->igreja_id === $this->igreja->id) {
            $cartao->renovar(Auth::user());
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Cartão renovado com sucesso!'
            ]);
            $this->dispatch('refreshCards');
        }
    }

    public function cancelarCartao($cartaoId)
    {
        $cartao = CartaoMembro::find($cartaoId);
        if ($cartao && $cartao->igreja_id === $this->igreja->id) {
            $cartao->cancelar('Cancelado pelo usuário', Auth::user());
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Cartão cancelado com sucesso!'
            ]);
            $this->dispatch('refreshCards');
        }
    }

    public function verHistorico($cartaoId)
    {
        $this->historicoCartao = CartaoMembroHistorico::where('cartao_id', $cartaoId)
            ->with(['realizadoPor'])
            ->orderBy('data_acao', 'desc')
            ->get()
            ->toArray();
    }

    public function fecharHistorico()
    {
        $this->historicoCartao = [];
    }

    public function selecionarCartao($cartaoId)
    {
        $cartao = CartaoMembro::with(['membro.user'])->find($cartaoId);
        if ($cartao && $cartao->igreja_id === $this->igreja->id) {
            $this->cartaoSelecionado = $cartao;
        }
    }



    // Métodos auxiliares
    private function gerarNumeroCartaoUnico()
    {
        do {
            $numero = 'IGREJA-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (CartaoMembro::where('numero_cartao', $numero)->exists());

        return $numero;
    }

    private function calcularDataValidade()
    {
        return now()->addMonths(12)->format('Y-m-d');
    }

    private function gerarQRCode($numeroCartao)
    {
        // Gera dados do QR Code com informações do cartão
        $dadosQR = json_encode([
            'numero_cartao' => $numeroCartao,
            'igreja_id' => $this->igreja->id,
            'membro_id' => $this->membro_id,
            'data_emissao' => $this->data_emissao,
            'data_validade' => $this->data_validade,
            'timestamp' => now()->toISOString()
        ]);

        // Gera QR Code usando BaconQrCode
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($dadosQR);

        return $qrCode;
    }

    private function camposObrigatoriosPreenchidos()
    {
        return !empty($this->membro_id) &&
               !empty($this->data_emissao) &&
               !empty($this->status);
    }

    public function gerarNumeroAutomaticamente()
    {
        if ($this->camposObrigatoriosPreenchidos() && empty($this->numero_cartao)) {
            $this->numero_cartao = $this->gerarNumeroCartaoUnico();
            $this->gerar_automaticamente = true;

            // Gera QR Code automaticamente
            if (!empty($this->numero_cartao)) {
                $this->qr_code_imagem = $this->gerarQRCode($this->numero_cartao);
            }
        }
    }

    public function updatedMembroId()
    {
        // Quando o membro é selecionado, gerar número automaticamente
        $this->gerarNumeroAutomaticamente();
    }


    private function gerarNomePastaIgreja()
    {
        $nomeIgreja = $this->igreja->nome;

        // Remove acentos
        $nomeIgreja = iconv('UTF-8', 'ASCII//TRANSLIT', $nomeIgreja);

        // Converte para maiúsculas
        $nomeIgreja = strtoupper($nomeIgreja);

        // Substitui espaços por underscore
        $nomeIgreja = str_replace(' ', '_', $nomeIgreja);

        // Remove caracteres especiais restantes
        $nomeIgreja = preg_replace('/[^A-Z0-9_]/', '', $nomeIgreja);

        return $nomeIgreja;
    }

    private function  fazerUploadFoto()
    {
        if (!$this->foto_arquivo) {
            return null;
        }

        try {
            // Gera nome da pasta da igreja
            $nomePastaIgreja = $this->gerarNomePastaIgreja();

            // Cria o caminho completo da pasta na pasta public com subpasta cartao
            $caminhoPasta = public_path("omnigrejas-img/{$nomePastaIgreja}/cartao");

            // Cria a pasta se não existir
            if (!file_exists($caminhoPasta)) {
                mkdir($caminhoPasta, 0755, true);
            }

            // Gera nome único para o arquivo
            $nomeArquivo = 'cartao_' . time() . '_' . uniqid() . '.' . $this->foto_arquivo->getClientOriginalExtension();

            // Caminho completo do arquivo de destino
            $caminhoCompleto = $caminhoPasta . '/' . $nomeArquivo;

            // Copia o arquivo do temporary para a pasta public
            if (copy($this->foto_arquivo->getRealPath(), $caminhoCompleto)) {
                // Retorna o caminho relativo à pasta public
                return "omnigrejas-img/{$nomePastaIgreja}/cartao/{$nomeArquivo}";
            } else {
                throw new \Exception('Falha ao copiar arquivo para pasta public');
            }
        } catch (\Exception $e) {
            throw new \Exception('Erro no upload da foto: ' . $e->getMessage());
        }
    }

    // Computed properties
    public function getCartoesProperty()
    {
        $query = CartaoMembro::with(['membro.user'])
            ->where('igreja_id', $this->igreja->id);

        // Aplicar filtros
        if ($this->filtroStatus) {
            $query->where('status', $this->filtroStatus);
        }

        if ($this->busca) {
            $query->whereHas('membro.user', function($q) {
                $q->where('name', 'ilike', '%' . $this->busca . '%');
            })->orWhere('numero_cartao', 'ilike', '%' . $this->busca . '%');
        }

        if ($this->filtroData) {
            switch ($this->filtroData) {
                case 'expirando':
                    $query->where('data_validade', '<=', now()->addDays(30));
                    break;
                case 'expirados':
                    $query->where('data_validade', '<', now());
                    break;
                case 'recentes':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
            }
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate($this->perPage);
    }

    public function getMembrosDisponiveisProperty()
    {
        // Buscar IDs dos membros que já têm cartão
        $membrosComCartao = CartaoMembro::where('igreja_id', $this->igreja->id)
            ->pluck('membro_id')
            ->toArray();

        return IgrejaMembro::with(['user'])
            ->where('igreja_id', $this->igreja->id)
            ->where('status', 'ativo')
            ->whereNotIn('id', $membrosComCartao)
            ->get()
            ->map(function($membro) {
                return [
                    'id' => $membro->id,
                    'nome' => $membro->user->name,
                    'cargo' => $membro->cargo,
                ];
            });
    }

    public function getEstatisticasProperty()
    {
        return [
            'total' => CartaoMembro::where('igreja_id', $this->igreja->id)->count(),
            'ativos' => CartaoMembro::where('igreja_id', $this->igreja->id)->where('status', 'ativo')->count(),
            'expirando' => CartaoMembro::where('igreja_id', $this->igreja->id)->where('data_validade', '<=', now()->addDays(30))->count(),
            'expirados' => CartaoMembro::where('igreja_id', $this->igreja->id)->where('data_validade', '<', now())->count(),
        ];
    }

    public function carregarConfiguracao()
    {
        $config = \App\Models\CartaoConfig::getConfiguracaoIgreja($this->igreja->id);

        if ($config) {
            $this->cor_fundo_header = $config->cor_fundo_header;
            $this->cor_texto_header = $config->cor_texto_header;
            $this->cor_texto_principal = $config->cor_texto_principal;
            $this->cor_texto_secundario = $config->cor_texto_secundario;
            $this->cor_acento = $config->cor_acento;
            $this->cor_status_ativo = $config->cor_status_ativo;
            $this->cor_status_inativo = $config->cor_status_inativo;
            $this->cor_status_perdido = $config->cor_status_perdido;
            $this->cor_status_danificado = $config->cor_status_danificado;
            $this->cor_status_renovado = $config->cor_status_renovado;
            $this->cor_status_cancelado = $config->cor_status_cancelado;
        }
    }

    public function salvarConfiguracao()
    {
        $this->validate([
            'cor_fundo_header' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'cor_texto_header' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'cor_texto_principal' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'cor_texto_secundario' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'cor_acento' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'cor_status_ativo' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'cor_status_inativo' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'cor_status_perdido' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'cor_status_danificado' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'cor_status_renovado' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'cor_status_cancelado' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        ], [
            'cor_fundo_header.required' => 'A cor do fundo do cabeçalho é obrigatória.',
            'cor_fundo_header.regex' => 'A cor deve estar no formato hexadecimal (#RRGGBB).',
            'cor_texto_header.required' => 'A cor do texto do cabeçalho é obrigatória.',
            'cor_texto_header.regex' => 'A cor deve estar no formato hexadecimal (#RRGGBB).',
            'cor_texto_principal.required' => 'A cor do texto principal é obrigatória.',
            'cor_texto_principal.regex' => 'A cor deve estar no formato hexadecimal (#RRGGBB).',
            'cor_texto_secundario.required' => 'A cor do texto secundário é obrigatória.',
            'cor_texto_secundario.regex' => 'A cor deve estar no formato hexadecimal (#RRGGBB).',
            'cor_acento.required' => 'A cor de destaque é obrigatória.',
            'cor_acento.regex' => 'A cor deve estar no formato hexadecimal (#RRGGBB).',
            'cor_status_ativo.required' => 'A cor do status ativo é obrigatória.',
            'cor_status_ativo.regex' => 'A cor deve estar no formato hexadecimal (#RRGGBB).',
            'cor_status_inativo.required' => 'A cor do status inativo é obrigatória.',
            'cor_status_inativo.regex' => 'A cor deve estar no formato hexadecimal (#RRGGBB).',
            'cor_status_perdido.required' => 'A cor do status perdido é obrigatória.',
            'cor_status_perdido.regex' => 'A cor deve estar no formato hexadecimal (#RRGGBB).',
            'cor_status_danificado.required' => 'A cor do status danificado é obrigatória.',
            'cor_status_danificado.regex' => 'A cor deve estar no formato hexadecimal (#RRGGBB).',
            'cor_status_renovado.required' => 'A cor do status renovado é obrigatória.',
            'cor_status_renovado.regex' => 'A cor deve estar no formato hexadecimal (#RRGGBB).',
            'cor_status_cancelado.required' => 'A cor do status cancelado é obrigatória.',
            'cor_status_cancelado.regex' => 'A cor deve estar no formato hexadecimal (#RRGGBB).',
        ]);

        try {
            $config = \App\Models\CartaoConfig::updateOrCreate(
                ['igreja_id' => $this->igreja->id],
                [
                    'cor_fundo_header' => $this->cor_fundo_header,
                    'cor_texto_header' => $this->cor_texto_header,
                    'cor_texto_principal' => $this->cor_texto_principal,
                    'cor_texto_secundario' => $this->cor_texto_secundario,
                    'cor_acento' => $this->cor_acento,
                    'cor_status_ativo' => $this->cor_status_ativo,
                    'cor_status_inativo' => $this->cor_status_inativo,
                    'cor_status_perdido' => $this->cor_status_perdido,
                    'cor_status_danificado' => $this->cor_status_danificado,
                    'cor_status_renovado' => $this->cor_status_renovado,
                    'cor_status_cancelado' => $this->cor_status_cancelado,
                    'created_by' => Auth::id(),
                ]
            );

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Configuração de cores salva com sucesso!'
            ]);

            // Fechar modal
            $this->dispatch('closeConfigModal');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar configuração: ' . $e->getMessage()
            ]);
        }
    }

    public function restaurarPadrao()
    {
        $coresPadrao = \App\Models\CartaoConfig::getCoresPadrao();

        $this->cor_fundo_header = $coresPadrao['cor_fundo_header'];
        $this->cor_texto_header = $coresPadrao['cor_texto_header'];
        $this->cor_texto_principal = $coresPadrao['cor_texto_principal'];
        $this->cor_texto_secundario = $coresPadrao['cor_texto_secundario'];
        $this->cor_acento = $coresPadrao['cor_acento'];
        $this->cor_status_ativo = $coresPadrao['cor_status_ativo'];
        $this->cor_status_inativo = $coresPadrao['cor_status_inativo'];
        $this->cor_status_perdido = $coresPadrao['cor_status_perdido'];
        $this->cor_status_danificado = $coresPadrao['cor_status_danificado'];
        $this->cor_status_renovado = $coresPadrao['cor_status_renovado'];
        $this->cor_status_cancelado = $coresPadrao['cor_status_cancelado'];

        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Cores restauradas para o padrão!'
        ]);
    }

    public function abrirModalConfig()
    {
        $this->carregarConfiguracao();
        $this->dispatch('openConfigModal');
    }

    public function render()
    {
        return view('church.members.member-cards', [
            'cartoes' => $this->cartoes,
            'membrosDisponiveis' => $this->membrosDisponiveis,
            'estatisticas' => $this->estatisticas,
        ]);
    }
}

