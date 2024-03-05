<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoUsuario extends Model
{
    use HasFactory;
    public $table = "tipo_usuarios";
    protected $fillable = ['codigo', 'nombre', 'estado'];

    public function permisos()
    {
        return $this->hasMany(PermisoUsuario::class, 'tipo_usuario');
    }
}
