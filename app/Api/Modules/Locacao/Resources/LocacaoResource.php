<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Resources;

use App\Api\Modules\Carro\Resources\CarroResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocacaoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cliente_id' => $this->cliente_id,
            'carro_id' => $this->carro_id,
            'data_inicio_periodo' => $this->data_inicio_periodo?->format('Y-m-d'),
            'data_final_previsto_periodo' => $this->data_final_previsto_periodo?->format('Y-m-d'),
            'data_final_realizado_periodo' => $this->data_final_realizado_periodo?->format('Y-m-d'),
            'valor_diaria' => (float) $this->valor_diaria,
            'km_inicial' => $this->km_inicial,
            'km_final' => $this->km_final,
            'cliente' => $this->whenLoaded('cliente', fn () => [
                'id' => $this->cliente->id,
                'nome' => $this->cliente->nome,
            ]),
            'carro' => CarroResource::make($this->whenLoaded('carro')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
