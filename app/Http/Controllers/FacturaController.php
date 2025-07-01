<?php
namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacturaController extends Controller
{
    // Muestra listado de facturas con filtros
    public function index(Request $request)
    {
        $query = Factura::query();
        
        // Aplica filtros según parámetros recibidos
        if ($request->filled('monto_min')) {
            $query->where('monto', '>=', $request->monto_min);
        }
        
        if ($request->filled('monto_max')) {
            $query->where('monto', '<=', $request->monto_max);
        }
        
        if ($request->filled('fecha_inicio')) {
            $query->where('fecha', '>=', $request->fecha_inicio);
        }
        
        if ($request->filled('fecha_fin')) {
            $query->where('fecha', '<=', $request->fecha_fin);
        }
        
        if ($request->filled('lugar')) {
            $query->where('lugar', 'like', '%'.$request->lugar.'%');
        }
        
        // Filtro por símbolo de moneda
        if ($request->filled('simbolo_moneda')) {
            $query->where('simbolo_moneda', $request->simbolo_moneda);
        }
        
        // Pagina los resultados
        $facturas = $query->orderBy('fecha', 'desc')->paginate(15);
        
        return view('facturas.index', compact('facturas'));
    }

    // Muestra formulario para crear nueva factura
    public function create()
    {
        return view('facturas.create');
    }

    // Almacena una nueva factura
    public function store(Request $request)
    {
        // Valida los datos del formulario
        $validated = $request->validate([
            'numero_factura' => 'required|unique:facturas',
            'monto' => 'required|numeric|min:0',
            'simbolo_moneda' => 'required|in:C$,$',
            'lugar' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha' => 'required|date',
            'imagen' => 'required|image|max:2048'
        ]);
        
        // Guarda la imagen
        $path = $request->file('imagen')->store('facturas', 'public');
        
        // Crea la factura
        $factura = Factura::create([
            'numero_factura' => $validated['numero_factura'],
            'monto' => $validated['monto'],
            'simbolo_moneda' => $validated['simbolo_moneda'],
            'lugar' => $validated['lugar'],
            'descripcion' => $validated['descripcion'],
            'fecha' => $validated['fecha'],
            'imagen_path' => $path
        ]);
        
        return redirect()->route('facturas.index')
            ->with('success', 'Factura registrada correctamente');
    }

    // Muestra los detalles de una factura
    public function show(Factura $factura)
    {
        return view('facturas.show', compact('factura'));
    }

    // Muestra formulario para editar factura
    public function edit(Factura $factura)
    {
        return view('facturas.edit', compact('factura'));
    }

    // Actualiza una factura existente
    public function update(Request $request, Factura $factura)
    {
        // Valida los datos del formulario
        $validated = $request->validate([
            'numero_factura' => 'required|unique:facturas,numero_factura,'.$factura->id,
            'monto' => 'required|numeric|min:0',
            'simbolo_moneda' => 'required|in:C$,$',
            'lugar' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha' => 'required|date',
            'imagen' => 'nullable|image|max:2048'
        ]);
        
        $data = [
            'numero_factura' => $validated['numero_factura'],
            'monto' => $validated['monto'],
            'simbolo_moneda' => $validated['simbolo_moneda'],
            'lugar' => $validated['lugar'],
            'descripcion' => $validated['descripcion'],
            'fecha' => $validated['fecha']
        ];
        
        // Procesa nueva imagen si se subió
        if ($request->hasFile('imagen')) {
            // Elimina imagen anterior
            Storage::disk('public')->delete($factura->imagen_path);
            
            // Guarda nueva imagen
            $path = $request->file('imagen')->store('facturas', 'public');
            $data['imagen_path'] = $path;
        }
        
        // Actualiza la factura
        $factura->update($data);
        
        return redirect()->route('facturas.index')
            ->with('success', 'Factura actualizada correctamente');
    }

    // Elimina una factura
    public function destroy(Factura $factura)
    {
        // Elimina la imagen asociada
        Storage::disk('public')->delete($factura->imagen_path);
        
        // Elimina la factura
        $factura->delete();
        
        return redirect()->route('facturas.index')
            ->with('success', 'Factura eliminada correctamente');
    }
}