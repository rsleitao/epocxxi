<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Concelho;
use App\Models\Distrito;
use Illuminate\Http\JsonResponse;

class GeoController extends Controller
{
    public function concelhos(Distrito $distrito): JsonResponse
    {
        $concelhos = $distrito->concelhos()->orderBy('nome')->get(['id_concelho', 'nome']);

        return response()->json($concelhos);
    }

    public function freguesias(Concelho $concelho): JsonResponse
    {
        $freguesias = $concelho->freguesias()->orderBy('nome')->get(['id_freguesia', 'nome']);

        return response()->json($freguesias);
    }
}
