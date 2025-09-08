<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'id_usuario';

    /**
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome_completo',
        'username',
        'email',
        'data_nascimento',
        'cpf',
        'rg',
        'rco_siape',
        'telefone',
        'formacao',
        'area_formacao',
        'data_registro',
        'status_aprovacao',
        'tipo_usuario',
        'id_escola',
        'password', 
    ];


    /**
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function escola()
    {
        return $this->belongsTo(Escola::class, 'id_escola', 'id_escola');
    }

    public function ofertasComponentes()
    {
        return $this->hasMany(OfertaComponente::class, 'id_professor', 'id_usuario');
    }
    
    public function escolaOndeEDiretor1()
    {
        return $this->hasOne(Escola::class, 'id_diretor_1', 'id_usuario');
    }

    public function escolaOndeEDiretor2()
    {
        return $this->hasOne(Escola::class, 'id_diretor_2', 'id_usuario');
    }

    public function notificacoes()
    {
        return $this->hasMany(Notificacao::class, 'id_usuario', 'id_usuario');
    }

    public function preferencias()
    {
        return $this->hasOne(UsuarioPreferencia::class, 'id_usuario', 'id_usuario');
    }
}