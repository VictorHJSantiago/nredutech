<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioPreferencia extends Model
{
    use HasFactory;

    protected $table = 'usuario_preferencias';
    protected $primaryKey = 'id_usuario';
    public $incrementing = false;

    protected $fillable = [
        'id_usuario',
        'notif_email',
        'notif_popup',
        'tema',
        'tamanho_fonte',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}