<?php

namespace App\Http\Controllers;

use App\Mail\BackupSuccessfulMail;
use App\Models\Notificacao;
use App\Models\Usuario;
use App\Models\UsuarioPreferencia;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse as HttpRedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class SettingsController extends Controller
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
            ->filter(fn($file) => \Illuminate\Support\Str::endsWith($file, '.zip'))
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
            'backups' => $paginatedBackups,
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
            $initiatingUser = Auth::user();

            Artisan::call('backup:run');
            $request->session()->forget('auth.password_confirmed_at');

            $latestBackup = $this->findLatestBackupFile();

            if (!$latestBackup) {
                Log::error('Backup Artisan executado, mas nenhum arquivo foi encontrado.');
                return redirect()->route('settings')->with('error', 'Nenhum arquivo de backup encontrado após a execução.');
            }

            $disk = Storage::disk($this->backupDisk);
            $backupDirectory = $this->backupPath;
            $allBackups = $disk->exists($backupDirectory) ? $disk->files($backupDirectory) : [];
            $zipBackups = array_filter($allBackups, fn($file) => \Illuminate\Support\Str::endsWith($file, '.zip'));

            $totalStorageUsed = 0;
            $oldestTimestamp = null;
            foreach ($zipBackups as $backup) {
                $totalStorageUsed += $disk->size($backup);
                $timestamp = $disk->lastModified($backup);
                if ($oldestTimestamp === null || $timestamp < $oldestTimestamp) {
                    $oldestTimestamp = $timestamp;
                }
            }

            $timestamp = $disk->lastModified($latestBackup);
            $backupDate = Carbon::createFromTimestamp($timestamp)->setTimezone('America/Sao_Paulo')->format('d/m/Y \à\s H:i');

            $backupDetails = [
                'appName' => config('app.name', 'Laravel'),
                'backupName' => config('backup.backup.name'),
                'diskName' => $this->backupDisk,
                'latestBackupSize' => $this->formatBytes($disk->size($latestBackup)),
                'backupCount' => count($zipBackups),
                'totalStorageUsed' => $this->formatBytes($totalStorageUsed),
                'latestBackupDate' => $backupDate,
                'oldestBackupDate' => $oldestTimestamp ? Carbon::createFromTimestamp($oldestTimestamp)->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i:s') : 'N/A',
            ];

            $administradores = Usuario::where('tipo_usuario', 'administrador')->where('status_aprovacao', 'ativo')->get();
            $initiatingUserName = $initiatingUser ? $initiatingUser->nome_completo : 'Sistema';

            foreach ($administradores as $admin) {
                if ($admin->email) {
                    Mail::to($admin->email)->send(new BackupSuccessfulMail($admin->nome_completo, $initiatingUserName, $backupDetails));
                }

                Notificacao::create([
                    'titulo' => 'Backup Realizado com Sucesso',
                    'mensagem' => "Um novo backup foi concluído em {$backupDate} por <strong>{$initiatingUserName}</strong>. Tamanho: {$backupDetails['latestBackupSize']}.",
                    'data_envio' => now(),
                    'status_mensagem' => 'enviada',
                    'id_usuario' => $admin->id_usuario,
                ]);
            }

            return redirect()->route('settings')
                ->with('success', "BACKUP DO DIA {$backupDate} REALIZADO COM SUCESSO!")
                ->with('download_backup_url', route('settings.backup.download.latest'));

        } catch (\Exception $e) {
            $message = 'Ocorreu um erro ao tentar realizar o backup.';
            Log::error($message . ' Erro: ' . $e->getMessage() . ' no arquivo ' . $e->getFile() . ' na linha ' . $e->getLine());
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

    public function showRestorePage(): View
    {
        return view('settings.restore');
    }

    public function uploadAndRestore(Request $request)
    {
        $request->session()->forget('auth.password_confirmed_at');

        $request->validate([
            'backup_file' => 'required|file|mimetypes:text/plain,application/sql,application/x-sql,text/sql,application/octet-stream',
        ], [
            'backup_file.required' => 'Você precisa enviar um arquivo.',
            'backup_file.mimetypes' => 'O arquivo de restauração deve ser um arquivo .sql válido.',
        ]);

        $file = $request->file('backup_file');

        $dbDriver = config('database.default', 'mysql');
        $dbConfig = config('database.connections.' . $dbDriver);

        if ($dbDriver === 'sqlite') {
        }

        if ($dbDriver === 'mysql' || $dbDriver === 'mariadb') {
            $dbHost = $dbConfig['host'];
            $dbName = $dbConfig['database'];
            $dbUser = $dbConfig['username'];
            $dbPass = $dbConfig['password'] ?? '';
            $filePath = $file->getRealPath();

            $commandTool = ($dbDriver === 'mariadb') ? 'mariadb' : 'mysql';

            $command = sprintf(
                '%s -h %s -u %s -p"%s" %s < %s',
                $commandTool,
                escapeshellarg($dbHost),
                escapeshellarg($dbUser),
                $dbPass,
                escapeshellarg($dbName),
                escapeshellarg($filePath)
            );

            $process = Process::fromShellCommandline($command);

            try {
                $process->mustRun();
                Log::info('Banco de dados restaurado com sucesso a partir do arquivo: ' . $file->getClientOriginalName());
                return Redirect::route('settings')->with('success', 'Banco de dados restaurado com sucesso.');

            } catch (ProcessFailedException $exception) {
                Log::error("Falha na restauração do banco de dados (ProcessFailedException) com o driver {$dbDriver}: " . $exception->getMessage());
                return Redirect::route('settings')->with('error', "Falha na restauração: Ocorreu um erro ao executar o comando de importação do banco de dados. Verifique os logs para mais detalhes.");
            } catch (\Exception $e) {
                Log::error("Falha na restauração (Exception Geral) com o driver {$dbDriver}: " . $e->getMessage());
                return Redirect::route('settings')->with('error', 'Falha na restauração: ' . $e->getMessage());
            }
        }

        return Redirect::route('settings')->with('error', "A restauração para o tipo de banco de dados '{$dbDriver}' não é suportada por este método.");
    }

    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(log($bytes, 1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}