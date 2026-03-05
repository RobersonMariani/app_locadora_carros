<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class CarroAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('id', 'integer')
            ->whereType('modelo_id', 'integer')
            ->whereType('placa', 'string')
            ->has('disponivel')
            ->whereType('km', 'integer')
            ->whereType('created_at', 'string')
            ->whereType('updated_at', 'string')
            ->etc();
    }
}
