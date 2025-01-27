<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parametro;
use Illuminate\Support\Facades\Log;
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
        try {
            // Validação dos dados
            $request->validate([
                'dias_faltantes' => 'required|string|max:255',
                'texto' => 'nullable|string|max:255',
                'valor' => 'nullable|string|max:255',
                'observacao' => 'required|string|max:255',
            ]);

            // Criação do parâmetro
            Parametro::create($request->all());

            // Redirecionar com mensagem de sucesso
            return redirect()->route('parametros.create')->with('success', 'Parâmetro criado com sucesso.');
        } catch (\Exception $e) {
            // Registrar o erro
            Log::error('Erro ao criar parâmetro: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e
            ]);

            // Redirecionar com mensagem de erro
            return redirect()->route('parametros.create')->withErrors(['error' => 'Erro ao criar parâmetro.']);
        }
    }

    public function edit(Parametro $parametro)
    {
        return view('parametros.edit', compact('parametro'));
    }

    public function update(Request $request, Parametro $parametro)
    {
        $request->validate([
            'texto' => 'required|string|max:255',
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
        foreach ($parametros as $id => $data) {
            $parametro = Parametro::find($id);
            if ($parametro) {
                if (is_array($data)) {
                    foreach ($data as $key => $value) {
                        $parametro->$key = $value;
                    }
                }
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
