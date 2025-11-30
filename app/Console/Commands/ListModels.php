<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ListModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lista todos os models da aplicação';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelsPath = app_path('Models');

        if (!File::exists($modelsPath)) {
            $this->error('Diretório Models não encontrado!');
            return Command::FAILURE;
        }

        $models = collect(File::allFiles($modelsPath))
            ->map(function ($file) {
                return str_replace('.php', '', $file->getBasename());
            })
            ->sort()
            ->values();

        if ($models->isEmpty()) {
            $this->info('Nenhum model encontrado.');
            return Command::SUCCESS;
        }

        $this->table(['Models'], $models->map(fn($model) => [$model]));
        $this->info("Total: {$models->count()} models encontrados.");

        return Command::SUCCESS;
    }
}
