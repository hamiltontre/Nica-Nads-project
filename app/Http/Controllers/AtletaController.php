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
        // Ordenamos por grupo y nombre para mejor visualización
        $atletas = Atleta::orderBy('grupo')
                         ->orderBy('nombre')
                         ->get();
        
        return view('atletas.index', compact('atletas'));
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
            'edad' => 'required|integer|min:5|max:99',
            'grupo' => 'required|in:Federados,Novatos,Juniors,Principiantes',
            'becado' => 'boolean',
            'foto' => 'nullable|image|max:2048' // Máximo 2MB
        ]);

        // Guardar la foto si existe
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('public/fotos');
            $validated['foto'] = str_replace('public/', '', $path);
        }
        // Asignar el usuario autenticado como creador
        $validated['user_id'] = Auth::user()->id;

        Atleta::create($validated);

        return redirect()->route('atletas.index')
                         ->with('success', 'Atleta creado correctamente');
    }


     public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    
}