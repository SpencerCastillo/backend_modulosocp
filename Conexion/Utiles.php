<?php 
class Utiles{

 const DIR_BASE="C:/FILES_ELECTNOTARIAL/";

	public  function getTokenValidation()
	{
		if(!class_exists('jwt') ) 
			include("libs/jwt.php");

		if(!class_exists('Clvs') ) 
		    require_once 'Clvs.php';

		$headers = apache_request_headers();

		$authorization="";
		if(isset($headers["Authorization"]))			
		    $authorization=$headers["Authorization"];
	    
	    if(!isset($_SESSION[NAME_TOKEN]))
	    {
	    		echo json_encode(array('status' =>"401" ,"msg"=>"Fuera de Sesión" ));
				die();
	    }

	  
	    $response="";
		$token=$_SESSION[NAME_TOKEN];
		$sessuuid=$_SESSION["UUID"];
		$ckkie=$_COOKIE["CK".NAME_TOKEN];
		if($authorization!=$token)
		{
				echo json_encode(array('status' =>"401" ,"msg"=>"No Autorizado, Inicie Sessión" ));
				die();
		}
	
		
		if($token!=$ckkie)
		{
			echo json_encode(array('status' =>"401" ,"msg"=>"::No Autorizado, Inicie Sessión::"));
				die();
		}

		if($ckkie!=$authorization)
		{
			echo json_encode(array('status' =>"401" ,"msg"=>"..::No Autorizado, Inicie Sessión::.."));
				die();
		}


		try{
			$get_values = JWT::decode($token, KEY_SECRET, array('HS256'));
			$payload=$get_values->payload;
			$xuuid=($payload->UUID);
			if($xuuid!=$sessuuid){
				echo json_encode(array('status' =>"401" ,"msg"=>"Código no Válido" ));
				die();
			}
		}catch (Exception $e)
		{
			if($e->getMessage() == "Expired token"){
				echo json_encode(array('status' =>"419" ,"msg"=>"Código Expirado" ));
				die();
			}else{
				echo json_encode(array('status' =>"400" ,"msg"=>"Error interno de Código" ));
				die();
			}
		}
	}


	public static function getCopiaVerificable($circuitid,$id,$estado,$tipo,$token)
{
	$_SESSION["USER_ELECT_NOT"]="PRUEBA";
	
	$estadoCircuitoId=$estado;

	if($estadoCircuitoId=="2"){
//		die("yes");
	$nombreNotaria=$_SESSION["USER_ELECT_NOT"];
	$nombreNotaria=trim($nombreNotaria);
    $nombreNotaria=str_replace(" ","_",$nombreNotaria);
	$dir_base=self::DIR_BASE.self::eliminar_acentos($nombreNotaria);
	 if(!file_exists($dir_base))
                    {
                          if(!mkdir($dir_base, 0777, true))
                              die('Fallo al crear las carpetas NOTARIA...');
                     }
    $ltipoCarpeta="";
    if($tipo=="CERTIFIED_COPY")
    	$ltipoCarpeta="CopiaVerificable_";
    else if($tipo=="SIGNED")
    	$ltipoCarpeta="DocumentoFirmado_";

	$files=self::DIR_BASE.self::eliminar_acentos($nombreNotaria)."/".$ltipoCarpeta.$circuitid.".zip";
	$contents="-1";
	if(isset($_SESSION["USER_ELECT_NOT"]) && $_SESSION["USER_ELECT_NOT"]!="")
	{

		$zip = new ZipArchive();
		
		if(file_exists($files))
		{	
			$zip->open($files);
			for($i = 0; $i < $zip->numFiles; $i++) 
			{
				if($zip->getNameIndex($i)=="docu_".$id.".pdf")
				{
						$fp = $zip->getStream($zip->getNameIndex($i));
				        if(!$fp) exit("failed\n");
				        $i=0;
				        while (!feof($fp)) {
				            $contents .= fread($fp,2);
				           
				            //$encoded = chunk_split($encoded, 76, "\r\n");
				          
				         //  var_dump($contents);
				            $i++;
				        }
				        return $contents;
				        fclose($fp);
		    			file_put_contents('t',$contents);
				}
			}
			return ($contents);
		}else{

			$token=$token;
			$data_api=self::getCircuitIdDocuments($token,$tipo,$circuitid);
			$nombreCopiaVer=$files;
			$zip->open($nombreCopiaVer, ZipArchive::CREATE);
			$dir = '.';
		 	$zip->addEmptyDir($dir);
			$zip->close();
			if (is_writable($nombreCopiaVer)){
		     	$handle = fopen($nombreCopiaVer, 'w+');
				if (fwrite($handle, $data_api) === FALSE) {
			        echo "Cannot write to file ($filename)";
			        exit;
			    }
			    fclose($handle);
			}
			$zip->open($nombreCopiaVer);
			for($i = 0; $i < $zip->numFiles; $i++) 
			{
				if($zip->getNameIndex($i)=="docu_".$id.".pdf")
				{
						$fp = $zip->getStream($zip->getNameIndex($i));
				        if(!$fp) exit("failed\n");
				        while (!feof($fp)) {
				            $contents .= fread($fp, 2);
				        }
				        return $contents;	
				        fclose($fp); 
				}
			}
			return $contents;			
		}
	  }else{
	  	return "-1";
	  }
	}
}

private static function getCircuitIdDocuments($token,$tipo,$circuitId)
{

   $curl = curl_init();
   curl_setopt_array($curl, array(
   CURLOPT_URL => "https://www.esignabox.com/api/2.0/circuit/docs/".$circuitId."?doctype=".$tipo,
   CURLOPT_RETURNTRANSFER => true,
   CURLOPT_ENCODING => '',
   CURLOPT_MAXREDIRS => 10,
   CURLOPT_TIMEOUT => 0,
   CURLOPT_FOLLOWLOCATION => true,
   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
   CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: Bearer '.$token
)));
 $response = curl_exec($curl);
 curl_close($curl);
// $response=json_decode($response);
 return $response;
}


  public  function get_client_ip() {
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