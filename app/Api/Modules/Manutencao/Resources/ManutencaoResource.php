<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Resources;

use App\Api\Modules\Carro\Resources\CarroResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManutencaoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'carro_id' => $this->carro_id,
            'tipo' => $this->tipo?->value,
            'tipo_label' => $this->tipo?->label(),
            'descricao' => $this->descricao,
            'valor' => $this->valor !== null ? (float) $this->valor : null,
            'km_manutencao' => $this->km_manutencao,
            'data_manutencao' => $this->data_manutencao?->format('Y-m-d'),
            'data_proxima' => $this->data_proxima?->format('Y-m-d'),
            'fornecedor' => $this->fornecedor,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'observacoes' => $this->observacoes,
            'carro' => CarroResource::make($this->whenLoaded('carro')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
