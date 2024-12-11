<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certification;
use App\Models\Parametro;

class NumberController extends Controller
{
    public function getNumbers()
    {
        // Obter o valor de dias_faltantes ou definir como 10 dias por padrão
        $daysUntilWarning = Parametro::where('dias_faltantes', 'dias_para_vencer')->value('valor') ?? 10;

        // Obter a data atual para comparação
        $currentDate = now();

        // Filtrar os certificados que estão vencidos ou perto de vencer
        $certifications = Certification::whereRaw('(DATEDIFF(validTo_time_t, ?) <= ?)', [$currentDate, $daysUntilWarning])
                                        ->orWhereRaw('(DATEDIFF(validTo_time_t, ?) <= 0)', [$currentDate])
                                        ->get(['numero']); // Buscar apenas a coluna 'numero'

        // Formatar os dados para o formato esperado pelo MultiSelect
        $formatted = $certifications->map(function ($certification) {
            return [
                'name' => $certification->numero,
                'code' => $certification->numero, // Usando o mesmo valor para 'code'
            ];
        });

        return response()->json($formatted);
    }
}
