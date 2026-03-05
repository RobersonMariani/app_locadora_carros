<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    private $modelo;

    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $modeloRepository = new ModeloRepository($this->modelo);

        if($request->has('atributos_marca')) {
            $atributos_marca = 'marca:id,'.$request->atributos_marca;
            $modeloRepository->selectAtributosRegistroRelacionados($atributos_marca);
        } else {
            $modeloRepository->selectAtributosRegistroRelacionados('marca');
        }

        if($request->has('filtro')) {
            $modeloRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {
            $modeloRepository->selectAtributos($request->atributos);
        }

        return response()->json($modeloRepository->getData(), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate($this->modelo->rules(), $this->modelo->feedback());
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs,
        ]);
        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $modelo = $this->modelo->with('marca')->find($id);
        if($modelo === null) {
            return response()->json(['erro' => 'Modelo pesquisado não existe'], 404);
        }
        return response()->json($modelo, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Modelo $modelo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->find($id);

        if($modelo === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        if($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo as regras definidas na Model
            foreach($modelo->rules() as $input => $regra) {

                //coletar apenas as regras aplicáveis aos parâmentros parciais da requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas, $this->modelo->feedback());
        } else {
            $request->validate($this->modelo->rules(), $this->modelo->feedback());
        }

        $modelo->fill($request->all());
        //remove o arquivo antigo, caso o novo arquivo tenha sido enviado no request
        if($request->file('imagem')) {
            Storage::disk('public')->delete($modelo->imagem);
            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens/modelos', 'public');
            $modelo->imagem = $imagem_urn;
        }
        $modelo->save();

        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);
        if($modelo === null) {
            return response()->json(['erro' => 'Impossível realizar a remoção. O recurso solicitado não existe'], 404);
        }

        //remove o arquivo antigo, caso o novo arquivo tenha sido enviado no request
        if($modelo->imagem) {
            Storage::disk('public')->delete($modelo->imagem);
        }

        $modelo->delete();
        return response()->json(['msg' => 'O modelo foi removido com sucesso'], 200);
    }
}
