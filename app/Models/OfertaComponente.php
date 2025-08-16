<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfertaComponente extends Model
{
    use HasFactory;

    protected $table = 'oferta_componentes';
    protected $primaryKey = 'id_oferta';

    protected $fillable = [
        'id_turma',
        'id_professor',
        'id_componente',
    ];

    public function turma()
    {
        return $this->belongsTo(Turma::class, 'id_turma', 'id_turma');
    }

    public function professor()
    {
        return $this->belongsTo(Usuario::class, 'id_professor', 'id_usuario');
    }

    public function componente()
    {
        return $this->belongsTo(ComponenteCurricular::class, 'id_componente', 'id_componente');
    }
    
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'id_oferta', 'id_oferta');
    }
}