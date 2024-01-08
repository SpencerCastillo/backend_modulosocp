<?php
require_once('models/Notario.php');



class  Api_NotarioAPI
{
     public function API()
     {
        // header('Content-Type: application/JSON');                
         $method = $_SERVER['REQUEST_METHOD'];
         switch ($method) {
         case 'GET'://consulta
             $this->get();
             break;     
         case 'POST'://inserta
             $this->post();
             
             break;                
         case 'PUT'://actualiza
             
             break;      
         case 'DELETE'://elimina
             
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
        /*
    private function my_status_header($setHeader=null) {
        static $theHeader=null;
        //if we already set it, then return what we set before (can't set it twice anyway)
        if($theHeader) {return $theHeader;}
        $theHeader = $setHeader;
      //  header('HTTP/1.1 '.$setHeader);
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
       }*/

     /**
     * función que segun el valor de "action" e "id":
     *  - mostrara una array con todos los registros de personas
     *  - mostrara un solo registro 
     *  - mostrara un array vacio
     */
     function get()
     {
        $obj = new Models_Notario();

         $actionsInfo = [
            'notariocorreo' => function () use ($obj) {
                    $data=$obj->getNotariaById($_GET["data"]);
                    echo json_encode($data); 
            },
            'notariasOficio' => function () use ($obj) {
                $data=$obj->getNotarias($_GET['qBuscar'],$_GET['idColegio']);
                echo json_encode($data);
            },
            'listNotaria' => function () use ($obj) {
                $data=$obj->getNotarias($_GET['qBuscar'],$_GET['idColegio']);
                $response=[];
                $data=$obj->getListNotarias($_GET["data"]); 
                $totals=$obj->getListNotariasCount($_GET["data"]); 

                $response["data"]=$data;
                $response["totals"]=$totals;               
                echo json_encode($response); 
            }

        ];

        if (isset($_GET['info']) && array_key_exists($_GET['info'], $actionsInfo)) {
            $actionsInfo[$_GET['info']]();
        } else 
            $this->response(400);
     } 

       function post()
     {
         $db = new Notario();
        if(isset($_GET['info']) and $_GET['info']=='notariocorreo')
            {         
                 $obj = json_decode( file_get_contents('php://input') );   
                $data=$db->setNotariocorreo($obj);                 
                echo json_encode($data);  
            }else{
                   $this->response(400);
                  }       
     }  


        
}//end class
?>