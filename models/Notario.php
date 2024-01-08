<?php 
class Models_Notario extends DB_Connect {
        
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
    





public function getNotariaById($get_data)
{
   $get_data=json_decode($get_data);
   $db=$this->connect();
   $sql="SELECT 
   EMAIL as EMAIL_NOTARIO ,EMAIL2 AS EMAIL_OFICIOS,
    EMAIL_COBRANZAS FROM DATAHISTORICA.NOTARIOS  WHERE ID=".$get_data->idnotaria;
   $stid = oci_parse($db,$sql);
   oci_execute($stid);
   $row = oci_fetch_assoc($stid);
   oci_free_statement($stid);
   oci_close($db); 

   return ($row);

}


public function getNotarias($qBuscar,$idColegio)
{
    $sqlBuscar="";
    if($qBuscar!="")
    {
        $sqlBuscar.=" WHERE UPPER(N.DESCRIPCION) LIKE '%".strtoupper($qBuscar)."%'";
    }


    if($idColegio!="" && intval($idColegio)>0)
    {
        if($sqlBuscar=="")
            $sqlBuscar.=" WHERE ";
        else
            $sqlBuscar.=" AND ";
            
        $sqlBuscar.="  N.IDCOLEGIO=".$idColegio;
    }

    $db=$this->connect();
    $sql="SELECT * FROM (
SELECT N.ID,UPPER(N.DESCRIPCION) AS NOMBRE FROM SISGEN.NOTARIA  N
".$sqlBuscar."
ORDER BY N.DESCRIPCION
) WHERE ROWNUM<=20
";
       $stid = oci_parse($db, $sql);
       oci_execute($stid);
       $data=[];
        while (($row = oci_fetch_assoc($stid)) != false) {
            $data[]=$row;       
        }

        oci_free_statement($stid);
        oci_close($db);   
        return $data;   
}


public function getListNotariasCount($get_data)
{

$info=json_decode($get_data);
$db=$this->connect();

$sql="
SELEcT count(1) as total FROM (";

$sql.="
SELECT DISTINCT NN.ID,CODIGO,UPPER(N.DESCRIPCION) AS NOMBRE,NN.EMAIL as EMAIL_NOTARIO ,NN.EMAIL2 AS EMAIL_OFICIOS,
NN.EMAIL_COBRANZAS,
N.ID AS IDNOTARIA_SISGEN 
FROM OCPREPORTE.NOTARIA N 
INNER JOIN DATAHISTORICA.NOTARIOS NN ON N.CODIGO=NN.RUC
LEFT JOIN OCPREPORTE.NOTARIAOFICIO  P ON N.ID=P.IDNOTARIA WHERE N.ID>0
";

if($info->idColegio!="")
    $sql.=" AND N.IDCOLEGIO=".$info->idColegio;

if($info->idNotaria!="")
    $sql.=" AND N.ID=".$info->idNotaria;

if($info->isSoloActivos==true)
{
    $sql.=" AND P.ACTIVO=1";
}
$sql.=" ) t";

$stid = oci_parse($db, $sql);
 oci_execute($stid);
$row = oci_fetch_assoc($stid);
oci_free_statement($stid);
oci_close($db);   
return $row["TOTAL"];     

}




public function getListNotarias($get_data)
{

$info=json_decode($get_data);
$pageInit=0;
        if($info->pageIndex!="" && $info->pageIndex>0)
            $pageInit=((int)$info->pageIndex*(int)$info->pageSize)+1;
        
        $valueMax= ((int)$info->pageSize*((int)$info->pageIndex+1));
       
$db=$this->connect();

$sql="
SELECT DISTINCT P.ACTIVO,NN.ID,CODIGO,UPPER(N.DESCRIPCION) AS NOMBRE,UPPER(C.NOMBRE) AS COLEGIO,NN.EMAIL as EMAIL_NOTARIO ,NN.EMAIL2 AS EMAIL_OFICIOS,
NN.EMAIL_COBRANZAS,
N.ID AS IDNOTARIA_SISGEN 
FROM OCPREPORTE.NOTARIA N 
INNER JOIN SISGEN.COLEGIO C ON N.IDCOLEGIO=C.ID
INNER JOIN DATAHISTORICA.NOTARIOS NN ON N.CODIGO=NN.RUC
LEFT JOIN OCPREPORTE.NOTARIAOFICIO  P ON N.ID=P.IDNOTARIA WHERE N.ID>0
";

if($info->idColegio!="")
    $sql.=" AND N.IDCOLEGIO=".$info->idColegio;

if($info->idNotaria!="")
    $sql.=" AND N.ID=".$info->idNotaria;

if($info->isSoloActivos==true)
{
    $sql.=" AND P.ACTIVO=1";
}

    
     $sql.=" ORDER BY NOMBRE  ";

        if($valueMax!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit-1:"0")." ROWS FETCH NEXT ".$info->pageSize." ROWS ONLY ";

//die($sql);
       $stid = oci_parse($db,$sql);
       oci_execute($stid);
       $data=[];
       $k=0;
        while (($row = oci_fetch_assoc($stid)) != false) {
            $row["NUMROW"]=$k+1;
            $row["EMAIL_NOTARIO"]=str_replace(";","\n",$row["EMAIL_NOTARIO"]);
            $row["EMAIL_OFICIOS"]=str_replace(";","\n",$row["EMAIL_OFICIOS"]);
            $row["EMAIL_COBRANZAS"]=str_replace(";","\n",$row["EMAIL_COBRANZAS"]);

            $data[]=$row;
        $k++;
        }
        oci_free_statement($stid);
        oci_close($db);   
        return $data;     

}

public function getListNotariasSeleccionCount($get_data)
{
$info=json_decode($get_data);
$db=$this->connect();

$sql="
SELECT COUNT(*) AS TOTAL FROM OCPREPORTE.NOTARIA N LEFT JOIN
DATAHISTORICA.NOTARIAPETICION P ON N.ID=P.IDNOTARIA WHERE N.ID>0
";

if($info->idColegio!="")
    $sql.=" AND N.IDCOLEGIO=".$info->idColegio;

if($info->idNotaria!="")
    $sql.=" AND N.ID=".$info->idNotaria;
   
  
         $stid = oci_parse($db,$sql);
          oci_execute($stid);
       $row = oci_fetch_assoc($stid);
       $total=$row["TOTAL"];
        oci_free_statement($stid);
        oci_close($db);   
        return $total;    
}

public function setNotariocorreo($get_data)
{
     $get_data=($get_data);
   $db=$this->connect();

   $sql="UPDATE DATAHISTORICA.NOTARIOS
    SET EMAIL='".$get_data->emailnotaria."',
    EMAIL2='".$get_data->emailoficios."',
    EMAIL_COBRANZAS='".$get_data->emailcobranza."'
    
     WHERE ID=".$get_data->idnotaria;
   $stid = oci_parse($db,$sql);
   oci_execute($stid);
   oci_free_statement($stid);
   oci_close($db); 

    $response=array("response"=>"correcto","status"=>"1");      
      return ($response);
}
    
}
?>