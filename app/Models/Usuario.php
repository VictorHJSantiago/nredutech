<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

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
    ];
    
    public function escola()
    {
        return $this->belongsTo(Escola::class, 'id_escola', 'id_escola');
    }

    public function ofertasComponentes()
    {
        return $this->hasMany(OfertaComponente::class, 'id_professor', 'id_usuario');
    }
    
    public function escolaResponsavel()
    {
        return $this->hasOne(Escola::class, 'id_diretor_responsavel', 'id_usuario');
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