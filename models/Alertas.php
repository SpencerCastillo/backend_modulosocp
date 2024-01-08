<?php 

class Models_Alertas extends DB_Connect {
        
    /**
     * Constructor de clase
     */
    function my_status_header($setHeader=null) {
        static $theHeader=null;
        //if we already set it, then return what we set before (can't set it twice anyway)
        if($theHeader) {return $theHeader;}
        $theHeader = $setHeader;
        header('HTTP/1.1 '.$setHeader);
        return $setHeader;
    } 
    
public function getGrupoAlerta()
{
    $db=$this->connect();
    $sql="  
          select id, upper(descripcion) as descripcion from 
          ocpreporte.grupo_alerta where estado=1 AND tipo='ocp'
          order by descripcion
       ";
     $stid = oci_parse($db,$sql);
     oci_execute($stid);
       $all=array();
        while (($row = oci_fetch_assoc($stid)) != false) {
            $all[]=$row;
        }
        return $all;
}


public function getGrupoAlertaSegmentacion()
{
    $db=$this->connect();
    $sql="  
          select id, upper(descripcion) as descripcion from 
          ocpreporte.grupo_alerta where estado=1 AND tipo='sgm'
          order by ID
       ";
     $stid = oci_parse($db,$sql);
     oci_execute($stid);
       $all=array();
        while (($row = oci_fetch_assoc($stid)) != false) {
            $all[]=$row;
        }
        return $all;
}

public function getGrupoScoring()
{
    $db=$this->connect();
    $sql="  
          select id, upper(descripcion) as descripcion from 
          ocpreporte.grupo_alerta where estado=1 AND tipo='sc'
          order by ID
       ";
     $stid = oci_parse($db,$sql);
     oci_execute($stid);
       $all=array();
        while (($row = oci_fetch_assoc($stid)) != false) {
            $all[]=$row;
        }
        return $all;
}
  


public function getGrupoAlertaCualitativa()
{
    $db=$this->connect();
    $sql="  
          select id, upper(descripcion) as descripcion from 
          ocpreporte.grupo_alerta where estado=1 AND tipo='clt'
          order by ID
       ";
     $stid = oci_parse($db,$sql);
     oci_execute($stid);
       $all=array();
        while (($row = oci_fetch_assoc($stid)) != false) {
            $all[]=$row;
        }
        return $all;
}


public function getAlertaProcesada22($data)
{
    $data=json_decode($data);
    $idTipoAlerta=isset($data->idTipoAlerta)?$data->idTipoAlerta:"0";
    $idGrupo=isset($data->idGrupoAlerta)?$data->idGrupoAlerta:"0";
   
                  $response=[];
                  $all=$this->getAlertaGeneral($data,$idGrupo); 
                  $totals=$this->getAlertaGeneralTotal($data,$idGrupo);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;

}
  
public function getAlertaProcesada($data)
{
    $data=json_decode($data);
    $idTipoAlerta=isset($data->idTipoAlerta)?$data->idTipoAlerta:"0";
    if($idTipoAlerta>0)
    {
        switch ($idTipoAlerta) {
             case '11':
                  $response=[];
                  $all=$this->getAlertaOnce($data); 
                  $totals=$this->getAlertaOnceTotal($data);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;

             case '13':
                  $response=[];
                  $all=$this->getAlertaTrece($data); 
                  $totals=$this->getAlertaTreceTotal($data);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;
          
           
             case '10':
                  $response=[];
                  $all=$this->getAlertaDiez($data); 
                  $response['data']=$all;
                  $response['total']=$this->getAlertaDiezTotal($data);
                  return $response;
        
             case '15':
                  $response=[];
                  $all=$this->getAlertaQuince($data); 
                  $totals=$this->getAlertaQuinceTotal($data);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;

             case '16':
                  $response=[];
                  $all=$this->getAlertaDieciseis($data); 
                  $totals=$this->getAlertaDieciseisTotal($data);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;

                case '17':
                  $response=[];
                  $all=$this->getAlertaDiecisiete($data); 
                  $totals=$this->getAlertaDiecisieteTotal($data);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;

                 case '12':
                  $response=[];
                  $all=$this->getAlertaDoce($data); 
                  $totals=$this->getAlertaDoceTotal($data);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;
                  case '1':
                  $response=[];
                  $all=$this->getAlertaUno($data); 
                  $totals=$this->getAlertaUnoTotal($data);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;

                  case '20':
                  $response=[];
                  $all=$this->getAlertaVeinte($data); 
                  $totals=$this->getAlertaVeinteTotal($data);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;

                  case '5':
                  $response=[];
                  $idGrupo=11;
                  $all=$this->getAlertaGeneral($data,$idGrupo); 
                  $totals=$this->getAlertaGeneralTotal($data,$idGrupo);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;

                  case '18':
                  $response=[];
                  $all=$this->getAlertaGeneral($data,4); 
                  $totals=$this->getAlertaGeneralTotal($data,4);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;

                  case '19':
                  $response=[];
                  $all=$this->getAlertaGeneral($data,4); 
                  $totals=$this->getAlertaGeneralTotal($data,4);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;
                  
                  case '8':
                  $idGrupo=11;
                  $response=[];
                  $all=$this->getAlertaGeneral($data,$idGrupo); 
                  $totals=$this->getAlertaGeneralTotal($data,$idGrupo);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;
                  
                  case '3':
                  $idGrupo=11;
                  $response=[];
                  $all=$this->getAlertaGeneral($data,$idGrupo); 
                  $totals=$this->getAlertaGeneralTotal($data,$idGrupo);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;

                  case '9':
                   $idGrupo=11;
                  $response=[];
                  $all=$this->getAlertaGeneral($data,$idGrupo); 
                  $totals=$this->getAlertaGeneralTotal($data,$idGrupo);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;
                  
                  case '6':
                  $idGrupo=11;
                  $response=[];
                  $all=$this->getAlertaGeneral($data,$idGrupo); 
                  $totals=$this->getAlertaGeneralTotal($data,$idGrupo);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;

                  case '7':
                  $idGrupo=111;
                  $response=[];
                  $all=$this->getAlertaGeneral($data,$idGrupo); 
                  $totals=$this->getAlertaGeneralTotal($data,$idGrupo);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;

                  case '2':
                  $idGrupo=11;
                  $response=[];
                  $all=$this->getAlertaGeneral($data,$idGrupo); 
                  $totals=$this->getAlertaGeneralTotal($data,$idGrupo);
                  $response['data']=$all;
                  $response['total']=$totals;
                  return $response;


                 break;



             default:
                 # code...
                 break;
         } 
    }
}

public function getDetalleAlerta($data)
{
    $data=json_decode($data);
    

                 $response=[];
              
                  $data=$this->getDetalleGeneral($data); 
                  $totals=12;
                  $response['data']=$data;
              //    $response['totals']=$totals;
                  return $response;


    
}

public function getDetalleTrece($data)
{
    $db=$this->connect();
    $idColegio=isset($data->idColegio)?$data->idColegio:"0";
    $idNotaria=isset($data->idNotaria)?$data->idNotaria:"0";
    $fechaInicio=isset($data->fechaInicio)?$data->fechaInicio:"";
    $fechaFin=isset($data->fechaFin)?$data->fechaFin:"";
    $sujeto=isset($data->sujeto)?trim($data->sujeto):"";


        $pageIndex=isset($data->pageIndex)?$data->pageIndex:"0";
        $pageSize=isset($data->pageSize)?$data->pageSize:"";
        $pageIndex=intval($pageIndex)+1;

    $all=array();

        $sql="  
        select 
  * from vm_escenario_trece 
WHERE ";
    
     if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
$sql.="  ";

     if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

         if($sujeto!="")
            $sql.=" and trim(sujeto)='".$sujeto."'";
//die($sql);


     $stid = oci_parse($db,$sql);
     oci_execute($stid);
       
        while (($row = oci_fetch_assoc($stid)) != false) {
            $row["notaria"]=$row["NOTARIA"];
            $row["colegio"]=$row["COLEGIO"];
            $row["numerodekardex"]=$row["NUMERODEKARDEX"];
            $row["contratante"]=$row["SUJETO"];
            $row["idnotaria"]=$row["IDNOTARIA"];
            $row["numerodeinstrumento"]=$row["NUMERODEINSTRUMENTO"];
            $all[]=$row;
        }
  
        return $all;
}


public function getDetalleNueve($data)
{
    $db=$this->connect();
    $idColegio=isset($data->idColegio)?$data->idColegio:"0";
    $idNotaria=isset($data->idNotaria)?$data->idNotaria:"0";
    $fechaInicio=isset($data->fechaInicio)?$data->fechaInicio:"";
    $fechaFin=isset($data->fechaFin)?$data->fechaFin:"";
    $sujeto=isset($data->sujeto)?trim($data->sujeto):"";


        $pageIndex=isset($data->pageIndex)?$data->pageIndex:"0";
        $pageSize=isset($data->pageSize)?$data->pageSize:"";
        $pageIndex=intval($pageIndex)+1;

    $all=array();

        $sql="  
        select 
  notaria,colegio,numerodekardex,contratante,idNotaria,to_char(fechanacimiento,'dd/mm/YYYY') as fechanacimiento,numerodeinstrumento from vm_escenario_nueve
WHERE ";
    
     if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
$sql.="  ";

     if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

         if($sujeto!="")
            $sql.=" and trim(contratante)='".$sujeto."'";
//die($sql);


     $stid = oci_parse($db,$sql);
     oci_execute($stid);
       
        while (($row = oci_fetch_assoc($stid)) != false) {
            $row["notaria"]=$row["NOTARIA"];
            $row["colegio"]=$row["COLEGIO"];
            $row["numerodekardex"]=$row["NUMERODEKARDEX"];
            $row["contratante"]=$row["CONTRATANTE"];
            $row["idnotaria"]=$row["IDNOTARIA"];
            $row["fechanacimiento"]=$row["FECHANACIMIENTO"];
            $row["numerodeinstrumento"]=$row["NUMERODEINSTRUMENTO"];


            
            $all[]=$row;
        }
  
        return $all;
}


public function getDetalleOnce($data)
{
    $db=$this->connect();
    $idColegio=isset($data->idColegio)?$data->idColegio:"0";
    $idNotaria=isset($data->idNotaria)?$data->idNotaria:"0";
    $fechaInicio=isset($data->fechaInicio)?$data->fechaInicio:"";
    $fechaFin=isset($data->fechaFin)?$data->fechaFin:"";
    $sujeto=isset($data->sujeto)?trim($data->sujeto):"";


        $pageIndex=isset($data->pageIndex)?$data->pageIndex:"0";
        $pageSize=isset($data->pageSize)?$data->pageSize:"";
        $pageIndex=intval($pageIndex)+1;

    $all=array();

        $sql="  
        select 
   NOTARIA,colegio,numerodekardex,partidaregistral,idNotaria from vm_escenario_once
WHERE  ";
    
     if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
$sql.="  ";

     if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

         if($sujeto!="")
            $sql.=" and trim(partidaregistral)='".$sujeto."'";
//die($sql);


     $stid = oci_parse($db,$sql);
     oci_execute($stid);
       
        while (($row = oci_fetch_assoc($stid)) != false) {
            $row["notaria"]=$row["NOTARIA"];
            $row["colegio"]=$row["COLEGIO"];
            $row["numerodekardex"]=$row["NUMERODEKARDEX"];
            $row["contratante"]=$row["PARTIDAREGISTRAL"];
            $row["idnotaria"]=$row["IDNOTARIA"];
            
            $all[]=$row;
        }
  
        return $all;
}






public function getDetalleSiete($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $sujeto=$this->getValorString($data->sujeto);
    
    
/*
        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;*/
    $all=array();

        $sql="  
        select 
   NOTARIA,colegio,numerodekardex,cliente as contratante,idNotaria,cargo,numeroinstrumento as numerodeinstrumento from vm_escenario_siete
WHERE  ";
    
     if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
$sql.="  ";

     if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

         if($sujeto!="")
            $sql.=" and cliente='".$sujeto."'";

        $all=$this->getListAllRows($sql);
        return $all;
}


public function getDetalleQuince($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $sujeto=$this->getValorString($data->sujeto);
    
    
/*
        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;*/
    $all=array();

        $sql="  
        select 
   NOTARIA,colegio,numerodekardex,sujeto as contratante,idNotaria,numerodeinstrumento from vm_escenario_quince
WHERE  ";
    
     if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
$sql.="  ";

     if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

         if($sujeto!="")
            $sql.=" and sujeto='".$sujeto."'";

        $all=$this->getListAllRows($sql);
        return $all;
}



public function getDetalleDieciseis($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $sujeto=$this->getValorString($data->sujeto);
    
    
/*
        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;*/
    $all=array();

        $sql="  
        select 
   NOTARIA,colegio,numerodekardex,cliente as contratante,idNotaria,numerodeinstrumento from vm_escenario_dieciseis
WHERE  ";
    
     if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
$sql.="  ";

     if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

         if($sujeto!="")
            $sql.=" and cliente='".$sujeto."'";

        $all=$this->getListAllRows($sql);
        return $all;
}





public function getDetalleDiecisiete($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $sujeto=$this->getValorString($data->sujeto);
    
    
/*
        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;*/
    $all=array();

        $sql="  
        select 
   NOTARIA,colegio,numerodekardex,sujeto as contratante,idNotaria,numerodeinstrumento from vm_escenario_diecisiete
WHERE  ";
    
     if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
$sql.="  ";

     if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

         if($sujeto!="")
            $sql.=" and sujeto='".$sujeto."'";

        $all=$this->getListAllRows($sql);
        return $all;
}



public function getDetalleUno($data)
{

  //  $data=json_decode($data);
    $db=$this->connect();
    $idalerta=$this->getValorNumerico($data->idalerta);
 

  
          $sql="  
       select colegio,notaria,sujeto as contratante,fechaautorizacion,numerodekardex,numerodeinstrumento,
idcolegio,idnotaria
from ocpreporte.detalle_alerta_ocp where idalerta=".$idalerta;
         $all=$this->getListAllRows($sql);
        return $all;
 
}

public function getDetalleVeinte($data)
{

  //  $data=json_decode($data);
    $db=$this->connect();
    $idalerta=$this->getValorNumerico($data->idalerta);
 

  
          $sql="  
       select colegio,notaria,sujeto as contratante,fechaautorizacion,numerodekardex,numerodeinstrumento,
idcolegio,idnotaria
from ocpreporte.detalle_alerta_ocp where idalerta=".$idalerta;
         $all=$this->getListAllRows($sql);
        return $all;
 
}


public function getDetalleGeneral($data)
{

  //  $data=json_decode($data);
    $db=$this->connect();
    $idalerta=$this->getValorNumerico($data->idalerta);
 

  
          $sql="  
       select colegio,notaria,sujeto as contratante,to_char(fechaautorizacion,'YYYY-mm-dd') as  fechaautorizacion,numerodekardex,numerodeinstrumento,
idcolegio,idnotaria,bandera,profesion,  '$'||' '||TO_CHAR(cuantia, '999,999.99') AS cuantia
from ocpreporte.detalle_alerta_ocp where idalerta=".$idalerta;
         $all=$this->getListAllRows($sql);
        return $all;
 
}








public function getDetalleDoce($data)
{
    $db=$this->connect();
    $idalerta=$this->getValorNumerico($data->idalerta);
  
          $sql="  
       select colegio,notaria,sujeto as contratante,to_char(fechaautorizacion,'YYYY-mm-dd') as  fechaautorizacion,numerodekardex,numerodeinstrumento,
idcolegio,idnotaria,bandera,profesion,  '$'||' '||TO_CHAR(cuantia, '999,999.99') AS cuantia
from ocpreporte.detalle_alerta_ocp where idalerta=".$idalerta;

         $all=$this->getListAllRows($sql);
        return $all;

}


public function getDetalleSeis($data)
{
    $db=$this->connect();
    $idColegio=isset($data->idColegio)?$data->idColegio:"0";
    $idNotaria=isset($data->idNotaria)?$data->idNotaria:"0";
    $fechaInicio=isset($data->fechaInicio)?$data->fechaInicio:"";
    $fechaFin=isset($data->fechaFin)?$data->fechaFin:"";
    $sujeto=isset($data->sujeto)?trim($data->sujeto):"";


        $pageIndex=isset($data->pageIndex)?$data->pageIndex:"0";
        $pageSize=isset($data->pageSize)?$data->pageSize:"";
        $pageIndex=intval($pageIndex)+1;

    $all=array();

        $sql="  
        select 
   NOTARIA,colegio,numerodekardex,cliente,idNotaria,numeroinstrumento as numerodeinstrumento,
  profesion
    from vm_escenario_seis
WHERE  ";
    
     if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
$sql.="  ";

     if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

         if($sujeto!="")
            $sql.=" and trim(cliente)='".$sujeto."'";
//die($sql);


     $stid = oci_parse($db,$sql);
     oci_execute($stid);
       
        while (($row = oci_fetch_assoc($stid)) != false) {
            $row["notaria"]=$row["NOTARIA"];
            $row["colegio"]=$row["COLEGIO"];
            $row["numerodekardex"]=$row["NUMERODEKARDEX"];
            $row["contratante"]=$row["CLIENTE"];
            $row["idnotaria"]=$row["IDNOTARIA"];
            $row["numerodeinstrumento"]=$row["NUMERODEINSTRUMENTO"];
            $row["profesion"]=$row["PROFESION"];


            
            $all[]=$row;
        }
  
        return $all;
}


public function getDetalleDiez($data)
{
  die("abc");
    $db=$this->connect();
    $idColegio=isset($data->idColegio)?$data->idColegio:"0";
    $idNotaria=isset($data->idNotaria)?$data->idNotaria:"0";
    $fechaInicio=isset($data->fechaInicio)?$data->fechaInicio:"";
    $fechaFin=isset($data->fechaFin)?$data->fechaFin:"";
    $sujeto=isset($data->sujeto)?trim($data->sujeto):"";


        $pageIndex=isset($data->pageIndex)?$data->pageIndex:"0";
        $pageSize=isset($data->pageSize)?$data->pageSize:"";
        $pageIndex=intval($pageIndex)+1;

    $all=array();

        $sql="  
        select 
  * from vm_escenario_diez
WHERE ";
    
     if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
$sql.="  ";

     if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

         if($sujeto!="")
            $sql.=" and partidaregistral='".$sujeto."'";
//die($sql);


     $stid = oci_parse($db,$sql);
     oci_execute($stid);
       
        while (($row = oci_fetch_assoc($stid)) != false) {
            $row["notaria"]=$row["NOTARIA"];
            $row["colegio"]=$row["COLEGIO"];
            $row["numerodekardex"]=$row["NUMERODEKARDEX"];

   //         $row["numerodeisntrumento"]=$row["NUMERODEINSTRUMENTO"];
            $row["contratante"]=$row["PARTIDAREGISTRAL"];
            $row["idnotaria"]=$row["IDNOTARIA"];
            
            $all[]=$row;
        }
  
        return $all;
}
public function getAlertaOnce($data)
{
    $db=$this->connect();
    $idColegio=isset($data->idColegio)?$data->idColegio:"0";
    $idNotaria=isset($data->idNotaria)?$data->idNotaria:"0";
    $fechaInicio=isset($data->fechaInicio)?$data->fechaInicio:"";
    $fechaFin=isset($data->fechaFin)?$data->fechaFin:"";

        $pageIndex=isset($data->pageIndex)?$data->pageIndex:"0";
        $pageSize=isset($data->pageSize)?$data->pageSize:"";
        $pageIndex=intval($pageIndex)+1;

    $all=array();

        $sql="  
        select trim(partidaregistral) as sujeto,
  count(1) as cantidad from vm_escenario_once
WHERE partidaregistral is not null and ";
    
     if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }

        $sql.=" GROUP BY trim(partidaregistral) HAVING count(1)>=3";

     if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
      //      $sql.= " LIMIT ".($pageInit>0?$pageInit.",":"")." ".$pageSize;
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";
         $all=$this->getListAllRows($sql);
        return $all;
}   


public function getAlertaNueve($data)
{
    $db=$this->connect();
    $idColegio=isset($data->idColegio)?$data->idColegio:"0";
    $idNotaria=isset($data->idNotaria)?$data->idNotaria:"0";
    $fechaInicio=isset($data->fechaInicio)?$data->fechaInicio:"";
    $fechaFin=isset($data->fechaFin)?$data->fechaFin:"";
    $sujeto=isset($data->nombrecontratante)?trim($data->nombrecontratante):"";
        $pageIndex=isset($data->pageIndex)?$data->pageIndex:"0";
        $pageSize=isset($data->pageSize)?$data->pageSize:"";
        $pageIndex=intval($pageIndex)+1;

    $all=array();

        $sql="  
        select trim(contratante) as contratante,
  count(1) as cantidad from vm_escenario_nueve
WHERE  ";
    
     if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
           if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

         if($sujeto!="")
            $sql.=" and trim(contratante)='".$sujeto."'";
        $sql.=" GROUP BY trim(contratante) ";

     if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
      //      $sql.= " LIMIT ".($pageInit>0?$pageInit.",":"")." ".$pageSize;
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";
    $all=$this->getListAllRows($sql);
    return $all;
}



public function getAlertaNueveTotal($data)
{

    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;
  $sujeto=isset($data->nombrecontratante)?trim($data->nombrecontratante):"";
    $all=array();

        $sql=" 

select count(1) as cantidad from (
select 
    trim(contratante) as contratante,
  count(1) as cantidad 
from
 vm_escenario_nueve
WHERE  ";

 if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

         if($sujeto!="")
            $sql.=" and trim(contratante)='".$sujeto."'";


$sql.=" GROUP BY
  trim(contratante) ) t1 ";

    $all=$this->getAllTotal($sql);
    return $all;
}



public function getAlertaTrece($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;

    $all=array();

        $sql="  SELECT trim(sujeto) as sujeto, count(1) as cantidad FROM vm_escenario_trece WHERE sujeto is not null and ";

 if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;
        

$sql.="
        group by (sujeto)
having count(1)>3
";
   
     if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
      //      $sql.= " LIMIT ".($pageInit>0?$pageInit.",":"")." ".$pageSize;
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";
     $all=$this->getListAllRows($sql);
        return $all;
}



public function getAlertaTreceTotal($data)
{

    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;

    $all=array();

        $sql=" 

select count(1) as cantidad from (
select 
    trim(sujeto) as sujeto, count(1) as cantidad 
from
 vm_escenario_trece
WHERE sujeto is not null and ";

 if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;
$sql.=" GROUP BY
  sujeto having count(1)>3 ) t1 ";

    $all=$this->getAllTotal($sql);
    return $all;
}



public function getAlertaSeis($data)
{
    $db=$this->connect();
    $idColegio=isset($data->idColegio)?$data->idColegio:"0";
    $idNotaria=isset($data->idNotaria)?$data->idNotaria:"0";
    $fechaInicio=isset($data->fechaInicio)?$data->fechaInicio:"";
    $fechaFin=isset($data->fechaFin)?$data->fechaFin:"";
     $sujeto=$this->getValorString($data->nombrecontratante);
        $pageIndex=isset($data->pageIndex)?$data->pageIndex:"0";
        $pageSize=isset($data->pageSize)?$data->pageSize:"";
        $pageIndex=intval($pageIndex)+1;

    $all=array();

        $sql="  select 
        cliente as contratante,
  sum(cuantia) as CUANTIA,
  count(1) as cantidad
from
 vm_escenario_seis
WHERE ";

 if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;
        
        if($sujeto!="")
            $sql.=" and trim(cliente)='".$sujeto."'";

 
$sql.=" GROUP BY
  cliente ";

    


     if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
      //      $sql.= " LIMIT ".($pageInit>0?$pageInit.",":"")." ".$pageSize;
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";

   $all=$this->getListAllRows($sql);
    return $all;
}



public function getAlertaSeisTotal($data)
{

    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $sujeto=$this->getValorString($data->nombrecontratante);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;

    $all=array();

        $sql=" 

select count(1) as cantidad from (
select 1
from
 vm_escenario_seis
WHERE ";

 if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

         if($sujeto!="")
            $sql.=" and trim(cliente)='".$sujeto."'";

$sql.=" GROUP BY
  cliente ) t1 ";

    $all=$this->getAllTotal($sql);
    return $all;
}



public function getAlertaSieteTotal($data)
{

    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;

    $all=array();

        $sql=" 

select count(1) as cantidad from (
select 1
from
 vm_escenario_siete
WHERE ";

 if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;
$sql.=" GROUP BY
  cliente ) t1 ";

    $all=$this->getAllTotal($sql);
    return $all;
}



public function getAlertaOnceTotal($data)
{

    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;
         $sujeto=isset($data->nombrecontratante)?trim($data->nombrecontratante):"";

    $all=array();

        $sql=" 

select count(1) as cantidad from (
select 1
from
 vm_escenario_once
WHERE  partidaregistral is not null and ";

 if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

          if($sujeto!="")
            $sql.=" and trim(partidaregistral)='".$sujeto."'";

$sql.=" GROUP BY
  trim(partidaregistral) HAVING count(1)>=3 ) t1 ";

    $all=$this->getAllTotal($sql);
    return $all;
}

public function getAlertaSiete($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;

    $all=array();

        $sql="  select 
        cliente as contratante,
  sum(cuantia) as CUANTIA,
  count(1) as cantidad
from
 vm_escenario_siete
WHERE ";

 if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;
        

 
$sql.=" GROUP BY
  cliente ";

     if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
      //      $sql.= " LIMIT ".($pageInit>0?$pageInit.",":"")." ".$pageSize;
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";
    $all=$this->getListAllRows($sql);
    return $all;
}



public function getAlertaQuince($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $nombrecontratante=$this->getValorString($data->nombrecontratante);
 
        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;
    $all=array();
        $sql="  select 
        sujeto,
  count(1) as cantidad
from
 vm_escenario_quince
WHERE ";

 if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

        if($nombrecontratante!="")
            $sql.=" and sujeto='".$nombrecontratante."'";
        
 
$sql.=" GROUP BY
  sujeto HAVING COUNT(sujeto) > 3 ";

     if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
      //      $sql.= " LIMIT ".($pageInit>0?$pageInit.",":"")." ".$pageSize;
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";
    $all=$this->getListAllRows($sql);
    return $all;
}



public function getAlertaDieciseis($data)
{


    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=3;
    $idGrupo=33;

    $numdocumento=$this->getValorString($data->numdocumento);
    $sujeto=$this->getValorString($data->nombrecontratante);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;

// die($idGrupo);

    $sql="  
        select id,sujeto as contratante,numdoc,idgrupo,
        (select count(1) from ocpreporte.detalle_alerta_ocp where idalerta=a.id)
        as cantidad
         from ocpreporte.alerta_ocp a
        where idtipoalerta=".$idTipoAlerta." and idGrupo=".$idGrupo." ";

        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

          $sql.="
               AND  exists (
            select 1 from ocpreporte.detalle_alerta_ocp where idalerta=a.id
            and fechacarga BETWEEN '".$inicio."' AND '".$fin."'
            FETCH NEXT 1 ROWS ONLY 
            )

          ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

          if($numdocumento!="")
            $sql.=" and numdoc='".$numdocumento."'";

          if($sujeto!="")
            $sql.=" and sujeto like '".trim($sujeto)."%'";
        
          if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";

         $all=$this->getListAllRows($sql);
        return $all;
}



public function getAlertaDiecisiete($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $nombrecontratante=$this->getValorString($data->nombrecontratante);
 
        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;
    $all=array();
        $sql="  select 
        sujeto,
  count(1) as cantidad
from
 vm_escenario_diecisiete
WHERE ";

 if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

        if($nombrecontratante!="")
            $sql.=" and sujeto='".$nombrecontratante."'";
        
$sql.=" GROUP BY
  sujeto  ";

     if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
      //      $sql.= " LIMIT ".($pageInit>0?$pageInit.",":"")." ".$pageSize;
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";
    $all=$this->getListAllRows($sql);
    return $all;
}





public function getAlertaDoce($data)
{

   
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=3;
    $idGrupo=2;

    $numdocumento=$this->getValorString($data->numdocumento);
    $sujeto=$this->getValorString($data->nombrecontratante);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;

// die($idGrupo);

    $sql="  
        select id,sujeto as sujeto,numdoc,idgrupo,
        (select count(1) from ocpreporte.detalle_alerta_ocp where idalerta=a.id)
        as cantidad
         from ocpreporte.alerta_ocp a
        where idtipoalerta=".$idTipoAlerta." and idGrupo=".$idGrupo." ";

        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

          $sql.="
               AND  exists (
            select 1 from ocpreporte.detalle_alerta_ocp where idalerta=a.id
            and fechacarga BETWEEN '".$inicio."' AND '".$fin."'
            FETCH NEXT 1 ROWS ONLY 
            )

          ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

          if($numdocumento!="")
            $sql.=" and numdoc='".$numdocumento."'";

          if($sujeto!="")
            $sql.=" and sujeto like '".trim($sujeto)."%'";
        
          if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";

 //       die($sql);
         $all=$this->getListAllRows($sql);
        return $all;
}



public function getAlertaUno($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=$this->getValorString($data->idTipoAlerta);
    $idGrupo=11;

    $numdocumento=$this->getValorString($data->numdocumento);
    $sujeto=$this->getValorString($data->nombrecontratante);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;

// die($idGrupo);

    $sql="  
        select id,sujeto as contratante,numdoc,idgrupo,
        (select count(1) from ocpreporte.detalle_alerta_ocp where idalerta=a.id)
        as cantidad
         from ocpreporte.alerta_ocp a
        where idtipoalerta=".$idTipoAlerta." and idGrupo=".$idGrupo." ";

        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

          $sql.="
               AND  exists (
            select 1 from ocpreporte.detalle_alerta_ocp where idalerta=a.id
            and fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."'
            FETCH NEXT 1 ROWS ONLY 
            )

          ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

          if($numdocumento!="")
            $sql.=" and numdoc='".$numdocumento."'";

          if($sujeto!="")
            $sql.=" and sujeto like '".trim($sujeto)."%'";
        
          if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";

      //  die($sql);
         $all=$this->getListAllRows($sql);
        return $all;
 }


public function getAlertaVeinte($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=$this->getValorString($data->idTipoAlerta);
    $idGrupo=5;

    $numdocumento=$this->getValorString($data->numdocumento);
    $sujeto=$this->getValorString($data->nombrecontratante);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;

// die($idGrupo);

    $sql="  
        select id,sujeto as contratante,numdoc,idgrupo,
        (select count(1) from ocpreporte.detalle_alerta_ocp where idalerta=a.id)
        as cantidad
         from ocpreporte.alerta_ocp a
        where idtipoalerta=".$idTipoAlerta." and idGrupo=".$idGrupo." ";

        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

          $sql.="
               AND  exists (
            select 1 from ocpreporte.detalle_alerta_ocp where idalerta=a.id
            and fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."'
            FETCH NEXT 1 ROWS ONLY 
            )

          ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

          if($numdocumento!="")
            $sql.=" and numdoc='".$numdocumento."'";

          if($sujeto!="")
            $sql.=" and sujeto like '".trim($sujeto)."%'";
        
          if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";

 //       die($sql);
         $all=$this->getListAllRows($sql);
        return $all;
 }


public function getAlertaGeneral($data,$idGrupox)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=$this->getValorString($data->idTipoAlerta);
    $idGrupo=$idGrupox;

    $numdocumento=$this->getValorString($data->numdocumento);
    $sujeto=$this->getValorString($data->nombrecontratante);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;

// die($idGrupo);

    $sql="  
        select id,sujeto as contratante,numdoc,idgrupo,
        (select count(1) from ocpreporte.detalle_alerta_ocp where idalerta=a.id)
        as cantidad
         from ocpreporte.alerta_ocp a
        where idtipoalerta=".$idTipoAlerta." and idGrupo=".$idGrupo." ";

        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

          $sql.="
               AND  exists (
            select 1 from ocpreporte.detalle_alerta_ocp where idalerta=a.id
            and TRUNC(fechacarga) BETWEEN TO_DATE('".$inicio."', 'DD/MM/YYYY')   AND   TO_DATE('".$fin."', 'DD/MM/YYYY')  
              FETCH NEXT 1 ROWS ONLY 
            )

          ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

          if($numdocumento!="")
            $sql.=" and numdoc='".$numdocumento."'";

          if($sujeto!="")
            $sql.=" and sujeto like '".trim($sujeto)."%'";
        
          if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";

     die($sql);
         $all=$this->getListAllRows($sql);
        return $all;
 }

public function getAlertaGeneralTotal($data,$idGrupox)
{
//    $data=json_decode($data);
    $db=$this->connect();


    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=$this->getValorString($data->idTipoAlerta);
    $numdocumento=$this->getValorString($data->numdocumento);
    $idGrupo=$idGrupox;

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;

    $sql="  
        select count(1) as cantidad from ocpreporte.alerta_ocp a
        where idtipoalerta=".$idTipoAlerta." and idGrupo=".$idGrupo." ";



        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

          $sql.="
               AND  exists (
            select 1 from ocpreporte.detalle_alerta_ocp where idalerta=a.id
            and fechacarga BETWEEN '".$inicio."' AND '".$fin."'
            FETCH NEXT 1 ROWS ONLY 
            )

          ";
         }


        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;
        
        if($numdocumento!="")
            $sql.=" and numdoc='".$numdocumento."'";


    $all=$this->getAllTotal($sql);
    return $all;
 }





public function getAlertaVeinteTotal($data)
{
//    $data=json_decode($data);
    $db=$this->connect();


    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=$this->getValorString($data->idTipoAlerta);
    $numdocumento=$this->getValorString($data->numdocumento);
    $idGrupo=5;

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;



    $sql="  
        select count(1) as cantidad from ocpreporte.alerta_ocp a
        where idtipoalerta=".$idTipoAlerta." and idGrupo=".$idGrupo." ";



        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

          $sql.="
               AND  exists (
            select 1 from ocpreporte.detalle_alerta_ocp where idalerta=a.id
            and fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."'
            FETCH NEXT 1 ROWS ONLY 
            )

          ";
         }


        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;
        
        if($numdocumento!="")
            $sql.=" and numdoc='".$numdocumento."'";


    $all=$this->getAllTotal($sql);
    return $all;
 }






public function getAlertaUnoTotal($data)
{
//    $data=json_decode($data);
    $db=$this->connect();


    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=$this->getValorString($data->idTipoAlerta);
    $numdocumento=$this->getValorString($data->numdocumento);
    $idGrupo=11;

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;



    $sql="  
        select count(1) as cantidad from ocpreporte.alerta_ocp a
        where idtipoalerta=".$idTipoAlerta." and idGrupo=".$idGrupo." ";



        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

          $sql.="
               AND  exists (
            select 1 from ocpreporte.detalle_alerta_ocp where idalerta=a.id
            and fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."'
            FETCH NEXT 1 ROWS ONLY 
            )

          ";
         }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;
        
        if($numdocumento!="")
            $sql.=" and numdoc='".$numdocumento."'";
    $all=$this->getAllTotal($sql);
    return $all;
 }

public function getAlertaDieciseisTotal($data)
{
   //    $data=json_decode($data);
    $db=$this->connect();


    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=3;
    $numdocumento=$this->getValorString($data->numdocumento);
    $idGrupo=33;

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;

    $sql="  
        select count(1) as cantidad from ocpreporte.alerta_ocp a
        where idtipoalerta=".$idTipoAlerta." and idGrupo=".$idGrupo." ";



        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

          $sql.="
               AND  exists (
            select 1 from ocpreporte.detalle_alerta_ocp where idalerta=a.id
            and fechacarga BETWEEN '".$inicio."' AND '".$fin."'
            FETCH NEXT 1 ROWS ONLY 
            )

          ";
         }


        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;
        
        if($numdocumento!="")
            $sql.=" and numdoc='".$numdocumento."'";


    $all=$this->getAllTotal($sql);
    return $all;
}




public function getAlertaDiecisieteTotal($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $nombrecontratante=$this->getValorString($data->nombrecontratante);
    


    $all=array();
        $sql="

select count(1) as cantidad from (
select 
sujeto,
  count(1) as cantidad
from
 vm_escenario_diecisiete
WHERE ";

 if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

        if($nombrecontratante!="")
            $sql.=" and sujeto='".$nombrecontratante."'";

        
 
$sql.=" GROUP BY
  sujeto ) t1 ";

    $all=$this->getAllTotal($sql);
    return $all;
}



public function getAlertaDoceTotal($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $nombrecontratante=$this->getValorString($data->nombrecontratante);
    


    $all=array();
        $sql="

select count(1) as cantidad from (
select 
NUMERODEKARDEX,
  count(1) as cantidad
from
 vm_escenario_doce
WHERE ";

 if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

        if($nombrecontratante!="")
            $sql.=" and cliente='".$nombrecontratante."'";

        
 
$sql.=" GROUP BY
  NUMERODEKARDEX ) t1 ";

    $all=$this->getAllTotal($sql);
    return $all;
}







public function getAlertaQuinceTotal($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $nombrecontratante=$this->getValorString($data->nombrecontratante);
    


    $all=array();
        $sql="

select count(1) as cantidad from (
select 
sujeto,
  count(1) as cantidad
from
 vm_escenario_quince
WHERE ";

 if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

        if($nombrecontratante!="")
            $sql.=" and sujeto='".$nombrecontratante."'";

        
 
$sql.=" GROUP BY
  sujeto HAVING COUNT(sujeto) > 3 ) t1 ";

    $all=$this->getAllTotal($sql);
    return $all;
}




public function getAlertaDiezTotal($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $nombrecontratante=$this->getValorString($data->nombrecontratante);
    


    $all=array();
        $sql="

select count(1) as cantidad from (
select 
partidaregistral,
  count(1) as cantidad
from
 vm_escenario_diez
WHERE partidaregistral is not null  and ";

 if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

        if($nombrecontratante!="")
            $sql.=" and cliente='".$nombrecontratante."'";

        
 
$sql.=" GROUP BY
  partidaregistral
HAVING count(1)>=3 ) t1 ";

    $all=$this->getAllTotal($sql);
    return $all;
}


public function getAlertaDiez($data)
{

    $db=$this->connect();
    $idColegio=isset($data->idColegio)?$data->idColegio:"0";
    $idNotaria=isset($data->idNotaria)?$data->idNotaria:"0";
    $fechaInicio=isset($data->fechaInicio)?$data->fechaInicio:"";
    $fechaFin=isset($data->fechaFin)?$data->fechaFin:"";
  
        $pageIndex=isset($data->pageIndex)?$data->pageIndex:"0";
        $pageSize=isset($data->pageSize)?$data->pageSize:"";
        $pageIndex=intval($pageIndex)+1;
    $sujeto=$this->getValorString($data->nombrecontratante);

    $all=array();

        $sql="  select 
        partidaregistral as sujeto,
  count(1) as cantidad
from
 vm_escenario_diez
WHERE partidaregistral is not null and ";

 if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.="  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;
        
         if($sujeto!="")
            $sql.=" and partidaregistral='".$sujeto."'";
 
$sql.=" GROUP BY
  partidaregistral
HAVING count(1)>=3";

     if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
      //      $sql.= " LIMIT ".($pageInit>0?$pageInit.",":"")." ".$pageSize;
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";

    $all=$this->getListAllRows($sql);
    return $all;

}



public function getGrupoAlertaOcp()
{
      $db=$this->connect();
    $sql="  
         select id,codigo,upper(descripcion) as descripcion from OCPREPORTE.grupo_alerta where tipo='sg'
         order by id
       ";
     $stid = oci_parse($db,$sql);
     oci_execute($stid);
       $all=array();
        while (($row = oci_fetch_assoc($stid)) != false) {
            $all[]=$row;
        }
        return $all;
}

public function getTipoAlertaOcp($data)
{
    $data=json_decode($data);
    $db=$this->connect();
    $idGrupo=isset($data->idGrupo)?$data->idGrupo:"0";
    $all=array();
    if(intval($idGrupo)>0)
    {

/*      if($idGrupo==7 || $idGrupo==8 || $idGrupo==9)
      {*/

         $sql="  
          select codigo as id,id as punto, (prefijo||' '||descripcion) as descripcion, punto from ocpreporte.tipo_alerta 
          where idgrupoalerta=".$idGrupo." AND activo=1 and tipogrupo='cnt'  ";
       $stid = oci_parse($db,$sql);
       oci_execute($stid);
         
          while (($row = oci_fetch_assoc($stid)) != false) {
              $all[]=$row;
          } 
/*
      }else{
          $sql="  
          select id, (etiquetaalerta||' '||descripcion) as descripcion,indicealerta as punto from sisgen.tipoalerta 
          where idgrupoalerta=".$idGrupo." AND monitoractivo=1 ";
       $stid = oci_parse($db,$sql);
       oci_execute($stid);
         
          while (($row = oci_fetch_assoc($stid)) != false) {
              $all[]=$row;
          } 

      }*/
    }
  
        return $all;
}

public function getTipoAlertaSeg($data)
{
    $data=json_decode($data);
    $db=$this->connect();
    $idGrupo=isset($data->idGrupo)?$data->idGrupo:"0";
    $all=array();
    if(intval($idGrupo)>0)
    {
        $sql="  
        select id, PREFIJO||' '||CODIGO||' - '||upper(descripcion) as descripcion,codigo,punto from ocpreporte.tipo_alerta 
        where tipogrupo='seg' and idgrupoalerta=".$idGrupo."  ORDER BY codigo";

   //     die($sql);
     $stid = oci_parse($db,$sql);
     oci_execute($stid);
       
        while (($row = oci_fetch_assoc($stid)) != false) {
            $all[]=$row;
        }


   // die($sql);
    }

  
        return $all;
}

public function getTipoAlerta($data)
{
    $data=json_decode($data);
    $db=$this->connect();
    $idGrupo=isset($data->idGrupo)?$data->idGrupo:"0";
    $all=array();
    if(intval($idGrupo)>0)
    {
        $sql="  
        select id, upper(descripcion) as descripcion,codigo,punto from ocpreporte.tipo_alerta 
        where activo=1 and idgrupoalerta=".$idGrupo." ORDER BY punto";

      //  die($sql);
     $stid = oci_parse($db,$sql);
     oci_execute($stid);
       
        while (($row = oci_fetch_assoc($stid)) != false) {
            $all[]=$row;
        }
    }
  
        return $all;
}

public function getDetalleAlertaUNo($data)
{
    $data=json_decode($data);
    $db=$this->connect();
    $idGrupo=isset($data->idGrupo)?$data->idGrupo:"0";
    $all=array();
    if(intval($idGrupo)>0)
    {
        $sql="  
        select id, upper(descripcion) as descripcion,codigo from ocpreporte.tipo_alerta 
        where idgrupoalerta=".$idGrupo." ORDER BY descripcion";
     $stid = oci_parse($db,$sql);
     oci_execute($stid);
       
        while (($row = oci_fetch_assoc($stid)) != false) {
            $all[]=$row;
        }
    }
  
        return $all;
}

public function getDetalleAlertaVeinte($data)
{
    $data=json_decode($data);
    $db=$this->connect();
    $idGrupo=isset($data->idGrupo)?$data->idGrupo:"0";
    $all=array();
    if(intval($idGrupo)>0)
    {
        $sql="  
        select id, upper(descripcion) as descripcion,codigo from ocpreporte.tipo_alerta 
        where idgrupoalerta=".$idGrupo." ORDER BY descripcion";
     $stid = oci_parse($db,$sql);
     oci_execute($stid);
       
        while (($row = oci_fetch_assoc($stid)) != false) {
            $all[]=$row;
        }
    }
  
        return $all;
}


public function getRptList($get_data)
       {        


        $info=json_decode($get_data);

        $sqlWhere="";
        $sqlInner="";
        $isLimit=true;
        $sqlSelect="";
        $sqlGroup="";
        $sqlOrder="";

        if($info->fechaInicio!="" && $info->fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($info->fechaInicio));
            $fin = date("d/m/Y", strtotime($info->fechaFin));

     
        }


       
       $db=$this->connect();

 $sql="SELECT a.*,nvl(ea.descripcion,'EN EVALUACION') as estado_alerta_ocp  FROM OCPREPORTE.ALERTA_RPT_SISGEN a
                 LEFT JOIN OCPREPORTE.ALERTA at on a.id=at.id_alerta_sisgen 
                 LEFT JOIN OCPREPORTE.ESTADO_ALERTA ea on at.ID_ESTADO_ALERTA=ea.ID 
                  where   TO_char(a.fechaalerta,'DD/MM/YYYY')  between '".$inicio."'  and '".$fin."' ";
            if(intval($info->idColegio)>0)
                $sql .= " AND a.idcolegio=".$info->idColegio;
            //d/m/Y
            if(intval($info->idNotaria)>0)
                $sql .=  " AND a.idnotaria=".$info->idNotaria;


            if(intval($info->idTipoAlerta)>0)
                $sql .=  " AND a.idtipoalerta=".$info->idTipoAlerta;

            if($info->numerokardex!="" && $info->numerokardex!=null)
                $sql .=  " AND a.kardex=".$info->numerokardex;

            if(intval($info->idEstadoOcp)>0)
            {
                 if(intval($info->idEstadoOcp)==2)
                    $sql .=  " AND ( at.id_estado_alerta=".$info->idEstadoOcp." OR at.id_estado_alerta is null)";
                else
                    $sql.=  " AND at.id_estado_alerta=".$info->idEstadoOcp;
            }
            $sql .= " order by a.fechaalerta desc ";



$stid = oci_parse($db,
$sql
);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $rowCount = 1;
       
        $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("C1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("D1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("E1")->getFont()->setBold(true);
         $objPHPExcel->getActiveSheet()->getStyle("F1")->getFont()->setBold(true);
          $objPHPExcel->getActiveSheet()->getStyle("G1")->getFont()->setBold(true);
           $objPHPExcel->getActiveSheet()->getStyle("H1")->getFont()->setBold(true);

        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        $objPHPExcel->getActiveSheet()->SetCellValue('A1',"COLEGIO");

        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->SetCellValue('B1',"NOTARIA");

        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->SetCellValue('C1',"FECHA ALERTA");

            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
        $objPHPExcel->getActiveSheet()->SetCellValue('D1',"DESCRIPCION");


            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->SetCellValue('E1',"KARDEX");


            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(50);
        $objPHPExcel->getActiveSheet()->SetCellValue('F1',"SUJETO");

           $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->SetCellValue('G1',"ESTADO SISGEN");

           $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->SetCellValue('H1',"ESTADO OCP");

        oci_execute($stid);
        $data=[];
        $i=2;
        while (($row = oci_fetch_assoc($stid)) != false) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i,$row["COLEGIO"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i,$row["NOTARIA"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i,$row["FECHAALERTA"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i,$row["DESCRIPCION"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i,$row["KARDEX"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i,$row["SUJETO"]);
             $objPHPExcel->getActiveSheet()->SetCellValue('G'.$i,$row["ESTADO"]);
              $objPHPExcel->getActiveSheet()->SetCellValue('H'.$i,$row["ESTADO_ALERTA_OCP"]);
           
            
            $i++;
        }

        oci_free_statement($stid);
        oci_close($db);

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $nameRpt="report".uniqid();
        $objWriter->save('rpt/'.$nameRpt.'.xlsx');

        $id=uniqid();

        return "rpt/".$nameRpt.".xlsx";

 }

 function cellColor($cells,$color,$objPHPExcel){
    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => $color
        )
    ));
}

}
?>