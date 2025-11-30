<?php

namespace App\Livewire\Church\Events;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Eventos\Escala;
use App\Models\Eventos\Evento;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;

#[Title('Gestão de escalas')]
#[Layout('components.layouts.app')]
class Scale extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $selectedEvent = '';
    public $perPage = 10;

    // Modal properties
    public $showModal = false;
    public $editingScale = null;
    public $culto_evento_id = '';
    public $membro_id = '';
    public $funcao = '';
    public $observacoes = '';

    protected $rules = [
        'culto_evento_id' => 'required|uuid|exists:eventos,id',
        'membro_id' => 'required|uuid|exists:igreja_membros,id',
        'funcao' => 'required|string|max:255',
        'observacoes' => 'nullable|string|max:500',
    ];

    protected $listeners = ['refreshScales' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedEvent()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedEvent = '';
        $this->resetPage();
    }

    public function openModal($scaleId = null)
    {
        if ($scaleId) {
            $scale = Escala::with(['evento', 'membro'])->find($scaleId);
            if ($scale) {
                $this->editingScale = $scale;
                $this->culto_evento_id = $scale->culto_evento_id;
                $this->membro_id = $scale->membro_id;
                $this->funcao = $scale->funcao;
                $this->observacoes = $scale->observacoes;
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
        $this->editingScale = null;
        $this->culto_evento_id = '';
        $this->membro_id = '';
        $this->funcao = '';
        $this->observacoes = '';
        $this->resetValidation();
    }

    public function saveScale()
    {
        $this->validate();

        $data = [
            'culto_evento_id' => $this->culto_evento_id,
            'membro_id' => $this->membro_id,
            'funcao' => $this->funcao,
            'observacoes' => $this->observacoes,
        ];

        if ($this->editingScale) {
            $this->editingScale->update($data);
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Escala atualizada com sucesso!'
            ]);
        } else {
            Escala::create($data);
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Membro escalado com sucesso!'
            ]);
        }

        $this->closeModal();
        $this->dispatch('refreshScales');
    }

    public function deleteScale($scaleId)
    {
        $scale = Escala::find($scaleId);
        if ($scale) {
            $scale->delete();
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Escala removida com sucesso!'
            ]);
            $this->dispatch('refreshScales');
        }
    }

    public function getScales()
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

        $query = Escala::with(['evento', 'membro.user'])
                       ->whereHas('evento', function ($q) use ($igrejaId) {
                           $q->where('igreja_id', $igrejaId);
                       });

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('evento', function ($eventQuery) {
                    $eventQuery->where('titulo', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('membro.user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('funcao', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedEvent) {
            $query->where('culto_evento_id', $this->selectedEvent);
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate($this->perPage);
    }

    public function getScaleStats()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            return [
                'total' => 0,
                'active' => 0,
                'completed' => 0,
                'new_this_month' => 0,
            ];
        }

        $totalScales = Escala::whereHas('evento', function ($q) use ($igrejaId) {
            $q->where('igreja_id', $igrejaId);
        })->count();

        $activeScales = Escala::whereHas('evento', function ($q) use ($igrejaId) {
            $q->where('igreja_id', $igrejaId)
              ->where('data_evento', '>=', now())
              ->where('status', 'agendado');
        })->count();

        $completedScales = Escala::whereHas('evento', function ($q) use ($igrejaId) {
            $q->where('igreja_id', $igrejaId)
              ->where('status', 'realizado');
        })->count();

        $newScalesThisMonth = Escala::whereHas('evento', function ($q) use ($igrejaId) {
            $q->where('igreja_id', $igrejaId);
        })->whereMonth('created_at', now()->month)
          ->whereYear('created_at', now()->year)
          ->count();

        return [
            'total' => $totalScales,
            'active' => $activeScales,
            'completed' => $completedScales,
            'new_this_month' => $newScalesThisMonth,
        ];
    }

    public function render()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        $events = $igrejaId ? Evento::where('igreja_id', $igrejaId)->where('status', 'agendado')->orderBy('data_evento')->get() : collect();
        $members = $igrejaId ? IgrejaMembro::with('user')->where('igreja_id', $igrejaId)->where('status', 'ativo')->get() : collect();

        return view('church.events.scale', [
            'scales' => $this->getScales(),
            'stats' => $this->getScaleStats(),
            'events' => $events,
            'members' => $members,
        ]);
    }
}
