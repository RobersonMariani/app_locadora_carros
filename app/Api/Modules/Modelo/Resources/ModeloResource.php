<?php

namespace App\Api\Modules\Modelo\Resources;

use App\Api\Modules\Marca\Resources\MarcaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ModeloResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'marca_id' => $this->marca_id,
            'nome' => $this->nome,
            'imagem_url' => $this->imagem ? Storage::disk('public')->url($this->imagem) : null,
            'numero_portas' => $this->numero_portas,
            'lugares' => $this->lugares,
            'air_bag' => $this->air_bag,
            'abs' => $this->abs,
            'marca' => $this->whenLoaded('marca', fn () => MarcaResource::make($this->marca)),
            'carros' => $this->whenLoaded('carros'),
        ];
    }
}
