<?php

namespace App\Http\Controllers;

use App\Models\Modulo;
use App\Models\PermisoUsuario;
use App\Models\TipoUsuario;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CompromisoRepository;
use Inertia\Inertia;

class TipoUsuarioController extends Controller
{
    public function verificarPermisos($user, $tipoUsuario)
    {
        if ($user && $user->tipo_usuario_id) {
            $permisos = PermisoUsuario::join('modulos', 'permiso_usuarios.modulo_id', '=', 'modulos.id')
                ->join('tipo_usuarios', 'permiso_usuarios.tipo_usuario', '=', 'tipo_usuarios.id')
                ->where('permiso_usuarios.tipo_usuario', $tipoUsuario)
                ->where('modulos.id', 3) // Módulo Tipo Usuario
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
        $compromisos = $compromisoRepository->getCompromisosForUser($user);
        $permisos = $this->verificarPermisos($user, $tipoUsuario);

        if (count($permisos) > 0) {
            if ($permisos[0]["visualizar"] == 0) {
                //NO TIENE PERMISOS PARA ACCEDER AL MODULO REDIRIGIR A UNA PAGINA QUE LE DIGA QUE NO TIENE PERMISOS PARA VER EL MODULO
                return Inertia::render('Accesos/sinPermisos', ['permisos' => $permisos[0],'compromisos' => $compromisos]);
            } else if ($permisos[0]["visualizar"] == 1) {
                $tipoUsuarios = TipoUsuario::all();
                // TIENE PERMISOS PARA ACCEDER AL MODULO
                return Inertia::render('TipoUsuarios/index', ['tipoUsuarios' => $tipoUsuarios, 'permisos' => $permisos[0],'compromisos' => $compromisos]);
            }
        } else {
            //NO TIENE PERMISOS REDIRIGIR A UNA PAGINA QUE LE DIGA QUE NO TIENE PERMISOS PARA VER EL MODULO
            return Inertia::render('Accesos/sinPermisos', ['permisos' => [],'compromisos' => $compromisos]);
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
                return Inertia::render('Accesos/sinPermisos', ['permisos' => $permisos[0],'compromisos' => $compromisos]);
            } else {
                //traer los modulos
                $modulo = Modulo::all();
                return Inertia::render('TipoUsuarios/createTipoUsuario', ["modulos" => $modulo,'compromisos' => $compromisos]);
            }
        } else {
            return Inertia::render('Accesos/sinPermisos', ['permisos' => [],'compromisos' => $compromisos]);
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

        $tipoUsuario = TipoUsuario::create([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'estado' => $request->estado
        ]);

        // CREAR LOS PERMISOS DE USUARIO SEGÚN LOS MÓDULOS
        $modulosSeleccionados = $request->input('permisos', []);

        // Guardar los permisos según los módulos seleccionados
        foreach ($modulosSeleccionados as $moduloId => $permisos) {
            $modulo = Modulo::find($moduloId);
            if ($modulo) {
                // Preparar los datos del permiso
                $permisoData = [
                    'modulo_id' => $moduloId,
                    'tipo_usuario' => $tipoUsuario->id,
                    'agregar' => $permisos['agregar'] ? 1 : 0,
                    'editar' => $permisos['editar'] ? 1 : 0,
                    'eliminar' => $permisos['eliminar'] ? 1 : 0,
                    'visualizar' => $permisos['visualizar'] ? 1 : 0,
                ];

                // Crear el permiso para el usuario y el módulo
                PermisoUsuario::create($permisoData);
            }
        }

        return redirect()->route('tipoUsuarios.index')->with('success', 'Tipo de Usuario creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(tipoUsuario $tipoUsuario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoUsuario $tipoUsuario,CompromisoRepository $compromisoRepository)
    {

        $user = Auth::user();
        $tipoUsuarios = $user->tipo_usuario_id;
        $compromisos = $compromisoRepository->getCompromisosForUser($user);
        $permisos = $this->verificarPermisos($user, $tipoUsuarios);

        if (count($permisos) > 0) {
            if ($permisos[0]["visualizar"] == 0 || $permisos[0]["editar"] == 0) {
                return Inertia::render('Accesos/sinPermisos', ['permisos' => $permisos[0],'compromisos' => $compromisos]);
            } else {
                $modulos = Modulo::all();
                $permisos = PermisoUsuario::where('tipo_usuario', $tipoUsuario->id)->get()->keyBy('modulo_id');

                $permisosPredeterminados = $modulos->map(function ($modulo) use ($permisos) {
                    $permiso = $permisos->get($modulo->id, []);

                    return [
                        'agregar' => $permiso['agregar'] ?? 0,
                        'editar' => $permiso['editar'] ?? 0,
                        'eliminar' => $permiso['eliminar'] ?? 0,
                        'visualizar' => $permiso['visualizar'] ?? 0,
                        'moduloid' => $modulo->id,
                        'idpermiso' => $permiso['id'] ?? 0
                        
                    ];
                });

                return Inertia::render(
                    'TipoUsuarios/EditTipoUsuario',
                    [
                        'tipoUsuario' => $tipoUsuario,
                        'modulos' => $modulos,
                        'permisos' => $permisosPredeterminados,
                        'compromisos' => $compromisos
                    ]
                );
            }
        } else {
            return Inertia::render('Accesos/sinPermisos', ['permisos' => [],'compromisos' => $compromisos]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->validationRules2(), $this->validationMessages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $tipousuario = TipoUsuario::find($id);

        if ($tipousuario) {

            $tipousuario->codigo = $request->codigo;
            $tipousuario->nombre = $request->nombre;
            $tipousuario->estado = $request->estado;
            if ($tipousuario->save()) {
                // MODIFICAR O AGREGAR LOS DIFERENTES PERMISOS DE USUARIO
                $modulosSeleccionados = $request->input('permisos', []);
                foreach ($modulosSeleccionados as $moduloId => $permisos) {
                    $modulo = Modulo::find($permisos["moduloid"]);
                    if ($modulo) {
                        $permisoData = [
                            'modulo_id' => $permisos["moduloid"],
                            'tipo_usuario' => $tipousuario->id,
                            'agregar' => ($permisos['agregar'] == 1 || $permisos['agregar'] == true) ? 1 : 0,
                            'editar' => ($permisos['editar'] == 1 || $permisos['editar'] == true) ? 1 : 0,
                            'eliminar' => ($permisos['eliminar'] == 1 || $permisos['eliminar'] == true) ? 1 : 0,
                            'visualizar' => ($permisos['visualizar'] == 1 || $permisos['visualizar'] == true) ? 1 : 0,
                        ];

                        if ($permisos["idpermiso"] > 0) {
                            $permisoUsuario = PermisoUsuario::find($permisos["idpermiso"]);
                            if ($permisoUsuario) {
                                $permisoUsuario->agregar = $permisoData["agregar"];
                                $permisoUsuario->editar = $permisoData["editar"];
                                $permisoUsuario->eliminar = $permisoData["eliminar"];
                                $permisoUsuario->visualizar = $permisoData["visualizar"];
                                if ($permisoUsuario->save()) {
                                }
                            }
                        } else {
                            PermisoUsuario::create($permisoData);
                        }
                    }
                }
                return redirect()->route('tipoUsuarios.index')->with('success', 'Tipo de Usuario actualizado exitosamente');
            } else {
                return redirect()->back()->withErrors('No existe el usuario');
            }
        } else {
            return redirect()->back()->withErrors('No existe el usuario');
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tipoUsuario = tipoUsuario::find($id);
        if (isset($tipoUsuario)) {
            $tipoUsuario->estado = 0;
            if ($tipoUsuario->save()) {
                return Inertia::location(route('tipoUsuarios.index'));
            } else {
                return redirect()->route('tipoUsuarios.index')->with('error', 'Error al actualizar el Tipo de Usuario');
            }
        } else {
            return redirect()->route('tipoUsuarios.index')->with('error', 'No se encontro el Tipo de Usuario');
        }
    }

    protected function validationRules()
    {
        return [
            'codigo' => ['required', 'string', Rule::unique('tipo_usuarios'), function ($attribute, $value, $fail) {
                if (!$this->validateCampo($value)) {
                    $fail("El " . $attribute . ' contiene caracteres especiales no permitidos.');
                }
            }],
            'nombre' => ['required', 'string', Rule::unique('tipo_usuarios'), function ($attribute, $value, $fail) {
                if (!$this->validateCampo($value)) {
                    $fail("El " . $attribute . ' contiene caracteres especiales no permitidos.');
                }
            }],
            'estado' => 'required|in:1,0',
        ];
    }

    protected function validationRules2()
    {
        return [
            'codigo' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!$this->validateCampo($value)) {
                    $fail("El " . $attribute . ' contiene caracteres especiales no permitidos.');
                }
            }],
            'nombre' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!$this->validateCampo($value)) {
                    $fail("El " . $attribute . ' contiene caracteres especiales no permitidos.');
                }
            }],
            'estado' => 'required|in:1,0',
        ];
    }

    protected function validationMessages()
    {
        return [
            'codigo.required' => 'El campo codigo es obligatorio.',
            'codigo.string' => 'El campo codigo debe ser una cadena.',
            'codigo.unique' => 'El campo codigo ya se encuentrado registrado.',
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El campo nombre debe ser una cadena.',
            'nombre.unique' => 'El campo nombre ya se encuentrado registrado.',
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.in' => 'El campo estado debe ser Activo(1) o Inactivo(0)',
            'codigo.regex' => 'El campo codigo contiene caracteres especiales no permitidos.',
            'nombre.regex' => 'El campo nombre contiene caracteres especiales no permitidos.',
        ];
    }

    protected function validateCampo($campo)
    {
        return !preg_match('/[|!,@#$%^&*()_+={}\[\]:.;"\'<>,?~\\\\]/', $campo);
    }
}
