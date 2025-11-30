<?php

namespace App\Livewire\Auth;

use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Helpers\SupabaseHelper;


#[Title('Selecionar Igreja')]
#[Layout('components.layouts.auth.guest')]
class SelectChurch extends Component
{
    public $igrejas = [];
    public $selectedIgrejaId;
    public $showAccessCodeModal = false;
    public $accessCode = '';
    public $showForgotCode = false;
    public $sendingEmail = false;

    public function mount()
    {
        $user = Auth::user();

        // Verificar se a sessão expirou (15 minutos = 900 segundos)
        if (session()->has('church_selection_start')) {
            $startTime = session('church_selection_start');
            $currentTime = now()->timestamp;
            $elapsedTime = $currentTime - $startTime;

            if ($elapsedTime > 900) { // 15 minutos
                Auth::logout();
                session()->flush();
                return redirect()->route('login')->with('error', 'Sua sessão expirou devido à inatividade. Por favor, faça login novamente.');
            }
        }

        // Definir timestamp de início da seleção se não existir
        if (!session()->has('church_selection_start')) {
            session(['church_selection_start' => now()->timestamp]);
        }

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
                    'membro_desde' => $membro->data_entrada->format('d/m/Y'),
                    'logo' => $logoUrl,
                    'has_logo' => !empty($logoUrl),
                ];
            });

        $this->igrejas = $igrejasCollection->toArray();

        // Se só tem uma igreja, selecionar automaticamente
        if (count($this->igrejas) === 1) {
            $this->selectedIgrejaId = $this->igrejas[0]['id'];
        }
    }

    public function selectChurch()
    {
        // Verificar se a sessão expirou antes de prosseguir
        if (session()->has('church_selection_start')) {
            $startTime = session('church_selection_start');
            $currentTime = now()->timestamp;
            $elapsedTime = $currentTime - $startTime;

            if ($elapsedTime > 900) { // 15 minutos
                Auth::logout();
                session()->flush();
                return redirect()->route('login')->with('error', 'Sua sessão expirou devido à inatividade. Por favor, faça login novamente.');
            }
        }

        $this->validate([
            'selectedIgrejaId' => 'required|exists:igrejas,id',
        ], [
            'selectedIgrejaId.required' => 'Por favor, selecione uma igreja para continuar.',
            'selectedIgrejaId.exists' => 'A igreja selecionada não foi encontrada.',
        ]);

        // Verificar se o usuário realmente pertence a esta igreja
        $pertence = IgrejaMembro::where('user_id', Auth::id())
            ->where('igreja_id', $this->selectedIgrejaId)
            ->where('status', 'ativo')
            ->exists();

        if (!$pertence) {
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

    public function validateAccessCode()
    {
        // Verificar se a sessão expirou antes de prosseguir
        if (session()->has('church_selection_start')) {
            $startTime = session('church_selection_start');
            $currentTime = now()->timestamp;
            $elapsedTime = $currentTime - $startTime;

            if ($elapsedTime > 900) { // 15 minutos
                Auth::logout();
                session()->flush();
                return redirect()->route('login')->with('error', 'Sua sessão expirou devido à inatividade. Por favor, faça login novamente.');
            }
        }

        $this->validate([
            'accessCode' => 'required|string',
        ]);

        // Enviar evento para JS mostrar spinner e bloquear modal
        $this->dispatch('start-connection');

        // Buscar a igreja selecionada
        $igreja = \App\Models\Igrejas\Igreja::find($this->selectedIgrejaId);

        if (!$igreja) {
            $this->addError('accessCode', 'Igreja não encontrada.');
            $this->dispatch('stop-connection');
            return;
        }

        // Verificar se o código está correto
        if ($this->accessCode !== $igreja->code_access) {
            $this->addError('accessCode', 'Código de acesso incorreto.');
            $this->accessCode = ''; // Limpar o campo quando incorreto
            $this->dispatch('stop-connection');
            return;
        }

        // Código correto, prosseguir
        $this->finalizeChurchSelection($igreja);
    }

    public function sendAccessCodeByEmail()
    {
        // Verificar se a sessão expirou antes de prosseguir
        if (session()->has('church_selection_start')) {
            $startTime = session('church_selection_start');
            $currentTime = now()->timestamp;
            $elapsedTime = $currentTime - $startTime;

            if ($elapsedTime > 900) { // 15 minutos
                Auth::logout();
                session()->flush();
                return redirect()->route('login')->with('error', 'Sua sessão expirou devido à inatividade. Por favor, faça login novamente.');
            }
        }

        $this->dispatch('show-sending-state');

        try {
            // Buscar a igreja selecionada
            $igreja = \App\Models\Igrejas\Igreja::find($this->selectedIgrejaId);
            $user = Auth::user();

            if (!$igreja || !$user) {
                $this->dispatch('email-sent-error', 'Erro ao enviar código de acesso.');
                $this->dispatch('hide-sending-state');
                return;
            }

            // Enviar email com código de acesso
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\AccessCodeMail($igreja, $user));

            $this->dispatch('email-sent-success', 'Código de acesso enviado para seu email!');
            $this->showForgotCode = false; // Voltar para modo normal

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao enviar código de acesso por email: ' . $e->getMessage());
            $this->dispatch('email-sent-error', 'Erro ao enviar código. Tente novamente.');
            $this->dispatch('hide-sending-state');
        }
    }

    private function finalizeChurchSelection($igreja)
    {
     //   \Illuminate\Support\Facades\Log::info('SelectChurch: Finalizando seleção de igreja', [
     //       'user_id' => \Illuminate\Support\Facades\Auth::id(),
     //        'user_email' => \Illuminate\Support\Facades\Auth::user()->email,
     //        'igreja_id' => $igreja->id,
     //        'igreja_nome' => $igreja->nome,
     //        'code_access_required' => !empty($igreja->code_access),
    //     ]);

        // Limpar timestamp de seleção da sessão
        session()->forget('church_selection_start');

        // Salvar na sessão
        session(['igreja_atual' => $igreja]);

        // Fechar modal se estiver aberto
        $this->showAccessCodeModal = false;

        $redirectRoute = \Illuminate\Support\Facades\Auth::user()->redirectDashboardRoute();

       // \Illuminate\Support\Facades\Log::info('SelectChurch: Redirecionando para dashboard', [
       //     'user_id' => \Illuminate\Support\Facades\Auth::id(),
       //     'redirect_route' => $redirectRoute,
       // ]);

        // Redirecionar para o dashboard apropriado
        return redirect()->route($redirectRoute);
    }

    public function render()
    {
        return view('components.layouts.auth.select-church');
    }
}
