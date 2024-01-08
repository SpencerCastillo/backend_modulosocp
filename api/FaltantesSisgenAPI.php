<?php 
require_once('models/FaltantesSisgen.php');

require 'libs/PHPExcel/Classes/PHPExcel/IOFactory.php';

class FaltantesSisgenAPI 
{
     public function API(){
    // header('Content-Type: application/JSON');                
         $method = $_SERVER['REQUEST_METHOD'];
         switch ($method) {
         case 'GET'://consulta
             $this->get();
             break;     
         case 'POST'://inserta
             
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

       function get()
     {  

        $db = new FaltantesSisgen();
        if(isset($_GET['info']) and $_GET['info']=='list')
        {   
                $response=[];
                $data=$db->getList($_GET["data"]); 
            //      $totals=$db->getRptsimpleCount($_GET["data"]); 

                $response["data"]=$data;
                //$response["totals"]=$totals;               
                echo json_encode($response);
        }else if(isset($_GET['info']) and $_GET['info']=='generar'){
                $data=$db->getRptGenerarExpRepetido1($_GET["data"]);
            echo json_encode($data);

        }else if(isset($_GET['info']) and $_GET['info']=='generar2'){
                $data=$db->getRptGenerarExpRepetido2($_GET["data"]);
            echo json_encode($data);
        
        }else    
                 $this->response(400);   
     }  
 }

