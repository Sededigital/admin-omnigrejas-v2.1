<?php

namespace App\Livewire\Ecommerce\Trial;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Igrejas\CategoriaIgreja;
use App\Models\Billings\Trial\TrialRequest;

#[Title('Solicitar Período de Teste - OmnIgrejas')]
#[Layout('components.layouts.subscription')]
class TrialSolicitar extends Component
{

    public $nome;

    public $email;

    public $password;
    
    public $password_confirmation;
    
    public $igreja_nome;
    
    public $denominacao = 'Evangélica';
    
    public $telefone;
    
    public $cidade;
    public $provincia = 'Luanda';


    public $aceitou_termos = false;

    public $mostrar_sucesso = false;
    public $dados_trial;


    protected function rules()
    {
        $rules = [
            'nome' => 'required|string|min:3|max:255',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|min:6|same:password',
            'igreja_nome' => 'required|string|min:3|max:255',
            'denominacao' => 'nullable|string|max:255',
            'telefone' => 'required|string|phone:AO',
            'cidade' => 'nullable|string|max:255',
            'provincia' => 'nullable|string|max:100',
        ];

        return $rules;
    }


    protected function messages(){

        $messages = [
            'telefone.validation'=>'O número de telefone deve ser um número válido',
            'aceitou_termos.required' => 'Você deve aceitar os termos de uso.',
        ];

        return $messages;
    }



    public function mount()
    {
        
        $user = Auth::user();

        if ($user) {
            return redirect()->route('ecommerce.home');
        }

    }

    public function solicitarTrial()
    {
        // Validar termos
        if (!$this->aceitou_termos) {
            $this->addError('aceitou_termos', 'Você deve aceitar os termos de uso.');
            return;
        }

        // Verificar se já existe um pedido pendente para este email
        $pedidoPendente = TrialRequest::where('email', $this->email)
            ->where('status', TrialRequest::STATUS_PENDENTE)
            ->first();

        if ($pedidoPendente) {
            $this->dispatch('solicitacao-pendente');
            return;
        }

        // Verificar se já foi aprovado antes e contar quantas vezes
        $pedidosAprovados = TrialRequest::where('email', $this->email)
            ->where('status', TrialRequest::STATUS_APROVADO)
            ->count();

        if ($pedidosAprovados >= 2) {
            $this->dispatch('limite-atingido');
            return;
        }

        // Validar todos os campos usando o método rules()
        $this->validate();

        try {
            // Criar solicitação de trial (não cria o trial ainda)
            $trialRequest = TrialRequest::create([
                'nome' => $this->nome,
                'email' => $this->email,
                'password' => $this->password,
                'igreja_nome' => $this->igreja_nome,
                'denominacao' => $this->denominacao,
                'telefone' => $this->telefone,
                'cidade' => $this->cidade,
                'provincia' => $this->provincia,
                'periodo_dias' => 10, // Trial padrão de 10 dias
                'status' => TrialRequest::STATUS_PENDENTE,
            ]);

            // Preparar dados para exibição
            $this->dados_trial = [
                'request' => $trialRequest,
                'usuario' => [
                    'nome' => $this->nome,
                    'email' => $this->email,
                    'telefone' => $this->telefone,
                    'cidade' => $this->cidade,
                    'provincia' => $this->provincia,
                ],
                'igreja' => [
                    'nome' => $this->igreja_nome,
                    'denominacao' => $this->denominacao,
                ],
                'periodo' => [
                    'dias' => 10,
                ],
            ];

            // Mostrar mensagem de sucesso
            $this->mostrar_sucesso = true;

            // Limpar formulário
            $this->reset(['nome', 'email', 'password', 'password_confirmation', 'igreja_nome', 'telefone', 'cidade', 'aceitou_termos']);

            // Disparar evento para analytics (opcional)
            $this->dispatch('trial-solicitado', [
                'email' => $this->dados_trial['usuario']['email'],
                'igreja' => $this->dados_trial['igreja']['nome']
            ]);

        } catch (\Exception $e) {
            $this->addError('geral', 'Erro ao processar solicitação: ' . $e->getMessage());
            Log::error('Erro ao criar solicitação de trial: ' . $e->getMessage());
        }
    }


    public function render()
    {   
        $user = Auth::user();

        if ($user) {
            return redirect()->route('ecommerce.home');
        }

        return view('ecommerce.trial.solicitar', [
            'categorias' => CategoriaIgreja::ativas()->orderBy('nome')->get()
        ]);
    }
}