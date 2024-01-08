<?php 

class Models_Encriptado extends DB_Connect {
        
    /**
     * Constructor de clase
     */
    function my_status_header($setHeader=null) {
        static $theHeader=null;

	}


	   public function getList($get_data)
       {      


  		$info=($get_data);



        $db=$this->connect();
     	
     	$pageIndex=$info["pageIndex"]."";
  		$pageSize=$info["pageSize"]."";
		$pageIndex=intval($pageIndex)+1;


 
       $sql=" 	
	SELECT C.ID,CC.NUM_OFICIO_OCP ,FECHA_OFICIO  AS FECHA_OFICIO_OCP,
E.DESCRIPCION AS ENTIDAD,NUM_OFICIO_ENTIDAD,FECHA_OFICIO_ENTIDAD,FECHA_RECEPCION_ENTIDAD,NUM_REGISTRO,RUTA_ARCHIVO,PROCESADO
FROM OCPREPORTE.CIRCULAR_OCP C INNER JOIN 
DATAHISTORICA.ENTIDAD E ON C.ENTIDAD=E.ID 
INNER JOIN OCPREPORTE.OFICIO_COLEGIO CC ON C.ID=CC.ID_CIRCULAR
 WHERE C.ESTADO=1 AND C.TERMINADO=1
       ";

//PXNOTARIA ES DE LA PLAT. ELECT. NOTARIAL
if(isset($_SESSION["PXNOTARIA"]) && $_SESSION["PXNOTARIA"]!=""){
    $sql.=" 

      AND (
        SELECT COUNT(1) FROM OCPREPORTE.NOTARIA WHERE ID=".$_SESSION["PXNOTARIA"]." AND IDCOLEGIO=CC.IDCOLEGIO
      )>0
  ";
}else
   $sql.=" AND CC.IDCOLEGIO=15 ";



        if(isset($info["fechaOficioOcpInicioSearch"]) && $info["fechaOficioOcpInicioSearch"]!="" &&  isset($info["fechaOficioOcpFinSearch"]) && $info["fechaOficioOcpFinSearch"]!="")
        {
            $inicio = date("d/m/Y", strtotime($info["fechaOficioOcpInicioSearch"]));
            $fin = date("d/m/Y", strtotime($info["fechaOficioOcpFinSearch"]));

            $sql.=" AND C.FECHA_OFICIO BETWEEN '".$inicio."' AND '".$fin."' ";
        }

        if(isset($info["oficioOcpSearch"]) &&  $info["oficioOcpSearch"]!="")
        	$sql.=" AND CC.NUM_OFICIO_OCP LIKE '%".$info["oficioOcpSearch"]."%'";

        if(isset($info["persona"]) &&  $info["persona"]!=""){
          $sql.=" AND 
            (
               SELECT COUNT(1)
              FROM OCPREPORTE.PERSONAINVESTIGADA
              WHERE  ESTADO=1 AND  translate((TRIM(UPPER(APELLIDO_PATERNO))||' '||TRIM(UPPER(APELLIDO_MATERNO))||' '||TRIM(UPPER(NOMBRES))),'ÁÉÍÓÚ','AEIOU') LIKE '".trim(($info["persona"]))."%'
              AND ID_CIRCULAR_OCP=C.ID
            )>0

          ";
        }

                
        $sql.=" ORDER BY C.ID DESC ";


        $pageInit=0;
        if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
      //      $sql.= " LIMIT ".($pageInit>0?$pageInit.",":"")." ".$pageSize;
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";

//    die($info["persona"]);
	   $stid = oci_parse($db,$sql);
       oci_execute($stid);
       $data=[];
        while (($row = oci_fetch_assoc($stid)) != false) {
            $data[]=$row;
        }

        oci_free_statement($stid);
        oci_close($db);   
        return $data;  
}



   public function getCountList($get_data)
       {      


  		$info=($get_data);
        $db=$this->connect();
     	

       $sql=" 	
	SELECT COUNT(1) AS TOTAL
FROM OCPREPORTE.CIRCULAR_OCP C INNER JOIN 
DATAHISTORICA.ENTIDAD E ON C.ENTIDAD=E.ID  
INNER JOIN OCPREPORTE.OFICIO_COLEGIO CC ON C.ID=CC.ID_CIRCULAR
WHERE C.ESTADO=1 AND  C.TERMINADO=1 AND CC.IDCOLEGIO=15 
       ";
        if(isset($info["fechaOficioOcpInicioSearch"]) && $info["fechaOficioOcpInicioSearch"]!="" &&  isset($info["fechaOficioOcpFinSearch"]) && $info["fechaOficioOcpFinSearch"]!="")
        {
            $inicio = date("d/m/Y", strtotime($info["fechaOficioOcpInicioSearch"]));
            $fin = date("d/m/Y", strtotime($info["fechaOficioOcpFinSearch"]));

            $sql.=" AND C.FECHA_OFICIO BETWEEN '".$inicio."' AND '".$fin."' ";
        }

        if(isset($info["oficioOcpSearch"]) &&  $info["oficioOcpSearch"]!="")
            $sql.=" AND CC.NUM_OFICIO_OCP LIKE '%".$info["oficioOcpSearch"]."%'";
  //      	$sql.=" AND NUM_OFICIO LIKE '%".$info["oficioOcpSearch"]."%'";
        
       
        
        if(isset($info["persona"]) &&  $info["persona"]!=""){
          $sql.=" AND 
            (
               SELECT COUNT(1)
              FROM OCPREPORTE.PERSONAINVESTIGADA
              WHERE  ESTADO=1 AND  translate((TRIM(APELLIDO_PATERNO)||' '||TRIM(APELLIDO_MATERNO)||' '||TRIM(NOMBRES)),'áéíóú','AEIOU') LIKE '".trim($info["persona"])."%'
              AND ID_CIRCULAR_OCP=C.ID
            )>0

          ";
        }

	    $stid = oci_parse($db,$sql);
        oci_execute($stid);
        $data=[];
        $row = oci_fetch_assoc($stid);
        $total=$row["TOTAL"];
        oci_free_statement($stid);
        oci_close($db);   
        return $total;  
}

}