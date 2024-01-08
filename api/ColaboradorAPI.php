<?php
class Api_ColaboradorAPI 
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
              $this->delete();
             break;
         default://metodo NO soportado
             echo 'METODO NO SOPORTADO';
             break;
         }
     }   


     function get()
     {
        
        $db = new Models_Colaborador();
        if(isset($_GET['info']) and $_GET['info']=='list')
            {         
                $data=$db->getList(); 
                $all["data"]=$data;                
                echo json_encode($all);  
        }else if(isset($_GET['info']) and $_GET['info']=='colaboradorById')
            {         
                $data=$db->getColaboradorById(); 
                $all["data"]=$data;                
                echo json_encode($all);  
        }else if(isset($_GET['info']) and $_GET['info']=='generarRpt')
            {         
                $data=$db->generarRpt();           
                echo json_encode($data);  
        }else if(isset($_GET['info']) and $_GET['info']=='estadoAlerta')
            {         
                $info=$db->getEstadoAlerta(); 
                $all["data"]=$info;           
                echo json_encode($all);  
        
        }else{
                   $this->response(400);
                  }       
     } 

       function post()
     {
         $db = new Colaborador();
        if(isset($_GET['info']) and $_GET['info']=='add')
        {         
                 $obj = json_decode( file_get_contents('php://input') );  
               
                $data=$db->addColaborador($obj);                 
                echo json_encode($data);
        }    
     }  


        function delete()
     {
            
     }  


        
}//end class
?>