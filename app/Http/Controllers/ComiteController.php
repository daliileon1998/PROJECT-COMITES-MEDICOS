<?php

namespace App\Http\Controllers;

use App\Models\Comite;
use App\Models\PermisoUsuario;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Repositories\CompromisoRepository;

class ComiteController extends Controller
{
    public function verificarPermisos($user, $tipoUsuario)
    {
        if ($user && $user->tipo_usuario_id) {
            $permisos = PermisoUsuario::join('modulos', 'permiso_usuarios.modulo_id', '=', 'modulos.id')
                ->join('tipo_usuarios', 'permiso_usuarios.tipo_usuario', '=', 'tipo_usuarios.id')
                ->where('permiso_usuarios.tipo_usuario', $tipoUsuario)
                ->where('modulos.id', 1) // Módulo Comite
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
                return Inertia::render('Accesos/sinPermisos', ['comites' => [], 'compromisos' => $compromisos, 'permisos' => $permisos[0]]);
            } else {
                $comites = Comite::with('usuario')->get();
                return Inertia::render('Comites/index', ['comites' => $comites, 'compromisos' => $compromisos, 'permisos' => $permisos[0]]);
            }
        } else {
            return Inertia::render('Accesos/sinPermisos', ['comites' => [], 'compromisos' => $compromisos, 'permisos' => $permisos[0]]);
        }
    }


    public function create(CompromisoRepository $compromisoRepository)
    {
        $user = Auth::user();
        $tipoUsuario = $user->tipo_usuario_id;

        $permisos = $this->verificarPermisos($user, $tipoUsuario);
        $compromisos = $compromisoRepository->getCompromisosForUser($user);

        if (count($permisos) > 0) {
            if ($permisos[0]["visualizar"] == 0 || $permisos[0]["agregar"] == 0) {
                return Inertia::render('Accesos/sinPermisos', ['permisos' => $permisos[0],'compromisos' => $compromisos]);
            } else {
                $usuarios = User::where('estado', 1)->get();
                return Inertia::render('Comites/createComite', ['usuarios' => $usuarios, 'compromisos' => $compromisos]);
            }
        } else {
            return Inertia::render('Accesos/sinPermisos', ['permisos' => [],'compromisos' => $compromisos]);
        }
    }


    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), $this->validationRules(), $this->validationMessages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $comite = new Comite($request->input());
        $comite->save();

        return redirect()->route('comites.index')->with('success', 'Comité creado exitosamente');
    }


    public function show(Comite $comite)
    {
        //
    }

    public function edit(Comite $comite, CompromisoRepository $compromisoRepository)
    {
        $user = Auth::user();
        $tipoUsuario = $user->tipo_usuario_id;

        $permisos = $this->verificarPermisos($user, $tipoUsuario);
        $compromisos = $compromisoRepository->getCompromisosForUser($user);

        if (count($permisos) > 0) {
            if ($permisos[0]["visualizar"] == 0 || $permisos[0]["editar"] == 0) {
                return Inertia::render('Accesos/sinPermisos', ['permisos' => $permisos[0],'compromisos' => $compromisos]);
            } else {
                
                $usuarios = User::all();
                return Inertia::render(
                    'Comites/EditComite',
                    [
                        'comite' => $comite,
                        'usuarios' => $usuarios,
                        'compromisos' => $compromisos
                    ]
                );
            }
        } else {
            return Inertia::render('Accesos/sinPermisos', ['permisos' => [],'compromisos' => $compromisos]);
        }
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->validationRules2(), $this->validationMessages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $comite = Comite::find($id);
        $comite->fill($request->input())->saveOrFail();
        return redirect()->route('comites.index')->with('success', 'Comité actualizado exitosamente');
    }

    public function destroy($id)
    {
        $comite = Comite::find($id);
        if (isset($comite)) {
            $comite->estado = 0;
            if ($comite->save()) {
                return Inertia::location(route('comites.index'));
            } else {
                return redirect()->route('comites.index')->with(['error' => 'Error al actualizar el Comité'], 500);
            }
        } else {
            return redirect()->route('comites.index')->with(['error' => 'No se encontró el Comité'], 404);
        }
    }

    protected function validationRules()
    {
        return [
            'codigo' => ['required', 'string', Rule::unique('comites'), function ($attribute, $value, $fail) {
                if (!$this->validateCampo($value)) {
                    $fail($attribute . ' contiene caracteres especiales no permitidos.');
                }
            }],
            'nombre' => ['required', 'string', Rule::unique('comites'), function ($attribute, $value, $fail) {
                if (!$this->validateCampo($value)) {
                    $fail($attribute . ' contiene caracteres especiales no permitidos.');
                }
            }],
            'usuario_id' => 'required|exists:users,id',
            'estado' => 'required|in:1,0',
        ];
    }

    protected function validationRules2()
    {
        return [
            'codigo' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!$this->validateCampo($value)) {
                    $fail($attribute . ' contiene caracteres especiales no permitidos.');
                }
            }],
            'nombre' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!$this->validateCampo($value)) {
                    $fail($attribute . ' contiene caracteres especiales no permitidos.');
                }
            }],
            'usuario_id' => 'required|exists:users,id',
            'estado' => 'required|in:1,0',
        ];
    }

    protected function validationMessages()
    {
        return [
            'codigo.required' => 'El campo codigo es obligatorio.',
            'codigo.string' => 'El campo codigo debe ser una cadena.',
            'codigo.unique' => 'El campo codigo ya se encuentrado registrado.',
            'codigo.regex' => 'El campo codigo contiene caracteres especiales no permitidos.',
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El campo nombre debe ser una cadena.',
            'nombre.unique' => 'El campo nombre ya se encuentrado registrado.',
            'nombre.regex' => 'El campo nombre contiene caracteres especiales no permitidos.',
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.in' => 'El campo estado debe ser Activo(1) o Inactivo(0)',
            'usuario_id.required' => 'El campo Usuario es obligatorio.',
            'usuario_id.exists' => 'El Usuario seleccionado no esta registrado.',
        ];
    }

    protected function validateCampo($campo)
    {
        return !preg_match('/[|!,@#$%^&*()_+={}\[\]:.;"\'<>,?~\\\\]/', $campo);
    }
}
