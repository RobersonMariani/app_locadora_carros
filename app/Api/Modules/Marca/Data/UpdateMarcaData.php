<?php

namespace App\Api\Modules\Marca\Data;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class UpdateMarcaData extends Data
{
    public function __construct(
        public ?string $nome = null,
        public ?UploadedFile $imagem = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        $marcaId = request()->route('marca');

        return [
            'nome' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('marcas', 'nome')->ignore($marcaId),
            ],
            'imagem' => ['nullable', 'file', 'mimes:png'],
        ];
    }

    public function toArrayModel(): array
    {
        $data = [];

        if ($this->nome !== null) {
            $data['nome'] = $this->nome;
        }

        return $data;
    }
}
