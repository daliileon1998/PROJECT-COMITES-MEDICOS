<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestionComite extends Model
{
    use HasFactory;
    public $table = "gestion_comites";
    protected $fillable = ['fecha_comite', 'comite_id', 'adjunto','acta', 'descripcion', 'estado'];

    public function comite()
    {
        return $this->belongsTo(Comite::class, 'comite_id');
    }

}
