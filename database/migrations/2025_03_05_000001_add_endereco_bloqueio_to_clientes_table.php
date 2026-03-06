<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('endereco', 255)->nullable()->after('cnh');
            $table->string('cidade', 100)->nullable()->after('endereco');
            $table->string('estado', 2)->nullable()->after('cidade');
            $table->string('cep', 10)->nullable()->after('estado');
            $table->boolean('bloqueado')->default(false)->after('cep');
            $table->string('motivo_bloqueio', 255)->nullable()->after('bloqueado');

            $table->index('bloqueado');
            $table->index('estado');
            $table->index('cidade');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'endereco',
                'cidade',
                'estado',
                'cep',
                'bloqueado',
                'motivo_bloqueio',
            ]);
        });
    }
};
