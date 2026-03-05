<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarroResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'modelo_id' => $this->modelo_id,
            'placa' => $this->placa,
            'disponivel' => $this->disponivel,
            'km' => $this->km,
            'modelo' => ModeloResource::make($this->whenLoaded('modelo')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
