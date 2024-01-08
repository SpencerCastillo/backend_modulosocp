<?php 


require 'libs/dompdf/vendor/autoload.php'
;
// reference the Dompdf namespace
use Dompdf\Dompdf;

class Models_AlertaInfoPDF extends DB_Connect {
    

    private $DIR_BASE="C:/FILES_ELECTNOTARIAL/";

    /**
     * Constructor de clase
     */
    function my_status_header($setHeader=null) {
        static $theHeader=null;
        //if we already set it, then return what we set before (can't set it twice anyway)
        if($theHeader) {return $theHeader;}
        $theHeader = $setHeader;
        header('HTTP/1.1 '.$setHeader);
        return $setHeader;
    } 
    

    public function verdocumento($data)
    {
      $info=json_decode($data);

      $idDocumento=isset($info->iddocumento)?$info->iddocumento:"0";
      $tipo=isset($info->tipo)?$info->tipo:"";
      

    $dompdf = new Dompdf();
    $dompdf->setPaper('A4');

    $dompdf->set_option('defaultMediaType', 'all');
    $dompdf->set_option('isFontSubsettingEnabled', true);
    $db=$this->connect();
    if($tipo=="ir")
      $texto=$this->getHtmlPdfInformeRiesgo($db,$idDocumento,$info);
    else
      $texto=$this->getHtmlPdfInformacion($db,$idDocumento,$info);
      
      $dompdf->loadHtml($texto);
      $dompdf->render();
      //$dompdf->stream($filename);
      $contenido=$dompdf->output();
     // $this->guardarDocumentoActa($contenido,$db);
      header('Content-Length: '.strlen($contenido));
      header("Content-type: application/pdf");
      header("Content-Disposition: inline; filename=alertadetalle.pdf");
      header('Cache-Control: public, must-revalidate, max-age=0');
      header('Pragma: public');
      echo $contenido;

    }


 private function getHtmlPdfInformacion($db,$id,$data)
  {

    include 'HttmlAlertaInfoPDF.php';
    $d=new HttmlAlertaInfoPDF;
    
    return $d->getHtmlPdfInformacion($db,$id,$data);

  }


 private function getHtmlPdfInformeRiesgo($db,$id,$data)
  {

    include 'HtmlInformeLaftRiesgoPDF.php';
    $d=new HtmlInformeLaftRiesgoPDF;
    
    return $d->getHtmlPdfInformacion($db,$id,$data);

  }


         //Obtiene la IP del cliente
   private function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    private function getPlatform() {
      $user_agent = $_SERVER['HTTP_USER_AGENT'];
       $plataformas = array(
      'Windows 10' => 'Windows NT 10.0+',
      'Windows 8.1' => 'Windows NT 6.3+',
      'Windows 8' => 'Windows NT 6.2+',
      'Windows 7' => 'Windows NT 6.1+',
      'Windows Vista' => 'Windows NT 6.0+',
      'Windows XP' => 'Windows NT 5.1+',
      'Windows 2003' => 'Windows NT 5.2+',
      'Windows' => 'Windows otros',
      'iPhone' => 'iPhone',
      'iPad' => 'iPad',
      'Mac OS X' => '(Mac OS X+)|(CFNetwork+)',
      'Mac otros' => 'Macintosh',
      'Android' => 'Android',
      'BlackBerry' => 'BlackBerry',
      'Linux' => 'Linux',
   );
   foreach($plataformas as $plataforma=>$pattern){
      if (preg_match('/(?i)'.$pattern.'/', $user_agent))
         return $plataforma;
   }
   return 'Otras';
  }



private function getBrowser(){
$user_agent = $_SERVER['HTTP_USER_AGENT'];
if(strpos($user_agent, 'MSIE') !== FALSE)
   return 'Internet explorer';
 elseif(strpos($user_agent, 'Edge') !== FALSE) //Microsoft Edge
   return 'Microsoft Edge';
 elseif(strpos($user_agent, 'Trident') !== FALSE) //IE 11
    return 'Internet explorer';
 elseif(strpos($user_agent, 'Opera Mini') !== FALSE)
   return "Opera Mini";
 elseif(strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== FALSE)
   return "Opera";
 elseif(strpos($user_agent, 'Firefox') !== FALSE)
   return 'Mozilla Firefox';
 elseif(strpos($user_agent, 'Chrome') !== FALSE)
   return 'Google Chrome';
 elseif(strpos($user_agent, 'Safari') !== FALSE)
   return "Safari";
 else
   return 'No hemos podido detectar su navegador';
}

private function getFileNameSolicitud()
{

     $nombreNotaria="PRUEBA";
             if(isset($_SESSION["USER_ELECT_NOT"]))
                $nombreNotaria=$_SESSION["USER_ELECT_NOT"];
                $nombreNotaria=trim($nombreNotaria);
                $nombreNotaria=str_replace(" ","_",$nombreNotaria);
                $dir_base=$this->DIR_BASE.$this->eliminar_acentos($nombreNotaria);
                   if(!file_exists($dir_base))
                   {
                      if(!mkdir($dir_base, 0777, true))
                           die('Fallo al crear las carpetas NOTARIA...');
                      }
                      $dir_solicitud=$dir_base."/SOLICITUD";
                      if(!file_exists($dir_solicitud))
                      {
                          if(!mkdir($dir_solicitud, 0777, true))
                              die('Fallo al crear las carpetas SOLICITUD...');
                      }
                $filePath=$dir_solicitud."/".uniqid().".pdf";
                return $filePath;
}
    public function add($post_data)
    { 
        $db=$this->connect();
        $data=$post_data;
        
     date_default_timezone_set('America/Lima');
     $ippublic=  $this->get_client_ip();
     $sistemao=$this->getPlatform();
     $navegador=$this->getBrowser();
        $idpeticion=$post_data->idpeticion;
        $idusuario=$GLOBALS["lid"];
        
        $sqlupdate="UPDATE OCPREPORTE.RECEPCION_SOLICITUD_NOTARIA 
           set ACTIVO=0
           WHERE IDCIRCULAR=:idpeticion 
        ";
        $stmt=oci_parse($db,$sqlupdate);
        oci_bind_by_name($stmt,":idpeticion",$idpeticion);
        oci_execute($stmt);

        $sql="INSERT INTO OCPREPORTE.RECEPCION_SOLICITUD_NOTARIA (IDCIRCULAR,FECHAREGISTRO,IDUSUARIOREGISTRO,ESTADO)
            VALUES  (:idpeticion,current_date,:idusuario,:estado)
        ";
        $stmt=oci_parse($db,$sql);
        oci_bind_by_name($stmt,":idpeticion",$idpeticion);
        oci_bind_by_name($stmt,":idusuario",$idusuario);
        oci_bind_by_name($stmt,":estado",$estado);

        oci_execute($stmt);
        oci_free_statement($stmt);

        oci_close($db);
        $response = array('status' =>1 ,'msg'=>"correcto" );
        return ($response);
    }


        private function eliminar_acentos($cadena){
        //Reemplazamos la A y a
        $cadena = str_replace(
        array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
        array('A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A'),
        $cadena
        );
 
        //Reemplazamos la E y e
        $cadena = str_replace(
        array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
        array('E', 'E', 'E', 'E', 'E', 'E', 'E', 'E'),
        $cadena );
 
        //Reemplazamos la I y i
        $cadena = str_replace(
        array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
        array('I', 'I', 'I', 'I', 'I', 'I', 'I', 'I'),
        $cadena );
 
        //Reemplazamos la O y o
        $cadena = str_replace(
        array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
        array('O', 'O', 'O', 'O', 'O', 'O', 'O', 'O'),
        $cadena );
 
        //Reemplazamos la U y u
        $cadena = str_replace(
        array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
        array('U', 'U', 'U', 'U', 'U', 'U', 'U', 'U'),
        $cadena );
 
        //Reemplazamos la N, n, C y c
        $cadena = str_replace(
        array('Ç', 'ç','ñ','Ñ'),
        array('C', 'C','N','N'),
        $cadena
        );
        
        return $cadena;
    }

}
?>