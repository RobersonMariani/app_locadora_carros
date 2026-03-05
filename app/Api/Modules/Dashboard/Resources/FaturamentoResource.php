<?php

declare(strict_types=1);

namespace App\Api\Modules\Dashboard\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaturamentoResource extends JsonResource
{
    /**
     * @param array{periodo: string, faturamento: float, quantidade_locacoes: int} $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'periodo' => $this->resource['periodo'],
            'faturamento' => (float) $this->resource['faturamento'],
            'quantidade_locacoes' => $this->resource['quantidade_locacoes'],
        ];
    }
}
