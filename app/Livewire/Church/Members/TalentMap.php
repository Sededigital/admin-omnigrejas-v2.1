<?php

namespace App\Livewire\Church\Members;

use Livewire\Component;

use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;


#[Title('Mapa de Talentos | Portal da Igreja')]
#[Layout('components.layouts.app')]
class TalentMap extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $selectedLevel = '';
    public $perPage = 12;

    // Modal properties
    public $showModal = false;
    public $editingMember = null;
    public $selectedMember = '';
    public $newSkill = '';
    public $newSkillLevel = 'iniciante';

    // Delete modal properties
    public $showDeleteModal = false;
    public $deleteMemberId;
    public $deleteSkill;

    protected $rules = [
        'selectedMember' => 'required|uuid|exists:igreja_membros,id',
        'newSkill' => 'required|string|max:255',
        'newSkillLevel' => 'required|in:iniciante,intermediario,avancado',
    ];

    protected $listeners = ['refreshTalents' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedLevel()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedLevel = '';
        $this->resetPage();
    }

    public function openModal($memberId = null)
    {
        try {
            if ($memberId) {
                $this->editingMember = IgrejaMembro::with('user')->find($memberId);
                if (!$this->editingMember) {
                    $this->dispatch('toast', [
                        'type' => 'danger',
                        'message' => 'Membro não encontrado!'
                    ]);
                    return;
                }
                $this->selectedMember = $memberId;
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
    }

    public function openDeleteModal($memberId, $skill)
    {
        $this->deleteMemberId = $memberId;
        $this->deleteSkill = $skill;
        $this->showDeleteModal = true;
    }

    public function confirmDelete()
    {
        $this->removeSkill($this->deleteMemberId, $this->deleteSkill);
        $this->closeDeleteModal();
        $this->js("setTimeout(() => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteSkillModal'));
            if (modal) modal.hide();
        }, 150);");
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteMemberId = null;
        $this->deleteSkill = null;
    }

    private function resetModal()
    {
        $this->editingMember = null;
        $this->selectedMember = '';
        $this->newSkill = '';
        $this->newSkillLevel = 'iniciante';
        $this->resetValidation();
    }

    public function addSkill()
    {
        $this->validate();

        try {
            // Verificar se a habilidade já existe para este membro
            $existingSkill = DB::table('habilidades_membros')
                ->where('membro_id', $this->selectedMember)
                ->where('habilidade', $this->newSkill)
                ->first();

            if ($existingSkill) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Esta habilidade já foi cadastrada para este membro!'
                ]);
                return;
            }

            // Adicionar nova habilidade
            DB::table('habilidades_membros')->insert([
                'membro_id' => $this->selectedMember,
                'habilidade' => $this->newSkill,
                'nivel' => $this->newSkillLevel,
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Habilidade adicionada com sucesso!'
            ]);

            $this->resetModal();
            $this->dispatch('refreshTalents');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao adicionar habilidade: ' . $e->getMessage()
            ]);
        }
    }

    public function removeSkill($memberId, $skill)
    {
        try {
            DB::table('habilidades_membros')
                ->where('membro_id', $memberId)
                ->where('habilidade', $skill)
                ->delete();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Habilidade removida com sucesso!'
            ]);

            $this->dispatch('refreshTalents');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao remover habilidade: ' . $e->getMessage()
            ]);
        }
    }

    public function updateSkillLevel($memberId, $skill, $newLevel)
    {
        try {
            DB::table('habilidades_membros')
                ->where('membro_id', $memberId)
                ->where('habilidade', $skill)
                ->update(['nivel' => $newLevel]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Nível da habilidade atualizado!'
            ]);

            $this->dispatch('refreshTalents');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao atualizar nível: ' . $e->getMessage()
            ]);
        }
    }

    public function getMembersWithSkills()
    {
        try {
            // Obter a igreja do usuário logado
            $igrejaId = Auth::user()->getIgrejaId();

            if (!$igrejaId) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Usuário não está associado a nenhuma Igreja ativa'
                ]);

                return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
            }

            $query = IgrejaMembro::with(['user', 'membroPerfil'])
                ->where('status', 'ativo')
                ->where('igreja_id', $igrejaId)
                ->whereHas('user', function ($q) {
                    if ($this->search) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    }
                });

            // Filtrar por nível de habilidade se selecionado
            if ($this->selectedLevel) {
                $query->whereHas('habilidades', function ($q) {
                    $q->where('nivel', $this->selectedLevel);
                });
            }

            return $query->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao carregar membros: ' . $e->getMessage()
            ]);

            return collect()->paginate($this->perPage);
        }
    }

    public function getSkillStats()
    {
        try {
            $igrejaId = Auth::user()->getIgrejaId();

            if (!$igrejaId) {
                return [
                    'total_members' => 0,
                    'members_with_skills' => 0,
                    'total_skills' => 0,
                    'beginner_count' => 0,
                    'intermediate_count' => 0,
                    'advanced_count' => 0,
                ];
            }

            $totalMembers = IgrejaMembro::where('igreja_id', $igrejaId)->where('status', 'ativo')->count();
            $membersWithSkills = DB::table('habilidades_membros')
                ->join('igreja_membros', 'habilidades_membros.membro_id', '=', 'igreja_membros.id')
                ->where('igreja_membros.igreja_id', $igrejaId)
                ->distinct('habilidades_membros.membro_id')
                ->count('habilidades_membros.membro_id');
            $totalSkills = DB::table('habilidades_membros')
                ->join('igreja_membros', 'habilidades_membros.membro_id', '=', 'igreja_membros.id')
                ->where('igreja_membros.igreja_id', $igrejaId)
                ->count();

            $skillLevels = DB::table('habilidades_membros')
                ->join('igreja_membros', 'habilidades_membros.membro_id', '=', 'igreja_membros.id')
                ->where('igreja_membros.igreja_id', $igrejaId)
                ->select('habilidades_membros.nivel', DB::raw('count(*) as count'))
                ->groupBy('habilidades_membros.nivel')
                ->pluck('count', 'habilidades_membros.nivel');

            return [
                'total_members' => $totalMembers,
                'members_with_skills' => $membersWithSkills,
                'total_skills' => $totalSkills,
                'beginner_count' => $skillLevels->get('iniciante', 0),
                'intermediate_count' => $skillLevels->get('intermediario', 0),
                'advanced_count' => $skillLevels->get('avancado', 0),
            ];

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao carregar estatísticas: ' . $e->getMessage()
            ]);
            return [
                'total_members' => 0,
                'members_with_skills' => 0,
                'total_skills' => 0,
                'beginner_count' => 0,
                'intermediate_count' => 0,
                'advanced_count' => 0,
            ];
        }
    }

    public function getSkillBadgeClass($level)
    {
        return match($level) {
            'iniciante' => 'bg-info text-light',
            'intermediario' => 'bg-warning',
            'avancado' => 'bg-success',
            default => 'bg-secondary'
        };
    }

    public function getSkillLevelText($level)
    {
        return match($level) {
            'iniciante' => 'Iniciante',
            'intermediario' => 'Intermediário',
            'avancado' => 'Avançado',
            default => 'Desconhecido'
        };
    }

    public function render()
    {
        return view('church.members.talent-map', [
            'members' => $this->getMembersWithSkills(),
            'stats' => $this->getSkillStats(),
        ]);
    }
}
