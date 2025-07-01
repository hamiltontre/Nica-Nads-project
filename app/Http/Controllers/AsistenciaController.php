<?php

namespace App\Http\Controllers;

use App\Models\Atleta;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    const GRUPOS_DISPONIBLES = ['Federados', 'Novatos', 'Juniors', 'Principiantes'];
    const TURNOS_DISPONIBLES = ['mañana', 'tarde'];

    // Muestra el registro de asistencias del mes actual
    public function index(Request $request)
    {
        $grupoSeleccionado = $this->validarGrupo($request->input('grupo', 'Federados'));
        $turno = $this->validarTurno($grupoSeleccionado, $request->input('turno', 'mañana'));

        $fechaInicio = now()->startOfMonth();
        $fechaFin = now()->endOfMonth();
        
            return view('asistencias.index', [
                'grupos' => self::GRUPOS_DISPONIBLES,
                'grupoSeleccionado' => $grupoSeleccionado,
                'turno' => $turno,
                'diasMes' => $this->generarDiasMes($fechaInicio, $fechaFin),
                'atletas' => $this->obtenerAtletas($grupoSeleccionado),
                'asistencias' => $this->obtenerAsistencias($fechaInicio, $fechaFin, $grupoSeleccionado)
            ]);
        }

    // Valida y retorna el grupo seleccionado
    protected function validarGrupo($grupo)
    {
        return in_array($grupo, self::GRUPOS_DISPONIBLES) ? $grupo : 'Federados';
    }

    /** Valida y retorna el turno seleccionado **/
    protected function validarTurno($grupo, $turno)
    {
        return ($grupo === 'Federados' && in_array($turno, self::TURNOS_DISPONIBLES)) 
            ? $turno 
            : 'tarde';
    }

    // Genera array con los días del mes
    protected function generarDiasMes($fechaInicio, $fechaFin)
    {
        $dias = [];
        $currentDay = $fechaInicio->copy();
        
        while ($currentDay <= $fechaFin) {
            $dias[] = [
                'fecha' => $currentDay->format('Y-m-d'),
                'dia' => $currentDay->day,
                'dia_semana' => $currentDay->shortDayName,
                'es_domingo' => $currentDay->isSunday(),
            ];
            $currentDay->addDay();
        }
        
        return $dias;
    }

    // Obtiene atletas filtrados por grupo
    protected function obtenerAtletas($grupo)
    {
        return Atleta::where('grupo', $grupo)
                   ->orderBy('nombre')
                   ->get();
    }

    /* Obtiene asistencias del rango de fechas */
    protected function obtenerAsistencias($fechaInicio, $fechaFin, $grupo)
    {
        $turnos = ($grupo === 'Federados') ? self::TURNOS_DISPONIBLES : ['tarde'];
        
        return Asistencia::whereBetween('fecha', [$fechaInicio, $fechaFin])
                       ->whereIn('turno', $turnos)
                       ->get()
                       ->groupBy(['atleta_id', 'fecha', 'turno']);
    }

    // Guarda las asistencias mediante AJAX
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asistencias' => 'required|array',
            'asistencias.*.atleta_id' => 'required|exists:atletas,id',
            'asistencias.*.fecha' => 'required|date',
            'asistencias.*.turno' => 'required|in:mañana,tarde',
            'asistencias.*.estado' => 'required|in:presente,ausente,libre'
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['asistencias'] as $asistencia) {
                if (!Carbon::parse($asistencia['fecha'])->isSunday()) {
                    Asistencia::updateOrCreate(
                        [
                            'atleta_id' => $asistencia['atleta_id'],
                            'fecha' => $asistencia['fecha'],
                            'turno' => $asistencia['turno']
                        ],
                        ['estado' => $asistencia['estado']]
                    );
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => "Asistencias guardadas"]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }

    /** Muestra el histórico de asistencias **/
   // Agrega este método al controlador
public function historico(Request $request)
{
    $grupoSeleccionado = $this->validarGrupo($request->input('grupo', 'Federados'));
    $turno = $this->validarTurno($grupoSeleccionado, $request->input('turno', 'mañana'));
    $mes = $request->input('mes', now()->month);
    $anio = $request->input('anio', now()->year);

    $fechaInicio = Carbon::create($anio, $mes, 1)->startOfMonth();
    $fechaFin = $fechaInicio->copy()->endOfMonth();

    return view('asistencias.historicos', [
        'grupos' => self::GRUPOS_DISPONIBLES,
        'grupoSeleccionado' => $grupoSeleccionado,
        'turno' => $turno,
        'mes' => $mes,
        'anio' => $anio,
        'meses' => $this->getMeses(),
        'anios' => range(now()->year, now()->year - 5),
        'diasMes' => $this->generarDiasMes($fechaInicio, $fechaFin),
        'atletas' => $this->obtenerAtletas($grupoSeleccionado),
        'asistencias' => $this->obtenerAsistencias($fechaInicio, $fechaFin, $grupoSeleccionado)
    ]);
}

protected function getMeses()
{
    return [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    ];
}

}