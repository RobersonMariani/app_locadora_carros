<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carros', function (Blueprint $table) {
            $table->index('disponivel');
            $table->index('cor');
            $table->index('ano_fabricacao');
        });

        Schema::table('locacoes', function (Blueprint $table) {
            $table->index(['status', 'data_final_realizado_periodo'], 'idx_locacoes_status_data_final');
            $table->index(['carro_id', 'status'], 'idx_locacoes_carro_status');
        });

        Schema::table('pagamentos', function (Blueprint $table) {
            $table->index('metodo_pagamento');
            $table->index(['locacao_id', 'data_pagamento'], 'idx_pagamentos_locacao_data');
        });
    }

    public function down(): void
    {
        Schema::table('carros', function (Blueprint $table) {
            $table->dropIndex(['disponivel']);
            $table->dropIndex(['cor']);
            $table->dropIndex(['ano_fabricacao']);
        });

        Schema::table('locacoes', function (Blueprint $table) {
            $table->dropIndex('idx_locacoes_status_data_final');
            $table->dropIndex('idx_locacoes_carro_status');
        });

        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropIndex(['metodo_pagamento']);
            $table->dropIndex('idx_pagamentos_locacao_data');
        });
    }
};
