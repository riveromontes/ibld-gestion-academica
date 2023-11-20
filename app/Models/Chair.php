<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chair extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre', 'fecha_inicio', 'ubicacion', 'coordinador', 'facilitator_id','inscripciones'
    ];

    public function modules()
    {
        return $this->belongsToMany('App\Models\Module');
    }
}
