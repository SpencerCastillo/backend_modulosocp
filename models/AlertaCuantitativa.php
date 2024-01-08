<?php 

class Models_AlertaCuantitativa extends DB_Connect {
        
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


public function getList($data)
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


 public function getDetalleAlerta($data)
{
    $data=json_decode($data);
    $db=$this->connect();
    //data: {"numdoc":"00860167","fechaInicio":"2023-06-01","fechaFin":"2023-12-01","idGrupo":"1","idTipoAlerta":"1","pageSize":1,"pageIndex":1}
    $numdoc=$this->getValorString($data->numdoc);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);

    $idGrupo=$this->getValorNumerico($data->idGrupo);
    $idTipoAlerta=$this->getValorNumerico($data->idTipoAlerta);

    $pageIndex=$this->getValorNumerico($data->pageIndex);
    $cantidad=$this->getValorNumerico($data->cantidad);
    $sujeto=$this->getValorString($data->sujeto);
   

    
    $partidaregistral=$this->getValorString($data->partidaregistral);

    $pageSize=$this->getValorString($data->pageSize);
    $pageIndex=intval($pageIndex)+1;

    if($fechaInicio=="null")
        $fechaInicio="";


    if($fechaFin=="null")
        $fechaFin="";
      

   $objBusqueda=$numdoc;
   $direccion="";
   $campoAgrupado="trim(numdoc)";
  if($idGrupo==12 && $idTipoAlerta==2){
        $campoAgrupado="trim(PARTIDAREGISTRAL)";
        $objBusqueda=$partidaregistral;
  }else if($idGrupo==13 && $idTipoAlerta==2){
        $campoAgrupado="trim(NUMEROPLACA)";
        $objBusqueda=$sujeto;
  }else if($idGrupo==17 && $idTipoAlerta==1){
        $direccion=$this->getValorString($data->direccion);
        $campoAgrupado="trim(direccion)";
        $objBusqueda=$direccion;
  }

 
    $sql="  
        select  colegio,notaria,sujeto,idnotaria,fechaautorizacion,fechacarga,numerodekardex,numerodeinstrumento,
          numdoc,partidaregistral,direccion
          from ocpreporte.detalle_alerta_ocp where id>0 ";
         

          if($idGrupo==17 && $idTipoAlerta==1)
              $sql.=" and  ".$campoAgrupado." like '".$direccion."%'";
          
          else if($idGrupo==16 && $idTipoAlerta==1)
              $sql.=" and  trim(sujeto)='".$sujeto."' ";
          
          else{

          if($objBusqueda!="")
            $sql.=" and ".$campoAgrupado."='$objBusqueda' ";

          if($sujeto!="")
               $sql.=" and trim(sujeto) Like '".$sujeto."%'";
          }
               

            if($fechaInicio !="")
              $sql.=" and  TRUNC(fechacarga) BETWEEN TO_DATE('$fechaInicio', 'YYYY-MM-DD')   AND   TO_DATE('$fechaFin', 'YYYY-MM-DD') " ;


          $sql.=" and idtipoalerta=$idTipoAlerta and idGrupo=$idGrupo
          order by fechacarga,numerodeinstrumento,fechaautorizacion
          OFFSET 0 ROWS FETCH NEXT $cantidad ROWS ONLY ";

         // die($sql);
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
    $idEstadoOcp = $this->getValorNumerico($data->idEstadoOcp);

//die(" idEstadoOcp ".$idEstadoOcp);

    $numdocumento=$this->getValorString($data->numdocumento);
    $sujeto=$this->getValorString($data->nombrecontratante);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;
    $sql="
    select tblview.* from (
      SELECT tblfinal.*,
              ROW_NUMBER() OVER (PARTITION BY TRUNC(fechageneracion) ORDER BY rn DESC) AS repetido

       FROM (
    ";

    $sql.=$this->getContenidoAlerta('inicio',$idGrupo,$idTipoAlerta);

          $sql.="
              SELECT d.id,
ta.prefijo as ETIQUETAALERTA,d.colegio,d.notaria,d.idnotaria,
tipodocumento as tipodoc,d.numdoc,d.sujeto,d.sujeto AS contratante,'' as rol,idGrupoAlerta,idTipoAlerta,
ta.descripcion as descripcionalerta,d.fechacarga as fechageneracion,
a.descripcion as acto,d.numerodekardex,to_char(d.fechaautorizacion,'dd/mm/YYYY') as fechainstrumento
,to_char(d.fechaautorizacion,'YYYY-mm-dd') as fechainstrumentosql,

'' as tipoinstrumento,d.numerodeinstrumento,
d.monto_patrimonial,
d.cuantia_participa,
d.partidaregistral,
d.direccion,
d.NUMEROPLACA,
'' as scoring_cliente,
1 as cantidad_alerta,
'' prioridad_atencion ";
$sql.=$this->getContenidoAlerta('cuerpo',$idGrupo,$idTipoAlerta);

$sql.="  FROM ocpreporte.detalle_alerta_ocp d 
inner join ocpreporte.tipo_alerta ta on d.idtipoalerta=ta.codigo and ta.idgrupoalerta=d.idGrupo and tipogrupo='cnt' and activo=1
left join sisgen.actojuridico a on d.idacto=a.id
where idtipoalerta=$idTipoAlerta and idGrupo=$idGrupo
          ";


        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

          if($numdocumento!="")
            $sql.=" and numdoc='".$numdocumento."'";

          if($sujeto!="")
            $sql.=" and sujeto like '".trim($sujeto)."%'";

       if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("Y-m-d", strtotime($fechaInicio));
            $fin = date("Y-m-d", strtotime($fechaFin));

          $sql.="
               AND 
            TRUNC(fechacarga) BETWEEN TO_DATE('".$inicio."', 'YYYY-MM-DD')   AND   TO_DATE('".$fin."', 'YYYY-MM-DD')  
          
          ";
        }

        if($idEstadoOcp>0)
            {
              $sql.=" AND (";
              if($idEstadoOcp==1)
              {

                  $sql.=" 
                ( select count(1) from ocpreporte.comentario_alerta ca 
                where ca.idalerta=d.id and ca.idtipoalerta=5 
                )=0  OR ";
              }
              $sql.="
                ( select ca.idestadoocp from ocpreporte.comentario_alerta ca 
                where ca.idalerta=d.id and ca.idtipoalerta=5   ORDER BY ca.id desc 
                FETCH NEXT 1 ROWS ONLY
                )=$idEstadoOcp
              ";
              $sql.=" ) ";
          
            }   

        $sql.=$this->getContenidoAlerta('fin',$idGrupo,$idTipoAlerta);

        $sql.=" order by fechageneracion desc, ";
        $sql.=" ".$this->getContenidoAlerta('orden',$idGrupo,$idTipoAlerta);
        $sql.=" ,numerodeinstrumento ";

      $sql.="  ) tblfinal order by fechageneracion desc
        ) tblview WHERE repetido=1
        order by fechageneracion desc
        ";

        if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";

//die($sql);
            $stid = oci_parse($db,$sql);
            oci_execute($stid);
            $all=array();
            while (($row = oci_fetch_assoc($stid)) != false) {

                foreach ($row as $key => $value ) {
                    $row[strtolower($key)]=$row[$key];
                }

                if (!isset($row["cantidad_kardex"]))
                      $row["cantidad_kardex"]="1";
                $all[]=$row;
            }
            oci_free_statement($stid);
            oci_close($db);

        return $all;
 }

public function getAlertaGeneralTotal($data,$idGrupox)
{
    $db=$this->connect();
    $idColegio=$this->getValorNumerico($data->idColegio);
    $idNotaria=$this->getValorNumerico($data->idNotaria);
    $fechaInicio=$this->getValorString($data->fechaInicio);
    $fechaFin=$this->getValorString($data->fechaFin);
    $idTipoAlerta=$this->getValorString($data->idTipoAlerta);
    $idGrupo=$idGrupox;
    $idEstadoOcp = $this->getValorNumerico($data->idEstadoOcp);

//die(" idEstadoOcp ".$idEstadoOcp);

    $numdocumento=$this->getValorString($data->numdocumento);
    $sujeto=$this->getValorString($data->nombrecontratante);

        $pageIndex=$this->getValorNumerico($data->pageIndex);
        $pageSize=$this->getValorString($data->pageSize);
        $pageIndex=intval($pageIndex)+1;
    $sql="";

    $sql.=$this->getContenidoAlerta('total',$idGrupo,$idTipoAlerta);

          $sql.="
              SELECT ";

          $sql.=$this->getContenidoAlerta('cuerpototal',$idGrupo,$idTipoAlerta);
       
          $sql.=" FROM ocpreporte.detalle_alerta_ocp d 
inner join ocpreporte.tipo_alerta ta on d.idtipoalerta=ta.codigo and ta.idgrupoalerta=d.idGrupo and tipogrupo='cnt' and activo=1
left join sisgen.actojuridico a on d.idacto=a.id
where idtipoalerta=$idTipoAlerta and idGrupo=$idGrupo
          ";


        if(intval($idNotaria)>0)
            $sql.=" and idnotaria=".$idNotaria;
        
        if(intval($idColegio)>0)
            $sql.=" and idcolegio=".$idColegio;

          if($numdocumento!="")
            $sql.=" and numdoc='".$numdocumento."'";

          if($sujeto!="")
            $sql.=" and sujeto like '".trim($sujeto)."%'";

       if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("Y-m-d", strtotime($fechaInicio));
            $fin = date("Y-m-d", strtotime($fechaFin));

          $sql.="
               AND 
            TRUNC(fechacarga) BETWEEN TO_DATE('".$inicio."', 'YYYY-MM-DD')   AND   TO_DATE('".$fin."', 'YYYY-MM-DD')  
          
          ";
        }

        if($idEstadoOcp>0)
            {
              $sql.=" AND (";
              if($idEstadoOcp==1)
              {

                  $sql.=" 
                ( select count(1) from ocpreporte.comentario_alerta ca 
                where ca.idalerta=d.id and ca.idtipoalerta=5 
                )=0  OR ";
              }
              $sql.="
                ( select ca.idestadoocp from ocpreporte.comentario_alerta ca 
                where ca.idalerta=d.id and ca.idtipoalerta=5   ORDER BY ca.id desc 
                FETCH NEXT 1 ROWS ONLY
                )=$idEstadoOcp
              ";
              $sql.=" ) ";
            }
        $sql.=$this->getContenidoAlerta('fin',$idGrupo,$idTipoAlerta);
    // die($sql);
         $all=$this->getAllTotal($sql);
        return $all;
 }
private function getContenidoAlerta($posicion='',$idGrupo=0,$idTipoAlerta=0)
{
// WHERE idgrupo = 13 AND idtipoalerta = 3 
  $sql="";
   $allIndex= array(
    '11' =>array(1,2,3,4,5,6,7,8,9),
    '12' => array(2,3,4),
    '13' => array(2,3,4),
    '14' => array(1,2),
    '15' => array(1),
    '16' => array(1),
    '17' => array(1,2,3),
    '18' => array(1,2,3),
    '19' => array(1)
     );

  //iDgrupO, idalerta, cantidad agrupado, monto,campo agreupado
  $allAgrupado= array(
    '11' =>array('1'=>
                      array('1','3','30000','numdoc'),

                 '2'=>
                      array('2','5','50000','numdoc'),

                '3'=>
                      array('3','3','0','sujeto'),
                '4'=>
                      array('4','1','0','numdoc'),
                '5'=>
                      array('5','3','0','numdoc'),
                
                '6'=>
                      array('6','0','0','numdoc'),
                '7'=>
                      array('7','0','0','numdoc'),
                '8'=>
                      array('8','0','0','numdoc'),
                '9'=>
                      array('9','0','0','numdoc'),
                      

                    ),
      '12' =>array('2'=>
                      array('2','3','0','partidaregistral'),
                    '3'=>
                      array('3','0','0','numdoc'),
                    '4'=>
                      array('4','0','0','numdoc')
                    ),
      '13' =>array('2'=>
                      array('2','3','0','NUMEROPLACA'),
                   '3'=>
                      array('3','3','0','sujeto'),
                    '4'=>
                      array('4','0','0','numdoc')
                    ),
      '15' =>array('1'=>
                      array('1','2','0','numdoc')
                    ),
      
      '17' =>array('1'=>
                      array('1','3','0','direccion'),
                   '3'=>
                      array('3','3','0','numdoc')
                    )



     );

  if($posicion=="orden")
  {
   $xcampoagrupado = isset($allAgrupado[$idGrupo][$idTipoAlerta][3])?$allAgrupado[$idGrupo][$idTipoAlerta][3]:"numdoc";
    $sql=" ".$xcampoagrupado." desc ";
  }else{

  

  $sql="";
  if($posicion=="inicio"){
     if (isset($allIndex[$idGrupo]) && in_array($idTipoAlerta, $allIndex[$idGrupo])) 
         $sql="select tbl.*,
               ROW_NUMBER() OVER
      (PARTITION BY numdoc,TRUNC(fechageneracion) ORDER BY fechageneracion ,nUmdoc) AS rn 
      ,
          '' AS cant_alerta 
          FROM ( ";
 
  }else if($posicion=="cuerpo"){
           if (isset($allIndex[$idGrupo]) && in_array($idTipoAlerta, $allIndex[$idGrupo])){
             $xcampoagrupado = isset($allAgrupado[$idGrupo][$idTipoAlerta][3])?$allAgrupado[$idGrupo][$idTipoAlerta][3]:"numdoc";
              $sql=" , ROW_NUMBER() OVER (PARTITION BY 
                     ".$xcampoagrupado."
               ORDER BY fechacarga,numerodeinstrumento) AS cantidad_kardex ";
           }

         
      if (isset($allAgrupado[$idGrupo])
        && isset($allAgrupado[$idGrupo][$idTipoAlerta])
        && isset($allAgrupado[$idGrupo][$idTipoAlerta][0]) 
      ) {

          $xcampoagrupado = isset($allAgrupado[$idGrupo][$idTipoAlerta][3])?$allAgrupado[$idGrupo][$idTipoAlerta][3]:"sujeto";
           // echo "aaaaaaaaa";
          $sql.=" , SUM(d.monto_patrimonial) OVER(PARTITION BY 
                ".$xcampoagrupado."
           ORDER BY fechacarga,numerodeinstrumento) AS suma_patrimonial ";
        }


  }else if($posicion=="fin"){
   if (isset($allIndex[$idGrupo]) && in_array($idTipoAlerta, $allIndex[$idGrupo])){
         $xcomparacioncantidad = isset($allAgrupado[$idGrupo][$idTipoAlerta][1])?$allAgrupado[$idGrupo][$idTipoAlerta][1]:"3";
          $sql=") tbl  WHERE cantidad_kardex >=".$xcomparacioncantidad."   ";

        }


      if (isset($allAgrupado[$idGrupo])
        && isset($allAgrupado[$idGrupo][$idTipoAlerta])
        && isset($allAgrupado[$idGrupo][$idTipoAlerta][0]) 
      ) {
              $xmonto = isset($allAgrupado[$idGrupo][$idTipoAlerta][2])?$allAgrupado[$idGrupo][$idTipoAlerta][2]:"numdoc";
                if(intval($xmonto)>0)
                  $sql.=" and suma_patrimonial>=".$xmonto;
             }

      
  }else if($posicion=="total"){
    if (isset($allIndex[$idGrupo]) && in_array($idTipoAlerta, $allIndex[$idGrupo]))
          $sql="select count(1) as cantidad FROM (  ";
      
  }else if($posicion=="cuerpototal"){
    if (isset($allIndex[$idGrupo]) && in_array($idTipoAlerta, $allIndex[$idGrupo])){

          $xcampoagrupado=  isset($allAgrupado[$idGrupo][$idTipoAlerta][3])?$allAgrupado[$idGrupo][$idTipoAlerta][3]:"sujeto";


          $sql=" ROW_NUMBER() OVER (PARTITION BY
              ".$xcampoagrupado."
          ORDER BY fechacarga,numerodeinstrumento) as cantidad_kardex  ";
          $sql.=" , ROW_NUMBER() OVER
(PARTITION BY numdoc,to_date(fechacarga,'YYYY-MM-DD') ORDER BY nUmdoc) AS rn  ";
          
        }
    else
           $sql=" count(1) as cantidad  ";


          if (isset($allAgrupado[$idGrupo])
        && isset($allAgrupado[$idGrupo][$idTipoAlerta])
        && isset($allAgrupado[$idGrupo][$idTipoAlerta][0]) 
      ) {
                $xcampoagrupado = isset($allAgrupado[$idGrupo][$idTipoAlerta][3])?$allAgrupado[$idGrupo][$idTipoAlerta][3]:"sujeto";
           // echo "aaaaaaaaa";
          $sql.=" , SUM(d.monto_patrimonial) OVER(PARTITION BY 
                ".$xcampoagrupado."
           ORDER BY fechacarga,numerodeinstrumento) AS suma_patrimonial ";
        }  

        //$partidaRegistral = $allAgrupado['11'][$index];

   }
  }
  return $sql;
  }
  private function getCantidadPorDni()
  {

  }
}

?>