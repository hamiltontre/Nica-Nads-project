<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_factura',
        'monto',
        'simbolo_moneda',
        'lugar',
        'descripcion',
        'fecha',
        'imagen_path'
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2'
    ];

    // Accesor para mostrar monto con símbolo
    public function getMontoFormateadoAttribute()
    {
        return $this->simbolo_moneda . ' ' . number_format($this->monto, 2);
    }
}