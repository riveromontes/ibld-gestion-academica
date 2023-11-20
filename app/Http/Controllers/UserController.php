<?php

namespace App\Http\Controllers;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
	 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users =User::with('person')->get();
        return response()->json(['message'=>'Listado de todos usuarios','data'=>$users]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {

        $user = User::create($request->all());
        $user->assignRole($request->rol);
        try{
        	$data = [
        		"nombre" => $request->name,
        		"apellido" => $request->apellido,
        		"cedula" => $request->cedula,
        		"correo" => $request->email,
        		"direccion" => $request->direccion,
        		"telefono" => $request->telefono
        	];
        	$user->person()->create($data);
        	
        	return response()->json(['message'=>'Usuario agregado']);
        }catch(\Exception $exception){
        	$user->delete();
        	return response()->json(['message'=>$exception->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Facilitator  $facilitator
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json(['message'=>'Información del usuario','data'=>$user]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Facilitator  $facilitator
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, User $user)
    {
        $user->update($request->all());
        try{
        	$data = [
        		"nombre" => $request->name,
        		"apellido" => $request->apellido,
        		"cedula" => $request->cedula,
        		"correo" => $request->email,
        		"direccion" => $request->direccion,
        		"telefono" => $request->telefono
        	];
        	$user->person()->update($data);
        	
        	return response()->json(['message'=>'Usuario actualizado']);
        }catch(\Exception $exception){
        	$user->delete();
        	return response()->json(['message'=>$exception->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Facilitator  $facilitator
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message'=>'Usuario borrado.']);
    }
    
}
