<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compromiso extends Model
{
    use HasFactory;
    public $table = "compromisos";
    protected $fillable = ['fecha_inicio', 'fecha_fin', 'gestion_comite_id', 'usuario_id', 'adjunto', 'descripcion', 'estado'];

    public function gestionComite()
    {
        return $this->belongsTo(GestionComite::class, 'gestion_comite_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

}
