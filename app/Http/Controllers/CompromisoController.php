<?php

namespace App\Http\Controllers;

use App\Models\Compromiso;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CompromisoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obten el valor de gestion_comite_id desde la solicitud
        $gestionComiteId = $request->get('gestion_comite_id');


        // Verifica si se proporcionó un valor para gestion_comite_id
        if ($gestionComiteId) {
            // Obtén los compromisos filtrados por el ID de gestion_comite
            $compromisos = Compromiso::where('gestion_comite_id', $gestionComiteId)
                ->with('usuario')
                ->get();

            foreach ($compromisos as $compromiso) {
                $compromiso->adjunto_url = asset('storage/' . $compromiso->adjunto);
            }
        } else {
            // Si no se proporciona gestion_comite_id, obtén todos los compromisos
            $compromisos = [];
        }

        return response()->json(['compromisos' => $compromisos]);
        // return Inertia::render('GestionComite/createGestionComite', ['compromisos' => $compromisos]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules(), $this->validationMessages());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rutaArchivo='';

        if ($request->hasFile('adjunto')) {

            $archivo = $request->file('adjunto');
            $timestamp = now()->timestamp;
            $nombreA = "compromiso_comite_" . $request->fecha_inicio . "_" . $request->fecha_inicio . "_" . $request->descripcion . "" . $timestamp;
            $extension = $archivo->getClientOriginalExtension();
            $rutaArchivo = $archivo->storeAs('compromisos', $nombreA. '.' . $extension, 'public');
        }
            $compromiso = Compromiso::create([
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'usuario_id' => $request->usuario_id,
                'gestion_comite_id' => $request->gestion_comite_id,
                'estado' => $request->estado,
                'descripcion' => $request->descripcion,
                'adjunto' => $rutaArchivo
            ]);

            return response()->json(['mensaje' => 'Compromiso creado con exito', 'error' => 0, 'compromisoId' => $request->gestion_comite_id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Compromiso $compromiso)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), $this->validationRules(), $this->validationMessages());

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $compromiso = Compromiso::find($id);

            if (!$compromiso) {
                return response()->json(['mensaje' => 'Compromiso no encontrado', 'error' => 100], 404);
            }

            $compromiso->fecha_inicio = $request->fecha_inicio;
            $compromiso->fecha_fin = $request->fecha_fin;
            $compromiso->usuario_id = $request->usuario_id;
            $compromiso->gestion_comite_id = $request->gestion_comite_id;
            $compromiso->estado = $request->estado;
            $compromiso->descripcion = $request->descripcion;

            if ($request->hasFile('adjunto')) {

                if ($compromiso->adjunto) {
                    Storage::disk('public')->delete($compromiso->adjunto);
                }

                $archivo = $request->file('adjunto');
                $timestamp = now()->timestamp;
                $nombreA = "compromiso_comite_" . $request->fecha_inicio . "_" . $request->fecha_inicio . "_" . $request->descripcion . "" . $timestamp;
                $extension = $archivo->getClientOriginalExtension();
                $rutaArchivo = $archivo->storeAs('compromisos', $nombreA. '.' . $extension, 'public');
                $compromiso->adjunto = $rutaArchivo;
            }

            $compromiso->save();

            return response()->json(['mensaje' => 'Compromiso actualizado con éxito', 'compromisoId' => $compromiso->id]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tip = Compromiso::find($id);
        if (isset($tip)) {
            $res = Compromiso::destroy($id);
            if ($res) {
                return response()->json(['mensaje' => 'Compromiso eliminado con éxito.', 'error' => 0]);
            } else {
                return response()->json(['mensaje' => 'El Compromiso no existe', 'error' => 100]);
            }
        } else {
            return response()->json(['mensaje' => 'No existe el Compromiso.', 'error' => 100]);
        }
    }

    protected function validationRules()
    {
        return [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'usuario_id' => 'required|exists:users,id',
            'gestion_comite_id' => 'required|exists:gestion_comites,id',
            'estado' => 'required|in:1,0,2,3',
        ];
    }

    protected function validationMessages()
    {
        return [
            'fecha_inicio.required' => 'El campo fecha de inicio es obligatorio.',
            'fecha_inicio.date' => 'El campo fecha de inicio debe ser una fecha.',
            'fecha_fin.required' => 'El campo fecha fin es obligatorio.',
            'fecha_fin.date' => 'El campo fecha fin debe ser una fecha.',
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.in' => 'El campo estado debe ser Activo(1) o Inactivo(0)',
            'usuario_id.required' => 'El campo Usuario es obligatorio.',
            'usuario_id.exists' => 'El Usuario seleccionado no esta registrado.',
            'gestion_comite_id.required' => 'El campo Gestion de Comite es obligatorio.',
            'gestion_comite_id.exists' => 'Gestion de Comite seleccionado no esta registrado.',
        ];
    }
}
