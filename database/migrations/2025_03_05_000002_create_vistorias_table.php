<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vistorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locacao_id')->constrained('locacoes')->cascadeOnDelete();
            $table->string('tipo', 20);
            $table->string('combustivel_nivel', 20);
            $table->integer('km_registrado');
            $table->text('observacoes')->nullable();
            $table->foreignId('realizado_por')->constrained('users')->cascadeOnDelete();
            $table->dateTime('data_vistoria');
            $table->timestamps();

            $table->unique(['locacao_id', 'tipo']);
            $table->index('realizado_por');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vistorias');
    }
};
