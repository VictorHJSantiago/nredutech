<?php

namespace App\Console\Commands;

use App\Models\UsuarioPreferencia;
use App\Models\Usuario;
use App\Models\Notificacao;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Throwable;

class RunScheduledBackup extends Command
{
    protected $signature = 'backup:run-scheduled';
    protected $description = 'Executa o backup agendado (diário ou semanal) com base na configuração do administrador.';

    public function handle()
    {
        Log::info('[Backup Agendado] Iniciando verificação.');
        $this->info('[Backup Agendado] Iniciando verificação.');

        // Tenta buscar a configuração de *qualquer* administrador ativo primeiro.
        // Se houver múltiplos admins com configurações diferentes, pegará a do primeiro encontrado.
        $adminPreference = UsuarioPreferencia::whereHas('usuario', function ($query) {
            $query->where('tipo_usuario', 'administrador')->where('status_aprovacao', 'ativo');
        })->orderBy('id_usuario')->first();

        if (!$adminPreference) {
             Log::warning('[Backup Agendado] Nenhuma configuração de preferência encontrada para administradores ativos. Verificando padrão.');
             // Se nenhum admin ativo tem preferência salva, busca o primeiro admin qualquer para compatibilidade.
             $adminUser = Usuario::where('tipo_usuario', 'administrador')->orderBy('id_usuario')->first();
             if ($adminUser) {
                 $adminPreference = UsuarioPreferencia::find($adminUser->id_usuario);
             }
        }

        // Define um padrão seguro caso nenhuma configuração seja encontrada
        $frequency = $adminPreference->backup_frequency ?? 'daily';
        if (!in_array($frequency, ['daily', 'weekly'])) {
             Log::warning("[Backup Agendado] Frequência inválida ('{$frequency}') encontrada ou nenhuma configuração definida. Usando 'daily' como padrão.");
             $frequency = 'daily';
        }

        Log::info("[Backup Agendado] Frequência configurada: {$frequency}.");
        $this->info("[Backup Agendado] Frequência configurada: {$frequency}.");

        $runBackup = false;
        $today = Carbon::now(config('app.timezone', 'America/Sao_Paulo'));

        if ($frequency === 'daily') {
            $runBackup = true;
            Log::info("[Backup Agendado] Agendamento diário. Backup será executado.");
            $this->info("[Backup Agendado] Agendamento diário. Backup será executado.");
        } elseif ($frequency === 'weekly') {
            // Verifica se hoje é o dia configurado para semanal (Domingo por padrão)
            if ($today->isSunday()) { // Pode mudar para ->isMonday(), ->isTuesday(), etc. se necessário
                $runBackup = true;
                Log::info("[Backup Agendado] Agendamento semanal. Hoje é Domingo. Backup será executado.");
                $this->info("[Backup Agendado] Agendamento semanal. Hoje é Domingo. Backup será executado.");
            } else {
                Log::info("[Backup Agendado] Agendamento semanal, mas hoje não é o dia configurado (Domingo). Backup não será executado.");
                $this->info("[Backup Agendado] Agendamento semanal, mas hoje não é o dia configurado (Domingo). Backup não será executado.");
            }
        }

        if ($runBackup) {
            try {
                Log::info('[Backup Agendado] Executando Artisan::call(\'backup:run\')...');
                $this->info('[Backup Agendado] Executando Artisan::call(\'backup:run\')...');

                // Executa o backup (DB + Arquivos, conforme config/backup.php)
                // O '--only-db' e '--only-files' são false por padrão, mas explicitamos para clareza
                Artisan::call('backup:run', ['--only-db' => false, '--only-files' => false]);

                $output = Artisan::output();
                Log::info('[Backup Agendado] Comando backup:run executado com sucesso. Output: ' . trim($output));
                $this->info('[Backup Agendado] Backup automático concluído com sucesso.');
                $this->notifyAdmins('Sucesso no Backup Automático', 'O backup automático agendado foi concluído com sucesso.');
                Log::info('[Backup Agendado] Notificações de sucesso enviadas.');

            } catch (Throwable $e) {
                Log::error('[Backup Agendado] Falha na execução do backup: ' . $e->getMessage(), [
                    'exception' => $e->getFile() . ':' . $e->getLine(),
                    'trace' => Str::limit($e->getTraceAsString(), 1000) // Limita o tamanho do trace no log
                ]);
                $this->error('[Backup Agendado] Falha no backup automático: ' . $e->getMessage());

                $this->notifyAdmins('Falha no Backup Automático', 'Ocorreu um erro durante a execução do backup automático agendado. Verifique os logs do sistema. Erro: '.$e->getMessage());
                Log::info('[Backup Agendado] Notificações de falha enviadas.');
                return Command::FAILURE; // Indica falha na execução do comando
            }
        } else {
             Log::info('[Backup Agendado] Verificação concluída. Backup não necessário hoje.');
             $this->info('[Backup Agendado] Verificação concluída. Backup não necessário hoje.');
        }

        return Command::SUCCESS; // Indica sucesso na execução do comando (mesmo que o backup não tenha rodado)
    }

    private function notifyAdmins(string $title, string $message): void
    {
         Log::info("[Backup Agendado] Tentando notificar administradores: '{$title}'");
         try {
             $administradores = Usuario::where('tipo_usuario', 'administrador')
                                       ->where('status_aprovacao', 'ativo')
                                       ->with('preferencias') // Carrega as preferências para checar email
                                       ->get();

             if ($administradores->isEmpty()) {
                 Log::warning('[Backup Agendado] Nenhum administrador ativo encontrado para notificar.');
                 return;
             }

             $backupDate = Carbon::now(config('app.timezone', 'America/Sao_Paulo'))->format('d/m/Y H:i');
             $fullMessage = $message . " (Data da execução: {$backupDate})";

             foreach ($administradores as $admin) {
                 // Notificação interna do sistema
                 Notificacao::create([
                     'titulo' => $title,
                     'mensagem' => $fullMessage,
                     'data_envio' => now(),
                     'status_mensagem' => 'enviada',
                     'id_usuario' => $admin->id_usuario,
                 ]);

                 // Notificação por e-mail, se habilitada
                 if ($admin->preferencias && $admin->preferencias->notif_email && $admin->email) {
                    // Implementar envio de e-mail aqui se necessário, usando Mail::to()...
                    Log::info("[Backup Agendado] (Simulação) Enviando e-mail para {$admin->email}.");
                 }
             }
             Log::info("[Backup Agendado] Notificações enviadas para {$administradores->count()} administrador(es).");

         } catch (Throwable $e) {
              Log::error('[Backup Agendado] Falha ao enviar notificações para administradores: ' . $e->getMessage());
         }
    }
}