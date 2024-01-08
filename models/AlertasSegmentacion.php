<?php 


class Models_AlertasSegmentacion extends DB_Connect {
        
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
    
public function getAlertaList($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=$this->getValorString($data->idTipoAlerta);
    $idGrupo=$this->getValorNumerico($data->idGrupoAlerta);

    $numdocumento=$this->getValorString($data->numdocumento);
    $sujeto=$this->getValorString($data->nombrecontratante);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;

// die($idGrupo);

    $sql="  
        select id,sujeto,numdoc,idgrupo,
        (select count(1) from ocpreporte.detalle_alerta where idalerta=a.id)
        as cantidad
         from ocpreporte.alerta a
        where idtipoalerta=".$idTipoAlerta." and idGrupo=".$idGrupo." ";

        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.=" AND  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
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

   //     die($sql);
         $all=$this->getListAllRows($sql);
        return $all;
 }






public function getAlertaCount($data)
{
//    $data=json_decode($data);
    $db=$this->connect();


    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=$this->getValorString($data->idTipoAlerta);
    $numdocumento=$this->getValorString($data->numdocumento);
    $idGrupo=$this->getValorNumerico($data->idGrupoAlerta);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;



    $sql="  
        select count(1) as cantidad from ocpreporte.alerta
        where idtipoalerta=".$idTipoAlerta." and idGrupo=".$idGrupo." ";

        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.=" AND  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
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


public function getAlertaClienteList($data)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=$this->getValorString($data->idTipoAlerta);
     $numdocumento=$this->getValorString($data->numdocumento);
        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;



    $sql="  
        select id,cliente as sujeto,numdoc,idgrupo,
        1
        as cantidad
         from OCPREPORTE.ALERTA_CLIENTE a
        where idtipoalerta=".$idTipoAlerta." ";

        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.=" AND  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

          if($numdocumento!="")
            $sql.=" and numdoc='".$numdocumento."'";

        
          if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";

//        die($sql);
         $all=$this->getListAllRows($sql);
        return $all;
 }
public function getAlertaClienteCount($data)
{
//    $data=json_decode($data);
    $db=$this->connect();


    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=$this->getValorString($data->idTipoAlerta);
    $numdocumento=$this->getValorString($data->numdocumento);


        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;



    $sql="  
        select count(1) as cantidad from ocpreporte.ALERTA_CLIENTE
        where idtipoalerta=".$idTipoAlerta." ";

        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.=" AND  fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
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



 public function getDetalleUmbralList($data)
{

    $data=json_decode($data);
    $db=$this->connect();
    $numdoc=$this->getValorNumerico($data->numdoc);
    $pageIndex=$this->getValorNumerico($data->pageIndex);
    $pageSize=$this->getValorString($data->pageSize);
    $pageIndex=intval($pageIndex)+1;

    $sql="
         select distinct idcolegio,idnotaria,colegio,notaria,cliente as sujeto,to_char(fechaautorizacion,'dd/mm/YYYY') as fechaautorizacion,
             numerodekardex,numerodeinstrumento,numdoc,actojuridico,
             tipodocumento as tipo_documento
             
             from OCPREPORTE.cabecera_segmentacion where numdoc='".$numdoc."'
             and TRUNC(fechacarga) >= TO_DATE('01/03/2023', 'DD/MM/YYYY') 
    ";
        /*
          if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";

        */
         $all=$this->getListAllRows($sql);
        return $all;
 }

 public function getDetalleList($data)
{
    $data=json_decode($data);
    $db=$this->connect();
    $idalerta=$this->getValorNumerico($data->idAlerta);
    $pageIndex=$this->getValorNumerico($data->pageIndex);
    $idGrupo=$this->getValorNumerico($data->idGrupo);
    $pageSize=$this->getValorString($data->pageSize);
    $pageIndex=intval($pageIndex)+1;

    if($idGrupo==8)
    {
            $sql="  
       select colegio,notaria,cliente as sujeto,fechaautorizacion,numerodekardex,numerodeinstrumento,
idcolegio,idnotaria,idgrupo
from ocpreporte.ALERTA_CLIENTE where id=".$idalerta;

    }else{
          $sql="  
       select colegio,notaria,sujeto,fechaautorizacion,numerodekardex,numerodeinstrumento,
idcolegio,idnotaria
from ocpreporte.detalle_alerta where idalerta=".$idalerta;

    }
        /*
          if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";

        */
         $all=$this->getListAllRows($sql);
        return $all;
 }

public function getUmbralesNaturales($data,$tipoPersona)
{
     $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $nombrecontratante=$this->getValorString($data->nombrecontratante);
    

    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=$this->getValorString($data->idTipoAlerta);
     $numdocumento=$this->getValorString($data->numdocumento);
        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;



    $sql=" 
SELECT distinct d.idcolegio,d.idNotaria,tipo_documento,fechaautorizacion,
notaria,colegio,sujeto,numdoc,numerodekardex,categoria_acto,segmento,
categoria_acto as acto,

TO_CHAR(monto_cliente, '999G999G999D00', 'NLS_NUMERIC_CHARACTERS = ''.,''')
as monto_cliente,
TO_CHAR(umbral, '999G999G999D00', 'NLS_NUMERIC_CHARACTERS = ''.,''')
as umbral,
TO_CHAR(diferencia, '999G999G999D00', 'NLS_NUMERIC_CHARACTERS = ''.,''')
as diferencia

 FROM OCPREPORTE.UMBRAL_FINAL2 d WHERE

     ";

        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

              $sql.="
             TRUNC(d.fechacarga) BETWEEN TO_DATE('".$inicio."', 'DD/MM/YYYY')   AND   TO_DATE('".$fin."', 'DD/MM/YYYY')  

          ";

         //   $sql.="   d.fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        if(intval($idNotaria)>0)
            $sql.=" and d.idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and d.idcolegio=".$idColegio;

        if($nombrecontratante!="")
            $sql.=" and d.sujeto like '".trim($nombrecontratante)."%'";

          if($numdocumento!="")
            $sql.=" and d.numdoc='".$numdocumento."'";

        if($tipoPersona=="N")
            $sql.=" and tipo_persona='N'";
        else if($tipoPersona=="J")
           $sql.=" and tipo_persona='J'";
        
        $sql.=" order by d.sujeto,segmento ";
        
          if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";

  
//      die($sql);
      $all=$this->getListAllRows($sql);
        return $all;
}


public function getUmbralesNaturalesCount($data,$tipoPersona)
{
  $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=$this->getValorString($data->idTipoAlerta);
     $numdocumento=$this->getValorString($data->numdocumento);
     $nombrecontratante=$this->getValorString($data->nombrecontratante);
        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;



    $sql=" 

select count(1) as cantidad from (
 SELECT distinct d.idcolegio,d.idNotaria,tipo_documento,fechaautorizacion,
notaria,colegio,sujeto,numdoc,numerodekardex,categoria_acto,segmento,
acto,monto_cliente,
umbral,diferencia FROM OCPREPORTE.UMBRAL_FINAL2 d WHERE


     ";

        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

//            $sql.="   d.fechaautorizacion BETWEEN '".$inicio."' AND '".$fin."' ";

                $sql.="
             TRUNC(d.fechacarga) BETWEEN TO_DATE('".$inicio."', 'DD/MM/YYYY')   AND   TO_DATE('".$fin."', 'DD/MM/YYYY')  ";

        }
        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

         if($nombrecontratante!="")
            $sql.=" and d.sujeto like '".trim($nombrecontratante)."%'";

          if($numdocumento!="")
            $sql.=" and numdoc='".$numdocumento."'";

            if($tipoPersona=="N")
                $sql.=" and tipo_persona='N'";
            else if($tipoPersona=="J")
                $sql.=" and tipo_persona='J'";

        $sql.=" ) ";
 
 
    $all=$this->getAllTotal($sql);
    return $all;
 }

}

