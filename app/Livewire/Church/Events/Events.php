<?php

namespace App\Livewire\Church\Events;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Eventos\Evento;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;

#[Title('Gestão de Eventos')]
#[Layout('components.layouts.app')]
class Events extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $selectedStatus = '';
    public $perPage = 10;

    // Modal properties
    public $showModal = false;
    public $editingEvent = null;
    public $titulo = '';
    public $tipo = 'outro';
    public $data_evento = '';
    public $hora_inicio = '';
    public $hora_fim = '';
    public $local_evento = '';
    public $descricao = '';
    public $responsavel = '';
    public $status = 'agendado';

    protected $rules = [
        'titulo' => 'required|string|max:255',
        'tipo' => 'required|in:culto,reuniao,ensaio,evento_social,outro',
        'data_evento' => 'required|date',
        'hora_inicio' => 'required|date_format:H:i',
        'hora_fim' => 'nullable|date_format:H:i|after:hora_inicio',
        'local_evento' => 'nullable|string|max:255',
        'descricao' => 'nullable|string|max:1000',
        'responsavel' => 'nullable|uuid|exists:users,id',
        'status' => 'required|in:agendado,realizado,cancelado',
    ];

    protected $listeners = ['refreshEvents' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus()
    {
        $this->resetPage();
    }

    public function setStatusFilter($status)
    {
        $this->selectedStatus = $status;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedStatus = '';
        $this->resetPage();
    }

    public function openModal($eventId = null)
    {
        if ($eventId) {
            $event = Evento::find($eventId);
            if ($event) {
                $this->editingEvent = $event;
                $this->titulo = $event->titulo;
                $this->tipo = $event->tipo;
                $this->data_evento = $event->data_evento;
                $this->hora_inicio = $event->hora_inicio ? $event->hora_inicio->format('H:i') : '';
                $this->hora_fim = $event->hora_fim ? $event->hora_fim->format('H:i') : '';
                $this->local_evento = $event->local_evento;
                $this->descricao = $event->descricao;
                $this->responsavel = $event->responsavel;
                $this->status = $event->status;
            }
        } else {
            $this->resetModal();
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetModal();
    }

    private function resetModal()
    {
        $this->editingEvent = null;
        $this->titulo = '';
        $this->tipo = 'outro';
        $this->data_evento = '';
        $this->hora_inicio = '';
        $this->hora_fim = '';
        $this->local_evento = '';
        $this->descricao = '';
        $this->responsavel = '';
        $this->status = 'agendado';
        $this->resetValidation();
    }

    public function mount(){

    }

    public function saveEvent()
    {
        try {

            $this->validate();

            // Obter a igreja do usuário logado
            $igrejaId = Auth::user()->getIgrejaId();

            if (!$igrejaId) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Usuário não está associado a nenhuma Igreja ativa'
                ]);
                return;
            }

            $data = [
                'igreja_id' => Auth::user()->getIgrejaId(),
                'titulo' => $this->titulo,
                'tipo' => $this->tipo,
                'data_evento' => $this->data_evento,
                'hora_inicio' => $this->hora_inicio,
                'hora_fim' => $this->hora_fim ?: null,
                'local_evento' => $this->local_evento,
                'descricao' => $this->descricao,
                'responsavel' => $this->responsavel ?: null,
                'status' => $this->status,
                'created_by' => Auth::id(),
            ];

            if ($this->editingEvent) {
                $this->editingEvent->update($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Evento atualizado com sucesso!'
                ]);
            } else {
                Evento::create($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Evento criado com sucesso!'
                ]);
            }

            $this->closeModal();
            $this->dispatch('refreshEvents');
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar evento: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteEvent($eventId)
    {
        try {

            $event = Evento::find($eventId);

            if ($event) {
                $event->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Evento excluído com sucesso!'
                ]);
                $this->dispatch('refreshEvents');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Evento não encontrado.'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir evento: ' . $e->getMessage()
            ]);
        }
    }

    public function toggleEventStatus($eventId)
    {
        $event = Evento::find($eventId);
        if ($event) {
            $newStatus = $event->status === 'agendado' ? 'cancelado' : 'agendado';
            $event->update(['status' => $newStatus]);
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Status do evento alterado com sucesso!'
            ]);
            $this->dispatch('refreshEvents');
        }
    }

    public function getEvents()
    {
        // Obter a igreja do usuário logado
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Usuário não está associado a nenhuma Igreja ativa'
            ]);

            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }

        $query = Evento::query()->where('igreja_id', $igrejaId);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('titulo', 'like', '%' . $this->search . '%')
                  ->orWhere('descricao', 'like', '%' . $this->search . '%')
                  ->orWhere('local_evento', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        return $query->orderBy('data_evento', 'desc')
                    ->paginate($this->perPage);
    }

    public function getEventStats()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            return [
                'total' => 0,
                'upcoming' => 0,
                'ongoing' => 0,
                'completed' => 0,
                'new_this_month' => 0,
            ];
        }

        $totalEvents = Evento::where('igreja_id', $igrejaId)->count();
        $upcomingEvents = Evento::where('igreja_id', $igrejaId)
                                ->where('data_evento', '>=', now())
                                ->where('status', 'agendado')
                                ->count();
        $ongoingEvents = 0; // Não temos status "em_andamento" na tabela
        $completedEvents = Evento::where('igreja_id', $igrejaId)
                                 ->where('status', 'realizado')
                                 ->count();
        $newEventsThisMonth = Evento::where('igreja_id', $igrejaId)
                                    ->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count();

        return [
            'total' => $totalEvents,
            'upcoming' => $upcomingEvents,
            'ongoing' => $ongoingEvents,
            'completed' => $completedEvents,
            'new_this_month' => $newEventsThisMonth,
        ];
    }

    public function getStatusLabel($status)
    {
        return match($status) {
            'agendado' => 'Agendado',
            'realizado' => 'Realizado',
            'cancelado' => 'Cancelado',
            default => 'Desconhecido'
        };
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'agendado' => 'primary',
            'realizado' => 'success',
            'cancelado' => 'danger',
            default => 'secondary'
        };
    }

    public function render()
    {
        return view('church.events.events', [
            'events' => $this->getEvents(),
            'stats' => $this->getEventStats(),
        ]);
    }
}
