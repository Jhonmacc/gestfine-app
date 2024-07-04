<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parametro;

class ParametroController extends Controller
{
    public function index()
    {
        $parametros = Parametro::all();
        return view('parametros.create', compact('parametros'));
    }

    public function show(Parametro $parametro)
    {
        return view('parametros.show', compact('parametro'));
    }

    public function create()
    {
        return view('parametros.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'dias_faltantes' => 'required|string|max:255',
            'valor' => 'required|string|max:255',
            'observacao' => 'required|string|max:255',
        ]);

        Parametro::create($request->all());
        return redirect()->route('parametros.create')->with('success', 'Parâmetro criado com sucesso.');
    }

    public function edit(Parametro $parametro)
    {
        return view('parametros.edit', compact('parametro'));
    }

    public function update(Request $request, Parametro $parametro)
    {
        $request->validate([
            'valor' => 'required|string|max:255',
        ]);

        $parametro->update($request->all());
        return redirect()->route('parametros.index')->with('success', 'Parâmetro atualizado com sucesso.');
    }

    public function editParameters()
    {
        $parametros = Parametro::all();
        return view('parametros.edit-parameters', compact('parametros'));
    }

    public function updateParameters(Request $request)
    {
        $parametros = $request->input('parametros');

        try {
            foreach ($parametros as $id => $valor) {
                $parametro = Parametro::find($id);
                if ($parametro) {
                    $parametro->valor = $valor;
                    $parametro->save();
                }
            }

            return response()->json(['success' => true, 'message' => 'Parâmetros atualizados com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao atualizar os parâmetros.'], 500);
        }
    }

    public function destroy(Parametro $parametro)
    {
        $parametro->delete();
        return redirect()->route('parametros.index')->with('success', 'Parâmetro excluído com sucesso.');
    }
}
