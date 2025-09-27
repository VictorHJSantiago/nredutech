<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveUserPreferenceRequest;
use App\Http\Resources\UserPreferenceResource;
use App\Models\Usuario;
use App\Models\UsuarioPreferencia;
use Illuminate\Http\JsonResponse;

class UserPreferenceController extends Controller
{
    public function show(Usuario $usuario): UserPreferenceResource
    {
        $preferencias = UsuarioPreferencia::firstOrNew(['id_usuario' => $usuario->id_usuario]);

        return new UserPreferenceResource($preferencias);
    }

    public function update(SaveUserPreferenceRequest $request, Usuario $usuario): UserPreferenceResource
    {
    
        $preferencias = UsuarioPreferencia::updateOrCreate(
            ['id_usuario' => $usuario->id_usuario], 
            $request->validated() 
        );

        return new UserPreferenceResource($preferencias);
    }

    public function destroy(Usuario $usuario): JsonResponse
    {
        $usuario->preferencias()->delete();

        return response()->json(null, 204);
    }
}