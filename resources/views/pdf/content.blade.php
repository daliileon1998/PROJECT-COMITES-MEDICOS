<div id="content">
    <div class="hijo">
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 15%; background-color:#bdd6ee;">&nbsp;&nbsp;<b>Acta No:</b></td>
                    <td style="width: 18%;">{{ $acta }}</td>
                    <td style="width: 15%; background-color:#bdd6ee;">&nbsp;&nbsp;<b>Tema:</b></td>
                    <td style="width: 52%;">{{$tema}}</td>
                </tr>
                <tr>
                    <td style="width: 15%; background-color:#bdd6ee;">&nbsp;&nbsp;<b>Fecha:</b></td>
                    <td style="width: 18%;">{{$fecha}}</td>
                    <td style="width: 15%; background-color:#bdd6ee;">&nbsp;&nbsp;<b>Ubicaci&oacute;n :</b></td>
                    <td style="width: 52%;">{{$ubicacion}}</td>
                </tr>
                <tr>
                    <td style="width: 15%; background-color:#bdd6ee;">&nbsp;&nbsp;<b>Hora Inicio:&nbsp;</b></td>
                    <td style="width: 18%;">{{$hora_inicio}}</td>
                    <td style="width: 15%; background-color:#bdd6ee;">&nbsp;&nbsp;<b>Hora Fin:</b></td>
                    <td style="width: 52%;">{{$hora_fin}}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <b>1. ANTES DE LA
            REUNI&Oacute;N</b></p>

    <div class="hijo">
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 25%; background-color:#bdd6ee ;"><b>Objetivo</b> <i>(Para qu&eacute; - Que
                            se quiere lograr)
                        </i></td>
                    <td style="width: 74.8872%;" colspan="3">{!! nl2br(e($objetivo)) !!}</td>
                </tr>
                <tr>
                    <td style="width: 25%; background-color:#bdd6ee;"><b>Agenda</b> <i>(Temas indispensables para
                            lograr los objetivos)</i></td>
                    <td style="width: 74.8872%;" colspan="3">{!! nl2br(e($agenda)) !!}</td>
                </tr>
                <tr>
                    <td style="width: 99.8496%; background-color:#bdd6ee;text-align: center;" colspan="4">
                        <b>Participantes</b> <i>(Personas cuya participación es imprescindible para lograr los
                            objetivos)</i>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%; background-color:#bdd6ee;text-align: center;" class="fr-cell-fixed "><b>Nombre</b></td>
                    <td style="width: 25%; background-color:#bdd6ee;text-align: center;"><b>Proceso</b></td>
                    <td style="width: 25%; background-color:#bdd6ee;text-align: center;"><b>Nombre</b></td>
                    <td style="width: 25%; background-color:#bdd6ee;text-align: center;" class="fr-cell-handler"><b>Proceso</b></td>
                </tr>

                @if(isset($participantes) && count($participantes) > 0)
                @for($i = 0; $i < count($participantes); $i +=2) <tr>
                    <td style="width: 25%;text-align: center;">{{$participantes[$i]['name']}}</td>
                    <td style="width: 25%;text-align: center;">{{$participantes[$i]['cargo']}}</td>

                    @if(isset($participantes[$i + 1]))
                    <td style="width: 25%;text-align: center;">{{$participantes[$i + 1]['name']}}</td>
                    <td style="width: 25%;text-align: center;">{{$participantes[$i + 1]['cargo']}}</td>
                    @else
                    <td style="width: 25%;"></td>
                    <td style="width: 25%;"></td>
                    @endif
                    </tr>
                    @endfor
                    @else
                    <tr>
                        <td style="width: 25%;"><br></td>
                        <td style="width: 25%;"><br></td>
                        <td style="width: 25%;"><br></td>
                        <td style="width: 25%;"><br></td>
                    </tr>
                    <tr>
                        <td style="width: 25%;"><br></td>
                        <td style="width: 25%;"><br></td>
                        <td style="width: 25%;"><br></td>
                        <td style="width: 25%;"><br></td>
                    </tr>
                    @endif

            </tbody>
        </table>
    </div>


    <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <b>2. DESARROLLO DE LA
            REUNI&Oacute;N</b></p>

    <!--<div class="hijo">
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 25%; background-color:#bdd6ee;"><i>(Descripci&oacute;n de los puntos
                            tratados en la reuni&oacute;n)</i></td>
                </tr>
                <tr>
                    <div >{!! $descripcion !!}</div>
                </tr>
            </tbody>
        </table>
    </div>-->
    <div class="hijo2">
        <div class="header" style="width: 97%; background-color:#bdd6ee; padding: 10px;"><i>(Descripción de los puntos tratados en la reunión)</i></div>
        <div class="descripcion">{!! $descripcion !!}</div>
    </div>

    <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <b>3. COMPROMISOS</b></p>

    <div class="hijo">
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 5%; background-color:#bdd6ee;"><br></td>
                    <td style="width: 30%; background-color:#bdd6ee;text-align: center;"><b>Descripci&oacute;n</b></td>
                    <td style="width: 20%; background-color:#bdd6ee;text-align: center;"><b>Responsable</b></td>
                    <td style="width: 20%; background-color:#bdd6ee;text-align: center;"><b>Fecha de Inicio</b></td>
                    <td style="width: 25%; background-color:#bdd6ee;text-align: center;"><b>Fecha de Finalizaci&oacute;n</b></td>
                </tr>

                @if(isset($compromisos) && count($compromisos)>0)

                @for($i = 0; $i < count($compromisos); $i++) <tr>
                    <td style="width: 5%;">{{$compromisos[$i]['indice']}}</td>
                    <td style="width: 30%;">{{$compromisos[$i]['descripcion']}}</td>
                    <td style="width: 20%;text-align: center;">{{$compromisos[$i]['usuario_name']}}</td>
                    <td style="width: 20%;text-align: center;">{{$compromisos[$i]['fecha_inicio']}}</td>
                    <td style="width: 25.0000%;text-align: center;">{{$compromisos[$i]['fecha_fin']}}</td>
                    </tr>
                    @endfor
                    @else
                    <tr>
                        <td style="width: 5%;"><br></td>
                        <td style="width: 30%;"><br></td>
                        <td style="width: 20%;"><br></td>
                        <td style="width: 20%;"><br></td>
                        <td style="width: 25%;"><br></td>
                    </tr>
                    <tr>
                        <td style="width: 5%;"><br></td>
                        <td style="width: 30%;"><br></td>
                        <td style="width: 20%;"><br></td>
                        <td style="width: 20%;"><br></td>
                        <td style="width: 25%;"><br></td>
                    </tr>
                    @endif
            </tbody>
        </table>
    </div>
    <br><br>
    <div class="hijo">
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 99.8496%; background-color:#bdd6ee;" colspan="4">
                        <div style="text-align: center;"><strong>FIRMA DE PARTICIPANTE</strong></div>
                    </td>
                </tr>
                @if(isset($firmas) && count($firmas) > 0)
                @for($i = 0; $i < count($firmas); $i +=2) <tr>
                    <tr>
                        <td style="width:25%;">
                            <div style="text-align: center;">{{$firmas[$i]['name']}}</div>
                        </td>
                        <td style="width:25%;">
                            <div style="text-align: center;">{{$firmas[$i]['cargo']}}</div>
                        </td>
                        @if(isset($firmas[$i + 1]))
                        <td style="width:25%;">
                            <div style="text-align: center;">{{$firmas[$i + 1]['name']}}</div>
                        </td>
                        <td style="width:25%;">
                            <div style="text-align: center;">{{$firmas[$i + 1]['cargo']}}</div>
                        </td>
                        @else
                        <td style="width:25%;">
                            <div style="text-align: center;"><br><br></div>
                        </td>
                        <td style="width:25%;">
                            <div style="text-align: center;"><br><br></div>
                        </td>
                        @endif
                    </tr>
                    <tr>
                        <td style="width:50%;" colspan="2">
                        @if(isset($firmas[$i]))
                            <div style="text-align: center;"><img src="{{ $firmas[$i]['firma_url'] }}" alt="Firma" style="width: 70px; height:70px;"></div>
                        @else
                            <div style="text-align: center;"><br><br></div>
                        @endif
                        </td>
                        <td style="width: 50%;" colspan="2">
                        @if(isset($firmas[$i + 1]))
                            <div style="text-align: center;"><img src="{{ $firmas[$i + 1]['firma_url'] }}" alt="Firma" style="width: 70px; height:70px;"></div>
                        @else
                            <div style="text-align: center;"><br><br></div>
                        @endif
                        </td>
                    </tr>
                    @endfor
                    @endif
            </tbody>
        </table>


        <br><br>
        <table>
        <tbody>
        <tr>
                        <td style="width: 49.9248%; background-color:#bdd6ee;" colspan="2">
                            <div style="text-align: center;"><strong>LIDER DE LA REUNI&Oacute;N</strong></div>
                        </td>
                        <td style="width: 49.9248%; background-color:#bdd6ee;" colspan="2">
                            <div style="text-align: center;"><strong>SECRETARIO</strong></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 49.9248%;text-align: center;" colspan="2">{{$lider}}</td>
                        <td style="width: 49.9248%;text-align: center;" colspan="2">{{$secretario}}</td>
                    </tr>
</tbody>
        </table>
    </div>
</div>

<style>
    #content {
        width: 100%;
    }

    .hijo {
        margin-left: 10px;
        margin-right: 10px;
    }

    .hijo2 {
        display: flex;
        flex-direction: column;
        border: 1px solid black;
        border-top: 1.5px solid black;
        margin-left: 10px;
        margin-right: 10px;
        /* Añade margen externo */
    }

    .header {
        background-color: #bdd6ee;
        padding: 10px;
        font-style: italic;
        border-bottom: 1px solid black;
        font-size: 11px;
        /* Línea divisoria entre encabezado y cuerpo */
    }

    .descripcion {
        padding: 10px;
    }
</style>