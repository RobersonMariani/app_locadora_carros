<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class ClienteAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('id', 'integer')
            ->whereType('nome', 'string')
            ->whereType('created_at', 'string')
            ->whereType('updated_at', 'string')
            ->etc();
    }
}
