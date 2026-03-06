<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class ManutencaoAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('id', 'integer')
            ->whereType('carro_id', 'integer')
            ->whereType('tipo', 'string')
            ->whereType('tipo_label', 'string')
            ->whereType('descricao', 'string')
            ->where('valor', fn ($v) => $v === null || is_int($v) || is_float($v))
            ->whereType('km_manutencao', 'integer')
            ->whereType('data_manutencao', 'string')
            ->has('data_proxima')
            ->has('fornecedor')
            ->whereType('status', 'string')
            ->whereType('status_label', 'string')
            ->has('observacoes')
            ->whereType('created_at', 'string')
            ->whereType('updated_at', 'string')
            ->etc();
    }
}
