<?php

namespace App\Livewire\Church\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pedidos\PedidoTipo;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Pedidos\PedidoEspecial;
use App\Models\Igrejas\IgrejaMembro;
use App\Models\Cursos\Curso;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Pedidos Especiais')]
#[Layout('components.layouts.app')]
class SpecialRequests extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $activeTab = 'requests'; // 'requests' ou 'urgent'
    public $search = '';
    public $selectedStatus = '';
    public $selectedType = '';

    // Propriedades para tipos de pedido
    public $showTypeForm = false; // Controla se mostra lista ou formulário
    public $editingType = null;
    public $typeNome = '';
    public $typeDescricao = '';
    public $typeCategoriaId = '';

    // Propriedade para armazenar tipos de pedido disponíveis
    public $tiposPedidoDisponiveis = [];

    // Propriedades do modal
    public $editingRequest = false;
    public $membro_id = '';
    public $pedido_tipo_id = '';
    public $data_pedido = '';
    public $responsavel_id = '';
    public $descricao = '';
    public $status = 'pendente';
    public $curso_id = '';

    protected $queryString = [
        'activeTab' => ['except' => 'requests'],
        'search' => ['except' => ''],
        'selectedStatus' => ['except' => ''],
        'selectedType' => ['except' => '']
    ];

    protected $listeners = [
        'open-request-modal' => 'openModal',
        'view-request' => 'viewRequest'
    ];

    protected $rules = [
        'membro_id' => 'required|exists:igreja_membros,id',
        'pedido_tipo_id' => 'required|exists:pedido_tipos,id',
        'data_pedido' => 'required|date',
        'responsavel_id' => 'nullable|exists:users,id',
        'descricao' => 'required|string|min:10',
        'status' => 'required|in:pendente,em_andamento,aprovado,rejeitado,concluido',
        'curso_id' => 'nullable|exists:cursos,id',
    ];

    protected $messages = [
        'membro_id.required' => 'O membro solicitante é obrigatório.',
        'pedido_tipo_id.required' => 'O tipo de pedido é obrigatório.',
        'data_pedido.required' => 'A data do pedido é obrigatória.',
        'descricao.required' => 'A descrição do pedido é obrigatória.',
        'descricao.min' => 'A descrição deve ter pelo menos 10 caracteres.',
    ];

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

    public function openTypeModal()
    {
        $this->resetValidation();
        $this->reset(['editingType', 'typeNome', 'typeDescricao', 'typeCategoriaId']);
        $this->showTypeForm = false; // Começa mostrando a lista
        $this->dispatch('show-modal', 'typesListModal');
    }

    public function showAddTypeForm()
    {
        $this->resetValidation();
        $this->reset(['editingType', 'typeNome', 'typeDescricao', 'typeCategoriaId']);

        // Preencher automaticamente com a categoria da igreja
        $igreja = Auth::user()->getIgreja();
        if ($igreja && $igreja->categoria_id) {
            $this->typeCategoriaId = $igreja->categoria_id;
        }

        $this->showTypeForm = true;
    }

    public function cancelTypeForm()
    {
        $this->resetValidation();
        $this->reset(['editingType', 'typeNome', 'typeDescricao', 'typeCategoriaId']);
        $this->showTypeForm = false;
    }

    public function editType($typeId)
    {
        $type = \App\Models\Pedidos\PedidoTipo::where('igreja_id', $this->getIgrejaId())
            ->find($typeId);

        if ($type) {
            $this->editingType = $type;
            $this->typeNome = $type->nome;
            $this->typeDescricao = $type->descricao;
            $this->typeCategoriaId = $type->categoria_id;
            $this->showTypeForm = true;
        } else {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Tipo de pedido não encontrado.'
            ]);
        }
    }

    public function saveType()
    {
        $this->validate([
            'typeNome' => [
                'required',
                'string',
                'max:200',
                function ($attribute, $value, $fail) {
                    $igrejaId = $this->getIgrejaId();
                    $query = \App\Models\Pedidos\PedidoTipo::where('igreja_id', $igrejaId)
                        ->where('nome', $value);

                    // Se estiver editando, excluir o próprio registro da verificação
                    if ($this->editingType) {
                        $query->where('id', '!=', $this->editingType->id);
                    }

                    if ($query->exists()) {
                        $fail('Já existe um tipo de pedido com este nome nesta igreja.');
                    }
                },
            ],
            'typeDescricao' => 'nullable|string|max:5000',
            'typeCategoriaId' => 'required|exists:categorias_igrejas,id',
        ], [
            'typeNome.required' => 'O nome do tipo é obrigatório.',
            'typeNome.max' => 'O nome deve ter no máximo 200 caracteres.',
            'typeDescricao.max' => 'A descrição deve ter no máximo 5000 caracteres.',
            'typeCategoriaId.required' => 'A categoria é obrigatória.',
            'typeCategoriaId.exists' => 'Categoria inválida.',
        ]);

        $data = [
            'nome' => $this->typeNome,
            'descricao' => $this->typeDescricao,
            'categoria_id' => $this->typeCategoriaId,
            'igreja_id' => $this->getIgrejaId(),
        ];

        if ($this->editingType) {
            $this->editingType->update($data);
            $message = 'Tipo de pedido atualizado com sucesso!';
        } else {
            \App\Models\Pedidos\PedidoTipo::create($data);
            $message = 'Tipo de pedido criado com sucesso!';

        }

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $message
        ]);

        $this->cancelTypeForm(); // Volta para a lista

        // Recarregar tipos de pedido disponíveis para atualizar o modal de pedido
        $this->tiposPedidoDisponiveis = PedidoTipo::where('igreja_id', $this->getIgrejaId())->where('ativo', TRUE)->get();
    }

    public function deleteType($typeId)
    {
        $type = \App\Models\Pedidos\PedidoTipo::where('igreja_id', $this->getIgrejaId())->find($typeId);

        if ($type) {
            if ($type->pedidosEspeciais()->exists()) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Não é possível excluir este tipo pois existem pedidos associados.'
                ]);
                return;
            }

            $type->delete();
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Tipo de pedido excluído com sucesso!'
            ]);
            
            $this->tiposPedidoDisponiveis = PedidoTipo::where('igreja_id', $this->getIgrejaId())->where('ativo', TRUE)->get();
            // Não precisa recarregar, o componente já se atualiza automaticamente
        } else {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Tipo de pedido não encontrado.'
            ]);
        }
    }

    public function toggleTypeStatus($typeId)
    {
        $type = \App\Models\Pedidos\PedidoTipo::where('igreja_id', $this->getIgrejaId())
            ->find($typeId);

        if ($type) {
            $type->update(['ativo' => !$type->ativo]);
            $status = $type->ativo ? TRUE : FALSE;

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => "Tipo de pedido {$status} com sucesso!"
            ]);

            $this->tiposPedidoDisponiveis = PedidoTipo::where('igreja_id', $this->getIgrejaId())->get();
            // Não precisa recarregar, o componente já se atualiza automaticamente
        }

        $this->tiposPedidoDisponiveis = PedidoTipo::where('igreja_id', $this->getIgrejaId())->get();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedStatus = '';
        $this->selectedType = '';
        $this->resetPage();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function setStatusFilter($status)
    {
        $this->selectedStatus = $status;
        $this->resetPage();
    }

    public function openModal($requestId = null)
    {
        $this->resetValidation();
        $this->resetForm();

        if ($requestId) {
            $request = PedidoEspecial::find($requestId);
            if ($request && $request->igreja_id === $this->getIgrejaId()) {
                $this->editingRequest = $request;
                $this->fill($request->toArray());
                $this->data_pedido = $request->data_pedido ? $request->data_pedido->format('Y-m-d') : '';
            }
        }

        $this->dispatch('show-modal', 'requestModal');
    }

    public function viewRequest($requestId)
    {
        $request = PedidoEspecial::with(['membro.user', 'pedidoTipo', 'responsavel', 'curso'])
            ->find($requestId);

        if ($request && $request->igreja_id === $this->getIgrejaId()) {
            $this->dispatch('view-request-details', $request);
        }
    }

    public function salvarPedido()
    {
        $this->validate();

        $igrejaId = $this->getIgrejaId();

        // Verificar se o membro já fez o mesmo tipo de pedido no mesmo dia
        $pedidoExistente = PedidoEspecial::where('membro_id', $this->membro_id)
            ->where('pedido_tipo_id', $this->pedido_tipo_id)
            ->where('data_pedido', $this->data_pedido)
            ->when($this->editingRequest, function($query) {
                return $query->where('id', '!=', $this->editingRequest->id ?? null);
            })
            ->exists();

        if ($pedidoExistente) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Este membro já fez um pedido do mesmo tipo nesta data. Não é permitido fazer pedidos duplicados no mesmo dia'
            ]);
           
            return;
        }

        $data = [
            'igreja_id' => $igrejaId,
            'membro_id' => $this->membro_id,
            'pedido_tipo_id' => $this->pedido_tipo_id,
            'data_pedido' => $this->data_pedido,
            'responsavel_id' => $this->responsavel_id ?: null,
            'descricao' => $this->descricao,
            'status' => $this->status,
            'curso_id' => $this->curso_id ?: null,
        ];

        if ($this->editingRequest) {
            $request = PedidoEspecial::find($this->editingRequest->id ?? null);
            if ($request) {
                $request->update($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Pedido atualizado com sucesso!'
                ]);
            }
        } else {
            PedidoEspecial::create($data);
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Pedido criado com sucesso!'
            ]);
        }
        
        $this->dispatch('hide-modal', 'requestModal');
        $this->resetForm();
        $this->resetValidation();
    }

    public function approveRequest($requestId)
    {
        $request = PedidoEspecial::find($requestId);
        if ($request && $request->igreja_id === $this->getIgrejaId()) {
            $request->aprovar(Auth::id());
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Pedido aprovado com sucesso!'
            ]);
        }
    }

    public function rejectRequest($requestId)
    {
        $request = PedidoEspecial::find($requestId);
        if ($request && $request->igreja_id === $this->getIgrejaId()) {
            $request->rejeitar(Auth::id(), 'Rejeitado pelo administrador');
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Pedido rejeitado!'
            ]);
        }
    }

    public function generatePdf($requestId)
    {
        try {
            $request = PedidoEspecial::with([
                'membro.user',
                'pedidoTipo',
                'responsavel',
                'curso',
                'igreja'
            ])->findOrFail($requestId);

            // Verificar se o usuário tem acesso a este pedido
            if ($request->igreja_id !== $this->getIgrejaId()) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Acesso negado ao pedido solicitado.'
                ]);
                return;
            }

            $data = [
                'request' => $request,
                'data_emissao' => now()->locale('pt-BR')->isoFormat('LL'),
                'numero_pedido' => 'PED-' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . '-' . now()->format('Y'),
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('church.orders.pdf.special-request', $data);
            $pdf->setPaper('a4', 'portrait');

            $filename = 'pedido-especial-' . $request->id . '-' . now()->format('Y-m-d') . '.pdf';

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao gerar PDF do pedido especial: ' . $e->getMessage(), [
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'request_id' => $requestId,
                'exception' => $e
            ]);

            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao gerar PDF do pedido. Tente novamente.'
            ]);
        }
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'pendente' => 'warning',
            'em_andamento' => 'info',
            'aprovado' => 'success',
            'rejeitado' => 'danger',
            'concluido' => 'primary',
            default => 'secondary'
        };
    }

    public function getStatusLabel($status)
    {
        return match($status) {
            'pendente' => 'Pendente',
            'em_andamento' => 'Em Andamento',
            'aprovado' => 'Aprovado',
            'rejeitado' => 'Rejeitado',
            'concluido' => 'Concluído',
            default => 'Não definido'
        };
    }

    private function resetForm()
    {
        $this->editingRequest = false;
        $this->membro_id = '';
        $this->pedido_tipo_id = '';
        $this->data_pedido = '';
        $this->responsavel_id = '';
        $this->descricao = '';
        $this->status = 'pendente';
        $this->curso_id = '';
    }

    private function getIgrejaId()
    {
        return Auth::user()->getIgrejaId();
    }

    public function render()
    {
        $igrejaId = $this->getIgrejaId();

        // Dados para aba de pedidos especiais
        $query = PedidoEspecial::with(['membro.user', 'pedidoTipo', 'responsavel'])
            ->where('igreja_id', $igrejaId);

        if ($this->search) {
            $query->whereHas('membro.user', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhere('descricao', 'like', '%' . $this->search . '%');
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        if ($this->selectedType) {
            $query->where('pedido_tipo_id', $this->selectedType);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        // Dados para aba de pedidos urgentes
        $urgentQuery = PedidoEspecial::with(['membro.user', 'pedidoTipo'])
            ->where('igreja_id', $igrejaId)
            ->where('status', 'pendente')
            ->where('data_pedido', '<=', now()->subDays(7));

        if ($this->search) {
            $urgentQuery->whereHas('membro.user', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhere('descricao', 'like', '%' . $this->search . '%');
        }

        if ($this->selectedType) {
            $urgentQuery->where('pedido_tipo_id', $this->selectedType);
        }

        $urgentRequests = $urgentQuery->orderBy('data_pedido', 'asc')->paginate(10);

        // Tipos de pedidos
        $requestTypes = PedidoTipo::where('igreja_id', $igrejaId)->get();

        // Dados para o modal
        $membrosDisponiveis = IgrejaMembro::with('user')
            ->where('igreja_id', $igrejaId)
            ->where('status', 'ativo')
            ->get();

        $tiposPedidoDisponiveis = PedidoTipo::where('igreja_id', $igrejaId)->where('ativo', TRUE)->get();

        // Atualizar a propriedade para manter sincronizado
        $this->tiposPedidoDisponiveis = $tiposPedidoDisponiveis;

        $responsaveisDisponiveis = User::whereHas('membros', function($query) use ($igrejaId) {
            $query->where('igreja_id', $igrejaId)
                  ->whereIn('cargo', ['pastor', 'ministro', 'admin' ]);
        })->get();

        $cursosDisponiveis = Curso::where('igreja_id', $igrejaId)->get();

        // Estatísticas
        $stats = [
            'total' => PedidoEspecial::where('igreja_id', $igrejaId)->count(),
            'pending' => PedidoEspecial::where('igreja_id', $igrejaId)->where('status', 'pendente')->count(),
            'in_progress' => PedidoEspecial::where('igreja_id', $igrejaId)->where('status', 'em_andamento')->count(),
            'completed' => PedidoEspecial::where('igreja_id', $igrejaId)->where('status', 'concluido')->count(),
            'urgent' => PedidoEspecial::where('igreja_id', $igrejaId)
                ->where('status', 'pendente')
                ->where('data_pedido', '<=', now()->subDays(7))
                ->count(),
        ];

        return view('church.orders.special-requests',[ 
            'requests' => $requests,
            'urgentRequests' => $urgentRequests,
            'requestTypes' => $requestTypes,
            'stats' => $stats,
            'membrosDisponiveis'=>$membrosDisponiveis,
            'tiposPedidoDisponiveis' => $this->tiposPedidoDisponiveis,
            'responsaveisDisponiveis' => $responsaveisDisponiveis,
            'cursosDisponiveis' => $cursosDisponiveis
        ]);
    }
}
