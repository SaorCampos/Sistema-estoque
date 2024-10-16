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
        Schema::create('items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nome');
            $table->text('descricao');
            $table->foreignUuid('user_id')->constrained('users');
            $table->unsignedInteger('estoque')->default(0);
            $table->string('categoria');
            $table->string('sub_categoria');
            $table->timestamp('criado_em');
            $table->timestamp('atualizado_em')->nullable();
            $table->string('atualizado_por')->nullable();
            $table->timestamp('deletado_em')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
