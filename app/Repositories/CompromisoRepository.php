<?php
namespace App\Repositories;

use App\Models\Compromiso;
use App\Models\TipoUsuario;

class CompromisoRepository
{
    public function getCompromisosForUser($user)
    {
        // Lógica para recuperar los compromisos según el usuario
        //return Compromiso::where('usuario_id', $userId)->get();
        $tipoUsuario = $user->tipo_usuario_id;
        $idUsuario = $user->id;
        //BUSCAR EL TIPO DE USUARIO SEGUN EL tipoUsuario
        $datosTipoUsuario = TipoUsuario::find($tipoUsuario);
        // VALIDAR SI EL USUARIO ES ADMINISTRADOR MOSTRAR TODOS LOS COMPROMISOS PENDIENTES
        if (strtoupper($datosTipoUsuario->nombre) == "ADMIN" || strtoupper($datosTipoUsuario->nombre) == "ADMINISTRADOR") {
            $compromisos = Compromiso::select(
                'compromisos.id as compromiso_id',
                'compromisos.fecha_inicio',
                'compromisos.fecha_fin',
                'compromisos.descripcion as compromiso_descripcion',
                'gestion_comites.fecha_comite',
                'gestion_comites.comite_id',
                'comites.nombre as nombre_comite',
                'users.name as nombre_usuario',
                'compromisos.estado as estado'
            )
                ->join('gestion_comites', 'compromisos.gestion_comite_id', '=', 'gestion_comites.id')
                ->join('comites', 'gestion_comites.comite_id', '=', 'comites.id')
                ->join('users', 'compromisos.usuario_id', '=', 'users.id')
                ->where('comites.estado', '=', 1)
                ->whereNotIn('compromisos.estado', [0, 3])
                ->orderBy('compromisos.fecha_inicio', 'asc')
                ->get();
        } else {
            // SI EL USUARIO ES EL RESPONSABLE DEL COMITE MOSTRAR TODOS LOS COMPROMISOS PENDIENTES
            $comites = $user->comites()->where('estado', 1)->get();
            if (count($comites) > 0) {
                $idsComites = $comites->pluck('id');
                $compromisos = Compromiso::select(
                    'compromisos.id as compromiso_id',
                    'compromisos.fecha_inicio',
                    'compromisos.fecha_fin',
                    'compromisos.descripcion as compromiso_descripcion',
                    'gestion_comites.fecha_comite',
                    'comites.nombre as nombre_comite',
                    'users.name as nombre_usuario',
                    'compromisos.estado as estado'
                )
                    ->whereIn('gestion_comite_id', function ($query) use ($idsComites) {
                        $query->select('id')->from('gestion_comites')->whereIn('comite_id', $idsComites);
                    })
                    ->join('gestion_comites', 'compromisos.gestion_comite_id', '=', 'gestion_comites.id')
                    ->join('comites', 'gestion_comites.comite_id', '=', 'comites.id')
                    ->join('users', 'compromisos.usuario_id', '=', 'users.id')
                    ->whereNotIn('compromisos.estado', [0, 3])
                    ->orderBy('compromisos.fecha_inicio', 'asc')
                    ->get();
            } else {
                // SINO TRAER LOS COMPROMISOS PENDIENTES DEL USUARIO LOGUEADO
                $compromisos = Compromiso::select(
                    'compromisos.id as compromiso_id',
                    'compromisos.fecha_inicio',
                    'compromisos.fecha_fin',
                    'compromisos.descripcion as compromiso_descripcion',
                    'gestion_comites.fecha_comite',
                    'gestion_comites.comite_id',
                    'comites.nombre as nombre_comite',
                    'users.name as nombre_usuario',
                    'compromisos.estado as estado'
                )
                    ->join('gestion_comites', 'compromisos.gestion_comite_id', '=', 'gestion_comites.id')
                    ->join('comites', 'gestion_comites.comite_id', '=', 'comites.id')
                    ->join('users', 'compromisos.usuario_id', '=', 'users.id')
                    ->where('comites.estado', '=', 1)
                    ->where('compromisos.usuario_id', '=', $idUsuario) // Agrega esta línea para filtrar por usuario logueado
                    ->whereNotIn('compromisos.estado', [0, 3])
                    ->orderBy('compromisos.fecha_inicio', 'asc')
                    ->get();
            }
        }

        return $compromisos;
    
    }

    // Puedes agregar más métodos según sea necesario para tu aplicación
}

