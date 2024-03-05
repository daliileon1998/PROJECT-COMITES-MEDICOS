<?php

namespace App\Http\Controllers;

use App\Models\GestionComite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
//use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;


class PdfController extends Controller
{
    public function generateAndModify(Request $request)
    {
        try {
            $datos = $request->all();

            //dd($datos);
            //$vista = View::make('pdf.template', $datos);

            $datos["descripcion"] = html_entity_decode($datos["descripcion"]);
           
            //dd($datos["descripcion"]);
            // Configurar opciones adicionales (header y footer)
            $options = [
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
            ];

            $pdf = Pdf::loadView('pdf.template', $datos, $options);
            
            $pdf->setOption('header-html', view('pdf.header'));
            $pdf->setOption('footer-html', view('pdf.footer2'));

            // Renderizar y guardar el PDF
            $nombreArchivo = 'Acta_' . $datos["nombreComite"] . '_' . $datos["fechaComite"] . '_' . $datos["idGestion"] . '.pdf';
            $pdf->save(storage_path('app/public/actas/' . $nombreArchivo));

            //GUARDAR LA URL EN LA GESTION DEL COMITE
            $gestionComite = GestionComite::find($datos["idGestion"]);
            if (isset($gestionComite)) {
                $gestionComite->acta = $nombreArchivo;
                $gestionComite->save();
            }
            return response()->json(['ruta' => asset('storage/actas/' . $nombreArchivo)], 200);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
