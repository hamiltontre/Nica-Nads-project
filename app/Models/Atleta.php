<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atleta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'edad',
        'becado',
        'grupo',
        'foto',
        'user_id'
    ];

    // Relación con asistencias
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    // Relación con el usuario creador
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Método para calcular asistencia mensual
    public function calcularAsistenciaMes($mes, $año)
    {
        return $this->asistencias()
            ->whereMonth('fecha', $mes)
            ->whereYear('fecha', $año)
            ->where('estado', 'asistio')
            ->count();
    }
}