<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
    use HasFactory;

    protected $table = 'turmas';
    protected $primaryKey = 'id_turma';

    protected $fillable = [
        'serie',
        'turno',
        'ano_letivo',
        'nivel_escolaridade',
        'id_escola',
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'id_escola', 'id_escola');
    }

    public function ofertasComponentes()
    {
        return $this->hasMany(OfertaComponente::class, 'id_turma', 'id_turma');
    }
}