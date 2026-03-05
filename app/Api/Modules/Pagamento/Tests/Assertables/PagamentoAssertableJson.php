<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class PagamentoAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('id', 'integer')
            ->whereType('locacao_id', 'integer')
            ->where('valor', fn ($v) => is_int($v) || is_float($v))
            ->has('tipo')
            ->has('tipo_label')
            ->has('metodo_pagamento')
            ->has('metodo_pagamento_label')
            ->has('data_pagamento')
            ->has('observacoes')
            ->whereType('created_at', 'string')
            ->etc();
    }
}
