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
            $table->string('cor', 30)->after('placa');
            $table->integer('ano_fabricacao')->after('cor');
            $table->integer('ano_modelo')->after('ano_fabricacao');
            $table->string('renavam', 30)->unique()->nullable()->after('ano_modelo');
        });
    }

    public function down(): void
    {
        Schema::table('carros', function (Blueprint $table) {
            $table->dropUnique(['renavam']);
            $table->dropColumn(['cor', 'ano_fabricacao', 'ano_modelo', 'renavam']);
        });
    }
};
