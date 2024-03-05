<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoUsuario extends Model
{
    use HasFactory;
    public $table = "permiso_usuarios";
    protected $fillable = ['agregar', 'editar', 'eliminar', 'visualizar', 'tipo_usuario','modulo_id'];

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }

    public function tipoUsuario()
    {
        return $this->belongsTo(TipoUsuario::class, 'tipo_usuario');
    }
}
