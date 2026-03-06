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
            $table->boolean('atrasada')->default(false)->after('observacoes');
            $table->index('atrasada');
        });
    }

    public function down(): void
    {
        Schema::table('locacoes', function (Blueprint $table) {
            $table->dropIndex(['atrasada']);
            $table->dropColumn('atrasada');
        });
    }
};
