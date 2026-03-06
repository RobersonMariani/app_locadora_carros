<?php

declare(strict_types=1);

namespace App\Api\Modules\Dashboard\Tests\Assertables;

use Illuminate\Testing\Fluent\AssertableJson;

class ResumoAssertableJson
{
    public static function schema(AssertableJson $json): AssertableJson
    {
        return $json
            ->whereType('total_marcas', 'integer')
            ->whereType('total_modelos', 'integer')
            ->whereType('total_carros', 'integer')
            ->whereType('total_clientes', 'integer')
            ->whereType('carros_disponiveis', 'integer')
            ->whereType('carros_locados', 'integer')
            ->whereType('locacoes_ativas', 'integer')
            ->whereType('locacoes_reservadas', 'integer')
            ->where('faturamento_mes', fn ($v) => is_int($v) || is_float($v))
            ->whereType('carros_em_manutencao', 'integer')
            ->where('taxa_ocupacao', fn ($v) => is_int($v) || is_float($v))
            ->whereType('locacoes_atrasadas', 'integer')
            ->whereType('total_multas_pendentes', 'integer')
            ->where('valor_multas_pendentes', fn ($v) => is_int($v) || is_float($v))
            ->where('total_a_receber', fn ($v) => is_int($v) || is_float($v))
            ->where('total_recebido_mes', fn ($v) => is_int($v) || is_float($v))
            ->whereType('manutencoes_proximas', 'integer')
            ->whereType('alertas_nao_lidos', 'integer')
            ->etc();
    }
}
