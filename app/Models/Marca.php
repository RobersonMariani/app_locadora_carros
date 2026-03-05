<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'imagem'];

    public function rules()
    {
        return [
            'nome' => 'required|unique:marcas',
            'imagem' => 'required|file|mimes:png',
        ];
    }

    public function feedback()
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'nome.unique' => 'O nome da marca já existe.',
            'imagem.mimes' => 'O campo :attribute deve ser um arquivo do tipo png'
        ];
    }

    public function modelos()
    {
        //uma marca possui vários modelos
        return $this->hasMany(Modelo::class);
    }
}
