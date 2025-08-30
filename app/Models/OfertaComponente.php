<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfertaComponente extends Model
{
    use HasFactory;

    protected $table = 'oferta_componentes';
    protected $primaryKey = 'id_oferta';

    protected $fillable = [
        'id_turma',
        'id_componente',
        'id_professor',
    ];

    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class, 'id_turma', 'id_turma');
    }

    public function componenteCurricular(): BelongsTo
    {
        return $this->belongsTo(ComponenteCurricular::class, 'id_componente', 'id_componente');
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_professor', 'id_usuario');
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'id_oferta', 'id_oferta');
    }
}
