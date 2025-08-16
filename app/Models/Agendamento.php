<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $table = 'agendamentos';
    protected $primaryKey = 'id_agendamento';

    protected $fillable = [
        'data_hora_inicio',
        'data_hora_fim',
        'status',
        'id_recurso',
        'id_oferta',
    ];
    
    public function recurso()
    {
        return $this->belongsTo(RecursoDidatico::class, 'id_recurso', 'id_recurso');
    }

    public function oferta()
    {
        return $this->belongsTo(OfertaComponente::class, 'id_oferta', 'id_oferta');
    }
    
    public function notificacao()
    {
        return $this->hasOne(Notificacao::class, 'id_agendamento', 'id_agendamento');
    }
}