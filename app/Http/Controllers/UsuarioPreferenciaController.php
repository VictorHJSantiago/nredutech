<?php

namespace App\Http\Controllers;

use App\Models\UsuarioPreferencia;
use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioPreferenciaController extends Controller
{
    public function store(Request $request)
    {
    
        // $id_usuario = auth()->id();
        
        $validatedData = $request->validate([
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'notif_email' => 'required|boolean',
            'notif_popup' => 'required|boolean',
            'tema' => 'required|in:claro,escuro',
            'tamanho_fonte' => 'required|in:padrao,medio,grande',
        ]);

        $preferencias = UsuarioPreferencia::updateOrCreate(
            ['id_usuario' => $validatedData['id_usuario']],
            $validatedData
        );
        
        return response()->json($preferencias, 200);
    }

    public function show(Usuario $usuario) 
    {
        $preferencias = UsuarioPreferencia::firstOrNew(['id_usuario' => $usuario->id_usuario]);
        return response()->json($preferencias);
    }

    public function update(Request $request, Usuario $usuario)
    {
        $validatedData = $request->validate([
            'notif_email' => 'sometimes|required|boolean',
            'notif_popup' => 'sometimes|required|boolean',
            'tema' => 'sometimes|required|in:claro,escuro',
            'tamanho_fonte' => 'sometimes|required|in:padrao,medio,grande',
        ]);
        
        $preferencias = UsuarioPreferencia::updateOrCreate(
            ['id_usuario' => $usuario->id_usuario],
            $validatedData
        );
        
        return response()->json($preferencias);
    }

    public function destroy(UsuarioPreferencia $usuarioPreferencia)
    {
        $usuarioPreferencia->delete();
        return response()->json(null, 204);
    }
}