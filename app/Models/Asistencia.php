<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $fillable = ['atleta_id', 'fecha', 'turno', 'estado'];
    
    public function atleta()
    {
        return $this->belongsTo(Atleta::class);
    }
}