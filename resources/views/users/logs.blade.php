<div>
    <div class="container-fluid p-4">
        @if(!$authenticated)
            <!-- Password Form -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header text-center">
                            <h4 class="text-info">
                                <i class="fas fa-lock me-2"></i>Acesso aos Logs
                            </h4>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="checkPassword">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Digite a senha para acessar os logs:</label>
                                    <input
                                        type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        id="password"
                                        wire:model="password"
                                        placeholder="Senha"
                                        autofocus
                                    >
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn bg-info text-light">
                                        <i class="fas fa-key me-2"></i>Acessar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-1 text-info">
                                <i class="fas fa-file-alt me-2"></i>Logs do Sistema
                            </h1>
                            <p class="mb-0 text-muted">Visualize e gerencie os logs do Laravel</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <button class="btn btn-danger me-2" wire:click="clearLog">
                                <i class="fas fa-trash me-2"></i>Limpar Log
                            </button>
                            <button class="btn bg-info text-light" wire:click="loadLog">
                                <i class="fas fa-sync me-2"></i>Atualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log Content -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 text-info">
                        <i class="fas fa-terminal me-2"></i>Conteúdo do Log
                    </h5>
                </div>
                <div class="card-body p-0">
                    <textarea
                        class="form-control border-0"
                        rows="30"
                        readonly
                        style="font-family: 'Courier New', monospace; font-size: 12px; resize: none; background-color: #f8f9fa;"
                        wire:model="logContent"
                    >{{ $logContent }}</textarea>
                </div>
                <div class="card-footer text-muted">
                    <small>Arquivo: storage/logs/laravel.log</small>
                </div>
            </div>
        @endif
    </div>
</div>