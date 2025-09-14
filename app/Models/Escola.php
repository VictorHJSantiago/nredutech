<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escola extends Model
{
    use HasFactory;

    protected $table = 'escolas';
    protected $primaryKey = 'id_escola';

    protected $fillable = [
        'nome',
        'endereco',
        'id_municipio',
        'id_diretor_responsavel',
        'nivel_ensino',
        'tipo', 
    ];

     public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio', 'id_municipio');
    }

    public function diretor()
    {
        return $this->belongsTo(Usuario::class, 'id_diretor_responsavel', 'id_usuario');
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_escola', 'id_escola');
    }

    public function diretoresAtivos()
    {
        return $this->hasMany(Usuario::class, 'id_escola', 'id_escola')
                    ->where('tipo_usuario', 'diretor')
                    ->where('status_aprovacao', 'ativo');
    }

    public function turmas()
    {
        return $this->hasMany(Turma::class, 'id_escola', 'id_escola');
    }
}