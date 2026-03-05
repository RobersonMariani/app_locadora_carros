<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Resources;

use App\Api\Modules\Locacao\Resources\LocacaoResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PagamentoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'locacao_id' => $this->locacao_id,
            'valor' => (float) $this->valor,
            'tipo' => $this->tipo?->value,
            'tipo_label' => $this->tipo?->label(),
            'metodo_pagamento' => $this->metodo_pagamento?->value,
            'metodo_pagamento_label' => $this->metodo_pagamento?->label(),
            'data_pagamento' => $this->data_pagamento?->format('Y-m-d'),
            'observacoes' => $this->observacoes,
            'created_at' => $this->created_at?->toIso8601String(),
            'locacao' => LocacaoResource::make($this->whenLoaded('locacao')),
        ];
    }
}
