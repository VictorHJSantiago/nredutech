<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecursoDidatico extends Model
{
    use HasFactory;

    protected $table = 'recursos_didaticos';
    protected $primaryKey = 'id_recurso';

    protected $fillable = [
        'nome',
        'tipo', 
        'marca',
        'numero_serie',
        'quantidade',
        'observacoes',
        'data_aquisicao', 
        'status',
    ];

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'id_recurso', 'id_recurso');
    }
}
