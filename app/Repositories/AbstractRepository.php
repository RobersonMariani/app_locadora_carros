<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function selectAtributosRegistroRelacionados($atributos)
    {
        $this->model = $this->model->with($atributos);
    }

    public function filtro($filtros)
    {
        $filtros = explode(';', $filtros);
        foreach($filtros as $condicao) {
            $c = explode(":", $condicao);
            $this->model = $this->model->where($c[0], $c[1], $c[2]);
        }
    }

    public function selectAtributos($atributos)
    {
        $this->model = $this->model->selectRaw($atributos);
    }

    public function getData()
    {
        return $this->model->get();
    }

    public function getDataPaginate($numeroRegistroPagina)
    {
        return $this->model->paginate($numeroRegistroPagina);
    }
}
