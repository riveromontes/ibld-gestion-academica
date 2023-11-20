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

		preg_match("/data:".$data[0]."\/(.*?);/",$file,$file_extension); // extract the image extension
 		$file = preg_replace('/data:'.$data[0].'\/(.*?);base64,/','',$file); // remove the type part
 		$file = str_replace(' ', '+', $file);
 		//se comprueba los tipos de archivo de docx, doc y pdf
 		switch ($data[1]) {
 			case 'vnd.openxmlformats-officedocument.wordprocessingml.document':
 			$fileName = $name.$cedula.'.docx';
 			break;
 			case 'msword':
 			$fileName = $name.$cedula.'.doc';
 			break;
 			case 'pdf':
 			$fileName = $name.$cedula.'.pdf';
 			break;
 		}
 		//se comprueba si es un tipo imagen y se le asigna la extension correspondiente
 		if ($data[0] === 'image') {

 			$fileName = $name.$cedula.'.'.$file_extension[1]; //generating unique file name;
 		}

 		Storage::disk('public')->put($cedula.'/'.$fileName,base64_decode($file));
 		return $fileName;

 	}

 }
