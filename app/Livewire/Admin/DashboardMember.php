<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Igrejas\IgrejaMembro;
use App\Models\Eventos\Evento;
use App\Models\Eventos\Escala;
use App\Models\Chats\Post;
use App\Models\Chats\Notificacao;
use App\Models\Pedidos\PedidoOracao;
use App\Models\Cursos\CursoMatricula;
use App\Models\Outros\EngajamentoPonto;
use App\Helpers\RBAC\PermissionHelper;

#[Title('Dashboard | Membro da Igreja')]
#[Layout('components.layouts.app')]
class DashboardMember extends Component
{
    // Propriedades públicas para as métricas principais
    public $totalEventos = 0;
    public $minhasEscalas = 0;
    public $pedidosOracao = 0;
    public $pontosEngajamento = 0;
    public $cursosAtivos = 0;
    public $notificacoesNaoLidas = 0;
    public $postsRecentes = 0;

    // Propriedades para dados do membro
    public $membro;
    public $igreja;
    public $funcaoMembro; // Função atual do membro
    public $proximosEventos;
    public $minhasProximasEscalas;
    public $ultimasNotificacoes;

    public function mount()
    {
        $this->carregarDadosMembro();
        if ($this->membro) {
            $this->carregarMetricas();
            $this->carregarDadosAdicionais();
        }
    }

    protected function carregarDadosMembro()
    {
        $user = Auth::user();

        // Buscar a membresia principal do usuário
        $this->membro = IgrejaMembro::where('user_id', $user->id)
            ->where('status', 'ativo')
            ->with(['igreja', 'user'])
            ->first();

        if ($this->membro) {
            $this->igreja = $this->membro->igreja;

            // Buscar função atual do membro
            try {
                $funcoes = PermissionHelper::getUserRoles($user);
                $this->funcaoMembro = $funcoes && $funcoes->isNotEmpty() ? $funcoes->first()->nome : $this->membro->cargo;
            } catch (\Exception $e) {
                $this->funcaoMembro = $this->membro->cargo;
            }
        }
    }

    protected function carregarMetricas()
    {
        $user = Auth::user();

        // Eventos futuros da igreja
        $this->totalEventos = Evento::where('igreja_id', $this->igreja->id)
            ->where('data_evento', '>=', now())
            ->count();

        // Minhas escalas ativas
        $this->minhasEscalas = Escala::where('membro_id', $this->membro->id)
            ->whereHas('evento', function($query) {
                $query->where('data_evento', '>=', now());
            })
            ->count();

        // Pedidos de oração feitos pelo membro
        $this->pedidosOracao = PedidoOracao::where('membro_id', $this->membro->id)
            ->where('atendido', false)
            ->count();

        // Pontos de engajamento do membro
        $this->pontosEngajamento = EngajamentoPonto::where('user_id', $user->id)
            ->where('igreja_id', $this->igreja->id)
            ->sum('pontos');

        // Cursos ativos do membro
        $this->cursosAtivos = CursoMatricula::where('membro_id', $this->membro->id)
            ->where('status', 'ativo')
            ->count();

        // Notificações não lidas
        $this->notificacoesNaoLidas = Notificacao::where('user_id', $user->id)
            ->where('lida', false)
            ->count();

        // Posts recentes da igreja
        $this->postsRecentes = Post::where('igreja_id', $this->igreja->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
    }

    protected function carregarDadosAdicionais()
    {
        // Próximos eventos da igreja
        $this->proximosEventos = Evento::where('igreja_id', $this->igreja->id)
            ->where('data_evento', '>=', now())
            ->orderBy('data_evento', 'asc')
            ->limit(5)
            ->get() ?? collect();

        // Minhas próximas escalas
        $this->minhasProximasEscalas = Escala::where('membro_id', $this->membro->id)
            ->whereHas('evento', function($query) {
                $query->where('data_evento', '>=', now());
            })
            ->with(['evento' => function($query) {
                $query->select('id', 'titulo', 'data_evento');
            }])
            ->join('eventos', 'escalas.culto_evento_id', '=', 'eventos.id')
            ->select('escalas.*')
            ->orderBy('eventos.data_evento', 'asc')
            ->limit(5)
            ->get() ?? collect();

        // Últimas notificações
        $this->ultimasNotificacoes = Notificacao::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get() ?? collect();
    }

    public function marcarNotificacaoComoLida($notificacaoId)
    {
        $notificacao = Notificacao::find($notificacaoId);
        if ($notificacao && $notificacao->user_id === Auth::id()) {
            $notificacao->update(['lida' => true]);
            $this->notificacoesNaoLidas = max(0, $this->notificacoesNaoLidas - 1);
        }
    }

    public function render()
    {
        return view('admin.dashboard-member');
    }
}
