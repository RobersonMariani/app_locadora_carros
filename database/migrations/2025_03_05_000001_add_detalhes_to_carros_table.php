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
            $table->string('combustivel', 20)->nullable()->after('renavam');
            $table->string('cambio', 20)->nullable()->after('combustivel');
            $table->string('categoria', 20)->nullable()->after('cambio');
            $table->boolean('ar_condicionado')->default(true)->after('categoria');
            $table->decimal('diaria_padrao', 10, 2)->nullable()->after('ar_condicionado');

            $table->index('combustivel');
            $table->index('cambio');
            $table->index('categoria');
        });
    }

    public function down(): void
    {
        Schema::table('carros', function (Blueprint $table) {
            $table->dropColumn([
                'combustivel',
                'cambio',
                'categoria',
                'ar_condicionado',
                'diaria_padrao',
            ]);
        });
    }
};
