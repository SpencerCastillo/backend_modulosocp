<?php
require_once('models/OperacionInforme.php');



class API_OperacionInformeAPI 
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
        $obj = new Models_OperacionInforme();
            $actions = [
                'list' => function () use ($obj) {
                     $data=$obj->getList(); 
                    $total=$obj->getCountList(); 
                    $all["data"]=$data; 
                    $all["total"]=$total; 
                    echo json_encode($all);   
                },
                'generarRpt' => function () use ($obj) {
                        $data=$obj->getRptList(); 
                        echo json_encode($data); 
                },
                'listCualitativa' => function () use ($obj) {
                        $data=$obj->getList(); 
                        $all["data"]=$data;                
                        echo json_encode($all);  
                },
                'laftrecepcion' => function () use ($obj) {
                        $dataRequest=json_decode($_GET["data"]);
                        $data=$obj->getLaftRecepcion($dataRequest); 
                        $response['data']=$data;
                        echo json_encode($response); 
                }
            ];

            if (isset($_GET['info']) && array_key_exists($_GET['info'], $actions)) 
                $actions[$_GET['info']]();
            else 
                $this->response(400);        
        
     } 

       function post()
     {
         $db = new Models_LaftNotario();
        if(isset($_GET['info']) and $_GET['info']=='addDocumentoLaft')
        {         
                 $obj = json_decode( file_get_contents('php://input') );  
                $data=$db->addDocumentoLaft($obj->data);                 
                echo json_encode($data);
        }if(isset($_GET['info']) and $_GET['info']=='addLaftNotario')
        {         
                 $obj = json_decode( file_get_contents('php://input') );  
                $data=$db->addLaftNotario($obj);                 
                echo json_encode($data);  
        
        }else{
                   $this->response(400);
                  }       
     }  


        function delete()
     {
         $db = new LaftNotario();
        if(isset($_GET['info']) and $_GET['info']=='eliminarAnexolaft')
            {         
                 $obj = json_decode( file_get_contents('php://input') );  
                $data=$db->eliminarAnexolaft();                 
                echo json_encode($data);  
            }else{
                   $this->response(400);
                  }       
     }  


        
}//end class
?>