<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Resources;

use App\Api\Modules\Carro\Resources\CarroResource;
use App\Api\Modules\Cliente\Resources\ClienteResource;
use App\Api\Modules\Locacao\Resources\LocacaoResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MultaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'locacao_id' => $this->locacao_id,
            'carro_id' => $this->carro_id,
            'cliente_id' => $this->cliente_id,
            'valor' => $this->valor !== null ? (float) $this->valor : null,
            'data_infracao' => $this->data_infracao?->format('Y-m-d'),
            'descricao' => $this->descricao,
            'codigo_infracao' => $this->codigo_infracao,
            'pontos' => $this->pontos,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'data_pagamento' => $this->data_pagamento?->format('Y-m-d'),
            'observacoes' => $this->observacoes,
            'locacao' => LocacaoResource::make($this->whenLoaded('locacao')),
            'carro' => CarroResource::make($this->whenLoaded('carro')),
            'cliente' => ClienteResource::make($this->whenLoaded('cliente')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
