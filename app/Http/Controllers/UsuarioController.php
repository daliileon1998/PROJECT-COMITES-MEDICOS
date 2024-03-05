<?php

namespace App\Http\Controllers;

use App\Models\PermisoUsuario;
use App\Models\TipoUsuario;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Repositories\CompromisoRepository;

class UsuarioController extends Controller
{

    public function verificarPermisos($user, $tipoUsuario)
    {
        if ($user && $user->tipo_usuario_id) {
            $permisos = PermisoUsuario::join('modulos', 'permiso_usuarios.modulo_id', '=', 'modulos.id')
                ->join('tipo_usuarios', 'permiso_usuarios.tipo_usuario', '=', 'tipo_usuarios.id')
                ->where('permiso_usuarios.tipo_usuario', $tipoUsuario)
                ->where('modulos.id', 4) // Módulo Usuario
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
        //$usuario = User::all();
        $usuario = User::with('tipoUsuario')->get();
        $usuario->map(function ($user) {
            $user->firma_url = asset('storage/' . $user->firma);
            return $user;
        });
        $user = Auth::user();
        $tipoUsuario = $user->tipo_usuario_id;

        $permisos = $this->verificarPermisos($user, $tipoUsuario);
        $compromisos = $compromisoRepository->getCompromisosForUser($user);

        if (count($permisos) > 0) {
            if ($permisos[0]["visualizar"] == 0) {
                return Inertia::render('Accesos/sinPermisos', ['permisos' => $permisos[0], 'compromisos' => $compromisos]);
            } else {

                return Inertia::render('Usuarios/index', ['Usuarios' => $usuario, 'permisos' => $permisos[0], 'compromisos' => $compromisos]);
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

        $permisos = $this->verificarPermisos($user, $tipoUsuario);
        $compromisos = $compromisoRepository->getCompromisosForUser($user);

        if (count($permisos) > 0) {
            if ($permisos[0]["visualizar"] == 0 || $permisos[0]["agregar"] == 0) {
                return Inertia::render('Accesos/sinPermisos', ['permisos' => $permisos[0], 'compromisos' => $compromisos]);
            } else {
                $tipoUsuario = TipoUsuario::select('id', 'nombre as name')->where('estado', 1)->get();
                return Inertia::render('Usuarios/createUsuario', ['tipoUsuario' => $tipoUsuario, 'compromisos' => $compromisos]);
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

        if ($request->hasFile('firma')) {

            $archivo = $request->file('firma');
            $nombreA = "firma_" . $request->name . "_" . $request->cc;
            $extension = $archivo->getClientOriginalExtension();
            $rutaArchivo = $archivo->storeAs('firmas', $nombreA. '.' . $extension, 'public');
        }

        $user = User::create([
            'cc' => $request->cc,
            'name' => $request->name,
            'estado' => $request->estado,
            'phone' => $request->phone,
            'email' => $request->email,
            'cargo' => $request->cargo,
            'firma'=>  $rutaArchivo,
            'password' => Hash::make($request->password2),
            'password2' => $request->password2,
            'tipo_usuario_id' => $request->tipo_usuario_id,

        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente');
        // return response()->json(['message' => 'Usuario creado exitosamente']);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $usuario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $usuario, CompromisoRepository $compromisoRepository)
    {
        $user = Auth::user();
        $tipoUsuario = $user->tipo_usuario_id;
        $compromisos = $compromisoRepository->getCompromisosForUser($user);
        $permisos = $this->verificarPermisos($user, $tipoUsuario);

        if (count($permisos) > 0) {
            if ($permisos[0]["visualizar"] == 0 || $permisos[0]["editar"] == 0) {
                return Inertia::render('Accesos/sinPermisos', ['permisos' => $permisos[0], 'compromisos' => $compromisos]);
            } else {

                $tipoUsuario = TipoUsuario::select('id', 'nombre as name')->get();
                //dd($usuario);
                if($usuario->firma !=""){
                    $usuario->firma_url = asset('storage/' . $usuario->firma); 
                    $usuario->firma = '';// Aquí se asigna firma_url directamente al usuario
                }else{
                    $usuario->firma_url = '';
                }
                
                return Inertia::render(
                    'Usuarios/EditUsuario',
                    [
                        'usuario' => $usuario,
                        'tipoUsuario' => $tipoUsuario,
                        'compromisos' => $compromisos
                    ]
                );
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
            return redirect()->back()->withErrors($validator)->withInput();
        }      


        $usuario = User::find($id);
        if (isset($usuario)) {
            $usuario->cc = $request->cc;
            $usuario->name = $request->name;
            $usuario->estado = $request->estado;
            $usuario->phone = $request->phone;
            $usuario->email = $request->email;
            $usuario->cargo = $request->cargo;

           if ($request->hasFile('firma')) {

                if ($usuario->firma) {
                    Storage::disk('public')->delete($usuario->firma);
                }

                $archivo = $request->file('firma');
                $nombreA = "firma_" . $request->name . "_" . $request->cc;
                $extension = $archivo->getClientOriginalExtension();
                $rutaArchivo = $archivo->storeAs('firmas', $nombreA. '.' . $extension, 'public');
                $usuario->firma = $rutaArchivo;
            }

            //$usuario->firma = $rutaArchivo;
            $usuario->password = Hash::make($request->password2);
            $usuario->password2 = $request->password2;
            $usuario->tipo_usuario_id = $request->tipo_usuario_id;
            if ($usuario->save()) {
                return response()->json(['mensaje' => 'Usuario actualizado con éxito']);
                return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente');
            } else {
                return redirect()->back()->withErrors('No se actualizo el Usuario');
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
        $usuario = User::find($id);
        if (isset($usuario)) {
            $usuario->estado = 0;
            if ($usuario->save()) {
                return Inertia::location(route('usuarios.index'));
            } else {
                return redirect()->route('usuarios.index')->with('error', 'Error al actualizar el Usuario');
            }
        } else {
            return redirect()->route('usuarios.index')->with('error', 'No se encontro el Usuario');
        }
    }

    protected function validationRules()
    {
        return [
            'cc' => ['required', 'numeric', Rule::unique('users'), function ($attribute, $value, $fail) {
                if (!$this->validateCampo($value)) {
                    $fail("El " . $attribute . ' contiene caracteres especiales no permitidos.');
                }
            }],
            'name' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!$this->validateCampo($value)) {
                    $fail("El " . $attribute . ' contiene caracteres especiales no permitidos.');
                }
            }],
            'estado' => 'required|in:1,0',
            'cargo' => 'required', 'string', function ($attribute, $value, $fail) {
                if (!$this->validateCampo($value)) {
                    $fail("El " . $attribute . ' contiene caracteres especiales no permitidos.');
                }
            },
            'phone' => 'numeric',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password2' => ['required', /*'confirmed',*/ Rules\Password::defaults()],
            'tipo_usuario_id' => 'required|exists:tipo_usuarios,id',
        ];
    }

    protected function validationRules2()
    {
        return [
            'cc' => ['required', 'numeric', function ($attribute, $value, $fail) {
                if (!$this->validateCampo($value)) {
                    $fail("El " . $attribute . ' contiene caracteres especiales no permitidos.');
                }
            }],
            'name' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!$this->validateCampo($value)) {
                    $fail("El " . $attribute . ' contiene caracteres especiales no permitidos.');
                }
            }],
            'estado' => 'required|in:1,0',
            'cargo' => 'required', 'string', function ($attribute, $value, $fail) {
                if (!$this->validateCampo($value)) {
                    $fail("El " . $attribute . ' contiene caracteres especiales no permitidos.');
                }
            },
            'phone' => 'numeric',
            'email' => 'required|string|lowercase|email|max:255',
            'password2' => ['required', Rules\Password::defaults()],
            'tipo_usuario_id' => 'required|exists:tipo_usuarios,id',
        ];
    }

    protected function validationMessages()
    {
        return [
            'cc.required' => 'El campo cedula es obligatorio.',
            'cc.numeric' => 'El campo cedula debe ser una cadena.',
            'cc.unique' => 'El campo cedula ya se encuentrado registrado.',
            'cc.regex' => 'El campo cedula contiene caracteres especiales no permitidos.',
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El campo nombre debe ser una cadena.',
            'nombre.regex' => 'El campo nombre contiene caracteres especiales no permitidos.',
            'nombre.unique' => 'El campo nombre ya se encuentrado registrado.',
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.in' => 'El campo estado debe ser Activo(1) o Inactivo(0)',
            'tipo_usuario_id.required' => 'El campo Tipo Usuario es obligatorio.',
            'tipo_usuario_id.exists' => 'El Tipo de Usuario seleccionado no esta registrado.',
            'phone.numeric' => 'El campo Telefono debe ser númerico.',
            'email.required' => 'El campo de correo electrónico es obligatorio.',
            'email.string' => 'El correo electrónico debe ser una cadena de texto.',
            'email.lowercase' => 'El correo electrónico debe estar en minúsculas.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'email.max' => 'El correo electrónico no debe tener más de :max caracteres.',
            'email.unique' => 'Este correo electrónico ya está en uso.',
            'password2.required' => 'El campo de contraseña es obligatorio.',
            //'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ];
    }

    protected function validateCampo($campo)
    {
        return !preg_match('/[|!,@#$%^&*()_+={}\[\]:.;"\'<>,?~\\\\]/', $campo);
    }
}
