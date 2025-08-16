<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    protected $table = 'municipios';
    protected $primaryKey = 'id_municipio';

    protected $fillable = [
        'nome',
        'tipo',
    ];

    public function escolas()
    {
        return $this->hasMany(Escola::class, 'id_municipio', 'id_municipio');
    }
}