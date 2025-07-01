<?php

namespace App\Http\Controllers;

use App\Models\Atleta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AtletaController extends Controller
{
    /**
     * Muestra el listado de todos los atletas
     */
   public function index()
    {
        $grupos = ['Federados', 'Novatos', 'Juniors', 'Principiantes'];

        $atletasPorGrupo = [];
        foreach ($grupos as $grupo) {
            $atletasPorGrupo[$grupo] = Atleta::where('grupo', $grupo)
                ->with(['asistencias' => function($query) {
                    $query->whereMonth('fecha', now()->month)
                          ->whereYear('fecha', now()->year);
                }])
                ->orderBy('nombre')
                ->paginate(12, ['*'], "page_{$grupo}");
        }

        return view('atletas.index', compact('grupos', 'atletasPorGrupo'));
    }

    /**
     * Muestra el formulario para crear un nuevo atleta
     */
    public function create()
    {
        $grupos = ['Federados', 'Novatos', 'Juniors', 'Principiantes'];
        return view('atletas.create', compact('grupos'));
    }

    /**
     * Almacena un nuevo atleta en la base de datos
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date|before:today',
            'grupo' => 'required|in:Federados,Novatos,Juniors,Principiantes',
            'becado' => 'sometimes|boolean',
            'foto' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('fotos', 'public');
            $validated['foto'] = $path;
        } else {
            $validated['foto'] = null;
        }

        $validated['becado'] = $request->has('becado');
        $validated['user_id'] = Auth::user()->id;

        Atleta::create($validated);

        return redirect()->route('atletas.index')
                         ->with('success', 'Atleta creado correctamente');
    }

    public function show(string $id)
    {
        // Por implementar si es necesario
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Atleta $atleta)
    {
        $grupos = ['Federados', 'Novatos', 'Juniors', 'Principiantes'];

        return view('atletas.edit', [
            'atleta' => $atleta,
            'grupos' => $grupos
        ]);
    }

    public function update(Request $request, Atleta $atleta)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date|before:today',
            'grupo' => 'required|in:Federados,Novatos,Juniors,Principiantes',
            'becado' => 'sometimes|boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('foto')) {
            if ($atleta->foto) {
                Storage::delete('public/'.$atleta->foto);
            }

            $path = $request->file('foto')->store('atletas', 'public');
            $validated['foto'] = $path;
        }

        $validated['becado'] = $request->has('becado');

        $atleta->update($validated);

        return redirect()->route('atletas.index')
            ->with('success', 'Atleta actualizado correctamente');
    }

    /**
     * Elimina el atleta especificado
     */
    public function destroy(Atleta $atleta)
    {
        if ($atleta->foto && Storage::disk('public')->exists($atleta->foto)) {
            Storage::disk('public')->delete($atleta->foto);
        }

        $atleta->delete();

        return redirect()->route('atletas.index')
            ->with('success', 'Atleta eliminado correctamente');
    }
}
