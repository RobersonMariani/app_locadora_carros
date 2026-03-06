<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50);
            $table->string('titulo', 255);
            $table->text('descricao')->nullable();
            $table->string('referencia_type')->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->boolean('lido')->default(false);
            $table->dateTime('data_alerta');
            $table->timestamps();

            $table->index('tipo');
            $table->index('lido');
            $table->index(['referencia_type', 'referencia_id']);
            $table->index('data_alerta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
