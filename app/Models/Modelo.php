<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;
    protected $fillable = ['marca_id', 'nome', 'imagem', 'numero_portas', 'lugares', 'air_bag', 'abs'];

    public function rules()
    {
        return [
            'marca_id' => 'exists:marcas,id',
            'nome' => 'required|unique:modelos|min:3',
            'imagem' => 'required|file|mimes:png, jpeg, jpg',
            'numero_portas' => 'required|integer|digits_between:1,5',
            'lugares' => 'required|integer|digits_between:1,20',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean',
        ];
    }

    public function feedback()
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'nome.unique' => 'O nome da marca já existe.',
            'imagem.mimes' => 'O campo :attribute deve ser um arquivo do tipo png, jpeg, jpg'
        ];
    }

    public function marca()
    {
        //um modelo pertecendo a uma marca
        return $this->belongsTo(Marca::class);
    }
}
