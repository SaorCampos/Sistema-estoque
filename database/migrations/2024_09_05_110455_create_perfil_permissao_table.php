<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perfil_permissao', function (Blueprint $table) {
            $table->foreignUuid('perfil_id')->constrained('perfil');
            $table->foreignUuid('permissao_id')->constrained('permissao');
            $table->timestamp('criado_em', 100);
            $table->string('criado_por', 100)->nullable();
            $table->timestamp('atualizado_em')->nullable();
            $table->string('atualizado_por')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfil_permissao');
    }
};
