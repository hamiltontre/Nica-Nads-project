<?php

namespace App\Http\Controllers;

use App\Models\Atleta;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    public function index(Request $request)
    {
        $grupoSeleccionado = $request->input('grupo', 'Federados');
        $mesActual = $request->input('mes', now()->month);
        $anioActual = $request->input('anio', now()->year);
        $turno = $grupoSeleccionado === 'Federados' ? $request->input('turno', 'mañana') : 'tarde';

        // Validar grupo seleccionado
        $gruposValidos = ['Federados', 'Novatos', 'Juniors', 'Principiantes'];
        if (!in_array($grupoSeleccionado, $gruposValidos)) {
            abort(404, 'Grupo no válido');
        }

        $fechaInicio = Carbon::create($anioActual, $mesActual, 1);
        $fechaFin = $fechaInicio->copy()->endOfMonth();
        
        // Obtener todos los días del mes
        $diasMes = [];
        $currentDay = $fechaInicio->copy();
        while ($currentDay <= $fechaFin) {
            $diasMes[] = [
                'fecha' => $currentDay->format('Y-m-d'),
                'dia' => $currentDay->day,
                'es_domingo' => $currentDay->isSunday(),
            ];
            $currentDay->addDay();
        }

        // Obtener atletas del grupo seleccionado
        $atletas = Atleta::where('grupo', $grupoSeleccionado)
                        ->orderBy('nombre')
                        ->get();

        // Obtener asistencias existentes
        $asistencias = Asistencia::whereBetween('fecha', [$fechaInicio, $fechaFin])
                                ->where('turno', $turno)
                                ->get()
                                ->groupBy(['atleta_id', 'fecha']);

        return view('asistencias.index', compact(
            'grupoSeleccionado',
            'gruposValidos',
            'mesActual',
            'anioActual',
            'turno',
            'diasMes',
            'atletas',
            'asistencias'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asistencias' => 'required|array',
            'asistencias.*.atleta_id' => 'required|exists:atletas,id',
            'asistencias.*.fecha' => 'required|date',
            'asistencias.*.turno' => 'required|in:mañana,tarde',
            'asistencias.*.estado' => 'required|in:presente,ausente,libre'
        ]);

        $asistencias = $request->asistencias;
        $count = 0;

        DB::beginTransaction();
        try {
            foreach ($asistencias as $asistencia) {
                // Saltar si es domingo
                if (Carbon::parse($asistencia['fecha'])->isSunday()) {
                    continue;
                }

                Asistencia::updateOrCreate(
                    [
                        'atleta_id' => $asistencia['atleta_id'],
                        'fecha' => $asistencia['fecha'],
                        'turno' => $asistencia['turno']
                    ],
                    ['estado' => $asistencia['estado']]
                );
                $count++;
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => "Se guardaron $count asistencias correctamente"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al guardar asistencias: ' . $e->getMessage()], 500);
        }
    }
}