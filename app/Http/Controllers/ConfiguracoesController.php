<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\UsuarioPreferencia;
use Illuminate\Http\RedirectResponse as HttpRedirectResponse; 
use Illuminate\Pagination\LengthAwarePaginator;

class ConfiguracoesController extends Controller
{
    private $backupDisk = 'backups';  
    private $backupPath; 

    public function __construct()
    {
        $this->backupPath = config('backup.backup.name', 'laravel-backup'); 
    }

    public function index(Request $request): View 
    {
        $authUser = Auth::user();
        $usuario = Usuario::where('email', $authUser->email)->firstOrFail();

        $preferencias = UsuarioPreferencia::firstOrCreate(
            ['id_usuario' => $usuario->id_usuario]
        );

        $disk = Storage::disk($this->backupDisk);
        
        $files = $disk->exists($this->backupPath) ? $disk->files($this->backupPath) : [];

        $allBackups = collect($files)
            ->filter(fn ($file) => \Illuminate\Support\Str::endsWith($file, '.zip'))
            ->map(function ($file) use ($disk) {
                return [
                    'name' => basename($file),
                    'size_raw' => $disk->size($file),
                    'size' => $this->formatBytes($disk->size($file)),
                    'timestamp' => $disk->lastModified($file),
                ];
            })
            ->sortByDesc('timestamp')
            ->values();

        $perPage = 5;
        $currentPage = $request->get('page', 1);
        $paginatedBackups = new LengthAwarePaginator(
            $allBackups->forPage($currentPage, $perPage)->map(function ($backup) {
                $backup['date'] = Carbon::createFromTimestamp($backup['timestamp'])
                                        ->setTimezone('America/Sao_Paulo')
                                        ->format('d/m/Y H:i:s');
                return $backup;
            }),
            $allBackups->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('settings', [
            'usuario' => $usuario, 
            'preferencias' => $preferencias, 
            'backups' => $paginatedBackups
        ]);
    }

    public function updatePreferences(Request $request): HttpRedirectResponse
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

    private function findLatestBackupFile(): ?string
    {
        $disk = Storage::disk($this->backupDisk);
        $backupDirectory = $this->backupPath;
        
        $allBackups = $disk->exists($backupDirectory) ? $disk->files($backupDirectory) : [];

        if (empty($allBackups)) {
            return null;
        }

        $latestTimestamp = 0;
        $latestFile = null;

        foreach ($allBackups as $backupPath) {
            if (\Illuminate\Support\Str::endsWith($backupPath, '.zip')) {
                $timestamp = $disk->lastModified($backupPath);
                
                if ($timestamp > $latestTimestamp) {
                    $latestTimestamp = $timestamp;
                    $latestFile = $backupPath;
                }
            }
        }
        return $latestFile;
    }

    public function initiateBackup(Request $request): HttpRedirectResponse
    {
        try {
            Artisan::call('backup:run', ['--only-db' => true]);
            $request->session()->forget('auth.password_confirmed_at');

            $latestBackup = $this->findLatestBackupFile();

            if (!$latestBackup) {
                 Log::error('Backup Artisan executado, mas nenhum arquivo foi encontrado.');
                 return redirect()->route('settings')->with('error', 'Nenhum arquivo de backup encontrado após a execução.');
            }

            $timestamp = Storage::disk($this->backupDisk)->lastModified($latestBackup);
            $backupDate = Carbon::createFromTimestamp($timestamp)->setTimezone('America/Sao_Paulo')->format('d/m/Y \à\s H:i');

            return redirect()->route('settings')
                ->with('success', "BACKUP DO DIA {$backupDate} REALIZADO COM SUCESSO!")
                ->with('download_backup_url', route('settings.backup.download.latest'));

        } catch (\Exception $e) {
            $message = 'Ocorreu um erro ao tentar realizar o backup.';
            Log::error($message . ' Erro: ' . $e->getMessage());
            return redirect()->route('settings')->with('error', $message);
        }
    }

    public function downloadLatestBackup(): StreamedResponse|HttpRedirectResponse
    {
        $latestBackupPath = $this->findLatestBackupFile();

        if (!$latestBackupPath) {
             abort(404, 'Nenhum arquivo de backup recente encontrado para download.');
        }

        return Storage::disk($this->backupDisk)->download($latestBackupPath);
    }

    public function downloadBackup(Request $request, $filename)
    {
        $request->session()->forget('auth.password_confirmed_at');
        
        $disk = Storage::disk($this->backupDisk);
        $fullPath = $this->backupPath . '/' . $filename;

        if (!$disk->exists($fullPath)) {
            return redirect()->route('settings')->with('error', 'Arquivo de backup não encontrado.');
        }
        
        $timestamp = $disk->lastModified($fullPath);
        $backupDate = Carbon::createFromTimestamp($timestamp)->setTimezone('America/Sao_Paulo')->format('d/m/Y \à\s H:i');
        
        $message = "BACKUP DO DIA ({$backupDate}) REALIZADO COM SUCESSO!";

        return redirect()->route('settings')
            ->with('success', $message)
            ->with('download_backup_url', route('settings.backup.download-file', ['filename' => $filename]));
    }

    public function downloadFile($filename)
    {
        $disk = Storage::disk($this->backupDisk);
        $fullPath = $this->backupPath . '/' . $filename;

        if (!$disk->exists($fullPath)) {
            abort(404, 'Arquivo de backup não encontrado.');
        }

        return $disk->download($fullPath);
    }

    public function uploadAndRestore(Request $request)
    {
        $request->session()->forget('auth.password_confirmed_at');

        $request->validate([
            'backup_file' => 'required|file|mimetypes:text/plain,application/sql,application/x-sql,text/sql',
        ], [
            'backup_file.required' => 'Você precisa enviar um arquivo.',
            'backup_file.mimetypes' => 'O arquivo de restauração deve ser um arquivo .sql válido (texto plano). Se você tiver certeza de que é um .sql, o tipo de arquivo pode estar sendo reportado incorretamente.',
        ]);

        $file = $request->file('backup_file');
        
        $dbDriver = config('database.default', 'mysql');
        $dbConfig = config('database.connections.' . $dbDriver);
        
        $dbHost = $dbConfig['host'];
        $dbName = $dbConfig['database'];
        $dbUser = $dbConfig['username'];
        $dbPass = $dbConfig['password'] ?? ''; 
        $filePath = $file->getRealPath();

        $command = sprintf(
            'mysql -h %s -u %s -p"%s" %s < %s',
            escapeshellarg($dbHost),
            escapeshellarg($dbUser),
            $dbPass, 
            escapeshellarg($dbName),
            escapeshellarg($filePath)
        );

        if ($dbDriver === 'sqlite') {
            Log::error('Falha na restauração: A restauração por upload de SQL só está configurada para MySQL/MariaDB, mas o driver padrão é SQLite.');
            return Redirect::route('settings')->with('error', 'Falha na restauração: O sistema está configurado para SQLite, mas a restauração tentou usar comandos do MySQL.');
        }

        $process = Process::fromShellCommandline($command);

        try {
            $process->mustRun();
            Log::info('Banco de dados restaurado com sucesso a partir do arquivo: ' . $file->getClientOriginalName());
            return Redirect::route('settings')->with('success', 'Banco de dados restaurado com sucesso.');

        } catch (ProcessFailedException $exception) {
            Log::error('Falha na restauração do banco de dados (ProcessFailedException): ' . $exception->getMessage());
            return Redirect::route('settings')->with('error', 'Falha na restauração: ' . $exception->getMessage());
        } catch (\Exception $e) {
             Log::error('Falha na restauração (Exception Geral): ' . $e->getMessage());
            return Redirect::route('settings')->with('error', 'Falha na restauração: ' . $e->getMessage());
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(log($bytes, 1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}