<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LevelCreateRequest;
use App\Http\Requests\LevelUpdateRequest;
use App\Models\Level;

class LevelController extends Controller
{
    public function index()
    {
    	$levels = Level::all();
    	return response()->json(['message'=>'Listado de niveles','data'=>$levels],200);
    }


    public function show(Level $level)
    {
        return response()->json(['message'=>'Información de nivel','data'=>$level],200);

    }

    public function store(LevelCreateRequest $request)
    {
        $level = Level::create($request->all());
        return response()->json(['message'=>'Nivel agregado'],200);
        
    }

    public function update(LevelUpdateRequest $request, Level $level)
    {
        $level->update($request->all());
        return response()->json(['message'=>'Nivel actualizada.','data'=>$level],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Facilitator  $facilitator
     * @return \Illuminate\Http\Response
     */
    public function destroy(Level $level)
    {
        $level->delete();
        return response()->json(['message'=>'Nivel eliminado.'],200);
    }
}
