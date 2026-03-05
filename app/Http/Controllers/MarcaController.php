<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    private $marca;

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //$marcas = Marca::all();
        $marcaRepository = new MarcaRepository($this->marca);
        
        if($request->has('atributos_modelos')) {
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $marcaRepository->selectAtributosRegistroRelacionados($atributos_modelos);
        } else {
            $marcaRepository->selectAtributosRegistroRelacionados('modelos');
        }

        if($request->has('filtro')) {
            $marcaRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {
            $marcaRepository->selectAtributos($request->atributos);
        }
        
        return response()->json($marcaRepository->getDataPaginate(5), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //$marca =  Marca::create($request->all());
        $request->validate($this->marca->rules(), $this->marca->feedback());
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/marcas', 'public');

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(/*Marca $marca */$id)
    {
        $marca = $this->marca->with('modelos')->find($id);
        if($marca === null) {
            return response()->json(['erro' => 'Marca pesquisada não existe'], 404);
        }
        return response()->json($marca, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, /*Marca $marca */$id)
    {
        //$marca->update($request->all());
        $marca = $this->marca->find($id);

        if($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        if($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo as regras definidas na Model
            foreach($marca->rules() as $input => $regra) {

                //coletar apenas as regras aplicáveis aos parâmentros parciais da requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas, $this->marca->feedback());
        } else {
            $request->validate($this->marca->rules(), $this->marca->feedback());
        }

        //preeenche o objeto $marca com os dados do request
        $marca->fill($request->all());

        //remove o arquivo antigo, caso o novo arquivo tenha sido enviado no request
        if($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);
            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens/marcas', 'public');
            $marca->imagem = $imagem_urn;
        }
        $marca->save();

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(/*Marca $marca */ $id)
    {
        //$marca->delete();

        $marca = $this->marca->find($id);
        if($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a remoção. O recurso solicitado não existe'], 404);
        }

        //remove o arquivo antigo, caso o novo arquivo tenha sido enviado no request
        if($marca->imagem) {
            Storage::disk('public')->delete($marca->imagem);
        }

        $marca->delete();
        return response()->json(['msg' => 'A marca foi removida com sucesso'], 200);
    }
}
