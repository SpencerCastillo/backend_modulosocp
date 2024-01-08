<?php 
class Models_HtmlInformeLaftRiesgoPDF 
{

public function getHtmlPdfInformacion($db,$id,$data)
  {
      $laftNotario =new Models_LaftNotario();

      $allDocumento=$laftNotario->getListDocumentoPorAlerta($id);


      $allNotaria=$allDocumento[0];
      $allContratante=$allDocumento[1];
      $allActo=$allDocumento[2];
      $allAlertaContratante=$allDocumento[4];
      $allAlertaActo=$allDocumento[3];





      $xhtml="<html>";
      $xhtml="<head>";
      $xhtml='<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>';

      $xhtml="<style>";
      $xhtml.="
            .panel {
      background-color: #004e6f;
      color: white;
      padding: 12px;
      font-weight: bold;
  }


.subtitulo {
    background-color: #d2dfe6;
    font-weight: bold;
    color: #004e6f;
    padding: 12px;
}

.texto {
    color: #004e6f;
    font-size: 16px;
    margin-bottom: 20px;
}

.panelalerta {
    border-width: 2px;
    padding: 5px;
    border-style: groove;
}



.alertas {
    background-color: #d9b37d;
    color: #004e6f;
    font-weight: bold;
    padding: 12px;
}

.label {
    font-weight: bold;
    color: #004e6f;
}

.sublabel {
    color: #004e6f;
}


      ";
      $xhtml.="</style>";

      $xhtml.="</head>";
      $xhtml.="<body>";


      $xhtml.='
      <div class="panel">
        INFORME DE RIESGO DEL NOTARIO
      </div>


        <div class="panelalerta">
        <div class="alertas">
          I. INICIO
        </div>

          <table class="sublabel" cellpadding="8">
            <tr>
                <td class="label">Tipo de Sujeto Obligado</td>
                <td>Oficial de Cumplimiento</td>
            </tr>
            <tr>
                <td class="label">Colegio</td>
                <td>'.$allNotaria["colegio"].'</td>
            </tr>
            <tr>
                <td class="label">Notaría</td>
                <td>'.$allNotaria["notaria"].'</td>
            </tr>
            </table>

        </div>


  <div class="panelalerta">
      <div class="alertas">II. INFORMACIÓN BÁSICA</div>
      <table class="sublabel" cellpadding="8">
          <tr>
              <td class="label">Número de operación inusual</td>
              <td>00009</td>
          </tr>

           <tr>
              <td class="label">Señales de alerta identificadas (del reportado), Tipo de Señal:</td>
              <td>Informe de Riesgo</td>
          </tr>

      </table>';




    $xhtml.='<table cellpadding="5">';
      $xhtml.='<tr>';
      $xhtml.='<td colspan="3" class="subtitulo"> ALERTAS IDENTIFICADAS POR CLIENTE</td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">35. Código de la señal de alerta</td>';
      $xhtml.='<td class="sublabel">36. Descripción de la señal de alerta</td>';
      $xhtml.='<td class="sublabel">37. Fuente de la señal de alerta</td>';
      $xhtml.='</tr>';
      

    foreach ($allAlertaContratante as $key => $ac) {
          $xhtml.='<tr>';
      $xhtml.='<td>'.$ac["id"].'</td>';
      $xhtml.='<td>'.$ac["alerta"].'</td>';
      $xhtml.='<td>Información obtenida en trámite</td>';
      $xhtml.='</tr>';
      }
      $xhtml.='</table>';


       $xhtml.='<table cellpadding="5">';
      $xhtml.='<tr>';
      $xhtml.='<td colspan="3" class="subtitulo">ALERTAS IDENTIFICADAS POR OPERACIONES RELACIONADAS CON EL ACTO</td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">35. Código de la señal de alerta</td>';
      $xhtml.='<td class="sublabel">36. Descripción de la señal de alerta</td>';
      $xhtml.='<td class="sublabel">37. Fuente de la señal de alerta</td>';
      $xhtml.='</tr>';
      

    foreach ($allAlertaActo as $key => $aa) {
          $xhtml.='<tr>';
      $xhtml.='<td>'.$aa["id"].'</td>';
      $xhtml.='<td>'.$aa["alerta"].'</td>';
      $xhtml.='<td>Información obtenida en trámite</td>';
      $xhtml.='</tr>';
      }
      $xhtml.='</table>';
$xhtml.='</div>';


$xhtml.='<div class="panelalerta">
    <div class="alertas">III. PERSONAS INVOLUCRADAS</div>';

    foreach ($allContratante as $key => $vc) {

      $xhtml.='<table  cellpadding="5">';


      $xhtml.='<tr>';
      $xhtml.='<td class="subtitulo">'.$vc["participacion"].'</td>';
      $xhtml.='<td class="subtitulo">'.$vc["primerapellido"]." ".$vc["segundoapellido"]." ".$vc["nombre"].'</td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Tipo Persona</td>';
      $xhtml.='<td>'.$vc["tipopersona"].'</td>';
      $xhtml.='</tr>';

      if($vc["idtipopersona"]==1){
      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Apellido Paterno</td>';
      $xhtml.='<td>'.$vc["primerapellido"].'</td>';
      $xhtml.='</tr>';
      }else if($vc["idtipopersona"]==2)
      {
        $xhtml.='<tr>';
        $xhtml.='<td class="sublabel">Denominación o Razón social</td>';
        $xhtml.='<td>'.$vc["primerapellido"].'</td>';
        $xhtml.='</tr>';
      }
      if($vc["idtipopersona"]==1){
        $xhtml.='<tr>';
        $xhtml.='<td class="sublabel">Apellido Materno</td>';
        $xhtml.='<td>'.$vc["segundoapellido"].'</td>';
        $xhtml.='</tr>';
      }
  
    if($vc["idtipopersona"]==1){
      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Nombres</td>';
      $xhtml.='<td>'.$vc["nombre"].'</td>';
      $xhtml.='</tr>';
    
      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Fecha de nacimiento</td>';
      $xhtml.='<td>'.$vc["fechanacimiento"].'</td>';
      $xhtml.='</tr>';


      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Nacionalidad</td>';
      $xhtml.='<td>'.$vc["pais"].'</td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Es PEP.*</td>';
      $xhtml.='<td>'.($vc["espep"]=="1"?"SI":"NO").'</td>';
      $xhtml.='</tr>';
     }

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Tipo de Documento</td>';
      $xhtml.='<td>'.$vc["tipodoc"].'</td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Número de Documento</td>';
      $xhtml.='<td>'.$vc["numerodocumento"].'</td>';
      $xhtml.='</tr>';


      if($vc["idtipopersona"]==1){
      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">País emisión documento</td>';
      $xhtml.='<td>'.$vc["pais"].'</td>';
      $xhtml.='</tr>';


      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">País emisión documento</td>';
      $xhtml.='<td>'.$vc["pais"].'</td>';
      $xhtml.='</tr>';


      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Profesión/Ocupación
</td>';
      $xhtml.='<td>'.$vc["profesion"].'</td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Correo electrónico

</td>';
      $xhtml.='<td>'.$vc["correo"].'</td>';
      $xhtml.='</tr>';


      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Nombre Empleador
</td>';
      $xhtml.='<td></td>';
      $xhtml.='</tr>';

        }


        if($vc["idtipopersona"]==1){
        $xhtml.='<tr>';
        $xhtml.='<td class="sublabel">Condición
  </td>';
        $xhtml.='<td>'.($vc["vinculado"]=="1"?"REPORTADO":"RELACIONADO").'</td>';
        $xhtml.='</tr>';

        $xhtml.='<tr>';
        $xhtml.='<td class="sublabel">Domicilio
  </td>';
        $xhtml.='<td>'.$vc["domicilio"].'</td>';
        $xhtml.='</tr>';
      }

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">País
</td>';
      $xhtml.='<td>'.$vc["pais"].'</td>';
      $xhtml.='</tr>';

       $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Departamento
</td>';
      $xhtml.='<td>'.$vc["departamento"].'</td>';
      $xhtml.='</tr>';

       $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Provincia
</td>';
      $xhtml.='<td>'.$vc["provincia"].'</td>';
      $xhtml.='</tr>';

       $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Distrito
</td>';
      $xhtml.='<td>'.$vc["distrito"].'</td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Calle, Avenida, vía y número</td>';
      $xhtml.='<td>'.$vc["domicilio"].'</td>';
      $xhtml.='</tr>';


        if($vc["idtipopersona"]==2){
        $xhtml.='<tr>';
        $xhtml.='<td class="sublabel">Condición
  </td>';
        $xhtml.='<td>'.($vc["vinculado"]=="1"?"REPORTADO":"RELACIONADO").'</td>';
        $xhtml.='</tr>';
       }

      $xhtml.='</table>
      <br>
      ';
    $xhtml.='';
      }
$xhtml.='</div>';


$xhtml.='<div class="panelalerta">
    <div class="alertas">IV: OPERACIONES y PRODUCTOS</div>';

    foreach ($allActo as $key => $va) {

      $xhtml.='<table cellpadding="5">';
      $xhtml.='<tr>';
      $xhtml.='<td  class="subtitulo">ACTO JURÍDICO</td>';
      $xhtml.='<td  class="subtitulo">'.$va["acto"].'</td>';
      $xhtml.='</tr>';
         $xhtml.='<tr>';
      $xhtml.='<td class="sublabel"> Moneda</td>';
      $xhtml.='<td>'.$va["tipomoneda"].'</td>';
    
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Fecha Instrumento</td>';
      $xhtml.='<td>'.$va["fechautorizacion"].'</td>';
      $xhtml.='</tr>';



      $xhtml.='<tr>';
      $xhtml.='<td  class="sublabel">Producto (nombre de acto)</td>';
      $xhtml.='<td>'.$va["acto"].'</td>';
      $xhtml.='</tr>';

          $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">Monto</td>';
      $xhtml.='<td>'.$va["cuantia"].'</td>';
      $xhtml.='</tr>';


      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">N° del producto</td>';
      $xhtml.='<td>'.$va["numeroinstrumento"].'</td>';
      $xhtml.='</tr>';

      $xhtml.='</table>
      <br>
      ';
    $xhtml.='';
      }
$xhtml.='</div>';



  
$xhtml.='</div>';



      $xhtml.="</body>";
      
      $xhtml.="</html>";
        

    return $xhtml;
  }
}