<?php
namespace App\Http\Controllers;

use App\Models\Atleta;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    // Constantes para grupos y turnos disponibles
    const GRUPOS_DISPONIBLES = ['Federados', 'Novatos', 'Juniors', 'Principiantes'];
    const TURNOS_DISPONIBLES = ['mañana', 'tarde'];

    // Muestra el registro de asistencias del mes actual
  public function index(Request $request)
{
    // Valida grupo y turno seleccionados
    $grupoSeleccionado = $this->validarGrupo($request->input('grupo', 'Federados'));
    $turno = $this->validarTurno($grupoSeleccionado, $request->input('turno', 'mañana'));

    // Establece rango de fechas (mes actual)
    $fechaInicio = now()->startOfMonth();
    $fechaFin = now()->endOfMonth();
    
    $diasMes = $this->generarDiasMes($fechaInicio, $fechaFin, $grupoSeleccionado);
    
    // Calcular días hábiles totales
    $diasHabiles = collect($diasMes)->filter(function($dia) use ($grupoSeleccionado) {
        if ($dia['es_domingo']) return false;
        
        if (in_array($grupoSeleccionado, ['Juniors', 'Principiantes'])) {
            // Para Juniors y Principiantes: solo Martes y Jueves son hábiles
            return in_array($dia['dia_semana_numero'], [2, 4, 6]); // 2=Martes, 4=Jueves, 6=Sabado
        }
        
        return true; // Para otros grupos todos los días excepto domingo son hábiles
    })->count();

    return view('asistencias.index', [
        'grupos' => self::GRUPOS_DISPONIBLES,
        'grupoSeleccionado' => $grupoSeleccionado,
        'turno' => $turno,
        'diasMes' => $diasMes,
        'atletas' => $this->obtenerAtletas($grupoSeleccionado),
        'asistencias' => $this->obtenerAsistencias($fechaInicio, $fechaFin, $grupoSeleccionado),
        'diasHabiles' => $diasHabiles
    ]);
}

    // Valida y retorna el grupo seleccionado
    protected function validarGrupo($grupo)
    {
        return in_array($grupo, self::GRUPOS_DISPONIBLES) ? $grupo : 'Federados';
    }

    // Valida y retorna el turno seleccionado
    protected function validarTurno($grupo, $turno)
    {
        return ($grupo === 'Federados' && in_array($turno, self::TURNOS_DISPONIBLES)) 
            ? $turno 
            : 'tarde';
    }

    // Genera array con los días del mes
   protected function generarDiasMes($fechaInicio, $fechaFin, $grupoSeleccionado = null)
{
    $dias = [];
    $currentDay = $fechaInicio->copy();
    
    while ($currentDay <= $fechaFin) {
        $esDomingo = $currentDay->isSunday();
        $esDiaInhabil = false;
        
        if (in_array($grupoSeleccionado, ['Juniors', 'Principiantes'])) {
            $esDiaInhabil = in_array($currentDay->dayOfWeek, [1, 3, 5]); // Lunes, Miércoles, Viernes
        }
        
        $dias[] = [
            'fecha' => $currentDay->format('Y-m-d'),
            'dia' => $currentDay->day,
            'dia_semana' => $currentDay->shortDayName,
            'es_domingo' => $esDomingo,
            'es_dia_inhabil' => $esDiaInhabil,
            'dia_semana_numero' => $currentDay->dayOfWeek
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

    // Obtiene asistencias del rango de fechas
   protected function obtenerAsistencias($fechaInicio, $fechaFin, $grupo)
{
    $turnos = ($grupo === 'Federados') ? self::TURNOS_DISPONIBLES : ['tarde'];
    
    return Asistencia::whereBetween('fecha', [
            $fechaInicio->format('Y-m-d'), 
            $fechaFin->format('Y-m-d')
        ])
        ->whereIn('turno', $turnos)
        ->get()
        ->groupBy(['atleta_id', function($item) {
            return Carbon::parse($item->fecha)->format('Y-m-d');
        }, 'turno']);
}

    // Guarda las asistencias mediante AJAX
    public function store(Request $request)
    {
        // Valida los datos recibidos
       $validated = $request->validate([
    'asistencias' => 'required|array',
    'asistencias.*.atleta_id' => 'required|exists:atletas,id',
    'asistencias.*.fecha' => 'required|date',
    'asistencias.*.turno' => 'required|in:mañana,tarde',
    'asistencias.*.estado' => 'required|in:presente,ausente,justificado,libre'
]);

        // Transacción para asegurar integridad de datos
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

    // Muestra el histórico de asistencias
    public function historico(Request $request)
    {
        // Valida grupo, turno, mes y año
        $grupoSeleccionado = $this->validarGrupo($request->input('grupo', 'Federados'));
        $turno = $this->validarTurno($grupoSeleccionado, $request->input('turno', 'mañana'));
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);

        // Establece rango de fechas según mes y año seleccionados
        $fechaInicio = Carbon::create($anio, $mes, 1)->startOfMonth();
        $fechaFin = $fechaInicio->copy()->endOfMonth();

        // Retorna vista con datos históricos
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

    // Retorna array con nombres de los meses
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