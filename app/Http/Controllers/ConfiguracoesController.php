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
use Symfony\Component\HttpFoundation\RedirectResponse;
class ConfiguracoesController extends Controller
{
    private $backupDisk = 'local';  

    private $backupPath; 

    public function __construct()
    {
        $this->backupPath = config('backup.backup.name'); //
    }

    public function index()
    {
        $disk = Storage::disk($this->backupDisk);
        // $files = $disk->files(); 
        $files = $disk->files($this->backupPath); 

        $backups = collect($files)
            ->filter(function ($file) {
                return \Illuminate\Support\Str::endsWith($file, '.zip');
            })
            ->reverse() 
            ->map(function ($file) use ($disk) {
                return [
                    'name' => basename($file),
                    'size_raw' => $disk->size($file),
                    'size' => $this->formatBytes($disk->size($file)),
                    'date' => Carbon::createFromTimestamp($disk->lastModified($file))->format('d/m/Y H:i:s'),
                ];
            })
            ->toArray();

        return view('settings', compact('backups'));
    }

    /**
     * @return string|null 
     */
    private function findLatestBackupFile(): ?string
    {
        $disk = Storage::disk('local'); 

        $backupDirectory = config('backup.backup.name');

        $allBackups = $disk->files($backupDirectory);

        if (empty($allBackups)) {
            return null;
        }

        $latestTimestamp = 0;
        $latestFile = null;

        foreach ($allBackups as $backupPath) {
            $timestamp = $disk->lastModified($backupPath);
            
            if ($timestamp > $latestTimestamp) {
                $latestTimestamp = $timestamp;
                $latestFile = $backupPath;
            }
        }

        return $latestFile;
    }

    public function runBackup(): StreamedResponse|RedirectResponse
    {
        try {
            Artisan::call('backup:run', ['--only-db' => true]);

            $latestBackupPath = $this->findLatestBackupFile();

            if (!$latestBackupPath) {
                 Log::error('Backup Artisan executado, mas findLatestBackupFile() não encontrou nenhum arquivo.');
                 return redirect()->route('settings')->with('error', 'Nenhum arquivo de backup encontrado após a execução.');
            }

            Log::info('Backup criado no servidor (' . $latestBackupPath . ') e download iniciado pelo usuário.');
            return Storage::disk('local')->download($latestBackupPath);


        } catch (\Exception $e) {
            $message = 'Ocorreu um erro ao tentar realizar e baixar o backup.';
            Log::error($message . ' Erro: ' . $e->getMessage());
            return redirect()->route('settings')->with('error', $message);
        }
    }

    public function downloadBackup($filename)
    {
        $disk = Storage::disk($this->backupDisk);
        // $path = $disk->path($filename); 

        $fullPath = $this->backupPath . '/' . $filename;

        if (!$disk->exists($fullPath)) {
            abort(404, 'Arquivo de backup não encontrado.');
        }

        // return response()->download($path);
        return $disk->download($fullPath); 
    }

    public function uploadAndRestore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql',
        ], [
            'backup_file.required' => 'Você precisa enviar um arquivo.',
            'backup_file.mimes' => 'O arquivo de restauração deve ser um arquivo .sql válido. (Extraia-o do .zip de backup primeiro)',
        ]);

        $file = $request->file('backup_file');
        
        $dbConfig = config('database.connections.mysql');
        $dbHost = $dbConfig['host'];
        $dbName = $dbConfig['database'];
        $dbUser = $dbConfig['username'];
        $dbPass = $dbConfig['password'];
        $filePath = $file->getRealPath();

        $command = sprintf(
            'mysql -h %s -u %s -p"%s" %s < %s',
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
            Log::error('Falha na restauração do banco de dados: ' . $exception->getMessage());
            return Redirect::route('settings')->with('error', 'Falha na restauração: ' . $exception->getMessage());
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}