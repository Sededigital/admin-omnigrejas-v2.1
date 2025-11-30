<?php

namespace App\Livewire\Church;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Chats\Post;
use App\Models\Chats\PostReaction;
use App\Models\Chats\Comentario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


#[Title('Posts | Portal da Igreja')]
#[Layout('components.layouts.app')]
class Posts extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // ========================================
    // PROPRIEDADES PARA NAVEGAÇÃO
    // ========================================
    public $abaAtiva = 'posts'; // posts, reacoes, comentarios

    // ========================================
    // PROPRIEDADES PARA POSTS
    // ========================================
    public $postSelecionado = null;
    public $isEditingPost = false;

    // Filtros para posts
    public $filtroPostStatus = '';
    public $filtroPostBusca = '';
    public $filtroPostTipo = ''; // texto, imagem, video

    // Formulário de posts
    public $postTitulo = '';
    public $postContent = '';
    public $postMedia = null; // arquivo enviado
    public $postMediaUrl = ''; // URL existente
    public $postMediaType = '';
    public $postIsVideo = false;

    // ========================================
    // PROPRIEDADES PARA REAÇÕES
    // ========================================
    public $postParaReacoes = null;

    // Filtros para reações
    public $filtroReacaoTipo = '';

    // ========================================
    // PROPRIEDADES PARA COMENTÁRIOS
    // ========================================
    public $postParaComentarios = null;
    public $novoComentario = '';
    public $comentarioEditando = null;
    public $comentarioEditado = '';

    // Filtros para comentários
    public $filtroComentarioBusca = '';

    // ========================================
    // PROPRIEDADES GERAIS
    // ========================================
    public $igrejaAtual;
    public $confirmacaoExclusao = false;
    public $itemParaExcluir = null;
    public $tipoExclusao = ''; // post, reacao

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'confirmarExclusao' => 'confirmarExclusao',
        'cancelarExclusao' => 'cancelarExclusao'
    ];

    public function mount()
    {
        $this->carregarIgrejaAtual();
    }

    protected function carregarIgrejaAtual()
    {
        $user = Auth::user();
        $this->igrejaAtual = $user->getIgreja();

        if (!$this->igrejaAtual) {
            abort(403, 'Usuário não está associado a nenhuma igreja.');
        }
    }

    // ========================================
    // MÉTODOS PARA POSTS
    // ========================================

    protected function getPostsQuery()
    {
        $query = Post::with(['autor', 'reactions'])
            ->withCount(['reactions as likes_count']) // Todas as reações agrupadas como likes
            ->where('igreja_id', $this->igrejaAtual->id);

        if ($this->filtroPostBusca) {
            $query->where(function($q) {
                $q->where('titulo', 'ILIKE', '%' . $this->filtroPostBusca . '%')
                  ->orWhere('content', 'ILIKE', '%' . $this->filtroPostBusca . '%');
            });
        }

        if ($this->filtroPostTipo) {
            switch ($this->filtroPostTipo) {
                case 'texto':
                    $query->whereNull('media_url');
                    break;
                case 'imagem':
                    $query->where('media_type', 'image');
                    break;
                case 'video':
                    $query->where('is_video', true);
                    break;
            }
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function abrirModalPost($postId = null)
    {
        $this->resetModalPost();

        if ($postId) {
            $post = Post::find($postId);

            if (!$post || $post->igreja_id !== $this->igrejaAtual->id) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Post não encontrado.'
                ]);
                return;
            }

            $this->postSelecionado = $post;
            $this->postTitulo = $post->titulo;
            $this->postContent = $post->content;
            $this->postMediaUrl = $post->media_url;
            $this->postMediaType = $post->media_type;
            $this->postIsVideo = $post->is_video;
            $this->isEditingPost = true;
        } else {
            $this->isEditingPost = false;
        }

        $this->dispatch('open-post-modal');
    }

    public function salvarPost()
    {
        // Validações manuais
        $this->validate([
            'postTitulo' => 'required|string|max:255',
            'postContent' => 'required|string|max:5000',
            'postMedia' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:10240', // 10MB
        ]);

        try {
            $mediaUrl = $this->postMediaUrl;
            $mediaType = $this->postMediaType;
            $isVideo = $this->postIsVideo;

            // Processar upload de mídia
            if ($this->postMedia) {
                // Deletar mídia anterior se existir
                if ($this->isEditingPost && $this->postSelecionado->media_url) {
                    \App\Helpers\SupabaseHelper::removerArquivo($this->postSelecionado->media_url);
                }

                // Fazer upload para Supabase
                $uploadResult = \App\Helpers\SupabaseHelper::fazerUploadPost($this->postMedia);

                $mediaUrl = $uploadResult['url'];
                $mediaNome = $uploadResult['nome'];
                $mediaTamanho = $uploadResult['tamanho'];
                $mediaMimeType = $uploadResult['mime_type'];
                $mediaType = $uploadResult['tipo'];
                $isVideo = $uploadResult['is_video'];
            }

            if ($this->isEditingPost) {
                $this->postSelecionado->update([
                    'titulo' => $this->postTitulo,
                    'content' => $this->postContent,
                    'media_url' => $mediaUrl,
                    'media_nome' => $mediaNome ?? $this->postSelecionado->media_nome,
                    'media_tamanho' => $mediaTamanho ?? $this->postSelecionado->media_tamanho,
                    'media_mime_type' => $mediaMimeType ?? $this->postSelecionado->media_mime_type,
                    'media_type' => $mediaType ?? $this->postSelecionado->media_type,
                    'is_video' => $isVideo ?? $this->postSelecionado->is_video,
                ]);

                $mensagem = 'Post atualizado com sucesso!';
            } else {
                Post::create([
                    'igreja_id' => $this->igrejaAtual->id,
                    'author_id' => Auth::id(),
                    'titulo' => $this->postTitulo,
                    'content' => $this->postContent,
                    'media_url' => $mediaUrl,
                    'media_nome' => $mediaNome,
                    'media_tamanho' => $mediaTamanho,
                    'media_mime_type' => $mediaMimeType,
                    'media_type' => $mediaType,
                    'is_video' => $isVideo,
                ]);

                $mensagem = 'Post criado com sucesso!';
            }

            $this->dispatch('close-post-modal');
            $this->resetModalPost();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => $mensagem
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar post: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'igreja_id' => $this->igrejaAtual->id ?? null,
                'dados' => [
                    'titulo' => $this->postTitulo,
                    'is_editing' => $this->isEditingPost
                ]
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar post. Verifique os campos.'
            ]);
        }
    }

    public function excluirPost($postId)
    {
        $post = Post::find($postId);

        if (!$post || $post->igreja_id !== $this->igrejaAtual->id) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Post não encontrado.'
            ]);
            return;
        }

        $this->postSelecionado = $post;
        $this->dispatch('open-delete-post-modal');
    }

    // ========================================
    // MÉTODOS PARA REAÇÕES
    // ========================================

    public function verReacoes($postId)
    {
        $post = Post::with(['reactions.user'])->find($postId);

        if (!$post || $post->igreja_id !== $this->igrejaAtual->id) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Post não encontrado.'
            ]);
            return;
        }

        $this->postParaReacoes = $post;
        $this->abaAtiva = 'reacoes';
    }

    public function verPost($postId)
    {
        $post = Post::find($postId);

        if (!$post || $post->igreja_id !== $this->igrejaAtual->id) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Post não encontrado.'
            ]);
            return;
        }

        $this->postSelecionado = $post;
        $this->dispatch('open-view-post-modal');
    }

    public function confirmarExclusaoPost($postId = null)
    {
        $postId = $postId ?: $this->postSelecionado?->id;

        if (!$postId) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Post não selecionado para exclusão.'
            ]);
            return;
        }

        try {
            $post = Post::find($postId);

            if (!$post || $post->igreja_id !== $this->igrejaAtual->id) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Post não encontrado.'
                ]);
                return;
            }

            // Deletar mídia se existir
            if ($post->media_url) {
                \App\Helpers\SupabaseHelper::removerArquivo($post->media_url);
            }

            // Deletar reações relacionadas
            $post->reactions()->delete();

            $post->delete();

            $this->dispatch('close-delete-post-modal');
            $this->resetModalPost();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Post excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir post: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'igreja_id' => $this->igrejaAtual->id ?? null,
                'post_id' => $postId
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir post. Tente novamente.'
            ]);
        }
    }

    public function voltarParaPosts()
    {
        $this->postParaReacoes = null;
        $this->abaAtiva = 'posts';
    }

    protected function getReacoesQuery()
    {
        if ($this->postParaReacoes) {
            // Reações de um post específico
            $query = $this->postParaReacoes->reactions()->with('user');
        } else {
            // Todas as reações da igreja
            $query = PostReaction::with(['user', 'post'])
                ->whereHas('post', function ($q) {
                    $q->where('igreja_id', $this->igrejaAtual->id);
                });
        }

        if ($this->filtroReacaoTipo) {
            $query->where('reaction', $this->filtroReacaoTipo);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function excluirReacao($postId, $userId)
    {
        try {
            // Verificar se a reação existe e pertence à igreja atual
            $reacao = PostReaction::where('post_id', $postId)
                ->where('user_id', $userId)
                ->whereHas('post', function ($q) {
                    $q->where('igreja_id', $this->igrejaAtual->id);
                })
                ->first();

            if (!$reacao) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Reação não encontrada.'
                ]);
                return;
            }

            // Deletar usando DB query devido à chave composta
            DB::table('post_reactions')
                ->where('post_id', $postId)
                ->where('user_id', $userId)
                ->delete();

            // Recarregar reações apenas se estamos visualizando um post específico
            if ($this->postParaReacoes) {
                $this->postParaReacoes->load(['reactions', 'reactions.user']);
            }

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Reação excluída com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir reação: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'igreja_id' => $this->igrejaAtual->id ?? null,
                'post_id' => $postId,
                'user_id_param' => $userId
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir reação. Tente novamente.'
            ]);
        }
    }

    // ========================================
    // MÉTODOS PARA COMENTÁRIOS
    // ========================================

    public function verComentarios($postId)
    {
        $post = Post::find($postId);

        if (!$post || $post->igreja_id !== $this->igrejaAtual->id) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Post não encontrado.'
            ]);
            return;
        }

        $this->postParaComentarios = $post;
        $this->abaAtiva = 'comentarios';
    }

    public function adicionarComentario()
    {
        $this->validate([
            'novoComentario' => 'required|string|max:1000',
        ]);

        if (!$this->postParaComentarios) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Post não selecionado.'
            ]);
            return;
        }

        try {
            Comentario::create([
                'user_id' => Auth::id(),
                'post_id' => $this->postParaComentarios->id,
                'conteudo' => $this->novoComentario,
            ]);

            $this->novoComentario = '';
            // Comentários serão recarregados automaticamente pela propriedade computada

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Comentário adicionado com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao adicionar comentário: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'post_id' => $this->postParaComentarios->id ?? null,
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao adicionar comentário.'
            ]);
        }
    }

    public function editarComentario($comentarioId)
    {
        $comentario = Comentario::find($comentarioId);

        if (!$comentario || $comentario->user_id !== Auth::id()) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Comentário não encontrado ou você não tem permissão para editá-lo.'
            ]);
            return;
        }

        $this->comentarioEditando = $comentario;
        $this->comentarioEditado = $comentario->conteudo;
    }

    public function salvarEdicaoComentario()
    {
        $this->validate([
            'comentarioEditado' => 'required|string|max:1000',
        ]);

        if (!$this->comentarioEditando) {
            return;
        }

        try {
            $this->comentarioEditando->update([
                'conteudo' => $this->comentarioEditado,
            ]);

            $this->comentarioEditando = null;
            $this->comentarioEditado = '';
            // Comentários serão recarregados automaticamente pela propriedade computada

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Comentário atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao editar comentário: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'comentario_id' => $this->comentarioEditando->id ?? null,
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao editar comentário.'
            ]);
        }
    }

    public function cancelarEdicaoComentario()
    {
        $this->comentarioEditando = null;
        $this->comentarioEditado = '';
    }

    public function excluirComentario($comentarioId)
    {
        $comentario = Comentario::find($comentarioId);

        if (!$comentario || ($comentario->user_id !== Auth::id() && !Auth::user()->hasRole(['admin', 'pastor' ]))) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Comentário não encontrado ou você não tem permissão para excluí-lo.'
            ]);
            return;
        }

        try {
            $comentario->delete();
            // Comentários serão recarregados automaticamente pela propriedade computada

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Comentário excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir comentário: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'comentario_id' => $comentarioId,
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir comentário.'
            ]);
        }
    }

    protected function getComentariosQuery()
    {
        if (!$this->postParaComentarios) {
            return Comentario::whereRaw('1 = 0');
        }

        $query = Comentario::join('users', 'comentarios.user_id', '=', 'users.id')
            ->select('comentarios.*', 'users.name as usuario_name', 'users.email as usuario_email', 'users.photo_url as usuario_photo_url')
            ->where('comentarios.post_id', $this->postParaComentarios->id);

        if ($this->filtroComentarioBusca) {
            $query->where('comentarios.conteudo', 'ILIKE', '%' . $this->filtroComentarioBusca . '%');
        }

        return $query->orderBy('comentarios.created_at', 'desc');
    }

    public function voltarParaPostsComentarios()
    {
        $this->postParaComentarios = null;
        $this->novoComentario = '';
        $this->comentarioEditando = null;
        $this->comentarioEditado = '';
        $this->abaAtiva = 'posts';
    }

    // ========================================
    // MÉTODOS DE RESET E UTILITÁRIOS
    // ========================================

    protected function resetModalPost()
    {
        $this->postSelecionado = null;
        $this->postTitulo = '';
        $this->postContent = '';
        $this->postMedia = null;
        $this->postMediaUrl = '';
        $this->postMediaType = '';
        $this->postIsVideo = false;
        $this->resetValidation();
    }

    // ========================================
    // LISTENERS PARA FILTROS
    // ========================================

    public function updatedAbaAtiva()
    {
        $this->resetPage();
    }

    public function updatedFiltroPostBusca()
    {
        $this->resetPage();
    }

    public function updatedFiltroPostTipo()
    {
        $this->resetPage();
    }

    public function updatedFiltroReacaoTipo()
    {
        $this->resetPage();
    }

    public function updatedFiltroComentarioBusca()
    {
        $this->resetPage();
    }

    // ========================================
    // PROPRIEDADES COMPUTADAS
    // ========================================

    public function getPostsProperty()
    {
        return $this->getPostsQuery()->paginate(12);
    }

    public function getReacoesProperty()
    {
        return $this->getReacoesQuery()->paginate(20);
    }

    public function getComentariosProperty()
    {
        return $this->getComentariosQuery()->paginate(15);
    }

    public function getReacaoTiposProperty()
    {
        return [
            'like' => 'Curtidas',
            'love' => 'Amei',
            'haha' => 'Engraçado',
            'wow' => 'Uau',
            'sad' => 'Triste',
            'angry' => 'Raiva',
        ];
    }

    public function render()
    {
        return view('church.posts.posts', [
            'posts' => $this->posts,
            'reacoes' => $this->reacoes,
            'comentarios' => $this->comentarios,
            'reacaoTipos' => $this->reacaoTipos,
        ]);
    }
}
