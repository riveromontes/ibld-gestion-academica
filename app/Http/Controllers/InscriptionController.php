<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inscription\CreateRequest;
use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Inscription;
use App\Models\User;
use App\Mail\NuevaInscripcion;
use App\Http\Controllers\MailController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Mail;

class InscriptionController extends Controller
{
    public function registroInscripcionPublic(CreateRequest $request)
    {
        try {
            $fecha = Carbon::now();
            $usuario = $request->nombre . ' ' . $request->apellido;
            DB::beginTransaction();
            $data_user = [
                "email" => $request->correo,
                "password" => rand(10000, 99999),
                "name" => $request->nombre
            ];
            $user = User::create($data_user);
            $data_person = [
                "nombre" => $request->nombre,
                "apellido" => $request->apellido,
                "cedula" => $request->cedula,
                "correo" => $request->correo,
                "direccion" => $request->direccion,
                "telefono" => $request->telefono
            ];
            $person = $user->person()->create($data_person);
            DB::commit();
        } catch (\Throwable  $exception) {
            DB::rollback();
            if (isset($user)) {
                $user->delete();
            }
            $mensaje = $exception->getMessage();
            $this->logGenerate($usuario, $fecha, $mensaje, $exception->getCode(), 'error', 'persona o usuario');
            return response()->json(['message' => 'Error al crear el usuario o la persona.', 'status' => $exception->getCode()], 400);
        }

        try {
            $authorization = $this->getBase64($request->input('autorizacion_pastoral'), 'autorizacion_', $request->cedula);
            $cedula_file = $this->getBase64($request->input('cedula_file'), 'cedula_', $request->cedula);
            $photo = $this->getBase64($request->input('foto'), 'foto_', $request->cedula);
            $estudios = $this->getBase64($request->input('estudios_cursados'), 'estudios_', $request->cedula);
            $planilla_ins = $this->getBase64($request->input('planilla_ins'), 'planilla_ins_', $request->cedula);
        } catch (\Throwable $exception) {
            if (isset($user)) {
                $user->delete();
            }
            $mensaje = $exception->getMessage();
            $this->logGenerate($usuario, $fecha, 'al crear archivo' . $mensaje, $exception->getCode(), 'error', 'crear archivo');
            return response()->json(['message' => 'Error al cargar los archivos.'], 400);
        }
        try {
            $inscripcion = [
                "fecha_inscripcion" => $date_now = Date('Y-m-d'),
                "pago" => false,
                "status_inscripcion" => "Pendiente por aprobar",
                "tipo_inscripcion" => "Nuevo ingreso",
                "person_id" => $person->id,
                'chair_module_id' => $request->chair,
                "planilla_ins" => $planilla_ins,
                "autorizacion_pastoral" => $authorization,
                "foto" => $photo,
                "cedula" => $cedula_file,
                "estudios_cursados" => $estudios
            ];

            $inscription = Inscription::create($inscripcion);

        } catch (\Throwable $exception) {
            if (isset($user)) {
                $user->delete();
            }
            $mensaje = $exception->getMessage();
            $this->logGenerate($usuario, $fecha, $mensaje, $exception->getCode(), 'error', 'inscriptions');
            return response()->json(['message' => 'Error al registrar la inscripción.'], 400);
        }
        try {
            $mailcontroller = new MailController;
            //$mailcontroller->enviarCorreoInscripcion($request->all());
        } catch (\Throwable $exception) {
            if (isset($user)) {
                $user->delete();
            }
            $mensaje = $exception->getMessage();
            $this->logGenerate($usuario, $fecha, $mensaje, $exception->getCode(), 'error', 'enviar correo');
            return response()->json(['message' => 'Error al intentar enviar el correo, vuelva a registrar su inscripción o contacte con soporte. '], 400);
        }

        $this->logGenerate($usuario, $fecha, 'Se creo la inscripcion correctamente', 200, 'error', 'inscriptions');

        return response()->json(['message' => 'Inscripción realizada con exito.', 'data' => $inscription]);
    }


    public function getBase64($file, $name, $cedula)
    {
        
        //\Log::debug('Nombre Archivo: ',['name' => $name]);
        \Log::debug('Contenido: ',['file' => $file]);
        
        

        try {
            // Verificar que el archivo Base64 tenga el formato correcto
            if (!str_starts_with($file, 'data:')) {
                throw new \Exception('Archivo Base64 inválido: El archivo no tiene el formato esperado.');
            }
    
            // Separar el encabezado del contenido Base64
            list($header, $data) = explode(',', $file);
            if (!$data) {
                throw new \Exception('Archivo Base64 inválido: No se encuentra el contenido Base64.');
            }
    
            // Extraer el tipo de archivo del encabezado
            preg_match('/data:(.*?);base64/', $header, $matches);
            if (!isset($matches[1])) {
                throw new \Exception('No se pudo determinar el tipo de archivo del contenido Base64.');
            }
    
            $mimeType = $matches[1];
    
            // Decodificar el contenido Base64
            $decodedFile = base64_decode($data);
            if ($decodedFile === false) {
                throw new \Exception('Error al decodificar el archivo Base64: El contenido no es un Base64 válido.');
            }
    
            // Definir la extensión del archivo según el tipo MIME
            $extension = match ($mimeType) {
                'application/pdf' => 'pdf',
                'image/png' => 'png',
                'image/jpeg' => 'jpg',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                'application/msword' => 'doc',
                default => throw new \Exception('Tipo de archivo no soportado: ' . $mimeType)
            };
    
            // Crear el nombre del archivo
            $fileName = $name . $cedula . '.' . $extension;
    
            // Guardar el archivo en el disco público
            $path = $cedula . '/' . $fileName;
            $saved = Storage::disk('public')->put($path, $decodedFile);
    
            if (!$saved) {
                throw new \Exception('Error al guardar el archivo en el sistema de almacenamiento.');
            }
    
            return $fileName;
    
        } catch (\Exception $exception) {
            // Loguear el error y devolver un mensaje específico
            \Log::error("Error al procesar el archivo Base64: " . $exception->getMessage());
            throw new \Exception('Error al cargar el archivo. Verifique que el archivo sea válido y vuelva a intentarlo.');
        }



//---------------------------------------------------------------------------------

     /*        // Verificar que el archivo Base64 tenga el formato correcto
    if (!str_starts_with($file, 'data:')) {
        throw new \Exception('Archivo Base64 inválido.');
    }

    // Separar el encabezado del contenido Base64
    list($header, $data) = explode(',', $file);

    // Extraer el tipo de archivo del encabezado
    preg_match('/data:(.*?);base64/', $header, $matches);
    if (!isset($matches[1])) {
        throw new \Exception('No se pudo determinar el tipo de archivo.');
    }

    $mimeType = $matches[1];

    // Decodificar el contenido Base64
    $decodedFile = base64_decode($data);
    if ($decodedFile === false) {
        throw new \Exception('Error al decodificar el archivo Base64.');
    }

    // Definir la extensión del archivo según el tipo MIME
    $extension = match ($mimeType) {
        'application/pdf' => 'pdf',
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/msword' => 'doc',
        default => throw new \Exception('Tipo de archivo no soportado.')
    };

    // Crear el nombre del archivo
    $fileName = $name . $cedula . '.' . $extension;

    // Guardar el archivo en el disco público
    Storage::disk('public')->put($cedula . '/' . $fileName, $decodedFile);

    return $fileName; */


//--------------------------------------------
		
	/* 	//se obtiene el tipo de archivo
		$data = explode('/', mime_content_type($file));

		if (!preg_match("/data:".$data[0]."\/(.*?);/",$file,$file_extension)) {
			throw new \Exception('Archivo Base64 inválido.');
		} // extract the image extension


		
		$data = explode('/', $matches[1]);
		$file = substr($file, strpos($file, ',') + 1);
		$file = base64_decode($file);
	
		if ($file === false) {
			throw new \Exception('Error al decodificar el archivo base64.');
		}
	
		switch ($data[1]) {
			case 'vnd.openxmlformats-officedocument.wordprocessingml.document':
				$fileName = $name . $cedula . '.docx';
				break;
			case 'msword':
				$fileName = $name . $cedula . '.doc';
				break;
			case 'pdf':
				$fileName = $name . $cedula . '.pdf';
				break;
			default:
				$fileName = $name . $cedula . '.' . $data[1];
				break;
		}
	
		Storage::disk('public')->put($cedula . '/' . $fileName, $file);
		return $fileName; */

 	}


    public function logGenerate($user, $fecha, $mensaje, $codigo, $status, $tabla)
    {
        $data = [
            'mensaje' => $mensaje,
            'tabla' => $tabla,
            'fecha' => $fecha,
            'usuario' => $user,
            'codigo' => $codigo,
            'status' => $status
        ];
        DB::table('logs')->insert($data);
    }

    public function getInscriptionPublic()
    {
        $inscriptions = DB::table('persons')
            ->join('inscriptions', 'persons.id', '=', 'inscriptions.person_id')
            ->join('chairs', 'inscriptions.chair_module_id', 'chairs.id')
            ->select('inscriptions.*', 'persons.correo', 'persons.nombre', 'persons.telefono', 'persons.apellido', 'persons.cedula', 'persons.direccion', 'inscriptions.cedula as cedula_file', 'chairs.nombre as catedra')
            ->get();
        if (!isset($inscriptions)) {
            return response()->json(['message' => 'No hay inscritos.']);
        }

        return response()->json(['message' => 'Listado de inscripciones.', 'data' => $inscriptions]);
    }
    public function changeStatus(Request $request, $id)
    {
        $status = $request->status_inscripcion;
        if ($status === "Por cancelar aranceles") {
        }
        $inscription = Inscription::find($id);
        $inscription->update(['status_inscripcion' => $request->status_inscripcion]);
        return response()->json(['message' => 'Actualizado con extio.']);
    }
}
