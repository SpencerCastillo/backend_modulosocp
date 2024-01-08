<?php 

require 'libs/PHPExcel/Classes/PHPExcel/IOFactory.php';

class Models_OperacionInforme extends DB_Connect {
        
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
    

public function getList()
{
  $idtipoLaft = $this->getValorIntSanit("idtipoLaft");
  $all=array();
  if($idtipoLaft==1)
  {
      $obj=new Models_AlertasCualitativa();
      $all= $obj->getListCualitativaAll();
  }else if($idtipoLaft==2)
  {
    $obj=new Models_OperacionInusual();
      $all= $obj->getListOperacionAll();
  }else if($idtipoLaft==3)
  {
    $obj=new Models_InformeRiesgo();
      $all= $obj->getListInformeAll();
  }else if($idtipoLaft==4)
  {
    $obj=new Models_InformeOficialCumplimiento();
      $all= $obj->getListInformeAll();
  }

  return $all;
}


public function getCountList()
{
  $idtipoLaft = $this->getValorIntSanit("idtipoLaft");
  $total=0;
  if($idtipoLaft==1)
  {
      $obj=new Models_AlertasCualitativa();
      $total= $obj->getListCountCualitativaAll();
  }else if($idtipoLaft==2)
  {
    $obj=new Models_OperacionInusual();
      $total= $obj->getCountOperacionAll();
    //  die("total : ".$total);
  }else if($idtipoLaft==4)
  {
    $obj=new Models_InformeOficialCumplimiento();
      $total= $obj->getCountAll();
  }


  return $total;
}


public function getRptList()
{
  $idtipoLaft = $this->getValorIntSanit("idtipoLaft");
  $response="";
  if($idtipoLaft==1)
  {
      $obj=new Models_AlertasCualitativa();
      $response= $obj->getReporte();
  }
  return $response;
}


public function getList_x()
{
  $iddocumento = $this->getValorIntSanit("iddocumento");
  $idtipoLaft = $this->getValorIntSanit("idtipoLaft");
  $idColegio = $this->getValorIntSanit("idColegio");
  $idNotaria = $this->getValorIntSanit("idNotaria");

  $bfechaInicio=isset($get_data["fechaInicio"])?$get_data["fechaInicio"]:"";
  $bfechaFin=isset($get_data["fechaFin"])?$get_data["fechaFin"]:"";
 
  $sql="
    select l.id,
c.nombre as colegio,
n.descripcion as notaria,
l.iddocumentonotarial,
d.numero as numerodeinstrumento,d.numerodekardex,
to_char(d.fechaautorizacion,'dd/mm/YYYY')as fechaautorizacion,
to_char(l.fecharegistro,'dd/mm/YYYY')as fecharegistro,
to_char(l.fecharegistro,'hh24:mi:ss') as horaregistro,
NVL((
    select ea.descripcion from ocpreporte.comentario_alerta ca 
    inner join ocpreporte.estado_alerta ea on ca.idestadoocp=ea.id 
     where idalerta=l.id ORDER BY ca.id desc FETCH NEXT 1 ROWS ONLY  
),'PENDIENTE') as estado_ocp,
t.abrev as tipo_instrumento
from ALERTAOPERACION.documento_laft_notario l 
inner join sisgen.documentonotarial d on l.iddocumentonotarial=d.id
inner join sisgen.notaria n on d.idnotaria=n.id
inner join sisgen.colegio c on n.idcolegio=c.id
INNER JOIN SISGEN.TIPOINSTRUMENTO t ON d.IDINSTRUMENTO=T.ID
where enviado=1 ";
  
  if($idtipoLaft>0)
    $sql.=" AND l.idtipoLaft=".$idtipoLaft;

   if(intval($idNotaria)>0)
            $sql.=" and d.idnotaria=".$idNotaria;
        
  if(intval($idColegio)>0)
            $sql.=" and n.idcolegio=".$idColegio;


   if($bfechaInicio!="" && $bfechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($bfechaInicio));
            $fin = date("d/m/Y", strtotime($bfechaFin));

            $sql.=" AND trunc(l.fecharegistro) BETWEEN '".$inicio."' AND '".$fin."' ";
        }

  
  
$sql.=" order by l.id desc
  ";
 // die("=>".$sql);
  $all=$this->getListAllRows($sql);
  return $all;
}

public function addDocumentoLaft($objRequest)
{

  $tipo=$this->getValorString($objRequest->tipo);
  $descripcion=$this->getValorString($objRequest->descripcion);
  $archivo=$objRequest->archivo;
  $iddocumento=$this->getValorString($objRequest->iddocumento);
  $tipoArchivo=$this->getValorString($objRequest->tipoArchivo);


   if($archivo!=null)
    {
        $nombreArchivo=$this->getValorString($archivo->nombreArchivo);
        $base64textString=$this->getValorString($archivo->base64textString);
            $dir_base="C:\anexos_alertas\laftnotario";
            $info = new SplFileInfo($nombreArchivo);
            $extension=$info->getExtension();
            $archivo = $base64textString;
            $archivo = base64_decode($archivo);
            $filePath=$dir_base."/".uniqid().".".$extension;
            file_put_contents($filePath, $archivo);

    }

    $sqlInsert="INSERT INTO ALERTAOPERACION.ANEXOS_LAFT_NOTARIO
        (IDDOCUMENTONOTARIAL,DESCRIPCION,NOMBREARCHIVO,DESCRIPCIONARCHIVO,FECHAREGISTRO,TIPO_LAFT,TIPO_ARCHIVO
        ) VALUES 

        (:iddocumento,
        :descripcion,
        :filePath,
        :nombreArchivo,
        SYSTIMESTAMP,
        :tipo,
        :tipoArchivo
        )  
        ";
       //echo $sqlInsert."<br><br><br>";
        $db=$this->connect();
        $stInsert=oci_parse($db,$sqlInsert);
      
        oci_bind_by_name($stInsert, ":iddocumento", $iddocumento);
        oci_bind_by_name($stInsert, ":descripcion", $descripcion);
        oci_bind_by_name($stInsert, ":filePath", $filePath);
         oci_bind_by_name($stInsert, ":nombreArchivo", $nombreArchivo);
        oci_bind_by_name($stInsert, ":tipo", $tipo);
        oci_bind_by_name($stInsert, ":tipoArchivo", $tipoArchivo);

        oci_execute($stInsert);  
        oci_free_statement($stInsert);
      oci_close($db);
      $response=array("response"=>"correcto","status"=>"1");      
      return ($response);
}


public function addLaftNotario($objRequest)
{
  $iddocumento=$this->getValorString($objRequest->iddocumento);
  $idsContratantes=$this->getValorString($objRequest->idsContratantes);
  $idsAlertaContratante=$this->getValorString($objRequest->idsAlertaContratante);
  $idsAlertaPorActo=$this->getValorString($objRequest->idsAlertaPorActo);

  $actosOtraAlerta=   ($objRequest->actosOtraAlerta);

/*
  var_dump($actosOtraAlerta);
  return;  */
//var_dump($idsContratantes);
/*
VALORESCONTRATANTES VARCHAR2(150) NULL,
VALORESALERTAPORCONTRATANTE VARCHAR2(150) NULL,
VALORESOTRASALERTAS VARCHAR2(150) NULL,
VALORESALERTAPORACTO VARCHAR2(150) NULL,
*/
    $sqlInsert="INSERT INTO ALERTAOPERACION.DOCUMENTO_LAFT_NOTARIO
        (IDDOCUMENTONOTARIAL,VALORESCONTRATANTES,VALORESALERTAPORCONTRATANTE,VALORESALERTAPORACTO,FECHAREGISTRO
        ) VALUES 
        (
        :iddocumento,:idsContratantes,:idsAlertaContratante,:idsAlertaPorActo,
        SYSTIMESTAMP
        )  returning  ID into :inserted_id
        ";
       //echo $sqlInsert."<br><br><br>";
        $db=$this->connect();
        $stInsert=oci_parse($db,$sqlInsert);
        $idNumber=0;
 
        oci_bind_by_name($stInsert, ":iddocumento", $iddocumento);
        oci_bind_by_name($stInsert, ":idsContratantes", $idsContratantes);
        oci_bind_by_name($stInsert, ":idsAlertaContratante", $idsAlertaContratante);
        oci_bind_by_name($stInsert, ":idsAlertaPorActo", $idsAlertaPorActo);
        oci_bind_by_name($stInsert, ":inserted_id", $idNumber,35);
        oci_execute($stInsert);  
        oci_free_statement($stInsert);

      if(isset($actosOtraAlerta) && sizeof($actosOtraAlerta)>0){
        foreach ($actosOtraAlerta as  $value) {
          if(isset($value->otraalerta) && $value->otraalerta!=""){
              $descripcion=$value->otraalerta;
                $sqlOf="INSERT INTO  ALERTAOPERACION.otra_alerta_laft_notario (IDDOCUMENTOLAFT,DESCRIPCION)
                VALUES (:iddocumentolaft,:descripcion)
                ";

                $stInsert=oci_parse($db,$sqlOf);
                oci_bind_by_name($stInsert, ":iddocumentolaft", $idNumber);
                oci_bind_by_name($stInsert, ":descripcion", $descripcion);
                oci_execute($stInsert);  

            }

        }
      } 
      oci_close($db);
      $response=array("response"=>"correcto","status"=>"1");      
      return ($response);
}


public function eliminarAnexolaft(){
  $id = $this->getValorIntSanit("id");
  $sql="
  UPDATE  ALERTAOPERACION.ANEXOS_LAFT_NOTARIO 
  SET estado=0
  WHERE 
  id=:id";
 
    $db=$this->connect();
        $st=oci_parse($db,$sql);
      
        oci_bind_by_name($st, ":id", $id);
        oci_execute($st);  
        oci_free_statement($st);
      oci_close($db);
      $response=array("response"=>"correcto","status"=>"1");      
      return ($response);
}



    public function getRptsimpleCount($get_data)
       {      

        $info=json_decode($get_data);

        $sqlWhere="";
        $sqlInner="";
        $isLimit=true;
        $sqlHaving="";

        $bcolegio=isset($info->colegio)?$info->colegio:"";
        $bnotaria=isset($info->notaria)?$info->notaria:"";
        $bacto=isset($info->acto)?$info->acto:"";
        $btipoInstrumento=isset($info->tipoInstrumento)?$info->tipoInstrumento:"";
        $bfechaInicio=isset($info->fechaInicio)?$info->fechaInicio:"";
        $bfechaFin=isset($info->fechaFin)?$info->fechaFin:"";
        $bnumeroInstrumento=isset($info->numeroInstrumento)?intval($info->numeroInstrumento):"0";
        $bnumeroKardex=isset($info->numeroKardex)?trim($info->numeroKardex):"";
        $bpageIndex=isset($info->pageIndex)?intval($info->pageIndex):"0";
        $bpageSize=isset($info->pageSize)?intval($info->pageSize):"0";

        $bminPatrimonial=isset($info->minPatrimonial)?trim($info->minPatrimonial):"";
        $bmaxPatrimonial=isset($info->maxPatrimonial)?trim($info->maxPatrimonial):"";

        $selectTipoDocumento=isset($info->selectTipoDocumento)?trim($info->selectTipoDocumento):"";
        $numerodocumento=isset($info->numerodocumento)?trim($info->numerodocumento):"";
            

        if($bcolegio!="")
            $sqlWhere.=" AND N.IDCOLEGIO=".$bcolegio;
        
        if($bnotaria!="")
            $sqlWhere.=" AND D.IDNOTARIA=".$bnotaria;

        if($bacto!="")
            $sqlWhere.=" AND O.IDACTOJURIDICO=".$bacto;

        if($btipoInstrumento!="")
            $sqlWhere.=" AND D.IDINSTRUMENTO=".$btipoInstrumento;
        
    
        if($bfechaInicio!="" && $bfechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($bfechaInicio));
            $fin = date("d/m/Y", strtotime($bfechaFin));

            $sqlWhere.=" AND D.FECHAAUTORIZACION BETWEEN '".$inicio."' AND '".$fin."' ";
        }

        $rol=isset($_SESSION["idacceso"])?$_SESSION["idacceso"]:"";

        if($rol==2 &&  isset($_SESSION) && $_SESSION["idnotaria"]!="")
            $sqlWhere.=" AND D.IDNOTARIA=".$_SESSION["idnotaria"];
        

        if(intval($bnumeroInstrumento)>0 )
            $sqlWhere.=" AND D.NUMERO=".$bnumeroInstrumento;

        if($bnumeroKardex!="")
            $sqlWhere.=" AND D.NUMERODEKARDEX='".$bnumeroKardex."'";

        
         if($numerodocumento!=""){
            $sqlWhere.=" AND di.descripcion='".$numerodocumento."'";
            if($selectTipoDocumento!="")
               $sqlWhere.=" AND di.tipodocumentoid=".$selectTipoDocumento;
        
         }
        $sqlPaginator="";
        $pageInit=0;
        if($bpageIndex>0)
            $pageInit=($bpageIndex*$bpageSize)+1;
        
        $valueMax= ($bpageSize*($bpageIndex+1));
        
        if( $bminPatrimonial!="" || $bmaxPatrimonial!="")
            $isLimit=false;

        if(trim($sqlWhere)!="")
            $isLimit=false;

        if($isLimit==true)
        {
            $sqlPaginator=" WHERE   to_char(FECHAAUTORIZACION, 'mm')=2  AND to_char(FECHAAUTORIZACION, 'yyyy')=2021 ";
        }
        

        if($bminPatrimonial!="" || $bmaxPatrimonial!="")
        {
            $sqlInner.=" INNER JOIN SISGEN.MEDIODEPAGO M ON OX.ID=M.IDOPERACION "  ; 
            $sqlHaving.="HAVING ";         
        }

        if($bminPatrimonial!=""){
            if($bminPatrimonial!=""  && $bmaxPatrimonial!="")
                $sqlHaving.="  SUM(M.CUANTIA)>=".$bminPatrimonial." AND SUM(M.CUANTIA)<= ".$bmaxPatrimonial;
            else  if($bminPatrimonial!="" && $bmaxPatrimonial=="" )
                $sqlHaving.="  SUM(M.CUANTIA)>=".$bminPatrimonial;
            else if($bmaxPatrimonial!="" && $bminPatrimonial=="")
                 $sqlHaving.=" SUM(M.CUANTIA)<=".$bmaxPatrimonial;
         }
       
       
       $db=$this->connect();
       
$sql="
SELECT COUNT(1) AS TOTAL FROM (
SELECT  TBL2.NUMERODEKARDEX,FECHAAUTORIZACION,TIPOINSTRUMENTO,NUMEROINSTRUMENTO,SCORING,
NOTARIA
FROM (
SELECT  TBL1.* FROM (
          SELECT  D.ID AS IDX, D.NUMERODEKARDEX,FECHAAUTORIZACION,T.ABREV AS TIPOINSTRUMENTO,
D.NUMERO AS NUMEROINSTRUMENTO,D.SCORING,
N.DESCRIPCION AS NOTARIA 
FROM SISGEN.DOCUMENTONOTARIAL D 
INNER JOIN SISGEN.TIPOINSTRUMENTO T ON D.IDINSTRUMENTO=T.ID
LEFT JOIN SISGEN.OPERACION O ON D.ID=O.IDDOCUMENTONOTARIAL
INNER JOIN SISGEN.NOTARIA N ON D.IDNOTARIA=N.ID ";

if($numerodocumento!="")
{
    $sql.="
INNER JOIN SISGEN.INTERVINIENTE i ON  O.ID=i.IDOPERACION
INNER JOIN SISGEN.SujetoDocIdentificativo sdi ON sdi.idpersona = i.idpersona
INNER JOIN SISGEN.Documentoidentificativo di  ON di.id = sdi.Iddocumentoidentificativo 

    ";
}
$sql.=" WHERE D.ID>0  ".$sqlWhere."
GROUP BY D.ID,NUMERODEKARDEX,FECHAAUTORIZACION,T.ABREV,D.NUMERO,D.SCORING,N.DESCRIPCION
ORDER BY IDX,NOTARIA,T.ABREV,D.NUMERO,FECHAAUTORIZACION
)  TBL1  GROUP BY IDX,NUMERODEKARDEX,FECHAAUTORIZACION,TIPOINSTRUMENTO,NUMEROINSTRUMENTO,SCORING,NOTARIA
) TBL2 
".$sqlInner."

".$sqlHaving."
)  TBL3 ".$sqlPaginator."
 ";

//die($sql);

 $stid = oci_parse($db,$sql);

       oci_execute($stid);
       $row = oci_fetch_assoc($stid);
       $total=$row["TOTAL"];


        oci_free_statement($stid);
        oci_close($db);   
        return $total;     
}



    public function getRptsimple($get_data)
       {      

        $info=json_decode($get_data);

        $bcolegio=isset($info->colegio)?$info->colegio:"";
        $bnotaria=isset($info->notaria)?$info->notaria:"";
        $bacto=isset($info->acto)?$info->acto:"";
        $btipoInstrumento=isset($info->tipoInstrumento)?$info->tipoInstrumento:"";
        $bfechaInicio=isset($info->fechaInicio)?$info->fechaInicio:"";
        $bfechaFin=isset($info->fechaFin)?$info->fechaFin:"";
        $bnumeroInstrumento=isset($info->numeroInstrumento)?intval($info->numeroInstrumento):"0";
        $bnumeroKardex=isset($info->numeroKardex)?trim($info->numeroKardex):"";
        $bpageIndex=isset($info->pageIndex)?intval($info->pageIndex):"0";
        $bpageSize=isset($info->pageSize)?intval($info->pageSize):"0";
        $bminPatrimonial=isset($info->minPatrimonial)?trim($info->minPatrimonial):"";
        $bmaxPatrimonial=isset($info->maxPatrimonial)?trim($info->maxPatrimonial):"";
        
        $selectTipoDocumento=isset($info->selectTipoDocumento)?trim($info->selectTipoDocumento):"";
        $numerodocumento=isset($info->numerodocumento)?trim($info->numerodocumento):"";
        



        $sqlWhere="";
        $sqlInner="";
        $isLimit=true;
        $sqlHaving="";

        if($bcolegio!="")
            $sqlWhere.=" AND N.IDCOLEGIO=".$bcolegio;
        
        if($bnotaria!="")
         $sqlWhere.=" AND D.IDNOTARIA=".$bnotaria;
        

        if($bacto!="")
            $sqlWhere.=" AND O.IDACTOJURIDICO=".$bacto;
        
        if($bnumeroInstrumento>0 )
            $sqlWhere.=" AND D.NUMERO=".$bnumeroInstrumento;
        
        
        if($bnumeroKardex!="")
            $sqlWhere.=" AND D.NUMERODEKARDEX='".$bnumeroKardex."'";

    
        if($btipoInstrumento!="")
            $sqlWhere.=" AND D.IDINSTRUMENTO=".$btipoInstrumento;


        
        if($numerodocumento!=""){
            $sqlWhere.=" AND di.descripcion='".$numerodocumento."'";
            if($selectTipoDocumento!="")
               $sqlWhere.=" AND di.tipodocumentoid=".$selectTipoDocumento;
        
       }
        if($bfechaInicio!="" && $bfechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($bfechaInicio));
            $fin = date("d/m/Y", strtotime($bfechaFin));

            $sqlWhere.=" AND D.FECHAAUTORIZACION BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        $rol=isset($_SESSION["idacceso"])?$_SESSION["idacceso"]:"";

        if($rol==2 &&  isset($_SESSION) && $_SESSION["idnotaria"]!="")
            $sqlWhere.=" AND D.IDNOTARIA=".$_SESSION["idnotaria"];
        


        $sqlPaginator="";
        $pageInit=0;
        if($bpageIndex>0)
            $pageInit=($bpageIndex*$bpageSize)+1;
        
        $valueMax= ($bpageSize*($bpageIndex+1));
        
        if($bminPatrimonial!="" || $bmaxPatrimonial!="" )
            $isLimit=false;

        if(trim($sqlWhere)!="")
            $isLimit=false;

        if($isLimit==true)
        {
            $sqlPaginator=" WHERE ROWNUM<=".$valueMax." AND to_char(FECHAAUTORIZACION, 'mm')=2  AND to_char(FECHAAUTORIZACION, 'yyyy')=2021 ";
        }else{
             $sqlPaginator="WHERE  ROWNUM<=".$valueMax;
        }
        

        if( ($bminPatrimonial!="" || $bmaxPatrimonial!=""))
        {
            $sqlInner.=" INNER JOIN SISGEN.MEDIODEPAGO M ON OX.ID=M.IDOPERACION "  ; 
            $sqlHaving.="HAVING ";         
        }

        if($bminPatrimonial && $bminPatrimonial!=""){
                if($minPatrimonial!=""  && $bmaxPatrimonial!="")
                    $sqlHaving.="  SUM(M.CUANTIA)>=".$bminPatrimonial." AND SUM(M.CUANTIA)<= ".$bmaxPatrimonial;
                else  if($bminPatrimonial!="" && $bmaxPatrimonial=="" )
                    $sqlHaving.="  SUM(M.CUANTIA)>=".$bminPatrimonial;
                else if($bmaxPatrimonial!="" && $bminPatrimonial=="")
                     $sqlHaving.=" SUM(M.CUANTIA)<=".$bmaxPatrimonial;
        }
       
       
       $db=$this->connect();
       
       $sql="
       SELECT TBL4.*,

(

    select 1 from ALERTAOPERACION.DOCUMENTO_LAFT_NOTARIO dc
     where dc.IDDOCUMENTONOTARIAL=TBL4.IDX   FETCH NEXT 1 ROWS ONLY  
) as EMITIDO


        FROM (
SELECT ROWNUM as RNUM,
TBL3.* FROM (
SELECT IDX, TBL2.NUMERODEKARDEX,to_char(FECHAAUTORIZACION,'dd/mm/YYYY') as FECHAAUTORIZACION,TIPOINSTRUMENTO,NUMEROINSTRUMENTO,
NOTARIA,COLEGIO,
IDNOTARIA,IDCOLEGIO,
LISTAGG(AX.DESCRIPCION, ',') WITHIN GROUP (ORDER BY AX.DESCRIPCION) AS NOMBREACTO
FROM (
SELECT  TBL1.* FROM (
          SELECT  D.ID AS IDX, D.NUMERODEKARDEX,FECHAAUTORIZACION,T.ABREV AS TIPOINSTRUMENTO,
D.NUMERO AS NUMEROINSTRUMENTO,
N.DESCRIPCION AS NOTARIA,CC.ID AS IDCOLEGIO,N.ID AS IDNOTARIA,CC.NOMBRE AS COLEGIO

FROM SISGEN.DOCUMENTONOTARIAL D 
INNER JOIN SISGEN.TIPOINSTRUMENTO T ON D.IDINSTRUMENTO=T.ID
INNER JOIN SISGEN.OPERACION O ON D.ID=O.IDDOCUMENTONOTARIAL
INNER JOIN SISGEN.NOTARIA N ON D.IDNOTARIA=N.ID
INNER JOIN SISGEN.COLEGIO CC ON N.IDCOLEGIO=CC.ID ";

if($numerodocumento!="")
{
    $sql.="
INNER JOIN SISGEN.INTERVINIENTE i ON  O.ID=i.IDOPERACION
INNER JOIN SISGEN.SujetoDocIdentificativo sdi ON sdi.idpersona = i.idpersona
INNER JOIN SISGEN.Documentoidentificativo di  ON di.id = sdi.Iddocumentoidentificativo 

    ";
}

/*
LEFT JOIN SISGEN.INTERVINIENTE IX ON  O.ID=IX.IDOPERACION
LEFT JOIN SISGEN.REPRESENTANTE RR ON IX.ID=RR.IDINTERVINIENTE 
LEFT JOIN SISGEN.SUJETO SX ON SX.ID=IX.IDPERSONA OR SX.ID=RR.IDPERSONA*/

$sql.="
WHERE D.ID>0  ".$sqlWhere."
GROUP BY D.ID,NUMERODEKARDEX,FECHAAUTORIZACION,T.ABREV,D.NUMERO,
N.DESCRIPCION,CC.NOMBRE,CC.ID,N.ID
ORDER BY IDX,NOTARIA,T.ABREV,FECHAAUTORIZACION,D.NUMERO,D.IDNOTARIA
)  TBL1  GROUP BY IDX,NUMERODEKARDEX,FECHAAUTORIZACION,TIPOINSTRUMENTO,
NUMEROINSTRUMENTO,NOTARIA,COLEGIO,IDCOLEGIO,IDNOTARIA
) TBL2 
LEFT JOIN SISGEN.OPERACION OX ON TBL2.IDX=OX.IDDOCUMENTONOTARIAL 
LEFT JOIN SISGEN.ACTOJURIDICO AX ON OX.IDACTOJURIDICO=AX.ID
".$sqlInner."
GROUP BY (IDX,NUMERODEKARDEX,FECHAAUTORIZACION,TIPOINSTRUMENTO,NUMEROINSTRUMENTO,NOTARIA,COLEGIO,IDNOTARIA,IDCOLEGIO)
".$sqlHaving."
)  TBL3 ".$sqlPaginator."
) TBL4 WHERE RNUM>=".$pageInit."
       ";


//die($sql);
 $stid = oci_parse($db,$sql);
         oci_execute($stid);
       $data=[];
        while (($row = oci_fetch_assoc($stid)) != false) {
            $row["NOTARIA"]=strtoupper($row["NOTARIA"]);
            $row["COLEGIO"]=strtoupper($row["COLEGIO"]);
            if($row["EMITIDO"]==1)
                    $row["EMITIDO"]="ENVIADO";


            $data[]=$row;
        }

        oci_free_statement($stid);
        oci_close($db);   
        return $data;     
}




public function getLaftRecepcion($data)
{
    $db=$this->connect();
    $iddocumento=$this->getValorNumerico($data->iddocumentolaft);
    $idcontratante=$this->getValorNumerico($data->idcontratante);

    $sqlValores=" select valorescontratantes,valoresalertaporcontratante,
       valoresalertaporacto
    from  ALERTAOPERACION.documento_laft_notario where id=".$iddocumento;
    $listValores=$this->getListAllRows($sqlValores);
    $listValores=$listValores[0];
    $valorescontratantes=isset($listValores["VALORESCONTRATANTES"])?$listValores["VALORESCONTRATANTES"]:"";

    $valoresalertaporcontratante=isset($listValores["valoresalertaporcontratante"])?$listValores["valoresalertaporcontratante"]:"";

    $valoresalertaporacto=isset($listValores["valoresalertaporacto"])?$listValores["valoresalertaporacto"]:"";
    

     $allValoresContratantes=[];

     $allValoresAlertaPorContratante=[];
        if($valoresalertaporcontratante!="")
        {
            $sql="select idgrupoalerta,id,descripcion from ocpreporte.tipo_alerta 
                    where id in (".$valoresalertaporcontratante.")
                    order by idgrupoalerta";
             $allValoresAlertaPorContratante=$this->getListAllRows($sql);
        }


           $allValoresPorActo=[];
           $sqlActo="select id,idacto,valoresalerta,otra_alerta from ALERTAOPERACION.acto_laft_notario
              where iddocumentolaft=".$iddocumento;
            $allActos=$this->getListAllRows($sqlActo);

            foreach ($allActos as $value) {
          
                //  $allPorActo=explode(":",$value);
                  $idActo=$value["idacto"];
                  $idTipoAlerta=$value["valoresalerta"];

                    $sql=" select aj.descripcion as acto from 
                      sisgen.actojuridico aj
                      where aj.id=".$idActo;

                    $allActosV=$this->getRow($sql);

                    if($idTipoAlerta!=""){
                    $acto=$allActosV["acto"];
                    $sql="select idgrupoalerta,id,descripcion,'".$acto."' as acto from ocpreporte.tipo_alerta 
                    where id in (".$idTipoAlerta.")
                    order by idgrupoalerta";
                    $allAlertasV=$this->getListAllRows($sql);
                    $allValoresPorActo[]=$allAlertasV;
                    }

            }
      


        $sql="
           select (primerapellido_razonsocial||' '||segundoapellido||' '||nombre) 
as nombre,si.abrev,c.numerodocumento,c.rol
from ALERTAOPERACION.contratante c 
inner join sisgen.tipodocumentoidentificativo si on c.idtipodocumento=si.id
where c.id=".$idcontratante;

            $allValoresContratantes=$this->getListAllRows($sql);
        

    return array($allValoresAlertaPorContratante,$allValoresContratantes,$allValoresPorActo);
}


}
?>