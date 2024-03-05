<?php

namespace App\Http\Controllers;

use App\Models\Comite;
use App\Models\Compromiso;
use App\Models\GestionComite;
use App\Models\PermisoUsuario;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Repositories\CompromisoRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GestionComiteController extends Controller
{

    public function verificarPermisos($user, $tipoUsuario)
    {
        if ($user && $user->tipo_usuario_id) {
            $permisos = PermisoUsuario::join('modulos', 'permiso_usuarios.modulo_id', '=', 'modulos.id')
                ->join('tipo_usuarios', 'permiso_usuarios.tipo_usuario', '=', 'tipo_usuarios.id')
                ->where('permiso_usuarios.tipo_usuario', $tipoUsuario)
                ->where('modulos.id', 2) // Módulo Gestion Comites
                ->select(
                    'permiso_usuarios.*',
                    'modulos.codigo as modulo_codigo',
                    'modulos.nombre as modulo_nombre',
                    'tipo_usuarios.codigo as tipo_usuario_codigo',
                    'tipo_usuarios.nombre as tipo_usuario_nombre'
                )
                ->get();
        } else {
            $permisos = [];
        }

        return $permisos;
    }

    public function index(CompromisoRepository $compromisoRepository)
    {
        $user = Auth::user();
        $tipoUsuario = $user->tipo_usuario_id;
        $permisos = $this->verificarPermisos($user, $tipoUsuario);
        $compromisos = $compromisoRepository->getCompromisosForUser($user);

        if (count($permisos) > 0) {
            if ($permisos[0]["visualizar"] == 0) {
                return Inertia::render('Accesos/sinPermisos', ['permisos' => $permisos[0], 'compromisos' => $compromisos]);
            } else {
                $gestionComites = GestionComite::select(
                    'gestion_comites.id',
                    'gestion_comites.fecha_comite as fecha',
                    'gestion_comites.adjunto',
                    'gestion_comites.descripcion',
                    'gestion_comites.adjunto',
                    'gestion_comites.acta',
                    'gestion_comites.estado',
                    'users.id as idUsuario',
                    'users.name',
                    'comites.id as idComite',
                    'comites.nombre'
                )
                    ->with('comite') // Solo cargamos la relación comite, no gestionComites
                    ->join('comites', 'comites.id', '=', 'gestion_comites.comite_id')
                    ->join('users', 'users.id', '=', 'comites.usuario_id')
                    ->get();

                foreach ($gestionComites as $gestionComite) {
                    $gestionComite->adjunto_url = asset('storage/' . $gestionComite->adjunto);
                    $gestionComite->acta_url = asset('storage/actas/' . $gestionComite->acta);
                    // Ahora cargamos los compromisos directamente desde la relación comite
                    $gestionComite->compromisos = Compromiso::join('users', 'compromisos.usuario_id', '=', 'users.id')
                        ->where('gestion_comite_id', $gestionComite->id)
                        ->select('compromisos.*', 'users.name as usuario_name')
                        ->get()
                        ->map(function ($compromiso, $index) {
                            $compromiso['indice'] = $index + 1;
                            return $compromiso;
                        })
                        ->toArray();
                }
                $usuarios = User::where('estado', 1)->get();

                $usuarios->map(function ($user) {
                    if($user->firma !=null){
                        $user->firma_url = asset('storage/' . $user->firma);
                    }else{
                        $user->firma_url ='';
                    }
                    return $user;
                });
                return Inertia::render('GestionComite/index', ['gestionComites' => $gestionComites, 'permisos' => $permisos[0], 'compromisosN' => $compromisos, 'usuarios'=> $usuarios]);
            }
        } else {
            return Inertia::render('Accesos/sinPermisos', ['permisos' => [], 'compromisos' => $compromisos]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CompromisoRepository $compromisoRepository)
    {
        $user = Auth::user();
        $tipoUsuario = $user->tipo_usuario_id;
        $compromisos = $compromisoRepository->getCompromisosForUser($user);
        $permisos = $this->verificarPermisos($user, $tipoUsuario);

        if (count($permisos) > 0) {
            if ($permisos[0]["visualizar"] == 0 || $permisos[0]["agregar"] == 0) {
                return Inertia::render('Accesos/sinPermisos', ['permisos' => $permisos[0], 'compromisos' => $compromisos]);
            } else {
                $comites = Comite::select('id', 'nombre as name')->get();
                $usuarios = User::where('estado', 1)->get();
                return Inertia::render('GestionComite/createGestionComite', ['comites' => $comites, 'usuarios' => $usuarios, 'compromisosN' => $compromisos]);
            }
        } else {
            return Inertia::render('Accesos/sinPermisos', ['permisos' => [], 'compromisos' => $compromisos]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules(), $this->validationMessages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $rutaArchivo='';

        if ($request->hasFile('adjunto')) {
            $archivo = $request->file('adjunto');
            $timestamp = now()->timestamp;
            $nombreA = "gestion_comite_" . $request->fecha_comite . "_" . $request->comite . "" . $timestamp;
            $extension = $archivo->getClientOriginalExtension();
            $rutaArchivo = $archivo->storeAs('comites', $nombreA . '.' . $extension, 'public');
        }


        if ($request->id > 0) {
            $gestionComite = GestionComite::find($request->id);
            if (isset($gestionComite)) {
                $gestionComite->fecha_comite = $request->fecha_comite;
                $gestionComite->comite_id = $request->comite_id;
                $gestionComite->estado = $request->estado;
                $gestionComite->descripcion = $request->descripcion;
                $gestionComite->adjunto = $rutaArchivo;

                if ($gestionComite->save()) {
                    $gestionComiteId = $gestionComite->id;
                    return response()->json(['mensaje' => 'Gestion de comite actualizado exitosamente', 'error' => '0', 'gestionComiteId' => $gestionComite->id]);
                } else {
                    return response()->json(['mensaje' => 'Error al guardar la gestion del comite', 'error' => '100', 'gestionComiteId' => 0]);
                }
            }
        } else {
            
            $gestionComite = GestionComite::create([
                'fecha_comite' => $request->fecha_comite,
                'comite_id' => $request->comite_id,
                'estado' => $request->estado,
                'descripcion' => $request->descripcion,
                'adjunto' => $rutaArchivo
            ]);

            $gestionComiteId = $gestionComite->id;
        }
        return response()->json(['mensaje' => 'Gestion de comite agregado exitosamente', 'error' => '0', 'gestionComiteId' => $gestionComite->id]);
        //return response()->json("Uploaded successfully!");
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(GestionComite $gestionComite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GestionComite $gestionComite, CompromisoRepository $compromisoRepository)
    {

        $user = Auth::user();
        $tipoUsuario = $user->tipo_usuario_id;
        $compromisos = $compromisoRepository->getCompromisosForUser($user);
        $permisos = $this->verificarPermisos($user, $tipoUsuario);

        if (count($permisos) > 0) {
            if ($permisos[0]["visualizar"] == 0 || $permisos[0]["agregar"] == 0) {
                return Inertia::render('Accesos/sinPermisos', ['permisos' => $permisos[0], 'compromisos' => $compromisos]);
            } else {
                $comites = Comite::select('id', 'nombre as name')->get();
                $usuarios = User::where('estado', 1)->get();
                $gestionComite->adjunto_url = asset('storage/' . $gestionComite->adjunto);
                return Inertia::render('GestionComite/EditGestionComite', ['comites' => $comites, 'usuarios' => $usuarios, 'compromisosN' => $compromisos, 'gestionComite' => $gestionComite]);
            }
        } else {
            return Inertia::render('Accesos/sinPermisos', ['permisos' => [], 'compromisos' => $compromisos]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->validationRules2(), $this->validationMessages());

        if ($validator->fails()) {
            return response()->json(['mensaje' => 'Error de validación', 'error' => '100', 'gestionComiteId' => 0]);
        }

        try {
            $gestionComite = GestionComite::findOrFail($id);

            // Lógica para el archivo adjunto
            if ($request->hasFile('adjunto')) {
                $archivo = $request->file('adjunto');


                $timestamp = now()->timestamp;
                $nombreA = "gestion_comite_" . $request->fecha_comite . "_" . $request->comite . "" . $timestamp;
                $extension = $archivo->getClientOriginalExtension();
                $rutaArchivo = $archivo->storeAs('comites', $nombreA . '.' . $extension, 'public');

                // Eliminar el archivo anterior si existe
                if ($gestionComite->adjunto) {
                    Storage::disk('public')->delete($gestionComite->adjunto);
                }

                $gestionComite->adjunto = $rutaArchivo;
            }

            // Actualizar otros campos
            $gestionComite->fecha_comite = $request->input('fecha_comite');
            $gestionComite->comite_id = $request->input('comite_id');
            $gestionComite->estado = $request->input('estado');
            $gestionComite->descripcion = $request->input('descripcion');

            if ($gestionComite->save()) {
                return response()->json(['mensaje' => 'Gestión de comité actualizada exitosamente', 'error' => '0', 'gestionComiteId' => $gestionComite->id]);
            } else {
                return response()->json(['mensaje' => 'Error al guardar la gestión del comité', 'error' => '100', 'gestionComiteId' => 0]);
            }
        } catch (\Exception $e) {
            return response()->json(['mensaje' => 'Error al actualizar la gestión del comité', 'error' => $e->getMessage(), 'gestionComiteId' => 0]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $gestionComite = GestionComite::find($id);
        if (isset($gestionComite)) {
            $gestionComite->estado = 0;
            if ($gestionComite->save()) {
                return Inertia::location(route('gestionComites.index'));
            } else {
                return redirect()->route('gestionComites.index')->with('error', 'Error al actualizar la Gestion de Comite');
            }
        } else {
            return redirect()->route('gestionComites.index')->with('error', 'No se encontro la Gestion de Comite');
        }
    }

    protected function validationRules()
    {
        return [
            'fecha_comite' => 'required|date',
            'comite_id' => 'required|exists:comites,id',
            'estado' => 'required|in:1,0',
        ];
    }

    protected function validationRules2()
    {
        return [
            'fecha_comite' => 'required|date',
            'comite_id' => 'required|exists:comites,id',
            'estado' => 'required|in:1,0',
        ];
    }

    protected function validationMessages()
    {
        return [
            'fecha_comite.required' => 'El campo fecha es obligatorio.',
            'fecha_comite.date' => 'El campo fecha debe ser una fecha.',
            'adjunto.required' => 'El archivo es obligatorio.',
            //'adjunto.mimes' => 'El archivo debe ser de tipo: pdf, doc o docx.',
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.in' => 'El campo estado debe ser Activo(1) o Inactivo(0)',
            'comite_id.required' => 'El campo Comite es obligatorio.',
            'comite_id.exists' => 'El Comite seleccionado no esta registrado.',
        ];
    }
}
