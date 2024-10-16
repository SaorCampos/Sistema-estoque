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
        Schema::create('movimentos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('item_id')->constrained('items');
            $table->foreignUuid('user_id')->constrained('users');
            $table->unsignedInteger('quantidade');
            $table->enum('tipo', ['ENTRADA', 'SAIDA']);
            $table->timestamp('data_movimentacao');
            $table->bigInteger('nota_fiscal')->unique()->nullable();
            $table->string('fornecedor')->nullable();
            $table->bigInteger('numero_controle_saida')->unique()->nullable();
            $table->string('local_destino')->nullable();
            $table->timestamp('criado_em');
            $table->timestamp('atualizado_em')->nullable();
            $table->string('atualizado_por')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimentos');
    }
};
