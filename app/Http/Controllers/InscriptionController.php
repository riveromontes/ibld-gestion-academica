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
            $mailcontroller->enviarCorreoInscripcion($request->all());
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
        //se obtiene el tipo de archivo
        $data = explode('/', mime_content_type($file));

        preg_match("/data:" . $data[0] . "\/(.*?);/", $file, $file_extension); // extract the image extension
        $file = preg_replace('/data:' . $data[0] . '\/(.*?);base64,/', '', $file); // remove the type part
        $file = str_replace(' ', '+', $file);
        //se comprueba los tipos de archivo de docx, doc y pdf
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
        }
        //se comprueba si es un tipo imagen y se le asigna la extension correspondiente
        if ($data[0] === 'image') {

            $fileName = $name . $cedula . '.' . $file_extension[1]; //generating unique file name;
        }
        $fileNameFinal = 'documentacion/' . $cedula . '/' . $fileName;
        Storage::disk('public')->put($fileNameFinal, base64_decode($file));
        return $fileNameFinal;
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
