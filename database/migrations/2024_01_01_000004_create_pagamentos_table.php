<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locacao_id')->constrained('locacoes')->cascadeOnDelete();
            $table->decimal('valor', 10, 2);
            $table->string('tipo');
            $table->string('metodo_pagamento');
            $table->date('data_pagamento');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index('locacao_id');
            $table->index('data_pagamento');
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
