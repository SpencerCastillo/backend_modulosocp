<?php 

//require 'PHPExcel/Classes/PHPExcel/IOFactory.php';

class Api_AlertaCualitativaAPI 
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
          $obj = new Models_AlertasCualitativa();
          $actions = [
                'addAlertaCualitativa' => function () use ($obj) {
                    $dataRequest=json_decode($_GET["data"]);
                    $data=$obj->guardarAlertaCualitativa($dataRequest); 
                    $response['data']=$data; 
                },
                'alertaseleccionada' => function () use ($obj) {
                    $dataRequest=json_decode($_GET["data"]);
                    $data=$obj->getAlertaSeleccionada($dataRequest); 
                    $response['data']=$data;
                    echo json_encode($response); 
                },
                'list' => function () use ($obj) {
                    $dataResponse=json_decode($_GET["data"]);
                    $data=$obj->getAlertaList($dataResponse); 
                    $total=12;
                    $response['data']=$data;
                    $response['total']=$total;
                    echo json_encode($response); 
                },
                'listAnexos' => function () use ($obj) {
                    $dataResponse=json_decode($_GET["data"]);
                    $data=$obj->getAnexosList($dataResponse); 
                    $response['data']=$data;
                    echo json_encode($response); 
                },
                'listComentario' => function () use ($obj) {
                    $dataResponse=json_decode($_GET["data"]);
                    $data=$obj->getComentariosList($dataResponse); 
                    $response['data']=$data;
                    echo json_encode($response);    
                }   

            ];

            if (isset($_GET['info']) && array_key_exists($_GET['info'], $actions)) 
                $actions[$_GET['info']]();
            else 
                $this->response(400);
     }

    private  function post()
     {  

         $obj = new Models_AlertasCualitativa();
         $requestData = json_decode(file_get_contents('php://input'));   
          $actions = [
                'addAnexos' => function () use ($obj,$requestData) {
                    $data=$obj->addAnexos($requestData); 
                    echo json_encode($data);   
                },
                'deleteAnexo' => function () use ($obj,$requestData) {
                    $data=$obj->deleteAnexo($requestData); 
                    echo json_encode($data); 
                },
                'addCalificacion'=>function () use ($obj,$requestData) {
                        $data=$obj->addCalificacion($requestData); 
                        echo json_encode($data);   
                }

           
            ];

            if (isset($_GET['info']) && array_key_exists($_GET['info'], $actions)) 
                $actions[$_GET['info']]('a');
            else 
                $this->response(400);

      
     }
 }

