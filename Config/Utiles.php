<?php 
class Config_Utiles{

 const DIR_BASE="C:/FILES_ELECTNOTARIAL/";

	public  function getTokenValidation()
	{
		if(!class_exists('jwt') ) 
			include("libs/jwt/jwt.php");

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

public static function getValuesTokenDescryp($token)
{
		if(!class_exists('jwt') ) 
			include("libs/jwt.php");

	try{
				$get_values = JWT::decode($token, KEY_SECRET, array('HS256'));
				$data=$get_values->data;
				$xuuid=($data->id);
				return $xuuid;
			}catch (Exception $e)
			{
				return "-1";
		}
}

public static function getIdNotaria()
{
		if(!class_exists('jwt') ) 
			include("libs/jwt.php");

	try{
			 	$headers = apache_request_headers();
				$authorization="";
				if(isset($headers["Authorization"]))      
				    $authorization=$headers["Authorization"];
				$get_values = JWT::decode($authorization, KEY_SECRET, array('HS256'));
				$data=$get_values->data;
				$xuuid=($data->id);
				return $xuuid;
			}catch (Exception $e)
			{
				return "-1";
		}
}





 public static function getValorDecimal($valor)
 {
 	if($valor!="" && is_numeric($valor)!="")
 		return number_format($valor, 2, '.', ',');
 	else
 		return "";
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

public static function getParametroSaneado($request,$nombreParametro="",$opciones=array(),$filter=FILTER_SANITIZE_STRING)
{
	$valor="";
	if (isset($request[$nombreParametro])){
		$valor=trim($request[$nombreParametro]);
	
			

		if ($opciones['required'] && $valor=="" ) {
	   		$gObject = new stdClass(); 
	   		$gObject->msg="El Campo ".$nombreParametro." es requerido";
	   		$gObject->error=true;
	   		$gObject->codigo=404;
	   		echo json_encode($gObject);
   			exit;
		}
		 $valor = filter_var($valor,$filter);
		if(isset($opciones['type']))
			{
				$tipo=$opciones['type'];
				if($tipo=="date")
					$valor=date("d/m/Y", strtotime($valor));
				else if($tipo=="password")
					$valor=addslashes($valor);
				else
					$valor = preg_replace("/[^a-zA-Z0-9]+/", "", $valor);

			}
 
	        return $valor;

   }else
   {
   			$gObject = new stdClass(); 
	   		$gObject->msg="El Campo ".$nombreParametro." no es reconocido ";
	   		$gObject->error=true;
	   		$gObject->codigo=404;
	   		echo json_encode($gObject);
	   		exit;
   }
    return $valor;
}

public static  function setSession($nombre,$valor)
{
	$_SESSION[$nombre]=$valor;
}

public static  function getSession($nombre)
{
	return $_SESSION[$nombre];
}
	
public static function getTokenSeguridad($idregistro)
{
    include("libs/jwt/jwt.php");
    $xuuid=bin2hex(random_bytes(20));
    $time = time();
    $items = array(
             'iat' => $time, // Tiempo que inició el token
             'exp' => $time + (21600), // Tiempo que expirará el token (+1 hora)
             'data' => array( // información del usuario
                      'id' => $idregistro,
                      'name' => 'Jncarlo'
                  ),
             'payload'=>array(
                    'UUID'=>$xuuid
                  )
              );
              $token = JWT::encode($items, KEY_SECRET);
    return $token;
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