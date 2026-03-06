<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class VistoriaAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('id', 'integer')
            ->whereType('locacao_id', 'integer')
            ->whereType('tipo', 'string')
            ->whereType('tipo_label', 'string')
            ->whereType('combustivel_nivel', 'string')
            ->whereType('combustivel_nivel_label', 'string')
            ->whereType('km_registrado', 'integer')
            ->has('observacoes')
            ->whereType('realizado_por', 'integer')
            ->whereType('data_vistoria', 'string')
            ->whereType('created_at', 'string')
            ->whereType('updated_at', 'string')
            ->etc();
    }
}
