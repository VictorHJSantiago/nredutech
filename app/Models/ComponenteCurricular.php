<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ComponenteCurricular extends Model
{
    use HasFactory;

    protected $table = 'componentes_curriculares';
    protected $primaryKey = 'id_componente';

    protected $fillable = [
        'nome',
        'descricao',
        'carga_horaria',
        'status',
        'id_usuario_criador', 
        'id_escola',
    ];

    public function ofertas()
    {
        return $this->hasMany(OfertaComponente::class, 'id_componente', 'id_componente');
    }

    public function criador()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_criador', 'id_usuario');
    }

    public function escola(): BelongsTo
    {
        return $this->belongsTo(Escola::class, 'id_escola', 'id_escola');
    }
}