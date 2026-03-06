<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Resources;

use App\Api\Modules\Modelo\Resources\ModeloResource;
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
            'cor' => $this->cor,
            'ano_fabricacao' => $this->ano_fabricacao,
            'ano_modelo' => $this->ano_modelo,
            'renavam' => $this->renavam,
            'combustivel' => $this->combustivel?->value,
            'combustivel_label' => $this->combustivel?->label(),
            'cambio' => $this->cambio?->value,
            'cambio_label' => $this->cambio?->label(),
            'categoria' => $this->categoria?->value,
            'categoria_label' => $this->categoria?->label(),
            'ar_condicionado' => $this->ar_condicionado,
            'diaria_padrao' => $this->diaria_padrao !== null ? (float) $this->diaria_padrao : null,
            'modelo' => ModeloResource::make($this->whenLoaded('modelo')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
