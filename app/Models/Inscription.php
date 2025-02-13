<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Person;

class Inscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'fecha_inscripcion', 'pago', 'status_inscripcion', 'tipo_inscripcion', 'planilla_ins', 'autorizacion_pastoral', 'foto', 'cedula', 'estudios_cursados', 'person_id', 'chair_module_id'
    ];


      // Definir la relación con la tabla 'persons'
      public function person()
      {
          return $this->belongsTo(Person::class, 'person_id');
      }



}
