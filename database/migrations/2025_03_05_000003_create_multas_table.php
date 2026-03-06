<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('multas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locacao_id')->constrained('locacoes')->cascadeOnDelete();
            $table->foreignId('carro_id')->constrained('carros')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->decimal('valor', 10, 2);
            $table->date('data_infracao');
            $table->string('descricao', 255);
            $table->string('codigo_infracao', 20)->nullable();
            $table->integer('pontos')->nullable();
            $table->string('status', 20);
            $table->date('data_pagamento')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index('locacao_id');
            $table->index('carro_id');
            $table->index('cliente_id');
            $table->index('status');
            $table->index('data_infracao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('multas');
    }
};
