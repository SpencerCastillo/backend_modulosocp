<?php 
class Api_ScoringAPI 
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


        $db = new Models_Scoring();
        if(isset($_GET['info']) and $_GET['info']=='rptList'){
                $data=$db->getRptList($_GET["data"]); 
                echo json_encode($data);        
        }else if(isset($_GET['info']) and $_GET['info']=='searchUbigeo'){
                $data=$db->getSearchUbigeo($_GET["data"]); 
                echo json_encode($data);        

        }else if(isset($_GET['info']) and $_GET['info']=='listFactorCliente'){
                $data=$db->getFactorCliente($_GET["data"]); 
                $total=$db->getFactorClienteCount($_GET["data"]); 

                $response['data']=$data;
                $response['total']=$total;
                echo json_encode($response);
        }else if(isset($_GET['info']) and $_GET['info']=='detalle'){
                $data=$db->getDetalle($_GET["data"]); 
                $response['data']=$data;
                $response['total']=1;
                echo json_encode($response);
        
        }else    
                 $this->response(400);   
     }  
 }

