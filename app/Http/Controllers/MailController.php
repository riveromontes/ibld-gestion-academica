<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\NuevaInscripcion;
class MailController extends Controller
{
    public function enviarCorreoInscripcion($data){
 		Mail::to($data['correo'])
 			->send(new NuevaInscripcion($data));
    }
}
