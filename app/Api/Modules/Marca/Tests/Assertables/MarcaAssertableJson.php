<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class MarcaAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('id', 'integer')
            ->whereType('nome', 'string')
            ->whereType('imagem_url', ['string', 'null'])
            ->etc();
    }
}
