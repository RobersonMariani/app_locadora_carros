<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    private $cliente;

    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //$clientes = cliente::all();
        $clienteRepository = new ClienteRepository($this->cliente);

        if($request->has('filtro')) {
            $clienteRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {
            $clienteRepository->selectAtributos($request->atributos);
        }

        return response()->json($clienteRepository->getData(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->cliente->rules(), $this->cliente->feedback());
        $cliente = $this->cliente->create([
           'nome' => $request->nome,
        ]);
        return response()->json($cliente, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cliente = $this->cliente->find($id);
        if($cliente === null) {
            return response()->json(['erro' => 'Cliente pesquisado não existe'], 404);
        }
        return response()->json($cliente, 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $cliente = $this->cliente->find($id);

        if($cliente === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        if($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo as regras definidas na Model
            foreach($cliente->rules() as $input => $regra) {

                //coletar apenas as regras aplicáveis aos parâmentros parciais da requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas, $this->cliente->feedback());
        } else {
            $request->validate($this->cliente->rules(), $this->cliente->feedback());
        }

        //preeenche o objeto $marca com os dados do request
        $cliente->fill($request->all());
        $cliente->save();

        return response()->json($cliente, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cliente = $this->cliente->find($id);
        if($cliente === null) {
            return response()->json(['erro' => 'Impossível realizar a remoção. O recurso solicitado não existe'], 404);
        }
        $cliente->delete();
        return response()->json(['msg' => 'O cliente foi removido com sucesso'], 200);
    }
}
