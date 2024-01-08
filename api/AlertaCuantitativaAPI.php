<?php 

class Api_AlertaCuantitativaAPI 
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

            $obj = new Models_AlertaCuantitativa();
            $actions = [
                'list' => function () use ($obj) {
                      $data=$obj->getList($_GET["data"]); 
                      echo json_encode($data);      
                },
                'detalleAlerta' => function () use ($obj) {
                    $info=$obj->getDetalleAlerta($_GET["data"]); 
                    $all['data']=$info;
                    echo json_encode($all);        
          
                }
            ];

            if (isset($_GET['info']) && array_key_exists($_GET['info'], $actions)) 
                $actions[$_GET['info']]();
            else 
                $this->response(400);        

     }  
 }

