<?php 

class HttmlAlertaInfoPDF 
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
        LAFT NOTARIA - '.strtoupper($allNotaria["notaria"]).'
      </div>


        <div class="panelalerta">
          <table class="sublabel" cellpadding="8">
            <tr>
                <td class="label">Notaría</td>
                <td>'.$allNotaria["notaria"].'</td>

                <td class="label">Colegio</td>
                <td>'.$allNotaria["colegio"].'</td>
            </tr>

            <tr>
                <td class="label">Fecha de Instr.</td>
                <td>'.$allNotaria["fechaautorizacion"].'</td>

                <td class="label">Tipo de Instr.</td>
                <td>'.$allNotaria["tipoinstrumento"].'</td>
            </tr>
            

            <tr>
                <td class="label">Número de Instrumento</td>
                <td>'.$allNotaria["numeroinstrumento"].'</td>

                <td class="label">Número de Kardex</td>
                <td>'.$allNotaria["numerodekardex"].'</td>
            </tr>
            

            </table>

        </div>


  <div class="panelalerta">
      <div class="alertas">PERSONA IDENTIFICADA</div>';

 foreach ($allContratante as $key => $vc) {
$xhtml.='    <table class="sublabel" cellpadding="8">
            <tr>
                <td class="label">Tipo de Doc.</td>
                <td>'.$vc["tipodoc"].'</td>

                <td class="label">Nro. Documento</td>
                <td>'.$vc["numerodocumento"].'</td>

                <td class="label">ROL UIF</td>
                <td>'.$vc["participacion"].'</td>
           </tr>

            <tr>
                <td class="label">Contratante</td>
                <td colspan="4">'.$vc["primerapellido"]." ".$vc["segundoapellido"]." ".$vc["nombre"].'</td>
            </tr>
            </table>';
}
$xhtml.='  </div>';


$xhtml.='</div>';








$xhtml.='<div class="panelalerta">
    <div class="alertas">ALERTAS POR ACTO</div>';
       $xhtml.='<table cellpadding="5">';
      $xhtml.='<tr>';
      $xhtml.='<td colspan="3" class="subtitulo">ALERTAS IDENTIFICADAS POR OPERACIONES RELACIONADAS CON EL ACTO</td>';
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