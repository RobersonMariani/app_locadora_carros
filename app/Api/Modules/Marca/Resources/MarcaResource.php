<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\Resources;

use App\Api\Modules\Modelo\Resources\ModeloResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MarcaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'imagem_url' => $this->imagem ? Storage::disk('public')->url($this->imagem) : null,
            'modelos' => $this->whenLoaded('modelos', fn () => ModeloResource::collection($this->modelos)),
        ];
    }
}
