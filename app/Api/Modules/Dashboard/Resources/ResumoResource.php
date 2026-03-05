<?php

declare(strict_types=1);

namespace App\Api\Modules\Dashboard\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResumoResource extends JsonResource
{
    /**
     * @param array<string, int|float> $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'total_marcas' => $this->resource['total_marcas'],
            'total_modelos' => $this->resource['total_modelos'],
            'total_carros' => $this->resource['total_carros'],
            'total_clientes' => $this->resource['total_clientes'],
            'carros_disponiveis' => $this->resource['carros_disponiveis'],
            'carros_locados' => $this->resource['carros_locados'],
            'locacoes_ativas' => $this->resource['locacoes_ativas'],
            'locacoes_reservadas' => $this->resource['locacoes_reservadas'],
            'faturamento_mes' => (float) $this->resource['faturamento_mes'],
        ];
    }
}
