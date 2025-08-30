<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\UsuarioPreferencia;
use App\Models\Municipio;
use App\Models\Escola;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan; // Para a funcionalidade de backup
use Illuminate\Support\Facades\Log; 

class ConfiguracoesController extends Controller
{
    public function index(): View
    {
        $authUser = Auth::user();
        $usuario = Usuario::where('email', $authUser->email)->firstOrFail();

        $preferencias = UsuarioPreferencia::firstOrCreate(
            ['id_usuario' => $usuario->id_usuario]
        );
        
        $municipios = Municipio::orderBy('nome')->get();
        $escolas = Escola::orderBy('nome')->get();
        $usuarios = Usuario::orderBy('nome_completo')->get();

        return view('settings', compact('usuario', 'preferencias', 'municipios', 'escolas', 'usuarios'));
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        $authUser = Auth::user();
        $usuario = Usuario::where('email', $authUser->email)->firstOrFail();

        $validatedData = $request->validate([
            'notif_email' => 'nullable|boolean',
            'notif_popup' => 'nullable|boolean',
            'tema' => 'required|in:claro,escuro',
            'tamanho_fonte' => 'required|in:padrao,medio,grande',
        ]);

        UsuarioPreferencia::updateOrCreate(
            ['id_usuario' => $usuario->id_usuario],
            [
                'notif_email' => $request->has('notif_email'),
                'notif_popup' => $request->has('notif_popup'),
                'tema' => $validatedData['tema'],
                'tamanho_fonte' => $validatedData['tamanho_fonte'],
            ]
        );

        return redirect()->route('settings')->with('success', 'Preferências de notificação e tema atualizadas com sucesso!');
    }
    public function runBackup(): RedirectResponse
    {
        try {
            Artisan::call('backup:run', ['--only-db' => true]);
            $message = 'Backup do banco de dados realizado com sucesso!';
            Log::info($message);
            return redirect()->route('settings')->with('success', $message);
        } catch (\Exception $e) {
            $message = 'Ocorreu um erro ao tentar realizar o backup.';
            Log::error($message . ' Erro: ' . $e->getMessage());
            return redirect()->route('settings')->with('error', $message);
        }
    }
}