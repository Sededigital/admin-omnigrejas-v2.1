<?php

namespace App\Providers;

use App\Models\Chats\Post;
use App\Observers\PostObserver;
use App\Models\Chats\Comentario;
use App\Models\Chats\PostReaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Observers\ComentarioObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Blade;
use App\Observers\PostReactionObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //  foreach (glob(app_path('Helpers') . '/*.php') as $filename) {
        //         require_once $filename;
        //     }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // DB::listen(function ($query) {
        //     Log::info($query->sql, $query->bindings);
        // });

         // Registra a diretiva Blade personalizada
        Blade::directive('inlineJs', function ($expression) {
            return "<?php echo App\Helpers\AssetHelper::inlineJs({$expression}); ?>";
        });

        require_once app_path('Helpers/Helper.php');
        $this->registerBladeDirectives();

        // Limpar arquivos temporários do Supabase (executar uma vez por hora)
        $this->limparArquivosTemporariosPeriodicamente();
         // se preferir deixar aqui

          // Registrar Observers para Sistema de Engajamento Automático
       Post::observe(PostObserver::class);
       Comentario::observe(ComentarioObserver::class);
       PostReaction::observe(PostReactionObserver::class);
       User::observe(UserObserver::class);
    }

    private function registerBladeDirectives(): void
    {
        Blade::directive('inlineCssList', fn($files) => "
        <?php
        foreach (explode(',', {$files}) as \$f) {
            \$f = trim(\$f, \" '\");
            \$css = file_get_contents(resource_path('css/'.\$f));
            \$css = minifyCss(\$css);
            echo \"<style>{\$css}</style>\";
        }
        ?>
        ");

        Blade::directive('inlineJsList', fn($files) => "
            <?php
            foreach (explode(',', {$files}) as \$f) {
                \$f = trim(\$f, \" '\");
                \$js = file_get_contents(resource_path('js/'.\$f));
                echo \"<script>{\$js}</script>\";
            }
            ?>
            ");


    }

    private function limparArquivosTemporariosPeriodicamente(): void
    {
        // \Illuminate\Support\Facades\Log::info('Verificando se deve executar limpeza de arquivos temporários');

        // Executar apenas uma vez por hora para evitar sobrecarga
        $cacheKey = 'supabase_temp_cleanup_last_run';
        $lastRun = \Illuminate\Support\Facades\Cache::get($cacheKey, 0);
        $now = time();

        if ($now - $lastRun < 3600) { // Menos de 1 hora
            // \Illuminate\Support\Facades\Log::info('Limpeza já executada recentemente, pulando');
            return;
        }

        // \Illuminate\Support\Facades\Log::info('Executando limpeza de arquivos temporários');

        try {
            $deletados = \App\Helpers\SupabaseHelper::limparArquivosTemporarios(1);
            // \Illuminate\Support\Facades\Log::info('Limpeza executada com sucesso', ['deletados' => $deletados]);

            \Illuminate\Support\Facades\Cache::put($cacheKey, $now, 3600); // Cache por 1 hora
        } catch (\Exception $e) {
            // \Illuminate\Support\Facades\Log::error('Erro na limpeza periódica de arquivos temporários', [
            //     'erro' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString()
            // ]);
        }
    }
}
