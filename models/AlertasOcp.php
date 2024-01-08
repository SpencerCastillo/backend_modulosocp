<?php 


class Models_AlertasOcp extends DB_Connect {
        
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
            and to_char(fechacarga,'dd/mm/YYYY') BETWEEN '".$inicio."' AND '".$fin."'
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
        select count(1) as cantidad from ocpreporte.alerta_ocp a
        where idtipoalerta=".$idTipoAlerta." and idGrupo=".$idGrupo." ";


            if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

          $sql.="
               AND  exists (
            select 1 from ocpreporte.detalle_alerta_ocp where idalerta=a.id
            and to_char(fechacarga,'dd/mm/YYYY') BETWEEN '".$inicio."' AND '".$fin."'
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





}

