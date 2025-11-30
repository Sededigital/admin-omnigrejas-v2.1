<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Models\User;
use App\Models\Igrejas\Igreja;
use App\Models\Igrejas\IgrejaMembro;
use App\Models\Igrejas\MembroPerfil;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Helpers\SupabaseHelper;

#[Title('Perfil | Igreja')]
#[Layout('components.layouts.app')]
class Show extends Component
{
    use WithFileUploads;

    public User $user;
    public $igreja;
    public $membroPerfil;

    // Propriedades para edição de perfil
    public $name;
    public $email;
    public $phone;
    public $photo;
    public $upload_photo;
    public $current_password;
    public $password;
    public $password_confirmation;

    // Propriedades específicas do admin
    public $igreja_nome;
    public $igreja_sigla;
    public $igreja_descricao;
    public $igreja_contacto;
    public $igreja_localizacao;
    public $igreja_logo;

    // Propriedades para perfil de membro
    public $genero;
    public $data_nascimento;
    public $endereco;
    public $observacoes;
    public $cargo;
    public $data_entrada;
    public $numero_membro;


    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user->id)],
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048', // 2MB max
            'upload_photo' => 'nullable|image|max:2048', // 2MB max
            'genero' => 'nullable|in:masculino,feminino,outro',
            'data_nascimento' => 'nullable|date|before:today',
            'endereco' => 'nullable|string|max:500',
            'observacoes' => 'nullable|string|max:500',
        ];

        if ($this->password) {
            $rules['current_password'] = 'required|string';
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        return $rules;
    }


    public function mount()
    {
        $this->user = Auth::user();
        $this->loadUserData();
        $this->loadIgrejaData();
        $this->loadMembroData();
    }

    protected function loadUserData()
    {
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone;
    }

    protected function loadIgrejaData()
    {
        if ($this->user->isIgrejaAdmin() || $this->user->isSuperAdmin()) {
            $this->igreja = $this->user->getIgreja();
            if ($this->igreja) {
                $this->igreja_nome = $this->igreja->nome;
                $this->igreja_sigla = $this->igreja->sigla;
                $this->igreja_descricao = $this->igreja->sobre;
                $this->igreja_contacto = $this->igreja->contacto;
                $this->igreja_localizacao = $this->igreja->localizacao;
            }
        }
    }

    protected function loadMembroData()
    {
        $membro = IgrejaMembro::where('user_id', $this->user->id)
            ->where('status', 'ativo')
            ->first();

        if ($membro) {
            $this->membroPerfil = $membro->membroPerfil;

            // Se não existe perfil de membro, criar um vazio
            if (!$this->membroPerfil) {
                $this->membroPerfil = new MembroPerfil([
                    'genero' => null,
                    'data_nascimento' => null,
                    'endereco' => null,
                    'observacoes' => null,
                ]);
            }

            // Carregar dados do perfil
            $this->genero = $this->membroPerfil->genero;
            $this->data_nascimento = $this->membroPerfil->data_nascimento ? $this->membroPerfil->data_nascimento->format('Y-m-d') : null;
            $this->endereco = $this->membroPerfil->endereco;
            $this->observacoes = $this->membroPerfil->observacoes;

            // Adicionar campos específicos do membro da igreja
            $this->cargo = $membro->cargo;
            $this->data_entrada = $membro->data_entrada ? $membro->data_entrada->format('Y-m-d') : null;
            $this->numero_membro = $membro->numero_membro;
        } else {
            // Se não é membro de igreja, ainda permitir editar informações pessoais básicas
            $this->membroPerfil = null;
            $this->cargo = null;
            $this->data_entrada = null;
            $this->numero_membro = null;
            $this->genero = null;
            $this->data_nascimento = null;
            $this->endereco = null;
            $this->observacoes = null;
        }
    }

    public function updatedPhoto()
    {
        // Método mantido para compatibilidade
    }

    public function updatedIgrejaLogo()
    {
        // Método mantido para compatibilidade
    }

    public function updatedUploadPhoto()
    {
        // Método mantido para compatibilidade
    }

    public function uploadPhoto()
    {
        // Validar apenas o campo de upload
        $this->validate([
            'upload_photo' => 'required|image|max:2048',
        ]);

        try {
            
            // Remove foto antiga se existir
            if ($this->user->photo_url) {
                SupabaseHelper::removerArquivo($this->user->photo_url);
            }

            // Fazer upload da nova foto
            $path = SupabaseHelper::fazerUploadPerfil($this->upload_photo, 'profile');

            // Atualizar usuário com nova foto
            $this->user->update(['photo_url' => $path]);

            // Limpar campo de upload
            $this->upload_photo = null;

            // Recarregar dados
            $this->loadUserData();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Foto de perfil atualizada com sucesso!'
            ]);

            // Atualizar navbar dinamicamente
            $this->dispatch('profile-photo-updated', [
                'photo_url' => $path,
                'timestamp' => time()
            ]);

            // Limpar arquivos temporários antigos
            SupabaseHelper::limparArquivosTemporarios(1);

            // Fechar modal
            $this->dispatch('close-upload-photo-modal');

        } catch (\Exception $e) {
            Log::error('Erro no upload da foto', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao fazer upload da foto: ' . $e->getMessage()
            ]);

            throw $e;
        }
    }

    public function updateProfile()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }

        try {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
            ];

            $this->user->update($data);

            // Atualizar dados do membro se existir
            $membro = IgrejaMembro::where('user_id', $this->user->id)
                ->where('status', 'ativo')
                ->first();

            if ($membro) {
                // Se não existe perfil de membro, criar um
                if (!$this->membroPerfil || !$this->membroPerfil->exists) {
                    $this->membroPerfil = MembroPerfil::create([
                        'id' => (string) Str::uuid(),
                        'igreja_membro_id' => $membro->id,
                        'genero' => $this->genero,
                        'data_nascimento' => $this->data_nascimento,
                        'endereco' => $this->endereco,
                        'observacoes' => $this->observacoes,
                        'created_by' => $this->user->id,
                    ]);
                } else {
                    // Atualizar perfil existente
                    $this->membroPerfil->update([
                        'genero' => $this->genero,
                        'data_nascimento' => $this->data_nascimento,
                        'endereco' => $this->endereco,
                        'observacoes' => $this->observacoes,
                    ]);
                }
            } else {
                // Se não é membro da igreja, informar que as informações adicionais não serão salvas
                if ($this->genero || $this->data_nascimento || $this->endereco || $this->observacoes) {
                    $this->addError('profile_warning', 'As informações de gênero, nascimento, endereço e observações só podem ser salvas para membros da igreja.');
                }
            }

            $this->loadUserData(); // Recarregar dados
            $this->loadMembroData(); // Recarregar dados do membro

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Perfil atualizado com sucesso!'
            ]);

            // Fechar modal após sucesso
            $this->dispatch('close-edit-profile-modal');

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar perfil', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'photo_info' => $this->photo ? [
                    'original_name' => $this->photo->getClientOriginalName(),
                    'size' => $this->photo->getSize(),
                    'mime_type' => $this->photo->getMimeType()
                ] : 'No photo'
            ]);

            // Mostrar erro específico para debug
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro: ' . $e->getMessage()
            ]);

            // Re-throw para debug adicional se necessário
            throw $e;
        }
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Verificar senha atual
        if (!Hash::check($this->current_password, $this->user->password)) {
            $this->addError('current_password', 'A senha atual está incorreta.');
            return;
        }

        try {
            $this->user->update([
                'password' => Hash::make($this->password)
            ]);

            // Senha alterada com sucesso

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Senha alterada com sucesso!'
            ]);

            // Limpar campos
            $this->current_password = '';
            $this->password = '';
            $this->password_confirmation = '';

            // Fechar modal após sucesso
            $this->dispatch('close-change-password-modal');

        } catch (\Exception $e) {
            Log::error('Erro ao alterar senha', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao alterar senha. Tente novamente.'
            ]);
        }
    }

    public function updateIgreja()
    {
        if (!$this->user->isIgrejaAdmin() && !$this->user->isSuperAdmin()) {
            $this->addError('permission', 'Você não tem permissão para editar dados da igreja.');
            return;
        }

        $this->validate([
            'igreja_nome' => 'required|string|max:255',
            'igreja_sigla' => 'nullable|string|max:50',
            'igreja_descricao' => 'nullable|string|max:1000',
            'igreja_contacto' => 'nullable|string|max:100',
            'igreja_localizacao' => 'nullable|string|max:255',
            'igreja_logo' => 'nullable|image|max:2048',
        ]);

        try {
            $data = [
                'nome' => $this->igreja_nome,
                'sigla' => $this->igreja_sigla,
                'sobre' => $this->igreja_descricao,
                'contacto' => $this->igreja_contacto,
                'localizacao' => $this->igreja_localizacao,
            ];

            // Upload do logo se foi enviado
            if ($this->igreja_logo) {
                try {
                    // Usar o novo método inteligente que cria pasta church-logo
                    $path = SupabaseHelper::fazerUploadLogoIgreja($this->igreja_logo, $this->igreja_nome);
                    $data['logo'] = $path;
                } catch (\Exception $e) {
                    throw $e;
                }
            }

            $this->igreja->update($data);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Dados da igreja atualizados com sucesso!'
            ]);

            $this->loadIgrejaData(); // Recarregar dados

            // Fechar modal após sucesso
            $this->dispatch('close-edit-church-modal');

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar dados da igreja', [
                'igreja_id' => $this->igreja->id ?? null,
                'user_id' => $this->user->id,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao atualizar dados da igreja. Tente novamente.'
            ]);
        }
    }



    public function render()
    {
        return view('profile.show', [
            'user' => $this->user,
            'igreja' => $this->igreja,
            'membroPerfil' => $this->membroPerfil,
        ]);
    }
}
