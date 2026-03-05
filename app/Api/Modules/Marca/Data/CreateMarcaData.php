<?php

namespace App\Api\Modules\Marca\Data;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class CreateMarcaData extends Data
{
    public function __construct(
        public string $nome,
        public UploadedFile $imagem,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'nome' => ['required', 'string', 'max:30', Rule::unique('marcas', 'nome')],
            'imagem' => ['required', 'file', 'mimes:png'],
        ];
    }

    public function toArrayModel(): array
    {
        return [
            'nome' => $this->nome,
        ];
    }
}
