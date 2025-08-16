<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    use HasFactory;

    protected $table = 'notificacoes';
    protected $primaryKey = 'id_notificacao';

    protected $fillable = [
        'titulo',
        'mensagem',
        'data_envio',
        'status_mensagem',
        'id_usuario',
        'id_agendamento',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function agendamento()
    {
        return $this->belongsTo(Agendamento::class, 'id_agendamento', 'id_agendamento');
    }
}