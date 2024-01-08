<?php 
require_once('models/SenalAlerta.php');


require 'libs/PHPExcel/Classes/PHPExcel/IOFactory.php';

class SenalAlertaAPI 
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

        $obj = new SenalAlerta();
        if(isset($_GET['info']) and $_GET['info']=='alerta'){
//                $obj = new AlertasSegmentacion();
                $dataResponse=json_decode($_GET["data"]);
                $data=$obj->getAlertaList($dataResponse); 
                $total=$obj->getAlertaCount($dataResponse); 

                $response['data']=$data;
                $response['total']=$total;
                echo json_encode($response);      
        }else if(isset($_GET['info']) and $_GET['info']=='detalle'){
          //      $obj = new AlertasSegmentacion();
                $response=[];
                $data=$obj->getDetalleList($_GET["data"]); 
                $total=123;
                $response['data']=$data;
                $response['total']=$total;
                echo json_encode($response);      
        
        }else    
                 $this->response(400);   
     }  
 }

