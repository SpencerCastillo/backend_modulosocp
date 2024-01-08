<?php 
require_once('models/ListHojaCotejo.php');
require 'libs/PHPExcel/Classes/PHPExcel/IOFactory.php';

class ListHojaCotejoAPI 
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
        $db = new ListHojaCotejo();
        if(isset($_GET['info']) and $_GET['info']=='actos')
        {   
          
                $data=$db->getActos($_GET["iddocumento"]); 
                echo json_encode($data);  
        
        
        }else if(isset($_GET['info']) and $_GET['info']=='contratantes'){
                 $data=$db->getContratantesPorOperacion($_GET["idoperacion"]); 
                echo json_encode($data);  
         
        }else    
                 $this->response(400); 



    }
    private function post()
     {  

        $db = new CircularOcp();
        if(isset($_GET['info']) and $_GET['info']=='addCircularOcp'){
                $obj = json_decode( file_get_contents('php://input') );   
                $data=$db->addCircularOcp($obj); 
                echo json_encode($data);      
        }else if(isset($_GET['info']) and $_GET['info']=='updateCircularOcp'){
                $obj = json_decode( file_get_contents('php://input') );   
                $data=$db->updateCircularOcp($obj); 
                echo json_encode($data);      

        }else if(isset($_GET['info']) and $_GET['info']=='delete'){
               $obj = json_decode(file_get_contents('php://input') );   
                $data=$db->deletePeticion($obj); 
                echo json_encode($data);  
        
        }else if(isset($_GET['info']) and $_GET['info']=='addPersonaInvestigada'){
               $obj = json_decode(file_get_contents('php://input') );   
                $data=$db->addPersonaInvestigada($obj); 
                echo json_encode($data);  
        }else if(isset($_GET['info']) and $_GET['info']=='envioEmailPorColegio'){
               $obj = json_decode(file_get_contents('php://input') );   
                $data=$db->envioEmailPorColegio($obj); 
                echo json_encode($data);  
        }else if(isset($_GET['info']) and $_GET['info']=='envioEmailBloque'){
               $obj = json_decode(file_get_contents('php://input') );   
                $data=$db->envioEmailBloque($obj); 
                echo json_encode($data);  
        }else if(isset($_GET['info']) and $_GET['info']=='documentoOficioOcp'){
                $obj = json_decode(file_get_contents('php://input') ); 
//                $response=[];
                $data=$db->getDocumentoOficioOcp($obj); 
             //   echo json_encode($data);  
        
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

