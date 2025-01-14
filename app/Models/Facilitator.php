<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facilitator extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre', 'cedula', 'apellido', 'direccion', 'telefono', 'correo', 'user_id'
    ];
}