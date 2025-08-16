<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    public function ofertas()
    {
        return $this->hasMany(OfertaComponente::class, 'id_componente', 'id_componente');
    }
}