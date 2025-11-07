<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Http\Resources\CityResource;
use App\Models\Municipio;
use App\Models\Notificacao;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CityController extends Controller
{
    public function index(): View 
    {
        $municipios = Municipio::with('escolas')->paginate(15);
        return view('escolas.index', ['municipios' => $municipios]);
    }

    public function store(StoreCityRequest $request): RedirectResponse
    {
        $municipio = Municipio::create($request->validated());
        $actor = Auth::user();
        
        $administradores = Usuario::where('tipo_usuario', 'administrador')->where('status_aprovacao', 'ativo')->get();
        $recipients = collect([$actor])->merge($administradores)->unique('id_usuario');

        foreach ($recipients as $recipient) {
            Notificacao::create([
                'titulo' => 'Novo Município Cadastrado',
                'mensagem' => "O município '{$municipio->nome}' foi adicionado ao sistema por {$actor->nome_completo}.",
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $recipient->id_usuario,
            ]);
        }

        return redirect()->route('escolas.index')->with('success', 'Município adicionado com sucesso!');
    }

    public function show(Municipio $municipio): CityResource
    {
        $municipio->load('escolas');
        return new CityResource($municipio);
    }

    public function edit(Municipio $municipio): View
    {
        return view('cities.edit', compact('municipio'));
    }

    public function update(UpdateCityRequest $request, Municipio $municipio): RedirectResponse
    {
        $nomeAntigo = $municipio->nome;
        $municipio->update($request->validated());
        $nomeNovo = $municipio->nome;
        $actor = Auth::user();

        $administradores = Usuario::where('tipo_usuario', 'administrador')->where('status_aprovacao', 'ativo')->get();
        $recipients = collect([$actor])->merge($administradores)->unique('id_usuario');
        
        foreach ($recipients as $recipient) {
            Notificacao::create([
                'titulo' => 'Município Atualizado',
                'mensagem' => "O município '{$nomeAntigo}' foi atualizado para '{$nomeNovo}' por {$actor->nome_completo}.",
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $recipient->id_usuario,
            ]);
        }

        return redirect()->route('escolas.index')->with('success', 'Município atualizado com sucesso!');
    }

    public function destroy(Municipio $municipio): RedirectResponse
    {
        if ($municipio->escolas()->exists()) {
            return redirect()->route('escolas.index')->with('error', 'Não é possível excluir um município que possui escolas associadas.');
        }

        $nomeMunicipio = $municipio->nome;
        $municipio->delete();
        $actor = Auth::user();

        $administradores = Usuario::where('tipo_usuario', 'administrador')->where('status_aprovacao', 'ativo')->get();
        $recipients = collect([$actor])->merge($administradores)->unique('id_usuario');

        foreach ($recipients as $recipient) {
            Notificacao::create([
                'titulo' => 'Município Excluído',
                'mensagem' => "O município '{$nomeMunicipio}' foi excluído do sistema por {$actor->nome_completo}.",
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $recipient->id_usuario,
            ]);
        }

        return redirect()->route('escolas.index')->with('success', 'Município excluído com sucesso!');
    }
}