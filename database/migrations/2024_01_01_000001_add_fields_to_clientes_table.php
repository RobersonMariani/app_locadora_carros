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
            $table->string('cpf', 14)->unique()->after('nome');
            $table->string('email')->unique()->nullable()->after('cpf');
            $table->string('telefone', 20)->nullable()->after('email');
            $table->date('data_nascimento')->nullable()->after('telefone');
            $table->string('cnh', 20)->unique()->nullable()->after('data_nascimento');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['cpf', 'email', 'telefone', 'data_nascimento', 'cnh']);
        });
    }
};
