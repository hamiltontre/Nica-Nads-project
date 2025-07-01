<?php
namespace App\Http\Controllers;

use App\Models\Atleta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AtletaController extends Controller
{
    // Grupos disponibles para los atletas
    const GRUPOS_DISPONIBLES = ['Federados', 'Novatos', 'Juniors', 'Principiantes'];
    
    /**
     * Muestra el listado de todos los atletas agrupados por grupo
     */
    public function index()
    {
        $grupos = self::GRUPOS_DISPONIBLES;
        $atletasPorGrupo = [];
        
        // Pre-cargamos datos para la vista inicial (primera pestaña)
        $grupoInicial = $grupos[0];
        $atletasPorGrupo[$grupoInicial] = $this->obtenerAtletasConAsistencias($grupoInicial);
        
        // Calculamos días hábiles del mes actual
        $diasHabiles = $this->calcularDiasHabiles($grupoInicial);
        
        return view('atletas.index', [
            'grupos' => $grupos,
            'atletasPorGrupo' => $atletasPorGrupo,
            'diasHabiles' => $diasHabiles
        ]);
    }
    
    /**
     * Obtiene atletas por grupo para carga AJAX
     */
    public function getAtletasByGrupo($grupo)
    {
        // Validar que el grupo sea válido
        if (!in_array($grupo, self::GRUPOS_DISPONIBLES)) {
            return response()->json(['error' => 'Grupo no válido'], 404);
        }
        
        $atletas = $this->obtenerAtletasConAsistencias($grupo);
        $diasHabiles = $this->calcularDiasHabiles($grupo);
        
        return response()->json([
            'html' => view('atletas._atletas_list', [
                'atletas' => $atletas,
                'diasHabiles' => $diasHabiles
            ])->render()
        ]);
    }
    
    /**
     * Obtiene atletas con sus asistencias para un grupo específico
     */
    private function obtenerAtletasConAsistencias($grupo)
    {
        return Atleta::where('grupo', $grupo)
            ->with(['asistencias' => function($query) {
                $query->whereMonth('fecha', now()->month)
                      ->whereYear('fecha', now()->year);
            }])
            ->orderBy('nombre')
            ->paginate(12);
    }
    
    /**
     * Calcula días hábiles según el grupo
     */
    private function calcularDiasHabiles($grupo)
    {
        $fechaInicio = now()->startOfMonth();
        $fechaFin = now()->endOfMonth();
        $dias = 0;
        
        while ($fechaInicio <= $fechaFin) {
            if (!$fechaInicio->isSunday()) {
                // Grupos especiales que solo entrenan ciertos días
                if (in_array($grupo, ['Juniors', 'Principiantes'])) {
                    if (in_array($fechaInicio->dayOfWeek, [2, 4, 6])) { // Martes, Jueves, Sábado
                        $dias++;
                    }
                } else {
                    $dias++;
                }
            }
            $fechaInicio->addDay();
        }
        
        return $dias;
    }

    // Muestra el formulario para crear un nuevo atleta
    public function create()
    {
        $grupos = self::GRUPOS_DISPONIBLES;
        return view('atletas.create', compact('grupos'));
    }

    // Almacena un nuevo atleta en la base de datos
    public function store(Request $request)
    {
        // Valida los datos del formulario
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date|before:today',
            'grupo' => 'required|in:' . implode(',', self::GRUPOS_DISPONIBLES),
            'becado' => 'sometimes|boolean',
            'foto' => 'nullable|image|max:2048'
        ]);

        // Procesa la foto si se subió
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('fotos', 'public');
            $validated['foto'] = $path;
        } else {
            $validated['foto'] = null;
        }

        // Asigna valores adicionales
        $validated['becado'] = $request->has('becado');
        $validated['user_id'] = Auth::id();

        // Crea el atleta
        Atleta::create($validated);

        return redirect()->route('atletas.index')
                         ->with('success', 'Atleta creado correctamente');
    }

    // Muestra el formulario para editar un atleta
    public function edit(Atleta $atleta)
    {
        return view('atletas.edit', [
            'atleta' => $atleta,
            'grupos' => self::GRUPOS_DISPONIBLES
        ]);
    }

    // Actualiza los datos de un atleta
    public function update(Request $request, Atleta $atleta)
    {
        // Valida los datos del formulario
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date|before:today',
            'grupo' => 'required|in:' . implode(',', self::GRUPOS_DISPONIBLES),
            'becado' => 'sometimes|boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Procesa la foto si se subió una nueva
        if ($request->hasFile('foto')) {
            if ($atleta->foto) {
                Storage::delete('public/'.$atleta->foto);
            }

            $path = $request->file('foto')->store('atletas', 'public');
            $validated['foto'] = $path;
        }

        $validated['becado'] = $request->has('becado');

        // Actualiza el atleta
        $atleta->update($validated);

        return redirect()->route('atletas.index')
            ->with('success', 'Atleta actualizado correctamente');
    }

    // Elimina el atleta especificado
    public function destroy(Atleta $atleta)
    {
        // Elimina la foto si existe
        if ($atleta->foto && Storage::disk('public')->exists($atleta->foto)) {
            Storage::disk('public')->delete($atleta->foto);
        }

        // Elimina el atleta
        $atleta->delete();

        return redirect()->route('atletas.index')
            ->with('success', 'Atleta eliminado correctamente');
    }
}