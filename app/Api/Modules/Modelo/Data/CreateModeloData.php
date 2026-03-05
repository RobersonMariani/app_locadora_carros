<?php

namespace App\Api\Modules\Modelo\Data;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class CreateModeloData extends Data
{
    public function __construct(
        public int $marcaId,
        public string $nome,
        public UploadedFile $imagem,
        public int $numeroPortas,
        public int $lugares,
        public bool $airBag,
        public bool $abs,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'marca_id' => ['required', 'integer', 'exists:marcas,id'],
            'nome' => ['required', 'string', 'max:30', 'min:3', Rule::unique('modelos', 'nome')],
            'imagem' => ['required', 'file', 'mimes:png,jpeg,jpg'],
            'numero_portas' => ['required', 'integer', 'digits_between:1,5'],
            'lugares' => ['required', 'integer', 'digits_between:1,20'],
            'air_bag' => ['required', 'boolean'],
            'abs' => ['required', 'boolean'],
        ];
    }

    public function toArrayModel(): array
    {
        return [
            'marca_id' => $this->marcaId,
            'nome' => $this->nome,
            'numero_portas' => $this->numeroPortas,
            'lugares' => $this->lugares,
            'air_bag' => $this->airBag,
            'abs' => $this->abs,
        ];
    }
}
