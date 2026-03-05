<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class AuthAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('id', 'integer')
            ->whereType('name', 'string')
            ->whereType('email', 'string')
            ->etc();
    }
}
