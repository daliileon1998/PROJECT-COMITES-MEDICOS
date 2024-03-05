<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comite extends Model
{
    use HasFactory;
    public $table = "comites";
    protected $fillable = ['codigo', 'nombre', 'usuario_id', 'descripcion', 'estado'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function gestionComites()
    {
        return $this->hasMany(GestionComite::class, 'comite_id');
    }
}
