<?php 


class HtmlInformeLaftPDF 
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
        FICHA DE EVALUACIÓN - OPERACIONES INUSUALES
      </div>


        <div class="panelalerta">
        <div class="alertas">SECCIÓN I: DATOS DE IDENTIFICACIÓN</div>

          <table class="sublabel" cellpadding="8">
            <tr>
                <td class="label">1. Notaría</td>
                <td>'.$allNotaria["notaria"].'</td>
            </tr>
            <tr>
                <td class="label">2. Colegio</td>
                <td>'.$allNotaria["colegio"].'</td>
            </tr>

            </table>

        </div>


  <div class="panelalerta">
      <div class="alertas">SECCIÓN II: DATOS DE IDENTIFICACIÓN DE LA OPERACIÓN INUSUAL</div>
      <table class="sublabel" cellpadding="8">
          <tr>
              <td class="label">3. Número de operación inusual</td>
              <td>00009</td>
          </tr>
      </table>
  </div>';


$xhtml.='<div class="panelalerta">
    <div class="alertas">SECCIÓN III: DATOS DE IDENTIFICACIÓN DE LAS PERSONAS INVOLUCRADAS EN LA OPERACIÓN INUSUAL</div>';

    foreach ($allContratante as $key => $vc) {

      $xhtml.='<table  cellpadding="5">';

      $xhtml.='<tr>';
      $xhtml.='<td colspan="2" class="subtitulo">'.$vc["participacion"].'</td>';
      $xhtml.='<td colspan="2" class="subtitulo">'.$vc["primerapellido"]." ".$vc["segundoapellido"]." ".$vc["nombre"].'</td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel"> 5. Tipo Persona</td>';
      $xhtml.='<td>'.$vc["tipopersona"].'</td>';
      $xhtml.='<td class="sublabel">6. Tipo de Documento</td>';
      $xhtml.='<td>'.$vc["tipodoc"].'</td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel"> 7. Número de Documento</td>';
      $xhtml.='<td>'.$vc["numerodocumento"].'</td>';
      $xhtml.='<td class="sublabel">8. Condición de Residencia</td>';
      $xhtml.='<td>---</td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel"> 9. Es PEP.*</td>';
      $xhtml.='<td>'.($vc["espep"]=="1"?"SI":"NO").'</td>';
      $xhtml.='<td class="sublabel">10. Cargo Público de ser PEP´s</td>';
      $xhtml.='<td>---</td>';
      $xhtml.='</tr>';


      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel"> 11. Apellido Paterno o Razón Social</td>';
      $xhtml.='<td>'.$vc["primerapellido"].'</td>';
      $xhtml.='<td class="sublabel"> 12. Apellido Materno</td>';
      $xhtml.='<td>'.$vc["segundoapellido"].'</td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel"> 13. Nombres</td>';
      $xhtml.='<td>'.$vc["nombre"].'</td>';
      $xhtml.='<td class="sublabel"> 14. Nacionalidad</td>';
      $xhtml.='<td>'.$vc["pais"].'</td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">  15. Fecha de Nacimiento</td>';
      $xhtml.='<td>'.$vc["fechanacimiento"].'</td>';
      $xhtml.='<td class="sublabel"> 16. Ocupación</td>';
      $xhtml.='<td>'.$vc["profesion"].'</td>';
      $xhtml.='</tr>';

        $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">  17. Ocupación (otros)</td>';
      $xhtml.='<td>'.$vc["otraprofesion"].'</td>';
      $xhtml.='<td class="sublabel"> 18. Actividad Económica</td>';
      $xhtml.='<td>'.$vc["sector"].'</td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">  19. Cargo (otros)</td>';
      $xhtml.='<td>'.$vc["cargo"].'</td>';
      $xhtml.='<td class="sublabel">  20. Domicilio</td>';
      $xhtml.='<td>'.$vc["domicilio"].'</td>';
      $xhtml.='</tr>';


      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">  21. Departamento | Provincia | Distrito)</td>';
      $xhtml.='<td>'.$vc["departamento"]." | ".$vc["provincia"]." | ".$vc["distrito"].'</td>';
      $xhtml.='<td class="sublabel">  22. Teléfono</td>';
      $xhtml.='<td>'.$vc["telefono"].'</td>';
      $xhtml.='</tr>';

    $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">  23. Condición en la operación inusual (reportado o relacionado)</td>';
      $xhtml.='<td>'.($vc["vinculado"]=="1"?"Reportado":"Relacionado").'</td>';
      $xhtml.='<td class="sublabel"> 24. Descripción de su participación en el acto</td>';
      $xhtml.='<td>'.$vc["participacion"].'</td>';
      $xhtml.='</tr>';

      $xhtml.='</table>
      <br>
      ';
    $xhtml.='';
      }
$xhtml.='</div>';






$xhtml.='<div class="panelalerta">
    <div class="alertas">SECCIÓN IV: DATOS RELACIONADOS A LA DESCRIPCIÓN DE LA OPERACIÓN INUSUAL</div>';

    foreach ($allActo as $key => $va) {

      $xhtml.='<table cellpadding="5">';

      $xhtml.='<tr>';
      $xhtml.='<td colspan="1" class="subtitulo">ACTO JURÍDICO</td>';
      $xhtml.='<td colspan="3" class="subtitulo">'.$va["acto"].'</td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel"> 25. Tipo de fondos, bienes que se realizó la operación</td>';
      $xhtml.='<td></td>';
      $xhtml.='<td></td>';
      $xhtml.='<td></td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">26. Tipo de operación</td>';
      $xhtml.='<td colspan="3">'.$va["acto"].'</td>';
      $xhtml.='</tr>';



      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">27. Descripción del tipo de operación en caso "Otros"</td>';
      $xhtml.='<td>---</td>';
      $xhtml.='<td class="sublabel">28. N° del instrumento público protocolar</td>';
      $xhtml.='<td>'.$va["numeroinstrumento"].'</td>';
      $xhtml.='</tr>';

      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel"> 29. Origen de fondos</td>';
      $xhtml.='<td></td>';
      $xhtml.='<td></td>';
      $xhtml.='<td></td>';
      $xhtml.='</tr>';



      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel"> 30. Moneda de la operación</td>';
      $xhtml.='<td>'.$va["tipomoneda"].'</td>';
      $xhtml.='<td class="sublabel">31. Descripción en caso sea "Otra"</td>';
      $xhtml.='<td>---</td>';
      $xhtml.='</tr>';


      $xhtml.='<tr>';
      $xhtml.='<td class="sublabel">32. Monto total de la operación</td>';
      $xhtml.='<td>'.$va["cuantia"].'</td>';
      $xhtml.='<td class="sublabel">33. Tipo de cambio</td>';
      $xhtml.='<td>---</td>';
      $xhtml.='</tr>';


     $xhtml.='<tr>';
      $xhtml.='<td class="sublabel" colspan="2"> 34. Descripción breve de la operación (señale agumentos que lo llevaron a
                    calificar como
                    inusual la operación)</td>';
      $xhtml.='<td colspan="2">'.$va["comentario_laft"].'</td>';
   
      $xhtml.='</tr>';

      $xhtml.='</table>
      <br>
      ';
    $xhtml.='';
      }
$xhtml.='</div>';






$xhtml.='<div class="panelalerta">
    <div class="alertas">SECCIÓN V: SEÑALES DE ALERTA IDENTIFICACIÓN</div>';
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




      $xhtml.="</body>";
      
      $xhtml.="</html>";
        

    return $xhtml;
  }
}