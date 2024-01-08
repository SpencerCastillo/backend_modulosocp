<?php 

require_once 'libs/spout/vendor/autoload.php';
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;

use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;

class Models_Scoring extends DB_Connect {
        
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


public function getFactorClienteCount($data)
{
    $db=$this->connect();
    $data=json_decode($data);
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=$this->getValorString($data->idTipoAlerta);
    $idGrupo=$this->getValorString($data->idGrupoAlerta);

    $numdocumento=$this->getValorString($data->numdocumento);
    $sujeto=$this->getValorString($data->nombrecontratante);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;





    $sql="  
        select  count(1) as cantidad

 from OCPREPORTE.SCORING_FACTOR_CLIENTE c

  WHERE c.numdoc is not null ";
 
 if($idTipoAlerta==1)
    $sql.=" AND IDTIPOPERSONA=".$idTipoAlerta;

if($idTipoAlerta==2)
   $sql.=" AND IDTIPOPERSONA=".$idTipoAlerta;
  
        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

          $sql.="
            and TRUNC(fechacarga) BETWEEN TO_DATE('".$inicio."', 'DD/MM/YYYY')   AND   TO_DATE('".$fin."', 'DD/MM/YYYY')  

          ";
        }


        if(intval($idNotaria)>0)
            $sql.=" and c.idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and c.idcolegio=".$idColegio;

          if($numdocumento!="")
            $sql.=" and c.numdoc='".$numdocumento."'";

          if($sujeto!="")
            $sql.=" and c.contratante like '".trim($sujeto)."%'";
        
 

    $all=$this->getAllTotal($sql);
    return $all;

}
public function getFactorCliente($data)
{
    $db=$this->connect();
    $data=json_decode($data);
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=$this->getValorString($data->idTipoAlerta);
    $idGrupo=$this->getValorString($data->idGrupoAlerta);

    $numdocumento=$this->getValorString($data->numdocumento);
    $sujeto=$this->getValorString($data->nombrecontratante);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;

    $pEdad_N=0.05;
    $pTipoDoc_N=0.1;
    $pOcupacion_N=0.1;
    $pNacionalidad_N=0.2;
    $pResidencia_N=0.2;
    $pPeps_N=0.15;
    $pEspecial_N=0.2;


    $pTipoDoc_J=0.1;
    $pActividad_J=0.1;
    $pNacionalidad_J=0.25;
    $pResidencia_J=0.25;
    $pEspecial_J=0.30;


    $cliente=0.6;
    $servicio=0.25;
    $geografica=0.15;



    $sql="  
        select  notaria,contratante,
 tipo_documento,numdoc,
(riesgo_laft_tipodoc ||'('||tipo_documento||')') as riesgo_laft_tipodoc,

 edad,(riesgo_laft_edad ||'('||edad||')') as riesgo_laft_edad,actividades_economicas as sector,

 (riesgo_laft_sector ||'('||actividades_economicas||')') as riesgo_laft_sector,
 pais_nacionalidad as nacionalidad,
(riesgo_laft_nacionalidad ||'('||pais_nacionalidad||')') as riesgo_laft_nacionalidad,
ocupacion,
(riesgo_laft_ocupacion ||'('||ocupacion||')') as riesgo_laft_ocupacion,
residencia,
(riesgo_laft_residencia ||'('||residencia||')') as riesgo_laft_residencia_zg,
(riesgo_laft_nacionalidad ||'('||pais_nacionalidad||')') as riesgo_laft_residencia,
PUNTAJE_SCORING_TIPODOC,
nvl(PUNTAJE_SCORING_EDAD,0) as PUNTAJE_SCORING_EDAD,
nvl(PUNTAJE_SCORING_OCUPACION,0) as PUNTAJE_SCORING_OCUPACION,
nvl(PUNTAJE_SCORING_ACTIVIDAD_ECONOMICA,0) as PUNTAJE_SCORING_ACTIVIDAD_ECONOMICA,
nvl(PUNTAJE_SCORING_nacionalidad,0) as PUNTAJE_SCORING_nacionalidad,
nvl(PUNTAJE_SCORING_PEPS,0) as PUNTAJE_SCORING_PEPS,
nvl(PUNTAJE_SCORING_LPI,0) as PUNTAJE_SCORING_LPI,
nvl(PUNTAJE_SCORING_residencia,0) as PUNTAJE_SCORING_residencia_zg,
nvl(PUNTAJE_SCORING_nacionalidad,0) as PUNTAJE_SCORING_residencia,
nvl(puntaje_scoring_residencia_notaria,0) as puntaje_scoring_residencia_notaria,
RIESGO_LAFT_residencia_notaria,
(
    select SCORE_FACTOR_SERVICIO from OCPREPORTE.score_factor_servicio where numdoc_interno=c.numdoc
  
)as puntaje_servicio,
(
select score_factor_zona_geografica from ocpreporte.SCORE_FACTOR_ZONA_GEOGRAFICA
 where numdoc_interno=c.numdoc
) as score_factor_zona_geografica,

regimen

 from OCPREPORTE.SCORING_FACTOR_CLIENTE c
  WHERE c.numdoc is not null ";
 
 if($idTipoAlerta==1)
    $sql.=" AND IDTIPOPERSONA=".$idTipoAlerta;

if($idTipoAlerta==2)
   $sql.=" AND IDTIPOPERSONA=".$idTipoAlerta;
  
        if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

          $sql.="
            and TRUNC(fechacarga) BETWEEN TO_DATE('".$inicio."', 'DD/MM/YYYY')   AND   TO_DATE('".$fin."', 'DD/MM/YYYY')  

          ";
        }



        if(intval($idNotaria)>0)
            $sql.=" and c.idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and c.idcolegio=".$idColegio;

          if($numdocumento!="")
            $sql.=" and c.numdoc='".$numdocumento."'";

          if($sujeto!="")
            $sql.=" and c.contratante like '".trim($sujeto)."%'";
        
        $sql.=" ORDER BY idnotaria,contratante ";
          if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";

 //     die($sql);
        $stid=oci_parse($db,$sql);
        oci_execute($stid);
        $all=[];
        
        while($row=oci_fetch_assoc($stid))
        {
          $factorCliente=0;
          $factorGeografica=0;

           foreach ($row as $key => $value ) {
                    $row[strtolower($key)]=str_replace(',', '.',$row[$key]);
                }


          if($idTipoAlerta==1)
          {
              $factorCliente+=$row["puntaje_scoring_edad"]*$pEdad_N;
              $factorCliente+=$row["puntaje_scoring_tipodoc"]*$pTipoDoc_N;
              $factorCliente+=$row["puntaje_scoring_ocupacion"]*$pOcupacion_N;
              $factorCliente+=$row["puntaje_scoring_nacionalidad"]*$pNacionalidad_N;
              $factorCliente+=$row["puntaje_scoring_residencia"]*$pResidencia_N;
              $factorCliente+=$row["puntaje_scoring_peps"]*$pPeps_N;
              $factorCliente+=$row["puntaje_scoring_lpi"]*$pEspecial_N;
              $factorCliente*=$cliente;
              
              
          }else if($idTipoAlerta==2)
          {
              $factorCliente+=$row["puntaje_scoring_tipodoc"]*$pTipoDoc_J;
              $factorCliente+=$row["puntaje_scoring_actividad_economica"]*$pActividad_J;
              $factorCliente+=$row["puntaje_scoring_nacionalidad"]*$pNacionalidad_J;
              $factorCliente+=$row["puntaje_scoring_residencia"]*$pResidencia_J;
              $factorCliente+=$row["puntaje_scoring_lpi"]*$pEspecial_J;
              $factorCliente*=$cliente;
              
          }

            /*
              $factorGeografica+=$row["puntaje_scoring_residencia_zg"]*0.5;
              $factorGeografica+=$row["puntaje_scoring_residencia_notaria"]*0.5;
              $factorGeografica*=$geografica;*/ 
              $factorGeografica=$row["score_factor_zona_geografica"];


          $row["factor_cliente"]=number_format($factorCliente,4);
          $row["factor_geografica"]=number_format($factorGeografica,4);
          $puntajeServicio=number_format($row["puntaje_servicio"],4);
          $row["puntaje_servicio"]=$puntajeServicio;
          $totalScoring=$factorCliente+$factorGeografica+floatval($puntajeServicio);
          $row["total_scoring"]=number_format($totalScoring,4);


          $row["calificacion_final"]="";
          
          if($row["regimen"]=="S")
              $row["calificacion_final"]="0.8";
          else  if($row["regimen"]=="R")
              $row["calificacion_final"]="1.2";
          else
              $row["calificacion_final"]="1";


              $totalNivelFinal=$totalScoring*floatval($row["calificacion_final"]);
              $row["calificacion_final"]=number_format($totalNivelFinal,4);;


            if($totalNivelFinal>7.038)
              $nivelFInal="CRÍTICO";
            else if($totalNivelFinal>6.5889 && $totalNivelFinal<=7.038)
              $nivelFInal="ALTO";
            else if($totalNivelFinal>3.8897 && $totalNivelFinal<=6.5889)
              $nivelFInal="MEDIO";
            else if($totalNivelFinal<=3.8897)
              $nivelFInal="BAJO";

            $row["nivel_final"]=$nivelFInal;
              

            $all[]=$row;
        }
        oci_free_statement($stid);
        oci_close($db);
        return $all;

}

public function getDetalle($data)
{

   $data=json_decode($data);
    $db=$this->connect();
    $numdoc=$this->getValorString($data->numdoc);
 /*
    $sql="
select 
distinct idnotaria,
notaria,
to_char(fechaautorizacion,'dd/mm/YYYY') as fechaautorizacion,
numerodeinstrumento,numerodekardex,
tipo_documento,numdoc,sujeto as contratante,actojuridico
from ocpreporte.detalle_alerta_general where numdoc='".$numdoc."'
order by notaria,numerodeinstrumento,fechaautorizacion
";*/
$sql="
  SELECT dd.idnotaria, n.descripcion as notaria,
to_char(fechaautorizacion,'dd/mm/YYYY') as fechaautorizacion,dd.numero as numerodeinstrumento,
numerodekardex,DI.DESCRIPCION AS numdoc,
                tdi.abrev as tipo_documento,
                 (
                case s.idtipopersona when 1 then pf.cliente when 2 then pj.cliente else '' end
                )as contratante,aj.descripcion as actojuridico
                from sisgen.documentonotarial dd 
                inner join sisgen.operacion o on dd.id=o.iddocumentonotarial  
                inner join sisgen.actojuridico aj on o.idactojuridico=aj.id
                inner join sisgen.notaria n on dd.idnotaria=n.id
                inner join sisgen.interviniente i on o.id=i.idoperacion
                INNER JOIN SISGEN.SUJETO S ON S.ID=I.IDPERSONA 
                INNER JOIN SISGEN.SUJETODOCIDENTIFICATIVO SI ON S.ID=SI.IDPERSONA
                INNER JOIN SISGEN.DOCUMENTOIDENTIFICATIVO DI ON SI.IDDOCUMENTOIDENTIFICATIVO=DI.ID
                INNER JOIN SISGEN.tipodocumentoidentificativo tdi ON di.tipodocumentoid=tdi.id
                LEFT JOIN SISGEN.PERSONAFISICA PF ON S.IDPERSONAFISICA=PF.ID
                LEFT JOIN SISGEN.PERSONAJURIDICA PJ ON S.IDPERSONAJURIDICA=PJ.ID
                where di.DESCRIPCION='".$numdoc."'";
//die($sql);
         $all=$this->getListAllRows($sql);
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

$sql="
select * from ocpreporte.scoring_rpt
  ";

$sql.="
WHERE id>0
 ";

            if(isset($info->idColegio) && intval($info->idColegio)>0)
                $sql .= " AND idcolegio=".$info->idColegio;
            //d/m/Y
            if(isset($info->idNotaria) && intval($info->idNotaria)>0)
                $sql .=  " AND idnotaria=".$info->idNotaria;
        if(isset($info->patrimonioFinal) && intval($info->patrimonioFinal)>0)
            $sql.=" and monto_patrimonial between '".$info->patrimonioFinal."' and '".$info->patrimonioFinal."'";
         
       if($info->fechaInicio!="" && $info->fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($info->fechaInicio));
            $fin = date("d/m/Y", strtotime($info->fechaFin));
            $sql.=" and TO_char(FECHAAUTORIZACION,'DD/MM/YYYY')  between '".$inicio."'  and '".$fin."'";
        }

        if(isset($info->ubigeo) && $info->ubigeo)
            $sql.=" and UBIGEO_CONTRATANTE='".$info->ubigeo."'";
        
                
//die($sql);

$stid = oci_parse($db,
$sql
);
        oci_execute($stid);
        $data=[];
        $i=2;

        $nameRpt="rpt/report".uniqid().".xlsx";
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($nameRpt); // write data to a file or to a PHP stream

        $style = (new StyleBuilder())
             ->setFontBold()
           ->build();


        $cells_titulo = [
            "IDTIPOPERSONA","CLIENTE","CODIGO_CONDICION","CONDICION","ROLREPRESENTANTE","CODIGO_TIPO_DOC","TIPO_DOCUMENTO","NUMERO_DOCUMENTO_CLIENTE","CODIGO_TIPO_PERSONA","TIPO_PERSONA","FECHANACIMIENTO","CODIGO_PROFESION","PROFESION","CODIGO_CARGO","CARGO","CODIGO_OTRO_CARGO","OTROCARGO","OBJETO_SOCIAL","CODIGO_CIIU","CIIU_DESCRIPCION","CODIGO_nacionalidad_NACIONALIDAD","NOMBRE_nacionalidad_NACIONALIDAD","RESIDEPERU","CONDICION_RESIDENCIA","CODIGO_CONDICION_RESIDENCIA","CODIGO_nacionalidad_RESIDENCIA","NOMBRE_nacionalidad_RESIDENCIA","DEPARTAMENTO","residencia","DISTRITO","DIRECCION",





            "UBIGEO_CONTRATANTE",
            "TELEFONO_CONTRATANTE","CORREO_CONTRATANTE","BANDERA","CODIGO_ESTADO_CIVIL","ESTADO_CIVIL","CODIGO_TIPO_INSTRUMENTO","TIPOINSTRUMENTO","CODIGO_ACTO","DESCRIPCION_ACTO","FAMILIA_OPERACION","CODIGOTIPOOPERACION","TIPO_OPERACION","TIPOMONEDA","CODIGO_MONEDA","CUANTIA","TIPO_FONDO","CODIGO_TIPO_FONDO","NUMERODEKARDEX","FECHA","NUMEROINSTRUMENTO","SCORING","NOTARIA"
        ];
       $singleRow = WriterEntityFactory::createRowFromArray($cells_titulo,$style);
        $writer->addRow($singleRow);

        while (($row = oci_fetch_assoc($stid)) != false) {
            $values = [$row["IDTIPOPERSONA"],
             $row["CLIENTE"],
             $row["CODIGO_CONDICION"],
             $row["CONDICION"],
             $row["ROLREPRESENTANTE"],
             $row["CODIGO_TIPO_DOC"],
             $row["TIPO_DOCUMENTO"]

              ,$row["NUMERO_DOCUMENTO_CLIENTE"]
              ,$row["CODIGO_TIPO_PERSONA"]
              ,$row["TIPO_PERSONA"]
              ,$row["FECHANACIMIENTO"]
              ,$row["CODIGO_PROFESION"]
              ,$row["PROFESION"]
              ,$row["CODIGO_CARGO"]
              ,$row["CARGO"]
              ,$row["CODIGO_OTRO_CARGO"]
              ,$row["OTROCARGO"]
              ,$row["OBJETO_SOCIAL"]
              ,$row["CODIGO_CIIU"]
              ,$row["CIIU_DESCRIPCION"]
              ,$row["CODIGO_nacionalidad_NACIONALIDAD"]
              ,$row["NOMBRE_nacionalidad_NACIONALIDAD"]
              ,$row["RESIDEPERU"]
              ,$row["CONDICION_RESIDENCIA"]
              ,$row["CODIGO_CONDICION_RESIDENCIA"]
              ,$row["CODIGO_nacionalidad_RESIDENCIA"]
              ,$row["NOMBRE_nacionalidad_RESIDENCIA"]
              ,$row["DEPARTAMENTO"]
              ,$row["residencia"]
              ,$row["DISTRITO"]
              ,$row["DIRECCION"]
              ,$row["UBIGEO_CONTRATANTE"]
             ,$row["TELEFONO_CONTRATANTE"]
              ,$row["CORREO_CONTRATANTE"]
               ,$row["BANDERA"]
                ,$row["CODIGO_ESTADO_CIVIL"]
                 ,$row["ESTADO_CIVIL"]
                  ,$row["CODIGO_TIPO_INSTRUMENTO"]
                 ,$row["TIPOINSTRUMENTO"]
                 ,$row["CODIGO_ACTO"]
                 ,$row["DESCRIPCION_ACTO"]
                 ,$row["FAMILIA_OPERACION"]
                 ,$row["CODIGOTIPOOPERACION"]
                 ,$row["TIPO_OPERACION"]
                 ,$row["TIPOMONEDA"]
                 ,$row["CODIGO_MONEDA"]
                 ,$row["CUANTIA"]
                 ,$row["TIPO_FONDO"]
                 ,$row["CODIGO_TIPO_FONDO"]
                 ,$row["NUMERODEKARDEX"]
                 ,$row["FECHAAUTORIZACION"]
                 ,$row["NUMEROINSTRUMENTO"]
                  ,$row["SCORING"]
                   ,$row["NOTARIA"]

                      ];
            $rowFromValues = WriterEntityFactory::createRowFromArray($values);
            $writer->addRow($rowFromValues);
        }

        oci_free_statement($stid);
        oci_close($db);
        $writer->close();
        return $nameRpt;
 }


 public function getSearchUbigeo($get_data)
    {
    
    $info=json_decode($get_data);
    $data=[];

    $buscar="";

    if(isset($info->buscar))
     $buscar=$info->buscar;
    
    $buscar=trim($buscar);
    if(true){
      
      $buscar=strtoupper($buscar);
      $db=$this->connect();
    $sql="  
    SELECT CODIGO as ID, NOMBRE FROM (
        select de.id||p.id||d.id as codigo, (de.descripcion||'/'||p.descripcion||'/'||d.descripcion) as nombre from Sisgen.distrito d 
inner join Sisgen.residencia p on d.idresidencia=p.id
inner join Sisgen.departamento de on p.iddepartamento=de.id ";
if($buscar!="")
    $sql.= " where  ((de.descripcion||' '||p.descripcion||' '||d.descripcion)) like '%".$buscar."%' ";


$sql.=" ) WHERE ROWNUM<=15 ";
        
     $stid = oci_parse($db,$sql);
       oci_execute($stid);
       
       $i=0;
        while (($row = oci_fetch_assoc($stid)) != false) {
            $data[]=$row;
        }

        oci_free_statement($stid);
        oci_close($db);   
      }
      return $data; 
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