<?php 


require 'libs/PHPExcel/Classes/PHPExcel/IOFactory.php';

class Api_AlertasAPI 
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


         $obj = new Models_Alertas();
            $actions = [
                'rptlist' => function () use ($obj) {
                        $data=$obj->getRptList($_GET["data"]); 
                       echo json_encode($data);        
                },
                'grupoAlerta' => function () use ($obj) {
                    $data=$obj->getGrupoAlerta(); 
                    echo json_encode($data);         
                },
                'tipoAlerta' => function () use ($obj) {
                   $data=$obj->getTipoAlerta($_GET["data"]); 
                   echo json_encode($data);       
                },
                'alertaProcesar' => function () use ($obj) {
                   $data=$obj->getAlertaProcesada22($_GET["data"]); 
                  echo json_encode($data);   
                },
                 'detalleAlerta' => function () use ($obj) {
                     $data=$obj->getDetalleAlerta($_GET["data"]); 
                  echo json_encode($data); 
                },
                 'grupoAlertaOcp' => function () use ($obj) {
                    $data=$obj->getGrupoAlertaOcp(); 
                    echo json_encode($data);
                },
                 'grupoAlertaSeg' => function () use ($obj) {
                      $data=$obj->getGrupoAlertaSegmentacion(); 
                    echo json_encode($data);
                },
                 'grupoScoring' => function () use ($obj) {
                     $data=$obj->getGrupoScoring(); 
                    echo json_encode($data);
                },
                 'grupoAlertaCuali' => function () use ($obj) {
                      $data=$obj->getGrupoAlertaCualitativa(); 
                      echo json_encode($data);
                },
                 'tipoAlertaOcp' => function () use ($obj) {
                     $data=$obj->getTipoAlertaOcp($_GET["data"]); 
                    echo json_encode($data); 
                },
                'tipoAlertaSeg' => function () use ($obj) {
                    $data=$obj->getTipoAlertaSeg($_GET["data"]); 
                    echo json_encode($data); 
                },
                'alertaSegmentacion' => function () use ($obj) {
                          
                      $obj = new Models_AlertasSegmentacion();
                      $dataResponse=json_decode($_GET["data"]);
                      $idGrupoAlerta=isset($dataResponse->idGrupoAlerta)?$dataResponse->idGrupoAlerta:"";
                      $response=[];
                      if($idGrupoAlerta==8)
                      {
                          $data=$obj->getAlertaClienteList($dataResponse); 
                          $total=$obj->getAlertaClienteCount($dataResponse); 
                      }else if($idGrupoAlerta==20)
                      {
                          $data=$obj->getUmbralesNaturales($dataResponse,'N'); 
                          $total=$obj->getUmbralesNaturalesCount($dataResponse,'N'); 
                      
                      }else if($idGrupoAlerta==21)
                      {
                          $data=$obj->getUmbralesNaturales($dataResponse,'J'); 
                          $total=$obj->getUmbralesNaturalesCount($dataResponse,'J'); 
                      
                      }else{
                        //  die("stes");
                          $data=$obj->getAlertaList($dataResponse); 
                          $total=$obj->getAlertaCount($dataResponse); 
                      }                
                      $response['data']=$data;
                      $response['total']=$total;
                      echo json_encode($response);   
                },
                'detalleAlertaSegmentacion' => function () use ($obj) {
                     $obj = new Models_AlertasSegmentacion();
                    $response=[];
                    $data=$obj->getDetalleList($_GET["data"]); 
                    $total=123;
                    $response['data']=$data;
                    $response['total']=$total;
                    echo json_encode($response);
                },
                'detalleUmbralSegmentacion' => function () use ($obj) {
                    $obj = new Models_AlertasSegmentacion();
                    $response=[];
                    $data=$obj->getDetalleUmbralList($_GET["data"]); 
                    $total=123;
                    $response['data']=$data;
                    $response['total']=$total;
                    echo json_encode($response);      

                },
                'detalleUmbralSegmentacion' => function () use ($obj) {
                      $obj = new Models_AlertasSegmentacion();
                      $response=[];
                      $data=$obj->getDetalleUmbralList($_GET["data"]); 
                      $total=123;
                      $response['data']=$data;
                      $response['total']=$total;
                      echo json_encode($response); 
                },
                'addAlertaCualitativa'=> function () use ($obj){
                      $data=$obj->guardarAlertaCualitativa(); 
                      echo json_encode($data);
                }
            ];

            if (isset($_GET['info']) && array_key_exists($_GET['info'], $actions)) 
                $actions[$_GET['info']]();
            else 
                $this->response(400);   

     }  
 }

