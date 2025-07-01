<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Atleta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'becado',
        'grupo',
        'foto',
        'user_id'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'becado' => 'boolean'
    ];

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    // Accesor para calcular la edad
    public function getEdadAttribute()
    {
        return Carbon::parse($this->fecha_nacimiento)->age;
    }
}