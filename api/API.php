<?php 

if(!class_exists('DB_Connect') ) 
    include "Config/DB_Connect.php";

class API 
{
   private $allLoadApi=array();
   public static function getIdUsuarioSession()
    {
        return $GLOBALS["XID_USUARIOX"];
    }
    public function __construct()
    {
        $this->allLoadApi=array(
            "login",
            "reports",
            "notario",
            "alertas",
            "operacion_informe",
            "colaborador",
            "alerta_cuantitativa",
            "alerta_cualitativa","laft_notario","scoring",
            "notaria_instrumento"
        );
    }
     public function run()
     {
        // header('Content-Type: application/JSON');                
         $method = $_SERVER['REQUEST_METHOD'];
         if(isset($_GET["action"]))
         {  
            $_GET["action"]=strtolower($_GET["action"]);
            if(!isset($_SESSION))
                session_start();
            
            if(isset($_SESSION["PXNOTARIA"]))
                $GLOBALS["XID_USUARIOX"]=$_SESSION["PXNOTARIA"];
            else
                $GLOBALS["XID_USUARIOX"]="-1";

            if($_GET["action"]!="")
            {
                $_action=$_GET["action"];
                //die($_action);
                if(in_array($_action,$this->allLoadApi))
                {
                    $_action=str_replace("_"," ",$_GET["action"]);
                    $_action=ucwords($_action);
                    $_action=trim($_action);
                    $_action=str_replace(" ","",$_action);
                    $_class= "API_".$_action."API";
                    //die($_class);
                    if(class_exists($_class))
                    {
                        $_obj= new $_class();
                        $_obj->API();
                    } 
                }   


            }
        }
    }
}

?>  
