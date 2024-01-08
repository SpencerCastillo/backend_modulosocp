<?php
//require_once('models/Reports.php');
//require_once('DB.php');  
/*
require_once('vendor/php-excel-reader/excel_reader2.php');
require_once('vendor/SpreadsheetReader.php');
*/

require 'libs/PHPExcel/Classes/PHPExcel/IOFactory.php';

class Api_ReportsAPI 
{
     public function API()
     {
         header('Content-Type: application/JSON');                
         $method = $_SERVER['REQUEST_METHOD'];
         switch ($method) {
         case 'GET'://consulta
             $this->getReports();
             break;     
        
         default://metodo NO soportado
             echo 'METODO NO SOPORTADO';
             break;
         }

     }   

        /**
     * Respuesta al cliente
     * @param int $code Codigo de respuesta HTTP
     * @param String $status indica el estado de la respuesta puede ser "success" o "error"
     * @param String $message Descripcion de lo ocurrido
     */

    private function my_status_header($setHeader=null) {
        static $theHeader=null;
        //if we already set it, then return what we set before (can't set it twice anyway)
        if($theHeader) {return $theHeader;}
        $theHeader = $setHeader;
        header('HTTP/1.1 '.$setHeader);
        return $setHeader;
    }
     function response($code=200, $status="", $message="") 
       {
       $this->my_status_header($code);
        if( !empty($status) && !empty($message) )
           {
            $response = array("status" => $status ,"message"=>$message);  
            echo json_encode($response,JSON_PRETTY_PRINT);    
            } 
       }

     /**
     * función que segun el valor de "action" e "id":
     *  - mostrara una array con todos los registros de personas
     *  - mostrara un solo registro 
     *  - mostrara un array vacio
     */
     function getListIps()
     {              
                   $db = new Instrumento();
                   $response = $db->getListIps();   //consultas con mysqli
                   $data=array();
                   
                   foreach ($response as  $value) {
                    $data[]=$value['ip'];
                   }
                   return $data;
                     
     }
     function addRegisterIp($xip)
     {
        $db = new Instrumento();
        $response = $db->addRegisterIp($xip);
     }
     function getReports()
     {  
        $obj = new Models_Reports();

         $actionsInfo = [
            'getColegio' => function () use ($obj) {
                    $data=$obj->getColegios($_GET['qBuscar']);
                    echo json_encode($data);
            }



        ];

        if (isset($_GET['info']) && array_key_exists($_GET['info'], $actionsInfo)) {
            $actionsInfo[$_GET['info']]();
        } else {
            $this->response(400);
        }
        /*
        if(isset($_GET['action']) and $_GET['action']=='simple')
        {   
                $response=[];
                $data=$db->getRptsimple($_GET["data"]); 
                $totals=$db->getRptsimpleCount($_GET["data"]); 

                $response["data"]=$data;
                $response["totals"]=$totals;
                
                 echo json_encode($response);
        }else if(isset($_GET['action']) and $_GET['action']=='getColegio'){
                $data=$db->getColegios($_GET['qBuscar']);
            echo json_encode($data);
       }else if(isset($_GET['action']) and $_GET['action']=='getNotarias'){
                $data=$db->getNotarias($_GET['qBuscar'],$_GET['idColegio']);
            echo json_encode($data);
       }else if(isset($_GET['action']) and $_GET['action']=='getBanderas'){
                $data=$db->getBanderas($_GET['qBuscar']);
            echo json_encode($data);
        }else if(isset($_GET['action']) and $_GET['action']=='getActos'){
                $tipo="";
                if(isset($_GET["tipo"]) && $_GET["tipo"]!="")
                    $tipo=$_GET["tipo"];
                $data=$db->getActos($_GET['qBuscar'],$tipo);
            echo json_encode($data);
        }else if(isset($_GET['action']) and $_GET['action']=='getInstrumentos'){
                $data=$db->getInstrumentos($_GET['qBuscar']);
            echo json_encode($data);
        }else if(isset($_GET['action']) and $_GET['action']=='generar'){
                $data=$db->getRptGenerarsimple($_GET["data"]);
            echo json_encode($data);
        }else if(isset($_GET['action']) and $_GET['action']=='login'){
                $data=$db->getLogin($_GET["user"],$_GET["password"]);
            echo json_encode($data);

        }else if(isset($_GET['action']) and $_GET['action']=='validationSession'){
                $data=$db->getValidationSession();
            echo json_encode($data);
        
        }else if(isset($_GET['action']) and $_GET['action']=='cerrarSesion'){
                $data=$db->cerrarSesion();
            echo json_encode($data);
        
        }else    
                 $this->response(400);   */
     }  



     private  function getIdUnico()
        {
        $DesdeLetra = "a";
        $HastaLetra = "z";
        $DesdeNumero = 1;
        $HastaNumero = 9999999999;
        $today=getdate();
        $fec=$today["year"].$today["mon"].$today["mday"];

        $letraAleatoria = chr(rand(ord($DesdeLetra), ord($HastaLetra)));
        $numeroAleatorio = rand($DesdeNumero, $HastaNumero).$fec;
        return $numeroAleatorio.$letraAleatoria;
        }



     /**
     * Actualiza un recurso
     */
    function updatePeople() 
      {
        if( isset($_GET['action']) && isset($_GET['id']) )
         {
            if($_GET['action']=='peoples')
            {
                    $obj = json_decode( file_get_contents('php://input') );   
                    $objArr = (array)$obj;
                    if (empty($objArr))
                        {                        
                        $this->response(422,"error","Nothing to add. Check json");                        
                        }else if(isset($obj->name))
                            {
                            $db = new Instrumento();
                            $db->update($_GET['id'], $obj->name);
                            $this->response(200,"success","Record updated");                             
                            }else
                                {
                                  $this->response(422,"error","The property is not defined");                        
                                }     
              exit;
             }
         }
         $this->response(400);
      }



        
}//end class
?>