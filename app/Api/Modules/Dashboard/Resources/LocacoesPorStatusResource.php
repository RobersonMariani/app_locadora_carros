<?php

declare(strict_types=1);

namespace App\Api\Modules\Dashboard\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocacoesPorStatusResource extends JsonResource
{
    /**
     * @param array{status: string, label: string, quantidade: int} $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'status' => $this->resource['status'],
            'label' => $this->resource['label'],
            'quantidade' => $this->resource['quantidade'],
        ];
    }
}
