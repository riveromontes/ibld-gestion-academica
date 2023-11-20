<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ChairCreateRequest;
use App\Http\Requests\ChairUpdateRequest;
use App\Models\Chair;


class ChairController extends Controller
{
    public function index()
    {
        $chairs = Chair::all();
        return response()->json(['message' => 'Listado de catedras', 'data' => $chairs], 200);
    }


    public function show(Chair $chair)
    {
        return response()->json(['message' => 'Información de la catedra', 'data' => $chair], 200);
    }

    public function store(ChairCreateRequest $request)
    {
        Chair::create($request->all());
        return response()->json(['message' => 'Catedra agregada'], 200);
    }

    public function update(ChairUpdateRequest $request, Chair $chair)
    {
        $chair->update($request->all());
        return response()->json(['message' => 'Catedra actualizada.', 'data' => $chair], 200);
    }


    public function destroy(Chair $chair)
    {
        if (!$chair->delete()) {
            return response()->json(['message' => 'No existe la catedra'], 200);
        }
        return response()->json(['message' => 'Catedra eliminada.'], 200);
    }

    public function getChairsOpen()
    {
        //$chairs = Chair::where('inscripciones', '=',1)->get();
        $chairs = Chair::with('modules')->where('inscripciones', 1)->get();
        return response()->json(['message' => 'Listado de catedras', 'data' => $chairs], 200);
    }
}
