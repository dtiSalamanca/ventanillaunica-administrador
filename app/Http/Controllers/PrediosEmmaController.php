<?php

namespace App\Http\Controllers;

use App\Models\Predio;

use Illuminate\Http\Request;

class PrediosEmmaController extends Controller
{
    //
    public function validar($id)
    {
        $predio = Predio::query()->where('clave_predio', $id)->first();
        $predio->estatus_predio = 1;
        $predio->save();
        return response()->json(['message' => 'Predio validado correctamente']);
    }
}
