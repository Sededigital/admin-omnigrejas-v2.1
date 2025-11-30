<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         // Extensão CITEXT
        DB::statement("CREATE EXTENSION IF NOT EXISTS citext");

        // Extensão CITEXT
        DB::statement("CREATE EXTENSION IF NOT EXISTS citext");

        // Enum role_enum
        DB::statement("DROP TYPE IF EXISTS role_enum CASCADE");
        DB::statement("CREATE TYPE role_enum AS ENUM ('root','super_admin','admin','pastor','ministro','obreiro','diacono','membro','anonymous')");

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->string('name');
            $table->text('email')->unique();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->text('photo_url')->nullable();
            $table->string('denomination')->default('Geral');
            $table->boolean('is_active')->default(true);

            $table->uuid('created_by')->nullable();

            $table->timestampsTz();
            $table->timestampTz('deleted_at')->nullable();
        });

        // Ajustar email -> citext
         // Ajustar email -> citext
        DB::statement("ALTER TABLE users ALTER COLUMN email TYPE citext");

        // Adicionar a coluna role como ENUM nativo
        DB::statement("ALTER TABLE users ADD COLUMN role role_enum DEFAULT 'anonymous' NOT NULL");

        // Agora sim: FK de self reference
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
