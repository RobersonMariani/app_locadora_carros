<?php

declare(strict_types=1);

namespace App\Api\Modules\Alerta\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlertaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo' => $this->tipo?->value,
            'tipo_label' => $this->tipo?->label(),
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'referencia_type' => $this->referencia_type,
            'referencia_id' => $this->referencia_id,
            'lido' => (bool) $this->lido,
            'data_alerta' => $this->data_alerta?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
