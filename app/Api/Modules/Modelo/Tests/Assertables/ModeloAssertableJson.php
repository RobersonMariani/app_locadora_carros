<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class ModeloAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('id', 'integer')
            ->whereType('marca_id', 'integer')
            ->whereType('nome', 'string')
            ->whereType('imagem_url', ['string', 'null'])
            ->whereType('numero_portas', 'integer')
            ->whereType('lugares', 'integer')
            ->whereType('air_bag', 'boolean')
            ->whereType('abs', 'boolean')
            ->etc();
    }
}
