<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('carro_id')->constrained('carros')->cascadeOnDelete();
            $table->date('data_inicio_periodo');
            $table->date('data_final_previsto_periodo');
            $table->date('data_final_realizado_periodo')->nullable();
            $table->decimal('valor_diaria', 8, 2);
            $table->integer('km_inicial');
            $table->integer('km_final')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locacoes');
    }
};
