<?php 


if(!class_exists('ListHojaCotejo') ) 
    include "ListHojaCotejo.php";
  

class HtmlFormatoHojaCotejo 
{

public function getHtmlPdfInformacion($db,$id,$data)
  {

    $ListHojaCotejo =new ListHojaCotejo();
    $allContratantesBanderas=array();
    $documentoNotarial=$ListHojaCotejo->getDocumento($id,$data);
    $iddocumento=isset($documentoNotarial["IDDOCUMENTO"])?$documentoNotarial["IDDOCUMENTO"]:"0";
    if(intval($id)==0)
      $id=$iddocumento;
    $actosAll=$ListHojaCotejo->getActos($id);


    $notaria=isset($documentoNotarial["NOTARIA"])?trim($documentoNotarial["NOTARIA"]):"";
    $tipoInstrumento=isset($documentoNotarial["TIPO_INSTRUMENTO"])?trim($documentoNotarial["TIPO_INSTRUMENTO"]):"";

    $numeroInstrumento=isset($documentoNotarial["NUMERO_INSTRUMENTO"])?trim($documentoNotarial["NUMERO_INSTRUMENTO"]):"";
    $numeroKardex=isset($documentoNotarial["NUMERODEKARDEX"])?trim($documentoNotarial["NUMERODEKARDEX"]):"";
    


    $provincia=isset($documentoNotarial["PROVINCIA"])?trim($documentoNotarial["PROVINCIA"]):"";
    $distrito=isset($documentoNotarial["DISTRITO"])?trim($documentoNotarial["DISTRITO"]):"";
    $departamento=isset($documentoNotarial["DEPARTAMENTO"])?trim($documentoNotarial["DEPARTAMENTO"]):"";
    
    $direccion=isset($documentoNotarial["DIRECCION"])?trim($documentoNotarial["DIRECCION"]):"";
    $telefono=isset($documentoNotarial["TELEFONO"])?trim($documentoNotarial["TELEFONO"]):"";
    $fechaInstrumento=isset($documentoNotarial["FECHA_INSTRUMENTO"])?trim($documentoNotarial["FECHA_INSTRUMENTO"]):"";

    $fechaConclusion=isset($documentoNotarial["FECHA_CONCLUSION"])?trim($documentoNotarial["FECHA_CONCLUSION"]):"";

    
    $idInstrumento=isset($documentoNotarial["IDINSTRUMENTO"])?trim($documentoNotarial["IDINSTRUMENTO"]):"";
    $listBanderas=$ListHojaCotejo->getBanderasPorSujeto($iddocumento);
  
  

    $xhtml='
    <!DOCTYPE html>
<html>
<head>
    <title></title>

      <style>
      body{
        font-size:14px;
      }
    .titulo{
      font-weight:bold; 
      font-size:18px;
      color:white;
      background-color:#3f3f3f;
      padding:10px;
    }
    .encabezado{
       font-weight:bold; 
      font-size:25px;
      color:#da8916;
      padding:10px;
    }
    .encabezado2{
       font-weight:bold; 
      font-size:25px;
      color:#004e6f;
      padding:10px;
    }

     .subtitulo{
      font-weight:bold; 
      font-size:18px;
      color:#3f3f3f;
      background-color:#ababab;
      padding:10px;
    }

        .subtitulo2{
         font-weight:bold; 
      font-size:18px;
      color:#3f3f3f;
      padding:10px;
    }
     .subtitulo3{
         font-weight:bold; 
      font-size:18px;
       color:#3f3f3f;
      background-color:#cecece;
      padding:10px;
    }
    .subtitulo4{
         font-weight:bold; 
       color:#3f3f3f;
   font-size:15px;
    }

    .label{
      color:#004e6f;
      font-size:15px;
      font-weight: bold;
    }

    .sublabel{
      color:#808080;
    }
    td{
      padding:3px;
    }
    body{
        margin: 0mm 0mm 19mm 0mm;
      }

      .TableCont{
        margin:30px;
         border: solid;

      }
       .Table

        {

            display: table;

        }

        .Title

        {

            display: table-caption;

            text-align: center;

            font-weight: bold;

            font-size: larger;

        }

        .Heading

        {

            display: table-row;

            font-weight: bold;

            text-align: center;

        }

        .Row

        {

            display: table-row;

        }

        .Cell

        {

            display: table-cell;
            border-width: thin;

            padding-left: 5px;

            padding-right: 5px;
        }
        p{
          margin:2px;
        }

  </style>

</head>
<body>

  <div class="encabezado">'; 

    $nombreImagen="img/logo_ocp.png";
    $imagenBase64 = "data:image/png;base64," . base64_encode(file_get_contents($nombreImagen));

// $xhtml.="<img width='100' src='img/logo_ocp.png'>";


$xhtml.='

   MÓDULO OCP - <span class="encabezado2"> CNL </span>
  </div>
<br>

  <div class="titulo"> 
  NOTARIO(A) - '.$notaria." <br> ".$tipoInstrumento.'
  </div>
  <br>


<fieldset>
 <legend>Datos Notario</legend>
  <table>
    <tr>
      <td>Notaría</td>
      <td class="sublabel">'.$notaria.'</td>

      <td>Departamento</td>
       <td class="sublabel">'.$departamento.'</td> 

      <td>Provincia</td>
      <td class="sublabel">'.$provincia.'</td>
    </tr>


      <tr>
      <td>Dirección</td>
       <td class="sublabel">'.$direccion.'</td>

      <td>Distrito</td>
       <td class="sublabel">'.$distrito.'</td>

      <td>Teléfono</td>
       <td class="sublabel">'.$telefono.'</td>
    </tr>
  </table>
  </fieldset>
  <br>
 
 <fieldset>
 <legend>Escrituración</legend>
  <table>
    <tr>
      <td>N° Instrumento</td>
      <td class="sublabel">'.$numeroInstrumento.'</td>

      <td>N° Kardex</td>
       <td class="sublabel">'.$numeroKardex.'</td> 

      <td>Fecha Instrumento</td>
      <td class="sublabel">'.$fechaInstrumento.'</td>
    </tr>

    <tr>
      <td>Tipo Instrumento</td>
      <td class="sublabel">'.$tipoInstrumento.'</td>
      <td>Fecha de Conclusión</td>
      <td class="sublabel">'.$fechaConclusion.'</td>
      <td></td>
      <td class="sublabel"></td> 
    </tr>


  </table>
   </fieldset>
  <br>
  ';



foreach ($actosAll as $value) {
  $xhtml.='<div class="titulo"> '.$value["DESCRIPCION"].'</div>';
    $idOperacion=isset($value["IDOPERACION"])?$value["IDOPERACION"]:"0";

    $allRepresentantes=$ListHojaCotejo->getRepresentantesPorContratante($idOperacion);
  
    $listDetalleMedioPago=$ListHojaCotejo->getDetalleMedioPago($idOperacion);
    if(sizeof($listDetalleMedioPago)>0){
       $xhtml.='<div class="TableCont">';
       $xhtml.='<div class="subtitulo3">Detalle Medio de Pago</div>';
       $xhtml.='<div class="Table">';
       $xhtml.='<div class="Row">
                  <div class="Cell">
                      <p>Momento de Pago</p>
                  </div>
                  <div class="Cell">
                      <p>Medio de Pago</p>
                  </div>
                  <div class="Cell">
                      <p>Fecha de Pago</p>
                  </div>
                  <div class="Cell">
                      <p>Forma de Pago</p>
                  </div>
                    <div class="Cell">
                      <p>Cuantia</p>
                  </div>
                </div>';
foreach ($listDetalleMedioPago as $objPatri) {
  $tipoMedioPago=isset($objPatri["TIPO_MEDIO_PAGO"])?trim($objPatri["TIPO_MEDIO_PAGO"]):"";
  $momentoPago=isset($objPatri["MOMENTO_PAGO"])?trim($objPatri["MOMENTO_PAGO"]):"";
  $fechaPago=isset($objPatri["FECHAPAGO"])?trim($objPatri["FECHAPAGO"]):"";
  $formaPago=isset($objPatri["FORMA_PAGO"])?trim($objPatri["FORMA_PAGO"]):"";
  $cuantia=isset($objPatri["CUANTIA"])?$objPatri["CUANTIA"]:"";
  $tipoMoneda=isset($objPatri["TIPO_MONEDA"])?trim($objPatri["TIPO_MONEDA"]):"";
  $oportunidad=isset($objPatri["OPORTUNIDAD_PAGO"])?trim($objPatri["OPORTUNIDAD_PAGO"]):"";
  
      $xhtml.='<div class="Row">
                      <div class="Cell">
                          <p class="sublabel">'.$momentoPago.'</p>
                      </div>
                      <div class="Cell">
                        <p class="sublabel">'.$tipoMedioPago.'</p>
                      </div>
                      <div class="Cell">
                          <p class="sublabel">'.$fechaPago.'</p>
                      </div>
                      <div class="Cell">
                          <p class="sublabel">'.$formaPago.'</p>
                      </div>
                        <div class="Cell">
                          <p class="sublabel">'.$cuantia." ".$tipoMoneda.'</p>
                      </div>
                </div>';
    }
$xhtml.='               </div>
               </div>
          ';
    }

    if($idInstrumento==3 || $idInstrumento==4){
    $bienMueble=$ListHojaCotejo->getBienMueble($idOperacion);
    if(sizeof($bienMueble)>0){ 
      $sedeRegistral=isset($bienMueble["SEDE_REGISTRAL"])?$bienMueble["SEDE_REGISTRAL"]:"";
      $partidaRegistral=isset($bienMueble["PARTIDAREGISTRAL"])?$bienMueble["PARTIDAREGISTRAL"]:"";
      $placa=isset($bienMueble["PLACA"])?$bienMueble["PLACA"]:"";
      $numeroSerie=isset($bienMueble["NUMEROSERIE"])?$bienMueble["NUMEROSERIE"]:"";
      $marca=isset($bienMueble["MARCA"])?$bienMueble["MARCA"]:"";
      $anio=isset($bienMueble["ANIO"])?$bienMueble["ANIO"]:"";
      $combustible=isset($bienMueble["COMBUSTIBLE"])?$bienMueble["COMBUSTIBLE"]:"";
      $clase=isset($bienMueble["CLASE"])?$bienMueble["CLASE"]:"";

      $carroceria=isset($bienMueble["CARROCERIA"])?$bienMueble["CARROCERIA"]:"";
      $color=isset($bienMueble["COLOR"])?$bienMueble["COLOR"]:"";
      $motor=isset($bienMueble["MOTOR"])?$bienMueble["MOTOR"]:"";
      
      $cilindros=isset($bienMueble["NUMEROCILINDROS"])?$bienMueble["NUMEROCILINDROS"]:"";
      $ruedas=isset($bienMueble["NUMERORUEDA"])?$bienMueble["NUMERORUEDA"]:"";
      
       $xhtml.='<div class="TableCont">';
       $xhtml.='<div class="subtitulo3">Vehiculo Terrestre</div>';

       $xhtml.='<div class="Table">';
       $xhtml.=' <table>
    <tr>
      <td>Sede Registral</td>
      <td class="sublabel">'.$sedeRegistral.'</td>
      <td>Partida Registral</td>
       <td class="sublabel">'.$partidaRegistral.'</td> 
      <td>Número Serie</td>
      <td class="sublabel">'.$numeroSerie.'</td>
    </tr>

       <tr>
      <td>Placa</td>
      <td class="sublabel">'.$placa.'</td>
      <td>Marca</td>
       <td class="sublabel">'.$marca.'</td> 
      <td>Modelo</td>
      <td class="sublabel">'.$marca.'</td>
      </tr>

       <tr>
      <td>Año</td>
      <td class="sublabel">'.$anio.'</td>
      <td>Combustible</td>
       <td class="sublabel">'.$combustible.'</td> 
      <td>Clase</td>
      <td class="sublabel">'.$clase.'</td>
      </tr>


       <tr>
      <td>Carroceria</td>
      <td class="sublabel">'.$carroceria.'</td>
      <td>Color</td>
       <td class="sublabel">'.$color.'</td> 
      <td>Motor</td>
      <td class="sublabel">'.$motor.'</td>
      </tr>

       <tr>
      <td>Cilindros</td>
      <td class="sublabel">'.$cilindros.'</td>
      <td>Ruedas</td>
       <td class="sublabel">'.$ruedas.'</td> 
      <td></td>
      <td class="sublabel"></td>
      </tr>

  </table>';

$xhtml.='             </div>
               </div>
          ';
      }
    }else if ( $idInstrumento==1)
    {
//      die($idOperacion);
    $bienInMueble=$ListHojaCotejo->getBienInMueble($idOperacion);
  //  var_dump($bienInMueble);
    if(isset($bienInMueble)){ 
      $sedeRegistral=isset($bienInMueble["SEDE_REGISTRAL"])?$bienInMueble["SEDE_REGISTRAL"]:"";
      $partidaRegistral=isset($bienInMueble["PARTIDAREGISTRAL"])?$bienInMueble["PARTIDAREGISTRAL"]:"";

      $departamento=isset($bienInMueble["DEPARTAMENTO"])?$bienInMueble["DEPARTAMENTO"]:"";
      $provincia=isset($bienInMueble["PROVINCIA"])?$bienInMueble["PROVINCIA"]:"";
      $distrito=isset($bienInMueble["DISTRITO"])?$bienInMueble["DISTRITO"]:"";
      
      
       $xhtml.='<div class="TableCont">';
       $xhtml.='<div class="subtitulo3">Predio Urbano</div>';

       $xhtml.='<div class="Table">';
       $xhtml.=' <table>
    <tr>
      <td>Sede Registral</td>
      <td class="sublabel">'.$sedeRegistral.'</td>
      <td>Partida Registral</td>
       <td class="sublabel">'.$partidaRegistral.'</td> 
      <td></td>
      <td class="sublabel"></td>
    </tr>

       <tr>
      <td>Departamento</td>
      <td class="sublabel">'.$departamento.'</td>
      <td>Provincia</td>
       <td class="sublabel">'.$provincia.'</td> 
      <td>Distrito</td>
      <td class="sublabel">'.$distrito.'</td>
      </tr>

    

  </table>';

$xhtml.='             </div>
               </div>
          ';
      }
    } 


    //die($idOperacion);
    if(intval($idOperacion)>0){
      $listContratantesPor=$ListHojaCotejo->getContratantesPorOperacion($idOperacion);

      foreach ($listContratantesPor as  $rowContratante) {
        $documento=isset($rowContratante["NUM_DOC"])?$rowContratante["NUM_DOC"]:"";
        $sector=isset($rowContratante["SECTOR"])?$rowContratante["SECTOR"]:"";
        $actividad=isset($rowContratante["ACTIVIDAD"])?$rowContratante["ACTIVIDAD"]:"";
        $direccion=isset($rowContratante["DIRECCION"])?$rowContratante["DIRECCION"]:"";
        $correo=isset($rowContratante["CORREO"])?$rowContratante["CORREO"]:"";
        $telefono=isset($rowContratante["TELEFONO"])?$rowContratante["TELEFONO"]:"";
        
        $distrito=isset($rowContratante["DISTRITO"])?$rowContratante["DISTRITO"]:"";
        $provincia=isset($rowContratante["PROVINCIA"])?$rowContratante["PROVINCIA"]:"";
        $departamento=isset($rowContratante["DEPARTAMENTO"])?$rowContratante["DEPARTAMENTO"]:"";
        $importe=isset($rowContratante["CUANTIAORIGEN"])?$rowContratante["CUANTIAORIGEN"]:"0";
        $tipoMoenda=isset($rowContratante["TIPO_MONEDA"])?$rowContratante["TIPO_MONEDA"]:"";

        $estadCivil=isset($rowContratante["ESTADO_CIVIL"])?$rowContratante["ESTADO_CIVIL"]:"";
        $nacimiento=isset($rowContratante["FECHA_NACIMIENTO"])?$rowContratante["FECHA_NACIMIENTO"]:"";
        $profesion=isset($rowContratante["PROFESION"])?$rowContratante["PROFESION"]:"";
        $otraProfesion=isset($rowContratante["OTRAPROFESION"])?$rowContratante["OTRAPROFESION"]:"";

        $cargo=isset($rowContratante["CARGO"])?$rowContratante["CARGO"]:"";
        $nacionalidad=isset($rowContratante["PAIS"])?$rowContratante["PAIS"]:"";
        $condicion=isset($rowContratante["CONDICION"])?$rowContratante["CONDICION"]:"";
        $idinterviniente=isset($rowContratante["INTERVINIENTE"])?$rowContratante["INTERVINIENTE"]:"";

        $fechaFirma=isset($rowContratante["FECHAFIRMA"])?$rowContratante["FECHAFIRMA"]:"";
        $tipoDoc=isset($rowContratante["TIPO_DOC"])?$rowContratante["TIPO_DOC"]:"";
        

        $contratante="";
        if($rowContratante["IDTIPOPERSONA"]==1)
                  $contratante=$rowContratante["NOMBRE"]." ".$rowContratante["PRIMERAPELLIDO"]." ".$rowContratante["SEGUNDOAPELLIDO"];
                else if($rowContratante["IDTIPOPERSONA"]==2)
                  $contratante=$rowContratante["RAZONSOCIAL"];
        $idsujeto=isset($rowContratante["IDSUJETO"])?$rowContratante["IDSUJETO"]:"0";


              //AGREGAR CONTRATANTE AL ARRAY DE BANDERAS
        foreach ($listBanderas as $objBandera) {
            if(intval($objBandera["SUJETOID"])==intval($idsujeto))
            {
              $allContratantesBanderas[]=$rowContratante;
                break;
            }
        }

  //    var_dump($allContratantesBanderas);
      
      $xhtml.='<div class="TableCont">
        <div class="subtitulo">'.$condicion.'- '.$contratante.'('.$documento.') </div>';
      
      $xhtml.='<div class="Table">';
      $xhtml.='<div class="Row">
            <div class="Cell">
                <p>Documento</p>
            </div>
            <div class="Cell">
                <p>'.$tipoDoc.' - '.$documento.'</p>
            </div>
           
        </div>
        <div class="Row">
            <div class="Cell">
                <p>';
                if($rowContratante["IDTIPOPERSONA"]==1)
                    $xhtml.="Nombres y Apellidos";
                else if($rowContratante["IDTIPOPERSONA"]==2)
                    $xhtml.="Razón Social";

                $xhtml.='</p>
            </div>
            
            <div class="Cell">
                <p>';
                if($rowContratante["IDTIPOPERSONA"]==1)
                  $xhtml.=$rowContratante["NOMBRE"]." ".$rowContratante["PRIMERAPELLIDO"]." ".$rowContratante["SEGUNDOAPELLIDO"];
                else if($rowContratante["IDTIPOPERSONA"]==2)
                  $xhtml.=$rowContratante["RAZONSOCIAL"];
                else
                  $xhtml.="";
                $xhtml.='</p>
            </div>
        </div>';
         if($rowContratante["IDTIPOPERSONA"]==2){
        $xhtml.='
        <div class="Row">
            <div class="Cell">
                <p>Sector</p>
            </div>
            <div class="Cell">
                <p>'.$sector.'</p>
            </div>
        </div>';

        $xhtml.='
        <div class="Row">
            <div class="Cell">
                <p>Actividad</p>
            </div>
            <div class="Cell">
                <p>'.$actividad.'</p>
            </div>
        </div>';
      }
       if($rowContratante["IDTIPOPERSONA"]==1){
        $xhtml.='
        <div class="Row">
            <div class="Cell">
                <p>Estado Civil</p>
            </div>
            <div class="Cell">
                <p>'.$estadCivil.'</p>
            </div>

            <div class="Cell">
                <p>Fecha Nacimiento</p>
            </div>
            <div class="Cell">
                <p>'.$nacimiento.'</p>
            </div>
        </div>';

          $xhtml.='
        <div class="Row">
            <div class="Cell">
                <p>Profesión</p>
            </div>
            <div class="Cell">
                <p>'.$profesion.'</p>
            </div>

            <div class="Cell">
                <p>Otra Profesión</p>
            </div>
            <div class="Cell">
                <p>'.$otraProfesion.'</p>
            </div>

        </div>';


          $xhtml.='
        <div class="Row">
            <div class="Cell">
                <p>Cargo</p>
            </div>
            <div class="Cell">
                <p>'.$cargo.'</p>
            </div>

            <div class="Cell">
                <p>Nacionalidad</p>
            </div>
            <div class="Cell">
                <p>'.$nacionalidad.'</p>
            </div>

        </div>';

      }


          $xhtml.='
        <div class="Row">
            <div class="Cell">
                <p>Importe Participación</p>
            </div>
            <div class="Cell">
                <p>'.$importe." ".$tipoMoenda.'</p>
            </div>
        </div>';


          $xhtml.='
        <div class="Row">
            <div class="Cell">
                <p>Tipo de Persona</p>
            </div>
            <div class="Cell">
                <p>';
                  if($rowContratante["IDTIPOPERSONA"]==1)
                    $xhtml.="NATURAL";
                  else  if($rowContratante["IDTIPOPERSONA"]==2)
                      $xhtml.="JURIDICA";
                    else $xhtml.="";
                $xhtml.='</p>
            </div>
        </div>';


          $xhtml.='
        <div class="Row">
            <div class="Cell">
                <p>Dirección</p>
            </div>
            <div class="Cell">
                <p>'.$direccion.'</p>
            </div>
        </div>';

            $xhtml.='
        <div class="Row">
            <div class="Cell">
                <p>Ubicación</p>
            </div>
            <div class="Cell">
                <p>'.$distrito."/ ".$provincia." / ".$departamento.'</p>
            </div>
        </div>';

            $xhtml.='
        <div class="Row">
            <div class="Cell">
                <p>Correo</p>
            </div>
            <div class="Cell">
                <p>'.$correo.'</p>
            </div>

             <div class="Cell">
                <p>Telefono</p>
            </div>
            <div class="Cell">
                <p>'.$telefono.'</p>
            </div>

        </div>';


          $xhtml.='
        <div class="Row">
            <div class="Cell">
                <p>Fecha Firma</p>
            </div>
            <div class="Cell">
                <p>';
                  if($rowContratante["IDTIPOPERSONA"]==1)
                    $xhtml.=$fechaFirma;
                  else  if($rowContratante["IDTIPOPERSONA"]==2)
                      $xhtml.="";
                    else $xhtml.="";
                $xhtml.='</p>
            </div>
        </div>';


        if(sizeof($allRepresentantes)>0){
        foreach ($allRepresentantes as $objRepre) {

            foreach ($listBanderas as $objBandera) {
                if(intval($objBandera["SUJETOID"])==intval($objRepre["IDSUJETO"]))
                {
                  $allContratantesBanderas[]=$objRepre;
                    break;
                }
            }
          

          if(intval($objRepre["INTERVINIENTE"])==intval($idinterviniente))
          {
            $ListHojaCotejo->runValues($objRepre);

              $xhtml.='<div class="TableCont">';
              $xhtml.='<div class="subtitulo3">REPRESENTANTE - '.$ListHojaCotejo->getValorFormatString("CLIENTE").'</div>';
              $xhtml.='<div class="Table">';
                  $xhtml.='<div class="Row">
                            <div class="Cell">
                                <p>Documento</p>
                            </div>
                            <div class="Cell">
                                <p>'.$ListHojaCotejo->getValorFormatString("NUM_DOC").'</p>
                            </div>
                           </div>
                            ';  

                  $xhtml.='<div class="Row">
                            <div class="Cell">
                                <p>Nombres y Apellidos</p>
                            </div>
                            <div class="Cell">
                                <p>'.$ListHojaCotejo->getValorFormatString("CLIENTE").'</p>
                            </div>
                           </div>
                            ';
                     $xhtml.='
                      <div class="Row">
                          <div class="Cell">
                              <p>Estado Civil</p>
                          </div>
                          <div class="Cell">
                              <p>'.$ListHojaCotejo->getValorFormatString("ESTADO_CIVIL").'</p>
                          </div>

                          <div class="Cell">
                              <p>Fecha Nacimiento</p>
                          </div>
                          <div class="Cell">
                              <p>'.$ListHojaCotejo->getValorFormatString("FECHA_NACIMIENTO").'</p>
                          </div>
                      </div>';

                  $xhtml.='
                      <div class="Row">
                          <div class="Cell">
                              <p>Profesión</p>
                          </div>
                          <div class="Cell">
                              <p>'.$ListHojaCotejo->getValorFormatString("PROFESION").'</p>
                          </div>

                          <div class="Cell">
                              <p>Otra Profesión</p>
                          </div>
                          <div class="Cell">
                              <p>'.$ListHojaCotejo->getValorFormatString("OTRAPROFESION").'</p>
                          </div>
                      </div>';


                  $xhtml.='
                      <div class="Row">
                          <div class="Cell">
                              <p>Cargo</p>
                          </div>
                          <div class="Cell">
                              <p>'.$ListHojaCotejo->getValorFormatString("CARGO").'</p>
                          </div>

                          <div class="Cell">
                              <p>Nacionalidad</p>
                          </div>
                          <div class="Cell">
                              <p>'.$ListHojaCotejo->getValorFormatString("PAIS").'</p>
                          </div>
                      </div>';

                    $xhtml.='
                          <div class="Row">
                              <div class="Cell">
                                  <p>Dirección</p>
                              </div>
                              <div class="Cell">
                                 <p>'.$ListHojaCotejo->getValorFormatString("DIRECCION").'</p>
                              </div>
                          </div>';

                              $xhtml.='
                          <div class="Row">
                              <div class="Cell">
                                  <p>Ubicación</p>
                              </div>
                              <div class="Cell">
                                  <p>'.$ListHojaCotejo->getValorFormatString("DISTRITO")."/ ".$ListHojaCotejo->getValorFormatString("PROVINCIA")." / ".$ListHojaCotejo->getValorFormatString("DEPARTAMENTO").'</p>
                              </div>
                          </div>';

                      $xhtml.='
                          <div class="Row">
                              <div class="Cell">
                                  <p>Correo</p>
                              </div>
                              <div class="Cell">
                                   <p>'.$ListHojaCotejo->getValorFormatString("CORREO").'</p>
                              </div>

                               <div class="Cell">
                                  <p>Telefono</p>
                              </div>
                              <div class="Cell">
                                  <p>'.$ListHojaCotejo->getValorFormatString("TELEFONO").'</p>
                              </div>

                          </div>';
                  
           
              $xhtml.='</div>';
              $xhtml.='</div>';


          }
          
        }
      }



    $xhtml.="<br>";


  $xhtml.='  </div>'; 

    




$xhtml.='</div> ';
          }

    }
}

//var_dump($allContratantesBanderas);
if(sizeof($allContratantesBanderas)>0)
{
  foreach ($allContratantesBanderas as $objContrBandera) {

    $xhtml.="<fieldset>";
      $xhtml.="<legend class='subtitulo4'>";

      $xhtml.="".$objContrBandera["NOMBRE"]." ".$objContrBandera["PRIMERAPELLIDO"]." ".$objContrBandera["SEGUNDOAPELLIDO"]."";
      $xhtml.="</legend>";

      $xhtml.='<table>';

       $xhtml.="<tr class='subtitulo3'>";
            $xhtml.="<td>Bandera</td>";
            $xhtml.="<td>Porcentaje</td>";
            $xhtml.="<td>Lista Origen</td>";
          $xhtml.="</tr>";


      foreach ($listBanderas as  $objBandera) {
          if(intval($objBandera["SUJETOID"])==intval($objContrBandera["IDSUJETO"])){
              $xhtml.="<tr>";
                $xhtml.="<td>".$objBandera["BANDERA"]."</td>";
               $xhtml.="<td>".$objBandera["PORCENTAJE"]."%"."</td>";
               $xhtml.="<td>".$objBandera["LISTA"]."</td>";
              $xhtml.="</tr>";
          }
        }
       $xhtml.="</table>";
  
       $xhtml.="</fieldset>";

  }
}

/*
 $listBanderas=$ListHojaCotejo->getBanderasPorSujeto($iddocumento);
  
    if(sizeof($listBanderas)>0){
       $xhtml.='<div class="TableCont">';
      $xhtml.='<div class="Table">';

      $xhtml.='<div class="Row">
                  <div class="Cell">
                      <p class="subtitulo2">BANDERAS</p>
                  </div>
                  <div class="Cell">
                      <p class="subtitulo2">LISTA</p>
                  </div>

                </div>';
foreach ($listBanderas as $objBandera) {
  $bandera=isset($objBandera["BANDERA"])?trim($objBandera["BANDERA"]):"";
  $porcentaje=isset($objBandera["PORCENTAJE"])?trim($objBandera["PORCENTAJE"]):"";
  $lista=isset($objBandera["LISTA"])?trim($objBandera["LISTA"]):"";
  $delito=isset($objBandera["DELITO"])?trim($objBandera["DELITO"]):"";
  
      $xhtml.='<div class="Row">
                      <div class="Cell">
                          <p class="sublabel">'.$bandera.'</p>
                      </div>
                      <div class="Cell">
                          <p class="sublabel">';
                       

                          $xhtml.='</p>
                      </div>
                      
                    
                </div>';
    }



$xhtml.='               </div>
               </div>
          ';
    }*/


$xhtml.='


</body>
</html>
    ';

    return $xhtml;
  }
}