<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class MultaAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('id', 'integer')
            ->whereType('locacao_id', 'integer')
            ->whereType('carro_id', 'integer')
            ->whereType('cliente_id', 'integer')
            ->has('valor')
            ->whereType('data_infracao', 'string')
            ->whereType('descricao', 'string')
            ->has('codigo_infracao')
            ->has('pontos')
            ->has('status')
            ->has('status_label')
            ->has('data_pagamento')
            ->has('observacoes')
            ->whereType('created_at', 'string')
            ->whereType('updated_at', 'string')
            ->etc();
    }
}
