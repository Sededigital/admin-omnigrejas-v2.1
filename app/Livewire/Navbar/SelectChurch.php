<?php

namespace App\Livewire\Navbar;

use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Helpers\SupabaseHelper;

class SelectChurch extends Component
{
    public $igrejas = [];
    public $selectedIgrejaId;
    public $showAccessCodeModal = false;
    public $accessCode = '';

    public function mount()
    {
        $this->loadIgrejas();
        $this->selectedIgrejaId = session('igreja_atual.id') ?? null;
    }

    private function loadIgrejas()
    {
        $user = Auth::user();

        // Buscar igrejas do usuário
        $igrejasCollection = IgrejaMembro::where('user_id', $user->id)
            ->where('status', 'ativo')
            ->with(['igreja.categoria'])
            ->get()
            ->map(function ($membro) {
                $logoUrl = null;
                if ($membro->igreja->logo) {
                    $logoUrl = SupabaseHelper::obterUrl($membro->igreja->logo);
                }

                return [
                    'id' => $membro->igreja->id,
                    'nome' => $membro->igreja->nome,
                    'sigla' => $membro->igreja->sigla,
                    'categoria' => $membro->igreja->categoria?->nome ?? 'Geral',
                    'cargo' => $membro->cargo,
                    'localizacao' => $membro->igreja->localizacao,
                    'logo' => $logoUrl,
                    'has_logo' => !empty($logoUrl),
                    'code_access' => $membro->igreja->code_access,
                ];
            });

        $this->igrejas = $igrejasCollection->toArray();
    }

    public function selectChurchFromDropdown($igrejaId)
    {
        // Mostrar spinner
        $this->dispatch('show-spinner');

        $this->selectedIgrejaId = $igrejaId;

        $this->validate([
            'selectedIgrejaId' => 'required|exists:igrejas,id',
        ], [
            'selectedIgrejaId.required' => 'Por favor, selecione uma igreja.',
            'selectedIgrejaId.exists' => 'A igreja selecionada não foi encontrada.',
        ]);

        // Verificar se o usuário realmente pertence a esta igreja
        $pertence = IgrejaMembro::where('user_id', Auth::id())
            ->where('igreja_id', $this->selectedIgrejaId)
            ->where('status', 'ativo')
            ->exists();

        if (!$pertence) {
            $this->dispatch('hide-spinner');
            $this->addError('selectedIgrejaId', 'Você não tem permissão para acessar esta igreja.');
            return;
        }

        // Buscar a igreja selecionada
        $igreja = \App\Models\Igrejas\Igreja::find($this->selectedIgrejaId);

        // Verificar se a igreja tem código de acesso configurado
        if (!empty($igreja->code_access)) {
            // Abrir modal de código de acesso
            $this->showAccessCodeModal = true;
            $this->dispatch('open-access-code-modal');
        } else {
            // Se não tem código de acesso, prosseguir normalmente
            $this->finalizeChurchSelection($igreja);
        }
    }

    public function selectChurch()
    {
        // Método mantido para compatibilidade, mas agora usa selectChurchFromDropdown
        $this->selectChurchFromDropdown($this->selectedIgrejaId);
    }

    public function validateAccessCode()
    {
        $this->validate([
            'accessCode' => 'required|string',
        ]);

        // Buscar a igreja selecionada
        $igreja = \App\Models\Igrejas\Igreja::find($this->selectedIgrejaId);

        if (!$igreja) {
            $this->addError('accessCode', 'Igreja não encontrada.');
            return;
        }

        // Verificar se o código está correto
        if ($this->accessCode !== $igreja->code_access) {
            $this->addError('accessCode', 'Código de acesso incorreto.');
            $this->accessCode = ''; // Limpar o campo quando incorreto
            return;
        }

        // Código correto, prosseguir
        $this->finalizeChurchSelection($igreja);
    }

    private function finalizeChurchSelection($igreja)
    {
        // Salvar na sessão
        session(['igreja_atual' => $igreja]);

        // Fechar modal se estiver aberto
        $this->showAccessCodeModal = false;

        // Resetar código de acesso
        $this->accessCode = '';

        // Redirecionar para o dashboard apropriado
        return redirect()->route(Auth::user()->redirectDashboardRoute());
    }

    public function render()
    {
        return view('navbar.select-church');
    }
}
