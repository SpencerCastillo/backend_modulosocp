<?php 
require_once('models/ConsultaContratante.php');
require 'libs/PHPExcel/Classes/PHPExcel/IOFactory.php';

class ConsultaContratanteAPI 
{
     public function API(){
     header('Content-Type: application/JSON');                
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
             $this->delete();
             break;
         default://metodo NO soportado
             echo 'METODO NO SOPORTADO';
             break;
        }
    }
    private function get(){
        $db = new ConsultaContratante();
        if(isset($_GET['info']) and $_GET['info']=='list')
        {   
                $response=[];
                $data=$db->getList($_GET["data"]); 
                $totals=$db->getCount($_GET["data"]);

                $response["data"]=$data;
                $response["totals"]=$totals;               
                echo json_encode($response);  
        }else if(isset($_GET['info']) and $_GET['info']=='generar')
        {   
                $response=[];
                $data=$db->getgenerar($_GET["data"]); 
                echo json_encode($data);  

        
        }else    
                 $this->response(400); 



    }
    private function post()
     {  

        $db = new ConsultaContratante();
        if(isset($_GET['info']) and $_GET['info']=='addCircularOcp'){
                $obj = json_decode( file_get_contents('php://input') );   
                $data=$db->addCircularOcp($obj); 
                echo json_encode($data);      
        }else if(isset($_GET['info']) and $_GET['info']=='importarPersonasPorOficio'){
                $obj = json_decode(file_get_contents('php://input') ); 
//                $response=[];
                $data=$db->addImportarPersonasPorOficio($obj); 
                echo json_encode($data);  
        
        }else    
                 $this->response(400);   
     }  

     private function delete()
     {
        $db = new CircularOcp();
        if(isset($_GET['info']) and $_GET['info']=='deleteOficio')
        {   
                $data=$db->deleteOficio($_GET['id']); 
                echo json_encode($data);  
        }else if(isset($_GET['info']) and $_GET['info']=='deleteColegioOficio')
        {   
                $data=$db->deleteColegioOficio($_GET['id']); 
                echo json_encode($data);  
        }
     }
 }

