<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\Data;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class UpdateModeloData extends Data
{
    public function __construct(
        public ?int $marcaId = null,
        public ?string $nome = null,
        public ?UploadedFile $imagem = null,
        public ?int $numeroPortas = null,
        public ?int $lugares = null,
        public ?bool $airBag = null,
        public ?bool $abs = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        $modeloId = request()->route('modelo');

        return [
            'marca_id' => ['nullable', 'integer', 'exists:marcas,id'],
            'nome' => ['nullable', 'string', 'max:30', 'min:3', Rule::unique('modelos', 'nome')->ignore($modeloId)],
            'imagem' => ['nullable', 'file', 'mimes:png,jpeg,jpg'],
            'numero_portas' => ['nullable', 'integer', 'digits_between:1,5'],
            'lugares' => ['nullable', 'integer', 'digits_between:1,20'],
            'air_bag' => ['nullable', 'boolean'],
            'abs' => ['nullable', 'boolean'],
        ];
    }

    public function toArrayModel(): array
    {
        return collect([
            'marca_id' => $this->marcaId,
            'nome' => $this->nome,
            'numero_portas' => $this->numeroPortas,
            'lugares' => $this->lugares,
            'air_bag' => $this->airBag,
            'abs' => $this->abs,
        ])->filter(fn ($value) => $value !== null)->toArray();
    }
}
