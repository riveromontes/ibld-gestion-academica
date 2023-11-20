<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ModuleCreateRequest;
use App\Http\Requests\ModuleUpdateRequest;
use App\Models\Module;

class ModuleController extends Controller
{
    public function index()
    {
    	$modules = Module::all();
    	return response()->json(['message'=>'Listado de modulos','data'=>$modules],200);
    }


    public function show(Module $module)
    {
        return response()->json(['message'=>'Información de modulo','data'=>$module],200);

    }

    public function store(ModuleCreateRequest $request)
    {
        $module = Module::create($request->all());
        return response()->json(['message'=>'Modulo agregado'],200);
        
    }

    public function update(ModuleUpdateRequest $request, Module $module)
    {
        $module->update($request->all());
        return response()->json(['message'=>'Modulo actualizada.','data'=>$module],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Facilitator  $facilitator
     * @return \Illuminate\Http\Response
     */
    public function destroy(Module $module)
    {
        $module->delete();
        return response()->json(['message'=>'Modulo eliminado.'],200);
    }
}
