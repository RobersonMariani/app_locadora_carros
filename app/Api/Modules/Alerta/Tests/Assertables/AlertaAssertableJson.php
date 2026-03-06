<?php

declare(strict_types=1);

namespace App\Api\Modules\Alerta\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class AlertaAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('id', 'integer')
            ->whereType('tipo', 'string')
            ->whereType('tipo_label', 'string')
            ->whereType('titulo', 'string')
            ->whereType('descricao', 'string')
            ->whereType('referencia_type', 'string')
            ->whereType('referencia_id', 'integer')
            ->whereType('lido', 'boolean')
            ->whereType('data_alerta', 'string')
            ->whereType('created_at', 'string')
            ->whereType('updated_at', 'string')
            ->etc();
    }
}
