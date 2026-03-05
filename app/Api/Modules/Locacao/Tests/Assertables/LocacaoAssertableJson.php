<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class LocacaoAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('id', 'integer')
            ->whereType('cliente_id', 'integer')
            ->whereType('carro_id', 'integer')
            ->whereType('data_inicio_periodo', 'string')
            ->whereType('data_final_previsto_periodo', 'string')
            ->whereType('data_final_realizado_periodo', ['string', 'null'])
            ->where('valor_diaria', fn ($v) => is_int($v) || is_float($v))
            ->whereType('km_inicial', 'integer')
            ->whereType('km_final', ['integer', 'null'])
            ->whereType('created_at', 'string')
            ->whereType('updated_at', 'string')
            ->etc();
    }
}
