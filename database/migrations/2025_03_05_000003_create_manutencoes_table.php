<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manutencoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carro_id')->constrained('carros')->cascadeOnDelete();
            $table->string('tipo', 20);
            $table->string('descricao', 255);
            $table->decimal('valor', 10, 2);
            $table->integer('km_manutencao');
            $table->date('data_manutencao');
            $table->date('data_proxima')->nullable();
            $table->string('fornecedor', 100)->nullable();
            $table->string('status', 20);
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index('carro_id');
            $table->index('tipo');
            $table->index('status');
            $table->index('data_manutencao');
            $table->index('data_proxima');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manutencoes');
    }
};
