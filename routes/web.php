<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User; 
use Illuminate\Support\Str;


// --- ROTAS PÚBLICAS (PARA VISITANTES) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => view('auth.login'))->name('login');

    Route::post('/login', function (Request $request) {
        $request->validate(['email' => ['required', 'email']]);
        $user = User::firstOrCreate(
            ['email' => $request->email],
            ['name' => 'Usuário ' . Str::before($request->email, '@'), 'password' => Hash::make(Str::random(16))]
        );
        Auth::login($user, true);
        $request->session()->regenerate();
        return redirect()->intended('/');
    });

    Route::get('/register', fn() => view('auth.register'))->name('register');

    Route::post('/register', function (Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user = User::create($request->only('name', 'email', 'password'));
        Auth::login($user);
        return redirect()->route('index');
    });
});


// --- ROTAS PROTEGIDAS (PARA USUÁRIOS LOGADOS) ---
Route::middleware(['auth'])->group(function () {

    Route::get('/', fn() => view('index'))->name('index');

    Route::get('/dashboard', fn() => redirect()->route('index'))->name('dashboard');

    Route::get('/disciplines', function () {
        $disciplines = [
            ['id' => 1, 'name' => 'Cálculo I', 'code' => 'CSI101'],
            ['id' => 2, 'name' => 'Programação Orientada a Objetos', 'code' => 'CSI202'],
            ['id' => 3, 'name' => 'Estrutura de Dados', 'code' => 'CSI303'],
        ];
        return view('disciplines.discipline-list', ['disciplines' => $disciplines]);
    })->name('discipline-list');

    Route::get('/professors', function () {
        $professors = [
            ['id' => 1, 'name' => 'Dr. Alan Turing', 'department' => 'Ciência da Computação'],
            ['id' => 2, 'name' => 'Dra. Ada Lovelace', 'department' => 'Matemática Aplicada'],
        ];
        return view('professors.professor-list', ['professors' => $professors]);
    })->name('professor-list');

    Route::get('/resources', function () {
        $resources = [
            ['id' => 1, 'title' => 'Livro: Código Limpo', 'type' => 'PDF'],
            ['id' => 2, 'title' => 'Videoaula: Integrais Duplas', 'type' => 'Vídeo'],
        ];
        return view('resources.resource-list', ['resources' => $resources]);
    })->name('resource-list');

    Route::get('/users', function () {
        $users = User::orderBy('name')->get(); 
        return view('users.user-list', ['users' => $users]);
    })->name('user-list');

    Route::get('/reports', fn() => view('reports'))->name('reports');
    
    Route::get('/laboratories', fn() => view('laboratories.laboratory-list'))->name('laboratory-list');
    
    Route::get('/settings', fn() => view('settings'))->name('settings');

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});