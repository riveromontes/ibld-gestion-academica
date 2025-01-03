<?php

namespace App\Http\Controllers;
use App\Http\Requests\Inscription\CreateRequest;
use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Inscription;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class InscriptionPublic extends Controller
{
	public function inscripcion(CreateRequest $request){
		try{

			$data_user = [
				"email" =>$request->correo,
				"password"=>rand(10000,99999),
				"name"=>$request->nombre
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

		}catch(\Exception $exception){
			if (isset($user) ) {
				$user->delete();
			}
			return response()->json(['message'=>$exception->getMessage(),'status'=>$exception->getCode(),]);
		}

		try{
			$authorization = $this->getBase64($request->input('autorizacion_pastoral'),'autorizacion_',$request->cedula);
			$cedula_file = $this->getBase64($request->input('cedula_file'),'cedula_',$request->cedula);
			$photo = $this->getBase64($request->input('foto'),'foto_',$request->cedula);
			$estudios = $this->getBase64($request->input('estudios_cursados'),'estudios_',$request->cedula);
			$planilla_ins = $this->getBase64($request->input('planilla_ins'),'planilla_ins_',$request->cedula);
		}catch(\Exception $exception){
			$user->delete();
			return response()->json(['message'=>$exception->getMessage()]);
		}
		try{
			$inscripcion = [
			    "fecha_inscripcion" => $date_now = Date('Y-m-d'),
			    "pago"=>false,
			    "status_inscripcion"=>"Pendiente por aprobar",
			    "tipo_inscripcion"=>"Nuevo ingreso",
			    "person_id"=>$person->id,
			    'chair_module_id' => $request->chair,
			    "planilla_ins" => $planilla_ins ,
			    "autorizacion_pastoral" => $authorization,
			    "foto" => $photo,
			    "cedula" => $cedula_file,
			    "estudios_cursados" => $estudios
		    ];
		    $inscription = Inscription::create($inscripcion);
		}catch(\Exception $exception){
			$user->delete();
			return response()->json(['message'=>$exception->getMessage()]);
		}
		return response()->json([
            'message'=>'Inscripción realizada con exito.',
            'data'=>$inscription]
        );
	}
	public function getBase64($file,$name,$cedula){
		
		
		
		//se obtiene el tipo de archivo
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
		return $fileName;

 	}

 }
