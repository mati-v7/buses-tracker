<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawPosition extends Model
{
    protected $fillable = [
        'linea',
        'unidad',
        'posicion',
        'estado',
        'sen',
        'hora_api',
        'online',
        'aire',
        'capturado_en',
    ];

    protected $casts = [
        'online' => 'boolean',
        'aire' => 'boolean',
        'hora_api' => 'datetime:H:i:s',
        'capturado_en' => 'datetime',
    ];
}
