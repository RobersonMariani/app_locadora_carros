<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locacoes', function (Blueprint $table) {
            $table->string('status')->default('reservada')->after('carro_id');
            $table->decimal('valor_total', 10, 2)->nullable()->after('valor_diaria');
            $table->text('observacoes')->nullable()->after('km_final');
        });

        Schema::table('locacoes', function (Blueprint $table) {
            $table->index('status');
            $table->index(['carro_id', 'data_inicio_periodo']);
            $table->index(['data_inicio_periodo', 'data_final_previsto_periodo']);
        });
    }

    public function down(): void
    {
        Schema::table('locacoes', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['carro_id', 'data_inicio_periodo']);
            $table->dropIndex(['data_inicio_periodo', 'data_final_previsto_periodo']);
        });

        Schema::table('locacoes', function (Blueprint $table) {
            $table->dropColumn(['status', 'valor_total', 'observacoes']);
        });
    }
};
