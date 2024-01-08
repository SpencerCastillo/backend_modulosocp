<?php
class Api_NotariaInstrumentoAPI 
{
     public function API()
     {
         header('Content-Type: application/JSON');                
         $method = $_SERVER['REQUEST_METHOD'];
         switch ($method) {
         case 'GET'://consulta
             $this->get();
             break;     
         case 'POST'://inserta
	         $this->add();
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


	private function get()
	{

         $obj = new Models_NotariaInstrumento();
          $actions = [
                'listInstrumentoPorNotaria' => function () use ($obj) {
                    $response=[];
                    $data=$obj->getInstrumentoByNotariaAndPeticion($_GET["data"]); 
                    echo json_encode($data);  
                },
                'verHojaCotejo' => function () use ($obj) {
                        $response=[];
                        $data=$obj->verHojaCotejo($_GET["data"]); 
                        echo json_encode($data);  
                }


            ];

            if (isset($_GET['info']) && array_key_exists($_GET['info'], $actions)) 
                $actions[$_GET['info']]();
            else 
                $this->response(400);
	
	}
	private function add()
	{
		$db = new Models_NotariaInstrumento();
		$obj = json_decode(file_get_contents('php://input') ); 
        if(isset($_GET['info']) and $_GET['info']=='add')
		{
                $data=$db->add($obj); 
                echo json_encode($data);
		}
	}

}
