<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModeloResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'numero_portas' => $this->numero_portas,
            'lugares' => $this->lugares,
            'air_bag' => $this->air_bag,
            'abs' => $this->abs,
        ];
    }
}
