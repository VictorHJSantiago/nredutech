<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveUsuarioPreferenciaRequest;
use App\Http\Resources\UsuarioPreferenciaResource;
use App\Models\Usuario;
use App\Models\UsuarioPreferencia;
use Illuminate\Http\JsonResponse;

class UsuarioPreferenciaController extends Controller
{
    public function show(Usuario $usuario): UsuarioPreferenciaResource
    {
        $preferencias = UsuarioPreferencia::firstOrNew(['id_usuario' => $usuario->id_usuario]);

        return new UsuarioPreferenciaResource($preferencias);
    }

    public function update(SaveUsuarioPreferenciaRequest $request, Usuario $usuario): UsuarioPreferenciaResource
    {
    
        $preferencias = UsuarioPreferencia::updateOrCreate(
            ['id_usuario' => $usuario->id_usuario], 
            $request->validated() 
        );

        return new UsuarioPreferenciaResource($preferencias);
    }

    public function destroy(Usuario $usuario): JsonResponse
    {
        $usuario->preferencias()->delete();

        return response()->json(null, 204);
    }
}