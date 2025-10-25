<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecursoDidatico extends Model
{
    use HasFactory;

    /**
     *
     * @var string
     */
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
        'id_escola',
        'id_usuario_criador',
    ];


    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'id_recurso', 'id_recurso');
    }

    public function escola(): BelongsTo
    {
        return $this->belongsTo(Escola::class, 'id_escola', 'id_escola');
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_criador', 'id_usuario');
    }
}