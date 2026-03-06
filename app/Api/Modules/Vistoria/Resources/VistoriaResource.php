<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VistoriaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'locacao_id' => $this->locacao_id,
            'tipo' => $this->tipo?->value,
            'tipo_label' => $this->tipo?->label(),
            'combustivel_nivel' => $this->combustivel_nivel?->value,
            'combustivel_nivel_label' => $this->combustivel_nivel?->label(),
            'km_registrado' => $this->km_registrado,
            'observacoes' => $this->observacoes,
            'realizado_por' => $this->realizado_por,
            'realizado_por_user' => $this->whenLoaded('realizadoPor', fn () => [
                'id' => $this->realizadoPor->id,
                'name' => $this->realizadoPor->name,
            ]),
            'data_vistoria' => $this->data_vistoria?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
