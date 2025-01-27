<?php

namespace App\Http\Controllers;

use App\Models\EnviaEmailParametro;
use Illuminate\Http\Request;

class EnviaEmailParametroController extends Controller
{

    public function parameterSendEmail()
    {
        return view('parametros.parameter-send-email');
        return view('emails.certificado_notificacao');
    }
    public function personalizeEmail()
    {
        return view('emails.certificado_notificacao');
    }
    public function index()
    {
        $parametros = EnviaEmailParametro::all();
        return response()->json($parametros);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string',
            'mensagem' => 'required|string',
        ]);

        $parametro = EnviaEmailParametro::create($validated);

        return response()->json(['message' => 'Os parâmetros foram salvos com sucesso!', 'parametro' => $parametro]);
    }

    public function update(Request $request, $id)
    {
        $parametro = EnviaEmailParametro::findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'required|string',
            'mensagem' => 'required|string',
        ]);

        $parametro->update($validated);

        return response()->json(['message' => 'Os parâmetros foram atualizados com sucesso!', 'parametro' => $parametro]);
    }


    public function destroy($id)
    {
        $parametro = EnviaEmailParametro::findOrFail($id);
        $parametro->delete();

        return response()->json(['message' => 'Parâmetro excluído com sucesso!']);
    }

    public function toggleStatus($id)
    {
        $parametro = EnviaEmailParametro::findOrFail($id);
        $parametro->status = !$parametro->status;
        $parametro->save();

        return response()->json(['message' => 'Status atualizado com sucesso!', 'status' => $parametro->status]);
    }
}
