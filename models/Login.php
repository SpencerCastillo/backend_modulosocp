<?php 



class Models_Login extends DB_Connect {
        
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



    public function getLogin()
       {
      $connect=$this->connect();
      $request = json_decode(file_get_contents('php://input'),true);
      $usuario=Config_Utiles::getParametroSaneado($request,"username",array('required'=>true,'type'=>'string'));
      $clave=Config_Utiles::getParametroSaneado($request,"password",array('required'=>true,'type'=>'password'));

      $sql="SELECT ID,USUARIO,APELLIDOS,NOMBRES,ROL,IDACCESO,IDNOTARIA FROM OCPREPORTE.USUARIO WHERE USUARIO=:ussu AND CLAVE=:pass";
      $stid = oci_parse($connect, $sql);
      oci_bind_by_name($stid, ':ussu', $usuario);
      oci_bind_by_name($stid, ':pass', $clave);
        oci_execute($stid);
        $enSession=false;
        $idSesion=0;
        while($row = oci_fetch_object($stid)){
            $enSession=true;
            $idSesion=$row->ID;
            Config_Utiles::setSession("lid",$row->ID);
            Config_Utiles::setSession("user",strtoupper($row->NOMBRES)." ".strtoupper($row->APELLIDOS));
            Config_Utiles::setSession("lrol",$row->ROL);
            Config_Utiles::setSession("idacceso",$row->IDACCESO);
            Config_Utiles::setSession("idnotaria",$row->IDNOTARIA);
        }
        if($enSession){

           $xip="";
            //$xip=Utiles::get_client_ip();
            
            $sql="UPDATE  OCPREPORTE.USUARIO SET FECHA_HORA_SESION=CURRENT_TIMESTAMP,IP='".$xip."'
            WHERE ID=".$_SESSION["lid"];
            $stid = oci_parse($connect, $sql);
            oci_execute($stid);
            oci_free_statement($stid);
            oci_close($connect);
            $token=Config_Utiles::getTokenSeguridad($idSesion);
            $list=array('id_user'=>base64_encode($idSesion),'nombre'=>Config_Utiles::getSession('user'),'msg'=>'correcto','rol'=>1,'token'=>$token,'codigo'=>100);              
          }else
            $list=array('id_user'=>0,'nombre'=>"PRUEBA",'msg'=>'Datos incorrectos','rol'=>1,'codigo'=>101);
          
        return ($list);
    
}


public function cerrarSesion()
{
    unset($_SESSION["PXNOTARIA"]);
    unset($_SESSION["USER_ELECT_NOT"]);
    unset($_SESSION["NUMDOC"]);
    unset($_SESSION["XRUC"]);
    unset($_SESSION["CORREO"]);
    unset($_SESSION[NAME_TOKEN]);
    unset($_COOKIE["CK".NAME_TOKEN]);

    unset($_SESSION["UUID"]);
    return  array('0' =>'session cerrada');
}

public function getTokenSeguridadProcesos()
{

//    Utiles::getTokenValidation();
  require_once 'conexion/Clvs.php';
    $data= array('estado' =>0 ,'msg'=>"","id_t"=>"");
    if(isset($_SESSION["PXNOTARIA"])){
      $data=array('estado'=>1,'msg'=>'correcto','id_t'=>$_SESSION[NAME_TOKEN]);
    }
    return $data;
}
public function getVerificarSesion()
{
$request = $_GET["data"];
$request=json_decode($request,true);

$idnotaria=Config_Utiles::getParametroSaneado($request,"codigo",array('required'=>true,'type'=>'string'));
$idnotaria=base64_decode($idnotaria);

    $headers = apache_request_headers();
    $authorization="";
    if(isset($headers["Authorization"]))      
        $authorization=$headers["Authorization"];
    $idNotariaToken =Config_Utiles::getValuesTokenDescryp($authorization);
    
    if(intval($idnotaria)!=intval($idNotariaToken))
      $data=array('msg'=>'no');
    else
      $data=array('msg'=>'si');
    
     return ($data);

 /*  if(isset($_SESSION["PXNOTARIA"])){
        $data=array('msg'=>'si','id_user'=>$_SESSION["PXNOTARIA"],'nombre'=>$_SESSION["USER_ELECT_NOT"],'numdoc'=>$_SESSION["NUMDOC"],'correo'=>$_SESSION["CORREO"],'rol'=>$_SESSION["ROL"]);
      }
      else{
//        $data=array('msg'=>'si','id_user'=>137,'nombre'=>"Prueba",'numdoc'=>"123",'correo'=>"abc@gmail.com");
        
         $data=array('msg'=>'no');
      }
        return ($data); */
}


}
?>