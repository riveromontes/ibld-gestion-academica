<?php

namespace App\Http\Controllers;

use App\Models\Facilitator;
use Illuminate\Http\Request;
use App\Http\Requests\FacilitatorCreateRequest;
use App\Http\Requests\FacilitatorUpdateRequest;


class FacilitatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $facilitators = Facilitator::all();
        return response()->json(['message'=>'Listado de todos los facilitadores','data'=>$facilitators],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FacilitatorCreateRequest $request)
    {
        $facilitator = Facilitator::create($request->all());
        return response()->json(['message'=>'Facilitador agregado'],200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Facilitator  $facilitator
     * @return \Illuminate\Http\Response
     */
    public function show(Facilitator $facilitator)
    {
        return response()->json(['message'=>'Información del facilitador','data'=>$facilitator],200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Facilitator  $facilitator
     * @return \Illuminate\Http\Response
     */
    public function update(FacilitatorUpdateRequest $request, Facilitator $facilitator)
    {
        $facilitator->update($request->all());
        return response()->json(['message'=>'Facilitador actualizado.','data'=>$facilitator],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Facilitator  $facilitator
     * @return \Illuminate\Http\Response
     */
    public function destroy(Facilitator $facilitator)
    {
        $facilitator->delete();
        return response()->json(['message'=>'Facilitator eliminado.'],200);
    }
}
