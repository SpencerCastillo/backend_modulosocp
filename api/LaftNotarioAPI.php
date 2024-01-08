<?php
class Api_LaftNotarioAPI 
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
        $obj = new Models_LaftNotario();

          $actions = [
                'listAnexolaft' => function () use ($obj) {
                    $data=$obj->getListAnexolaft($_GET); 
                    $all["data"]=$data;                
                    echo json_encode($all);   
                },
                'actos' => function () use ($obj) {
                    $data=$obj->getActos($_GET["iddocumento"]); 
                    echo json_encode($data); 
                },
                'notariasOficio'=>function () use ($obj) {
                         $data=$obj->getNotarias($_GET['qBuscar'],$_GET['idColegio']);
                          echo json_encode($data);  
                },
                'listNotaria'=>function () use ($obj) {
                        $response=[];
                        $data=$obj->getListNotarias($_GET["data"]); 
                        $totals=$obj->getListNotariasCount($_GET["data"]); 

                        $response["data"]=$data;
                        $response["totals"]=$totals;  
                },
                'listLaftNotario'=>function () use ($obj) {
                         $response=[];
                        $data=$obj->getRptsimple($_GET["data"]); 
                        $totals=$obj->getRptsimpleCount($_GET["data"]); 

                        $response["data"]=$data;
                        $response["totals"]=$totals;
                         echo json_encode($response);
                },
                'laftNotarioById'=>function () use ($obj) {
                     $response=[];
                    $data=$obj->getLaftNotarioById(); 
                    $response["data"]=$data;
                     echo json_encode($response);  
                },
                'contratantes'=>function () use ($obj) {
                      $data=$obj->getContratantesPorOperacion(); 
                      echo json_encode($data);   
                },
                'listDocumentoPorAlerta'=>function () use ($obj) {
                     $data=$obj->getListDocumentoPorAlerta(); 
                echo json_encode($data);   
                },
                'validarAnexoTipo'=>function () use ($obj) {
                    $data=$obj->getValidarAnexosTipoAlerta(); 
                echo json_encode($data);    
                },
                 'pdfInusual'=>function () use ($obj) {
                     $obj= new  Models_InformeLaftPDF();
                     $data=$obj->verdocumento($_GET['data']);   
                },
                 'pdfAlerta'=>function () use ($obj) {
                    $obj= new  Models_AlertaInfoPDF();
                     $data=$obj->verdocumento($_GET['data']); 
                    echo json_encode($data);  
                },
                'generar'=>function () use ($obj) {
                     $obj= new  Models_RptLaftNotario();
                     $data=$obj->getgenerar($_GET['data']); 
                    echo json_encode($data);
                },

            ];

            if (isset($_GET['info']) && array_key_exists($_GET['info'], $actions)) 
                $actions[$_GET['info']]();
            else 
                $this->response(400);
 
     } 

       function post()
     {
         $db = new LaftNotario();
        if(isset($_GET['info']) and $_GET['info']=='addDocumentoLaft')
        {         
                 $obj = json_decode( file_get_contents('php://input') );  
                $data=$db->addDocumentoLaft($obj->data);                 
                echo json_encode($data);
        }else if(isset($_GET['info']) and $_GET['info']=='addLaftNotario')
        {         
                 $obj = json_decode( file_get_contents('php://input') );  
                $data=$db->addLaftNotario($obj);                 
                echo json_encode($data);  
        }else if(isset($_GET['info']) and $_GET['info']=='addLaftNotarioResumido')
        {         
                 $obj = json_decode( file_get_contents('php://input') );  
                $data=$db->addLaftNotarioResumido($obj);                 
                echo json_encode($data);  
        
        }else{
                 //  echo "...";
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