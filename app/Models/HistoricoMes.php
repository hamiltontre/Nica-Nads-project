<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoMes extends Model
{
    protected $fillable = ['mes', 'anio', 'datos'];
    
    protected $casts = [
        'datos' => 'array'
    ];
}