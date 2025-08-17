<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Http\Resources\UsuarioResource;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UsuarioController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Usuario::query()->with(['escola', 'preferencias']);

        $query->when($request->query('status'), function ($q, $status) {
            return $q->where('status_aprovacao', $status);
        });

        $query->when($request->query('search'), function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('nome_completo', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
            });
        });
        
        $usuarios = $query->paginate($request->query('per_page', 15));

        return UsuarioResource::collection($usuarios);
    }

    public function store(StoreUsuarioRequest $request): UsuarioResource
    {
        $validatedData = $request->validated();
        $validatedData['data_registro'] = now();
        
        $usuario = Usuario::create($validatedData);

        return new UsuarioResource($usuario->load('escola'));
    }

    public function show(Usuario $usuario): UsuarioResource
    {
        $usuario->load(['escola', 'preferencias', 'notificacoes']);
        
        return new UsuarioResource($usuario);
    }

    public function update(UpdateUsuarioRequest $request, Usuario $usuario): UsuarioResource
    {
        $usuario->update($request->validated());

        return new UsuarioResource($usuario->fresh()->load('escola'));
    }

    public function destroy(Usuario $usuario): JsonResponse
    {
        $usuario->delete();

        return response()->json(null, 204);
    }
}