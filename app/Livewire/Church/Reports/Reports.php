<?php

namespace App\Livewire\Church\Reports;

use App\Models\Igrejas\RelatorioCulto;
use App\Models\Eventos\Evento;
use App\Models\Igrejas\CultoPadrao;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Relatórios de Culto')]
#[Layout('components.layouts.app')]
class Reports extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // Propriedades básicas
    public $igreja;
    public $showModal = false;
    public $editingReport = null;
    public $modoEdicao = false;
    public $relatorioVisualizacao = null;
    public $showViewModal = false;
    public $exportingReport = false;
    public $relatorioParaExcluir = null;
    public $showDeleteModal = false;

    // Filtros e busca
    public $search = '';
    public $filtroStatus = '';
    public $filtroData = '';
    public $perPage = 10;

    // Propriedades do formulário
    public $igreja_id = '';
    public $evento_id = '';
    public $culto_padrao_id = '';
    public $titulo = '';
    public $conteudo = '';
    public $numero_participantes = '';
    public $valor_oferta = '';
    public $observacoes = '';
    public $status = 'rascunho';
    public $data_relatorio = '';

    // Novos campos para estatísticas detalhadas
    public $numero_visitantes = '';
    public $numero_decisoes = '';
    public $numero_batismos = '';
    public $numero_conversoes = '';
    public $numero_reconciliacoes = '';
    public $numero_casamentos = '';
    public $numero_funeral = '';
    public $numero_outros_eventos = '';

    // Novos campos para valores financeiros
    public $valor_dizimos = '';
    public $valor_ofertas = '';
    public $valor_doacoes = '';
    public $valor_outros = '';

    // Novos campos para informações do culto
    public $tema_culto = '';
    public $pregador = '';
    public $pregador_convidado = '';
    public $texto_base = '';
    public $resumo_mensagem = '';
    public $tipo_culto = 'outro';
    public $dirigente = '';
    public $musica_responsavel = '';
    public $observacoes_gerais = '';

    // Campos de avaliação
    public $avaliado_por = '';
    public $data_avaliacao = '';

    protected $listeners = [
        'refreshReports' => '$refresh',
        'closeModal' => 'fecharModal',
        'modalClosed' => 'fecharModal',
        'openModal' => 'abrirModalEditar',
        'openDeleteModal' => 'abrirModalExclusao'
    ];

    public function mount()
    {
        $this->igreja = Auth::user()->getIgreja();

        if (!$this->igreja) {

            $dashboardRouteName = Auth::user()->redirectDashboardRoute();

            // 2. Redireciona usando o método do Livewire, com a opção 'navigate: true'
            return $this->redirect(route($dashboardRouteName), navigate: true);
        }

        $this->igreja_id = $this->igreja->id;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroStatus()
    {
        $this->resetPage();
    }

    public function updatingFiltroData()
    {
        $this->resetPage();
    }

    public function updatedValorDizimos()
    {
        // Método para atualizar quando valor_dizimos muda
        // Pode ser usado para cálculos em tempo real no futuro
    }

    public function updatedValorOfertas()
    {
        // Método para atualizar quando valor_ofertas muda
        // Pode ser usado para cálculos em tempo real no futuro
    }

    public function updatedValorDoacoes()
    {
        // Método para atualizar quando valor_doacoes muda
        // Pode ser usado para cálculos em tempo real no futuro
    }

    public function updatedValorOutros()
    {
        // Método para atualizar quando valor_outros muda
        // Pode ser usado para cálculos em tempo real no futuro
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filtroStatus = '';
        $this->filtroData = '';
        $this->resetPage();
    }

    public function abrirModalNovo()
    {

        $this->resetFormulario();
        $this->editingReport = null;
        $this->modoEdicao = false;
        $this->dispatch('openModal', null);
    }

    public function abrirModalEditar($reportId = null)
    {
        if ($reportId) {
            $report = RelatorioCulto::with(['evento', 'cultoPadrao'])->find($reportId);
            if ($report && $report->igreja_id === $this->igreja->id) {
                $this->editingReport = $report;
                $this->preencherFormulario($report);
                $this->modoEdicao = true;
            }
        } else {
            // Sempre resetar formulário quando criando novo relatório

            $this->resetFormulario();
            $this->modoEdicao = false;
        }
    }

    public function openModal($reportId = null)
    {
        $this->abrirModalEditar($reportId);
    }

    public function fecharModal()
    {
        $this->showModal = false;
        $this->resetFormulario();
        $this->editingReport = null;
        $this->modoEdicao = false;
    }

    public function fecharModalVisualizacao()
    {
        $this->showViewModal = false;
        $this->relatorioVisualizacao = null;
    }

    public function abrirModalExclusao($reportId)
    {
        $report = RelatorioCulto::find($reportId);
        if ($report && $report->igreja_id === $this->igreja->id) {
            $this->relatorioParaExcluir = $report;
            $this->showDeleteModal = true;
        }
    }

    public function fecharModalExclusao()
    {
        $this->showDeleteModal = false;
        $this->relatorioParaExcluir = null;
    }

    public function confirmarExclusao()
    {
        if ($this->relatorioParaExcluir) {
            $this->relatorioParaExcluir->delete();
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Relatório excluído com sucesso!'
            ]);
            $this->dispatch('refreshReports');
            $this->fecharModalExclusao();
        }
    }

    public function visualizarRelatorio($reportId)
    {


        $report = RelatorioCulto::with(['evento', 'cultoPadrao', 'criadoPor'])->find($reportId);
        if ($report && $report->igreja_id === $this->igreja->id) {
            $this->relatorioVisualizacao = $report;
            $this->showViewModal = true;

        } else {
            $this->relatorioVisualizacao = null;
            $this->showViewModal = false;
        }
    }

    private function resetFormulario()
    {

        $this->editingReport = null;
        $this->evento_id = '';
        $this->culto_padrao_id = '';
        $this->titulo = '';
        $this->conteudo = '';
        $this->numero_participantes = '';
        $this->valor_oferta = '';
        $this->observacoes = '';
        $this->status = 'rascunho';
        $this->data_relatorio = date('Y-m-d');

        // Reset novos campos
        $this->numero_visitantes = '';
        $this->numero_decisoes = '';
        $this->numero_batismos = '';
        $this->numero_conversoes = '';
        $this->numero_reconciliacoes = '';
        $this->numero_casamentos = '';
        $this->numero_funeral = '';
        $this->numero_outros_eventos = '';
        $this->valor_dizimos = '';
        $this->valor_ofertas = '';
        $this->valor_doacoes = '';
        $this->valor_outros = '';
        $this->tema_culto = '';
        $this->pregador = '';
        $this->pregador_convidado = '';
        $this->texto_base = '';
        $this->resumo_mensagem = '';
        $this->tipo_culto = 'outro';
        $this->dirigente = '';
        $this->musica_responsavel = '';
        $this->observacoes_gerais = '';
        $this->avaliado_por = '';
        $this->data_avaliacao = '';

        $this->resetValidation();
    }

    private function preencherFormulario($report)
    {
        $this->igreja_id = $report->igreja_id;
        $this->evento_id = $report->evento_id;
        $this->culto_padrao_id = $report->culto_padrao_id;
        $this->titulo = $report->titulo;
        $this->conteudo = $report->conteudo;
        $this->numero_participantes = $report->numero_participantes;
        $this->valor_oferta = $report->valor_oferta;
        $this->observacoes = $report->observacoes;
        $this->status = $report->status;
        $this->data_relatorio = $report->data_relatorio ? $report->data_relatorio->format('Y-m-d') : '';

        // Preencher novos campos
        $this->numero_visitantes = $report->numero_visitantes;
        $this->numero_decisoes = $report->numero_decisoes;
        $this->numero_batismos = $report->numero_batismos;
        $this->numero_conversoes = $report->numero_conversoes;
        $this->numero_reconciliacoes = $report->numero_reconciliacoes;
        $this->numero_casamentos = $report->numero_casamentos;
        $this->numero_funeral = $report->numero_funeral;
        $this->numero_outros_eventos = $report->numero_outros_eventos;
        $this->valor_dizimos = $report->valor_dizimos;
        $this->valor_ofertas = $report->valor_ofertas;
        $this->valor_doacoes = $report->valor_doacoes;
        $this->valor_outros = $report->valor_outros;
        $this->tema_culto = $report->tema_culto;
        $this->pregador = $report->pregador;
        $this->pregador_convidado = $report->pregador_convidado;
        $this->texto_base = $report->texto_base;
        $this->resumo_mensagem = $report->resumo_mensagem;
        $this->tipo_culto = $report->tipo_culto;
        $this->dirigente = $report->dirigente;
        $this->musica_responsavel = $report->musica_responsavel;
        $this->observacoes_gerais = $report->observacoes_gerais;
        // Campos de avaliação - só preenchidos se já foram definidos
        $this->avaliado_por = $report->avaliado_por;
        $this->data_avaliacao = $report->data_avaliacao ? $report->data_avaliacao->format('Y-m-d H:i:s') : '';
    }

    public function salvarRelatorio()
    {
        $this->validate([
            'titulo' => 'nullable|string|max:255',
            'conteudo' => 'required|string',
            'numero_participantes' => 'nullable|integer|min:0',
            'valor_oferta' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string|max:1000',
            'status' => 'required|in:rascunho,finalizado',
            'data_relatorio' => 'required|date',
            'evento_id' => 'nullable|uuid|exists:eventos,id',
            'culto_padrao_id' => 'nullable|uuid|exists:cultos_padrao,id',
            // Validação dos novos campos
            'numero_visitantes' => 'nullable|integer|min:0',
            'numero_decisoes' => 'nullable|integer|min:0',
            'numero_batismos' => 'nullable|integer|min:0',
            'numero_conversoes' => 'nullable|integer|min:0',
            'numero_reconciliacoes' => 'nullable|integer|min:0',
            'numero_casamentos' => 'nullable|integer|min:0',
            'numero_funeral' => 'nullable|integer|min:0',
            'numero_outros_eventos' => 'nullable|integer|min:0',
            'valor_dizimos' => 'nullable|numeric|min:0',
            'valor_ofertas' => 'nullable|numeric|min:0',
            'valor_doacoes' => 'nullable|numeric|min:0',
            'valor_outros' => 'nullable|numeric|min:0',
            'tema_culto' => 'nullable|string|max:255',
            'pregador' => 'nullable|string|max:255',
            'pregador_convidado' => 'nullable|string|max:255',
            'texto_base' => 'nullable|string|max:1000',
            'resumo_mensagem' => 'nullable|string|max:1000',
            'tipo_culto' => 'nullable|in:domingo,sexta,vigilia,especial,outro',
            'dirigente' => 'nullable|string|max:255',
            'musica_responsavel' => 'nullable|string|max:255',
            'observacoes_gerais' => 'nullable|string|max:1000',
            'avaliado_por' => 'nullable|string|max:255',
            'data_avaliacao' => 'nullable|date',
        ], [
            'conteudo.required' => 'O conteúdo do relatório é obrigatório.',
            'numero_participantes.integer' => 'O número de participantes deve ser um número inteiro.',
            'numero_participantes.min' => 'O número de participantes não pode ser negativo.',
            'valor_oferta.numeric' => 'O valor da oferta deve ser um número.',
            'valor_oferta.min' => 'O valor da oferta não pode ser negativo.',
            'data_relatorio.required' => 'A data do relatório é obrigatória.',
            'evento_id.exists' => 'O evento selecionado não foi encontrado.',
            'culto_padrao_id.exists' => 'O culto padrão selecionado não foi encontrado.',
            // Mensagens para novos campos
            'numero_visitantes.integer' => 'O número de visitantes deve ser um número inteiro.',
            'numero_decisoes.integer' => 'O número de decisões deve ser um número inteiro.',
            'numero_batismos.integer' => 'O número de batismos deve ser um número inteiro.',
            'numero_conversoes.integer' => 'O número de conversões deve ser um número inteiro.',
            'numero_reconciliacoes.integer' => 'O número de reconciliações deve ser um número inteiro.',
            'numero_casamentos.integer' => 'O número de casamentos deve ser um número inteiro.',
            'numero_funeral.integer' => 'O número de funerais deve ser um número inteiro.',
            'numero_outros_eventos.integer' => 'O número de outros eventos deve ser um número inteiro.',
            'valor_dizimos.numeric' => 'O valor dos dízimos deve ser um número.',
            'valor_ofertas.numeric' => 'O valor das ofertas deve ser um número.',
            'valor_doacoes.numeric' => 'O valor das doações deve ser um número.',
            'valor_outros.numeric' => 'O valor de outros deve ser um número.',
        ]);

        try {
            // Preparar dados de avaliação automática
            $dadosAvaliacao = [];
            if ($this->status === 'finalizado') {
                $dadosAvaliacao = [
                    'avaliado_por' => Auth::user()->name,
                    'data_avaliacao' => now()->format('Y-m-d H:i:s'),
                ];
            } elseif ($this->status === 'rascunho') {
                // Limpar avaliação se voltar para rascunho
                $dadosAvaliacao = [
                    'avaliado_por' => null,
                    'data_avaliacao' => null,
                ];
            }

            // Converter strings vazias para NULL para campos numéricos
            $camposNumericos = [
                'numero_participantes', 'valor_oferta', 'numero_visitantes', 'numero_decisoes',
                'numero_batismos', 'numero_conversoes', 'numero_reconciliacoes', 'numero_casamentos',
                'numero_funeral', 'numero_outros_eventos', 'valor_dizimos', 'valor_ofertas',
                'valor_doacoes', 'valor_outros'
            ];

            foreach ($camposNumericos as $campo) {
                if (isset($this->$campo) && $this->$campo === '') {
                    $this->$campo = null;
                }
            }

            if ($this->modoEdicao && $this->editingReport) {
                // Atualizar relatório existente
                $dadosUpdate = [
                    'evento_id' => $this->evento_id ?: null,
                    'culto_padrao_id' => $this->culto_padrao_id ?: null,
                    'titulo' => $this->titulo,
                    'conteudo' => $this->conteudo,
                    'numero_participantes' => $this->numero_participantes,
                    'valor_oferta' => $this->valor_oferta,
                    'observacoes' => $this->observacoes,
                    'status' => $this->status,
                    'data_relatorio' => $this->data_relatorio,
                    // Novos campos
                    'numero_visitantes' => $this->numero_visitantes,
                    'numero_decisoes' => $this->numero_decisoes,
                    'numero_batismos' => $this->numero_batismos,
                    'numero_conversoes' => $this->numero_conversoes,
                    'numero_reconciliacoes' => $this->numero_reconciliacoes,
                    'numero_casamentos' => $this->numero_casamentos,
                    'numero_funeral' => $this->numero_funeral,
                    'numero_outros_eventos' => $this->numero_outros_eventos,
                    'valor_dizimos' => $this->valor_dizimos,
                    'valor_ofertas' => $this->valor_ofertas,
                    'valor_doacoes' => $this->valor_doacoes,
                    'valor_outros' => $this->valor_outros,
                    'tema_culto' => $this->tema_culto,
                    'pregador' => $this->pregador,
                    'pregador_convidado' => $this->pregador_convidado,
                    'texto_base' => $this->texto_base,
                    'resumo_mensagem' => $this->resumo_mensagem,
                    'tipo_culto' => $this->tipo_culto,
                    'dirigente' => $this->dirigente,
                    'musica_responsavel' => $this->musica_responsavel,
                    'observacoes_gerais' => $this->observacoes_gerais,
                ];

                // Mesclar dados de avaliação
                $dadosUpdate = array_merge($dadosUpdate, $dadosAvaliacao);

                $this->editingReport->update($dadosUpdate);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Relatório atualizado com sucesso!'
                ]);

            } else {
                // Criar novo relatório
                $dadosCreate = [
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'igreja_id' => $this->igreja_id,
                    'evento_id' => $this->evento_id ?: null,
                    'culto_padrao_id' => $this->culto_padrao_id ?: null,
                    'created_by' => Auth::id(),
                    'titulo' => $this->titulo,
                    'conteudo' => $this->conteudo,
                    'numero_participantes' => $this->numero_participantes,
                    'valor_oferta' => $this->valor_oferta,
                    'observacoes' => $this->observacoes,
                    'status' => $this->status,
                    'data_relatorio' => $this->data_relatorio,
                    // Novos campos
                    'numero_visitantes' => $this->numero_visitantes,
                    'numero_decisoes' => $this->numero_decisoes,
                    'numero_batismos' => $this->numero_batismos,
                    'numero_conversoes' => $this->numero_conversoes,
                    'numero_reconciliacoes' => $this->numero_reconciliacoes,
                    'numero_casamentos' => $this->numero_casamentos,
                    'numero_funeral' => $this->numero_funeral,
                    'numero_outros_eventos' => $this->numero_outros_eventos,
                    'valor_dizimos' => $this->valor_dizimos,
                    'valor_ofertas' => $this->valor_ofertas,
                    'valor_doacoes' => $this->valor_doacoes,
                    'valor_outros' => $this->valor_outros,
                    'tema_culto' => $this->tema_culto,
                    'pregador' => $this->pregador,
                    'pregador_convidado' => $this->pregador_convidado,
                    'texto_base' => $this->texto_base,
                    'resumo_mensagem' => $this->resumo_mensagem,
                    'tipo_culto' => $this->tipo_culto,
                    'dirigente' => $this->dirigente,
                    'musica_responsavel' => $this->musica_responsavel,
                    'observacoes_gerais' => $this->observacoes_gerais,
                ];

                // Mesclar dados de avaliação
                $dadosCreate = array_merge($dadosCreate, $dadosAvaliacao);

                RelatorioCulto::create($dadosCreate);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Relatório criado com sucesso!'
                ]);
            }

            $this->fecharModal();
            $this->dispatch('refreshReports');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Erro de validação - campos com erro

            $erros = $e->errors();
            $camposComErro = array_keys($erros);
            $mensagemErro = 'Campos com erro: ' . implode(', ', $camposComErro) . '. Corrija os campos destacados em vermelho.';

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => $mensagemErro
            ]);
            throw $e; // Re-throw para mostrar erros nos campos

        } catch (\Exception $e) {
            // Log do erro
            \Illuminate\Support\Facades\Log::error('Erro ao salvar relatório: ' . $e->getMessage(), [
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'igreja_id' => $this->igreja->id ?? null,
                'dados' => [
                    'titulo' => $this->titulo,
                    'conteudo' => $this->conteudo,
                    'data_relatorio' => $this->data_relatorio,
                    'status' => $this->status,
                ],
                'exception' => $e
            ]);

            // Emitir alerta de erro
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar relatório. Verifique os campos obrigatórios e tente novamente.'
            ]);

            // Não fechar modal para permitir correção
        }
    }

    public function excluirRelatorio($reportId)
    {
        $report = RelatorioCulto::find($reportId);
        if ($report && $report->igreja_id === $this->igreja->id) {
            $report->delete();
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Relatório excluído com sucesso!'
            ]);
            $this->dispatch('refreshReports');
        }
    }

    public function alterarStatus($reportId)
    {
        $report = RelatorioCulto::find($reportId);
        if ($report && $report->igreja_id === $this->igreja->id) {
            $novoStatus = $report->status === 'rascunho' ? 'finalizado' : 'rascunho';
            $report->update(['status' => $novoStatus]);

            $mensagem = $novoStatus === 'finalizado' ? 'Relatório finalizado!' : 'Relatório movido para rascunho!';
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => $mensagem
            ]);
            $this->dispatch('refreshReports');
        }
    }

    public function exportReport($reportId)
    {
        $this->exportingReport = true;

        try {
            $report = RelatorioCulto::with(['evento', 'cultoPadrao', 'criadoPor', 'igreja'])
                ->where('igreja_id', $this->igreja->id)
                ->findOrFail($reportId);

            // Gerar PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('church.reports.pdf.report', [
                'report' => $report,
                'igreja' => $this->igreja
            ]);

            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 96
            ]);

            // Nome do arquivo
            $fileName = 'relatorio-culto-' . ($report->titulo ?: 'sem-titulo') . '-' . $report->id . '.pdf';

            // Salvar PDF temporariamente para anexar no email
            $tempPath = storage_path('app/temp/' . $fileName);
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            file_put_contents($tempPath, $pdf->output());

            // Enviar email com o PDF anexado
            try {
                \Illuminate\Support\Facades\Mail::to(Auth::user()->email)->send(
                    new \App\Mail\ReportMail($report, $tempPath)
                );

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Relatório gerado e enviado por email com sucesso!'
                ]);

                // Limpar arquivo temporário após envio
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

            } catch (\Exception $emailError) {
                \Illuminate\Support\Facades\Log::error('Erro ao enviar email do relatório', [
                    'report_id' => $reportId,
                    'email' => Auth::user()->email,
                    'error' => $emailError->getMessage()
                ]);

                // Mesmo com erro no email, permitir download do PDF
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Relatório gerado, mas houve erro no envio por email. Fazendo download...'
                ]);
            }

            // Retornar download do PDF
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $fileName);

        } catch (\Exception $e) {
            // Log do erro
            \Illuminate\Support\Facades\Log::error('Erro ao gerar relatório PDF: ' . $e->getMessage(), [
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'igreja_id' => $this->igreja->id ?? null,
                'report_id' => $reportId,
                'exception' => $e
            ]);

            // Flash message de erro
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao gerar relatório PDF. Tente novamente.'
            ]);

            // Redirecionar de volta
            return redirect()->back();
        } finally {
            $this->exportingReport = false;
        }
    }


    public function getRelatoriosProperty()
    {
        $query = RelatorioCulto::with(['evento', 'cultoPadrao', 'criadoPor'])
            ->where('igreja_id', $this->igreja->id);

        // Aplicar filtros
        if ($this->search) {
            $query->where(function($q) {
                $q->where('titulo', 'ilike', '%' . $this->search . '%')
                  ->orWhere('conteudo', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->filtroStatus) {
            $query->where('status', $this->filtroStatus);
        }

        if ($this->filtroData) {
            switch ($this->filtroData) {
                case 'hoje':
                    $query->whereDate('data_relatorio', today());
                    break;
                case 'semana':
                    $query->whereBetween('data_relatorio', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'mes':
                    $query->whereMonth('data_relatorio', now()->month)
                          ->whereYear('data_relatorio', now()->year);
                    break;
                case 'recentes':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
            }
        }

        return $query->orderBy('data_relatorio', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate($this->perPage);
    }

    public function getEstatisticasProperty()
    {
        return [
            'total' => RelatorioCulto::where('igreja_id', $this->igreja->id)->count(),
            'rascunhos' => RelatorioCulto::where('igreja_id', $this->igreja->id)->where('status', 'rascunho')->count(),
            'finalizados' => RelatorioCulto::where('igreja_id', $this->igreja->id)->where('status', 'finalizado')->count(),
            'este_mes' => RelatorioCulto::where('igreja_id', $this->igreja->id)
                                        ->whereMonth('data_relatorio', now()->month)
                                        ->whereYear('data_relatorio', now()->year)
                                        ->count(),
        ];
    }

    public function getEventosDisponiveisProperty()
    {
        return Evento::where('igreja_id', $this->igreja->id)
            ->where('tipo', 'culto')
            ->where('status', 'realizado')
            ->orderBy('data_evento', 'desc')
            ->limit(50)
            ->get();
    }

    public function getCultosPadraoDisponiveisProperty()
    {
        return CultoPadrao::where('igreja_id', $this->igreja->id)
            ->where('ativo', true)
            ->orderBy('titulo')
            ->get();
    }

    public function getStatusLabel($status)
    {
        return match($status) {
            'rascunho' => 'Rascunho',
            'finalizado' => 'Finalizado',
            default => 'Desconhecido'
        };
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'rascunho' => 'warning',
            'finalizado' => 'success',
            default => 'secondary'
        };
    }

    public function render()
    {
        return view('church.reports.reports', [
            'relatorios' => $this->relatorios,
            'estatisticas' => $this->estatisticas,
            'eventosDisponiveis' => $this->eventosDisponiveis,
            'cultosPadraoDisponiveis' => $this->cultosPadraoDisponiveis,
        ]);
    }
}
