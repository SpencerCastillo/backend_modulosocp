<?php 
class Api_LoginAPI 
{
     public function API(){
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

       function get()
     {  

        $db = new Models_Login();
        if(isset($_GET['info']) and $_GET['info']=='verificar')
        {  
                $data=$db->getVerificarSesion(); 
                        
                echo json_encode($data);
        }else if(isset($_GET['info']) and $_GET['info']=='ids')
        {  
                $data=$db->getTokenSeguridadProcesos(); 
                        
                echo json_encode($data);

        }else    
                 $this->response(400);   
     }  

     function post()
     {
         $obj = new Models_Login();
           $actions = [
                'inicio' => function () use ($obj) {
                    $data=$obj->getLogin(); 
                    echo json_encode($data);  
                },
                'cerrarSesion' => function () use ($obj) {
                    $data=$obj->cerrarSesion(); 
                    echo json_encode($data);  
                }
            ];

            if (isset($_GET['info']) && array_key_exists($_GET['info'], $actions)) 
                $actions[$_GET['info']]();
            else 
                $this->response(400);
            

    }

}

 ?>