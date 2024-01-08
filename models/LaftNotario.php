<?php 
class Models_LaftNotario extends DB_Connect {
        
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
    

public function getListAnexolaft($get_data)
{
  //var_dump($get_data);return;
  $iddocumento = $this->getValorIntSanit("iddocumento");
  $tipolaft = $this->getValorString($get_data["tipolaft"]);

  $sql="
  select id,descripcion,nombrearchivo,tipo_archivo,
  descripcionarchivo,to_char(fecharegistro,'dd/mm/YYYY') as fecharegistro,tipo_laft
  from  ALERTAOPERACION.ANEXOS_LAFT_NOTARIO WHERE 
  iddocumentonotarial=".$iddocumento." AND tipo_laft='".$tipolaft."' AND estado=1 ";

  $sql.=" ORDER BY id desc ";
  //die("=>".$sql);
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


public function getLaftNotarioById()
{
  $iddocumento = $this->getValorIntSanit("iddocumento");
  $allData=[];
  $iddocumentoLaf="";
  $all=[];
  $allActos=[];

  $sql="
  SELECT id,enviado,valorescontratantes,nvl(valoresalertaporcontratante,'') as valoresalertaporcontratante,
valoresalertaporacto,idestadoocp,idtipolaft,comentario_tipolaft
FROM alertaoperacion.documento_laft_notario
where iddocumentonotarial=".$iddocumento;

if($this->getRow($sql)!=null)
  $all=$this->getRow($sql);

if(isset($all["ID"]))
  $iddocumentoLaf=$all["ID"];

if($iddocumentoLaf!=""){
  $sqlActos="
  select idacto,valoresalerta,otra_alerta as otraalerta,id,idoperacion
  from  ALERTAOPERACION.ACTO_LAFT_NOTARIO where activo=1 and iddocumentolaft=".$iddocumentoLaf;

  $allActos=$this->getListAllRows($sqlActos);
}

  $allData[0]=$all;
  $allData[1]=$allActos;

  return $allData;
}

private function updatelaftNotario($objRequest)
{
   $iddocumento=$this->getValorString($objRequest->iddocumento);
  $idsContratantes=$this->getValorString($objRequest->idsContratantes);
  $idsAlertaContratante=$this->getValorString($objRequest->idsAlertaContratante);
  $descripcionLaft=$this->getValorString($objRequest->descripcionLaft);
  $iddocumentoalerta=$this->getValorString($objRequest->iddocumentoalerta);
  $tipoenvio=$this->getValorString($objRequest->tipoenvio);


  $estadoEnvio="0";
  if($tipoenvio=="envio")
    $estadoEnvio="1";
  



    $comentario=$descripcionLaft;

    $sqlUpdate="update  ALERTAOPERACION.DOCUMENTO_LAFT_NOTARIO
      set ENVIADO=:estadoEnvio, COMENTARIO_TIPOLAFT=:comentario,
      VALORESCONTRATANTES=:idsContratantes,
      VALORESALERTAPORCONTRATANTE=:idsAlertaContratante
      where id=:iddocumentoalerta
        ";
       //echo $sqlInsert."<br><br><br>";
        $db=$this->connect();
        $stUpdate=oci_parse($db,$sqlUpdate);
       
        oci_bind_by_name($stUpdate, ":estadoEnvio", $estadoEnvio);
        oci_bind_by_name($stUpdate, ":comentario", $comentario);
        oci_bind_by_name($stUpdate, ":idsContratantes", $idsContratantes);
        oci_bind_by_name($stUpdate, ":idsAlertaContratante", $idsAlertaContratante);
        oci_bind_by_name($stUpdate, ":iddocumentoalerta", $iddocumentoalerta);
        

        oci_execute($stUpdate);  
        oci_free_statement($stUpdate);


      $sqlAnularContratante="update  ALERTAOPERACION.contratante
      set activo=0
      where iddocumentonotarial=:iddocumento
        ";
        $db=$this->connect();
        $stContratante=oci_parse($db,$sqlAnularContratante);
       
        oci_bind_by_name($stContratante, ":iddocumento", $iddocumento);
        oci_execute($stContratante);  
        oci_free_statement($stContratante);
        
        $sqlAnularActos="update  ALERTAOPERACION.acto_laft_notario
      set activo=0
      where iddocumentolaft=:iddocumentoalerta
        ";
        $db=$this->connect();
        $stActo=oci_parse($db,$sqlAnularActos);
       
        oci_bind_by_name($stActo, ":iddocumentoalerta", $iddocumentoalerta);
        oci_execute($stActo);  
        oci_free_statement($stActo);


        oci_close($db);

}
public function addLaftNotarioResumido($objRequest)
{
  $iddocumento="1543305";
  $db=$this->connect();
  $valoresalertaporcontratante="99";
  $valorescontratantes="40397317";
  $idnotaria_laft=isset($_SESSION["idnotaria_laft"])?$_SESSION["idnotaria_laft"]:"137";
        $idTipoLaft=3;
        $comentario="";



             $sqlValor="  select codigo from  alertaoperacion.documento_laft_notario where idnotaria=$idnotaria_laft
                order by id desc 
                FETCH FIRST 1 ROWS ONLY";


                $stid = oci_parse($db,$sqlValor);
                oci_execute($stid);
                $rowValidacion = oci_fetch_assoc($stid);
                $codigo=$rowValidacion["CODIGO"];
                if($codigo=="")
                    $codigo=1;
                else
                  $codigo=$codigo+1;
            $sqlInsert="INSERT INTO ALERTAOPERACION.DOCUMENTO_LAFT_NOTARIO
            (FECHAREGISTRO,ENVIADO,IDTIPOLAFT,codigo,idnotaria,iddocumentonotarial,VALORESALERTAPORCONTRATANTE,VALORESCONTRATANTES
            ) VALUES (SYSTIMESTAMP,1,3,:codigo,:idnotaria_laft,:iddocumento,:valoresalertaporcontratante,:valorescontratantes)  returning  ID into :inserted_id
            ";
           //echo $sqlInsert."<br><br><br>";
            
            $stInsert=oci_parse($db,$sqlInsert);
            oci_bind_by_name($stInsert, ":codigo", $codigo);
            oci_bind_by_name($stInsert, ":idnotaria_laft", $idnotaria_laft);
            oci_bind_by_name($stInsert, ":iddocumento", $iddocumento);
            oci_bind_by_name($stInsert, ":valoresalertaporcontratante", $valoresalertaporcontratante);

            oci_bind_by_name($stInsert, ":valorescontratantes", $valorescontratantes);
            
            oci_bind_by_name($stInsert, ":inserted_id", $idNumber,35);
            oci_execute($stInsert);  
            oci_free_statement($stInsert);


  
              $idacto="1";
              $valores="105";
              $otraalerta=isset($value->otraalerta)?$value->otraalerta:"";

               $sqlOperacion="select cuantia,c.idtipomoneda from sisgen.operacion o inner join  sisgen.cuantia c on o.id=c.idoperacion where o.iddocumentonotarial=".$iddocumento;

                $stid = oci_parse($db,$sqlOperacion);
                oci_execute($stid);
                $rowOperacion = oci_fetch_assoc($stid);
                $lcuantia=$rowOperacion["CUANTIA"];
                $lidmoneda=$rowOperacion["IDTIPOMONEDA"];

              $sqlOf="INSERT INTO  ALERTAOPERACION.ACTO_LAFT_NOTARIO (IDDOCUMENTOLAFT,IDACTO,CUANTIA,IDMONEDA,valoresalerta)
                VALUES (:iddocumentolaft,:idacto,:lcuantia,:lidmoneda,:valores)
                ";

                $stInsert=oci_parse($db,$sqlOf);
                oci_bind_by_name($stInsert, ":iddocumentolaft", $idNumber);
                oci_bind_by_name($stInsert, ":idacto",$idacto);
                oci_bind_by_name($stInsert, ":lcuantia", $lcuantia);
                oci_bind_by_name($stInsert, ":lidmoneda", $lidmoneda);
                oci_bind_by_name($stInsert, ":valores", $valores);

                oci_execute($stInsert);  
      
    $allContratante=$this->getContratantesPorOperacion(true,$iddocumento);
    /*
    var_dump($allContratante);
    return;*/
    foreach ($allContratante as $val) {
     

        $sqlCont="INSERT INTO  alertaoperacion.contratante 
                (
IDTIPOPERSONA,IDTIPODOCUMENTO,NUMERODOCUMENTO,
IDPAIS,ESPEP,PRIMERAPELLIDO_RAZONSOCIAL,
SEGUNDOAPELLIDO,NOMBRE,IDDEPARTAMENTO,IDPROVINCIA,
IDDISTRITO,FECHANACIMIENTO,IDPROFESION,OTRAPROFESION,
ROL,IDSECTOR,OTRAACTIVIDAD,IDCARGO,DOMICILIO,TELEFONO,ORIGENFONDOS,IDTIPOINTERVENCION,
IDDOCUMENTOLAFT,IDSUJETO
                )
                VALUES (:idTipoPersona,:idtipoDocumento,:numdoc,:idPais,:esPep,:primerApellido,
                :segundoApellido,:nombre,:iddepartamento,:idprovincia,:iddistrito,
                :fechanacimiento,:idprofesion,:otraprofesion,:rol,:idsector,:otraactividad,
                :idcargo,:direccion,:telefono,:origenfondos,:idtipoIntervencion,:iddocumento,
                :idsujeto
                )
                ";
                $idTipoPersona=$val["IDTIPOPERSONA"];
                $idtipoDocumento=$val["TIPODOCUMENTOID"];
                $numdoc=$val["NUMDOC"];
                $idPais=$val["IDPAIS"];
                $esPep=$val["ES_PEP"];
                if($idTipoPersona==1)   
                  $primerApellido=$val["PRIMERAPELLIDO"];
                else if($idTipoPersona==2)   
                  $primerApellido=$val["RAZONSOCIAL"];
                $segundoApellido=$val["SEGUNDOAPELLIDO"];
                $nombre=$val["NOMBRE"];
                $iddepartamento=$val["IDDEPARTAMENTO"];
                $idprovincia=$val["IDPROVINCIA"];
                $iddistrito=$val["IDDISTRITO"];
                $fechanacimiento=$val["FECHANACIMIENTO"];
                $idprofesion=$val["IDPROFESION"];
                $otraprofesion=$val["OTRAPROFESION"];
                $rol=$val["ROL"];
                $idsector=$val["IDSECTOR"];
                $otraactividad=$val["ACTIVIDAD"];
                $idcargo=$val["IDCARGO"];
                $direccion=$val["RESTODIRECCION"];
                $telefono=$val["TELEFONO"];
                $origenfondos=$val["ORIGENFONDOS"];
                $idtipoIntervencion=$val["IDTIPOINTERVENCION"];
                $idsujeto=$val["IDSUJETO"];

                
          
                $stInsCont=oci_parse($db,$sqlCont);
                oci_bind_by_name($stInsCont, ":idTipoPersona", $idTipoPersona);
                oci_bind_by_name($stInsCont, ":idtipoDocumento", $idtipoDocumento);

                oci_bind_by_name($stInsCont, ":numdoc", $numdoc);
                oci_bind_by_name($stInsCont, ":idPais", $idPais);
                oci_bind_by_name($stInsCont, ":esPep", $esPep);
                oci_bind_by_name($stInsCont, ":primerApellido", $primerApellido);
                oci_bind_by_name($stInsCont, ":segundoApellido", $segundoApellido);
                oci_bind_by_name($stInsCont, ":nombre", $nombre);

                oci_bind_by_name($stInsCont, ":iddepartamento", $iddepartamento);
                oci_bind_by_name($stInsCont, ":idprovincia", $idprovincia);
                oci_bind_by_name($stInsCont, ":iddistrito", $iddistrito);
                oci_bind_by_name($stInsCont, ":fechanacimiento", $fechanacimiento);
                oci_bind_by_name($stInsCont, ":idprofesion", $idprofesion);
                oci_bind_by_name($stInsCont, ":otraprofesion", $otraprofesion);
                oci_bind_by_name($stInsCont, ":rol", $rol);
                oci_bind_by_name($stInsCont, ":idsector", $idsector);
                oci_bind_by_name($stInsCont, ":otraactividad", $otraactividad);
                oci_bind_by_name($stInsCont, ":idcargo", $idcargo);
                oci_bind_by_name($stInsCont, ":direccion", $direccion);
                oci_bind_by_name($stInsCont, ":telefono", $telefono);
                oci_bind_by_name($stInsCont, ":origenfondos", $origenfondos);
                oci_bind_by_name($stInsCont, ":idtipoIntervencion", $idtipoIntervencion);
                oci_bind_by_name($stInsCont, ":iddocumento", $idNumber);
                oci_bind_by_name($stInsCont, ":idsujeto", $idsujeto);
                oci_execute($stInsCont);  
    }

      oci_close($db);
      $response=array("response"=>"correcto","status"=>"1");      
      return ($response);


}
public function addLaftNotario($objRequest)
{
  $iddocumento=$this->getValorString($objRequest->iddocumento);
  $idsContratantes=$this->getValorString($objRequest->idsContratantes);
  $idsAlertaContratante=$this->getValorString($objRequest->idsAlertaContratante);
  $allAlertaPorActo=($objRequest->allAlertaPorActo);
 
//  var_dump($allAlertaPorActo);


  $tipoenvio=$this->getValorString($objRequest->tipoenvio);
  $descripcionLaft=$this->getValorString($objRequest->descripcionLaft);
  $tipoAlertaNumero=$this->getValorString($objRequest->tipoAlertaNumero);
  $infoEnviada=isset($objRequest->infoEnviada)?$objRequest->infoEnviada:"";
  $iddocumentoalerta=isset($objRequest->iddocumentoalerta)?$objRequest->iddocumentoalerta:"0";
  $idNumber=0;
  $db=$this->connect();




  $actosOtraAlerta=   ($objRequest->actosOtraAlerta);
  $estadoEnvio="0";

  if($tipoenvio=="envio")
    $estadoEnvio="1";
      

  if(intval($iddocumentoalerta)>0)
        {
            $this->updatelaftNotario($objRequest);
/*            $response=array("response"=>"correcto","status"=>"1");      
            return ($response);*/
              $idNumber=$iddocumentoalerta;
        }else
          {
        $idTipoLaft=$tipoAlertaNumero;
        $comentario=$descripcionLaft;

        $sqlInsert="INSERT INTO ALERTAOPERACION.DOCUMENTO_LAFT_NOTARIO
            (IDDOCUMENTONOTARIAL,VALORESCONTRATANTES,VALORESALERTAPORCONTRATANTE,FECHAREGISTRO,IDTIPOLAFT,ENVIADO,COMENTARIO_TIPOLAFT
            ) VALUES 
            (
            :iddocumento,:idsContratantes,:idsAlertaContratante,
            SYSTIMESTAMP,:idTipoLaft,:estadoEnvio,:comentario
            )  returning  ID into :inserted_id
            ";
           //echo $sqlInsert."<br><br><br>";
            
            $stInsert=oci_parse($db,$sqlInsert);
            
     
            oci_bind_by_name($stInsert, ":iddocumento", $iddocumento);
            oci_bind_by_name($stInsert, ":idsContratantes", $idsContratantes);
            oci_bind_by_name($stInsert, ":idsAlertaContratante", $idsAlertaContratante);
            oci_bind_by_name($stInsert, ":idTipoLaft", $idTipoLaft);
            oci_bind_by_name($stInsert, ":estadoEnvio", $estadoEnvio);
            oci_bind_by_name($stInsert, ":comentario", $comentario);
            oci_bind_by_name($stInsert, ":inserted_id", $idNumber,35);
            oci_execute($stInsert);  
            oci_free_statement($stInsert);

          }

      if(isset($allAlertaPorActo)){
        foreach ($allAlertaPorActo as $key => $value) {
              $idacto=$key;
              $valores=isset($value->idalerta)?$value->idalerta:"";
              $otraalerta=isset($value->otraalerta)?$value->otraalerta:"";
              $idoperacion=isset($value->idOperacion)?$value->idOperacion:"";
              $lcuantia="";
              $lidmoneda="";

              if(intval($idoperacion)>0){
                $sqlOperacion="select cuantia,c.idtipomoneda from sisgen.operacion o inner join  sisgen.cuantia c on o.id=c.idoperacion where o.id=".$idoperacion;

                $stid = oci_parse($db,$sqlOperacion);
                oci_execute($stid);
                $rowOperacion = oci_fetch_assoc($stid);
                $lcuantia=$rowOperacion["CUANTIA"];
                $lidmoneda=$rowOperacion["IDTIPOMONEDA"];
              }



              
              $sqlOf="INSERT INTO  ALERTAOPERACION.ACTO_LAFT_NOTARIO (IDDOCUMENTOLAFT,IDACTO,VALORESALERTA,OTRA_ALERTA,CUANTIA,IDMONEDA,idoperacion)
                VALUES (:iddocumentolaft,:idacto,:valores,:otraalerta,:lcuantia,:lidmoneda,:idoperacion)
                ";

                $stInsert=oci_parse($db,$sqlOf);
                oci_bind_by_name($stInsert, ":iddocumentolaft", $idNumber);
                oci_bind_by_name($stInsert, ":idacto", $idacto);
                oci_bind_by_name($stInsert, ":valores", $valores);
                oci_bind_by_name($stInsert, ":otraalerta", $otraalerta);
                oci_bind_by_name($stInsert, ":lcuantia", $lcuantia);
                oci_bind_by_name($stInsert, ":lidmoneda", $lidmoneda);
                oci_bind_by_name($stInsert, ":idoperacion", $idoperacion);



                oci_execute($stInsert);  
        }
      }

    $allContratante=$this->getContratantesPorOperacion(true,$iddocumento);
  
    foreach ($allContratante as $val) {
     

        $sqlCont="INSERT INTO  alertaoperacion.contratante 
                (
IDTIPOPERSONA,IDTIPODOCUMENTO,NUMERODOCUMENTO,
IDPAIS,ESPEP,PRIMERAPELLIDO_RAZONSOCIAL,
SEGUNDOAPELLIDO,NOMBRE,IDDEPARTAMENTO,IDPROVINCIA,
IDDISTRITO,FECHANACIMIENTO,IDPROFESION,OTRAPROFESION,
ROL,IDSECTOR,OTRAACTIVIDAD,IDCARGO,DOMICILIO,TELEFONO,ORIGENFONDOS,IDTIPOINTERVENCION,
IDDOCUMENTONOTARIAL,IDSUJETO
                )
                VALUES (:idTipoPersona,:idtipoDocumento,:numdoc,:idPais,:esPep,:primerApellido,
                :segundoApellido,:nombre,:iddepartamento,:idprovincia,:iddistrito,
                :fechanacimiento,:idprofesion,:otraprofesion,:rol,:idsector,:otraactividad,
                :idcargo,:direccion,:telefono,:origenfondos,:idtipoIntervencion,:iddocumento,
                :idsujeto
                )
                ";
                $idTipoPersona=$val["IDTIPOPERSONA"];
                $idtipoDocumento=$val["TIPODOCUMENTOID"];
                $numdoc=$val["NUMDOC"];
                $idPais=$val["IDPAIS"];
                $esPep=$val["ES_PEP"];
                if($idTipoPersona==1)   
                  $primerApellido=$val["PRIMERAPELLIDO"];
                else if($idTipoPersona==2)   
                  $primerApellido=$val["RAZONSOCIAL"];
                $segundoApellido=$val["SEGUNDOAPELLIDO"];
                $nombre=$val["NOMBRE"];
                $iddepartamento=$val["IDDEPARTAMENTO"];
                $idprovincia=$val["IDPROVINCIA"];
                $iddistrito=$val["IDDISTRITO"];
                $fechanacimiento=$val["FECHANACIMIENTO"];
                $idprofesion=$val["IDPROFESION"];
                $otraprofesion=$val["OTRAPROFESION"];
                $rol=$val["ROL"];
                $idsector=$val["IDSECTOR"];
                $otraactividad=$val["ACTIVIDAD"];
                $idcargo=$val["IDCARGO"];
                $direccion=$val["RESTODIRECCION"];
                $telefono=$val["TELEFONO"];
                $origenfondos=$val["ORIGENFONDOS"];
                $idtipoIntervencion=$val["IDTIPOINTERVENCION"];
                $idsujeto=$val["IDSUJETO"];

                
          
                $stInsCont=oci_parse($db,$sqlCont);
                oci_bind_by_name($stInsCont, ":idTipoPersona", $idTipoPersona);
                oci_bind_by_name($stInsCont, ":idtipoDocumento", $idtipoDocumento);

                oci_bind_by_name($stInsCont, ":numdoc", $numdoc);
                oci_bind_by_name($stInsCont, ":idPais", $idPais);
                oci_bind_by_name($stInsCont, ":esPep", $esPep);
                oci_bind_by_name($stInsCont, ":primerApellido", $primerApellido);
                oci_bind_by_name($stInsCont, ":segundoApellido", $segundoApellido);
                oci_bind_by_name($stInsCont, ":nombre", $nombre);

                oci_bind_by_name($stInsCont, ":iddepartamento", $iddepartamento);
                oci_bind_by_name($stInsCont, ":idprovincia", $idprovincia);
                oci_bind_by_name($stInsCont, ":iddistrito", $iddistrito);
                oci_bind_by_name($stInsCont, ":fechanacimiento", $fechanacimiento);
                oci_bind_by_name($stInsCont, ":idprofesion", $idprofesion);
                oci_bind_by_name($stInsCont, ":otraprofesion", $otraprofesion);
                oci_bind_by_name($stInsCont, ":rol", $rol);
                oci_bind_by_name($stInsCont, ":idsector", $idsector);
                oci_bind_by_name($stInsCont, ":otraactividad", $otraactividad);
                oci_bind_by_name($stInsCont, ":idcargo", $idcargo);
                oci_bind_by_name($stInsCont, ":direccion", $direccion);
                oci_bind_by_name($stInsCont, ":telefono", $telefono);
                oci_bind_by_name($stInsCont, ":origenfondos", $origenfondos);
                oci_bind_by_name($stInsCont, ":idtipoIntervencion", $idtipoIntervencion);
                oci_bind_by_name($stInsCont, ":iddocumento", $iddocumento);
                oci_bind_by_name($stInsCont, ":idsujeto", $idsujeto);
                oci_execute($stInsCont);  
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



    public function getRptsimple($get_data)
       {      

        $info=json_decode($get_data);


        $bfechaInicio=isset($info->fechaInicio)?$info->fechaInicio:"";
        $bfechaFin=isset($info->fechaFin)?$info->fechaFin:"";

        $bpageIndex=isset($info->pageIndex)?intval($info->pageIndex):"0";
        $bpageSize=isset($info->pageSize)?intval($info->pageSize):"0";


        $sqlWhere="";
        $sqlInner="";
        $isLimit=true;
        $sqlHaving="";

  

        $rol=isset($_SESSION["idacceso_laft"])?$_SESSION["idacceso_laft"]:"";


        $sqlPaginator="";
        $pageInit=0;
        if($bpageIndex>0)
            $pageInit=($bpageIndex*$bpageSize)+1;
        
        $valueMax= ($bpageSize*($bpageIndex+1));
        
       
       
       $db=$this->connect();
       
       $sql="
         select id,enviado,to_char(fecharegistro,'dd/mm/YYYY') 
            as fecharegistro,
            TO_CHAR(fecharegistro, 'HH:MI:SS') as horaregistro,
            lpad(codigo,5,0) as codigo
            from alertaoperacion.documento_laft_notario d
             WHERE d.id>0
        ";   

          if($rol==2 &&  isset($_SESSION) && $_SESSION["idnotaria_laft"]!="")
            $sql.=" AND D.IDNOTARIA=".$_SESSION["idnotaria_laft"];
        else
          $sql.="AND D.IDNOTARIA=137 ";

          if($bfechaInicio!="" && $bfechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($bfechaInicio));
            $fin = date("d/m/Y", strtotime($bfechaFin));

            $sql.=" AND trunc(D.fecharegistro) BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        $sql.=" order by d.id desc ";
        if($bpageIndex!="" && $bpageIndex>0)
            $pageInit=((int)($bpageIndex-1)*(int)$bpageSize);
        
        if($bpageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$bpageSize." ROWS ONLY ";
        $all=$this->getListAllRows($sql);
      return $all;
}



    public function getRptsimpleCount($get_data)
       {      

        $info=json_decode($get_data);


        $bfechaInicio=isset($info->fechaInicio)?$info->fechaInicio:"";
        $bfechaFin=isset($info->fechaFin)?$info->fechaFin:"";

        $bpageIndex=isset($info->pageIndex)?intval($info->pageIndex):"0";
        $bpageSize=isset($info->pageSize)?intval($info->pageSize):"0";


        $sqlWhere="";
        $sqlInner="";
        $isLimit=true;
        $sqlHaving="";

  

        $rol=isset($_SESSION["idacceso_laft"])?$_SESSION["idacceso_laft"]:"";


        $sqlPaginator="";
        $pageInit=0;
        if($bpageIndex>0)
            $pageInit=($bpageIndex*$bpageSize)+1;
        
        $valueMax= ($bpageSize*($bpageIndex+1));
        
       
       
       $db=$this->connect();
       
       $sql="
         select count(1) as cantidad
            from alertaoperacion.documento_laft_notario d
             WHERE d.id>0
        ";   

          if($rol==2 &&  isset($_SESSION) && $_SESSION["idnotaria_laft"]!="")
            $sql.=" AND D.IDNOTARIA=".$_SESSION["idnotaria_laft"];
        else
          $sql.="AND D.IDNOTARIA=137 ";

          if($bfechaInicio!="" && $bfechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($bfechaInicio));
            $fin = date("d/m/Y", strtotime($bfechaFin));

            $sql.=" AND trunc(D.fecharegistro) BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        $total=$this->getAllTotal($sql);
      return $total;
}


    public function getContratantesPorOperacion($insert=false,$xiddoc=0)
       {      
            $db=$this->connect();
            if(!$insert)
              $iddocumento = $this->getValorIntSanit("iddocumento");
            else
              $iddocumento = $xiddoc;
              
            $sql="
                SELECT distinct DI.DESCRIPCION AS numdoc,

                PF.NOMBRE,
                PF.PRIMERAPELLIDO,
                PF.SEGUNDOAPELLIDO,
                PJ.RAZONSOCIAL,
                tdi.descripcion as tipo_doc,
                IDTIPOPERSONA,
                DI.tipodocumentoid,
                dd.idpais,
                 (
                case s.idtipopersona when 1 then pf.cliente when 2 then pj.cliente else '' end
                )as contratante,pj.otraactividad as actividad,
                ofs.cuantiaorigen,
                ofs.origen as origenfondos,
                ofs.tipomoneda,
                tin.descripcion as condicion,
                i.id as interviniente,s.id as idsujeto,
                i.fechafirma,i.rolrepresentante as rol,
                (
                  CASE WHEN dln.id>0 THEN '1' 
                  ELSE ''
                  END
                ) AS ACTIVO,
                (
                                                  SELECT                 
                             1
              FROM sisgen.relaciones r 
              INNER JOIN sisgen.consultamanual cm ON r.consultamanualid = cm.id
              INNER JOIN sisgen.lista l ON r.listaid = l.id
              INNER JOIN sisgen.tipolista tl ON l.idtipolista = tl.id
              INNER JOIN sisgen.consultamanualresult crs ON cm.id = crs.consultaid
              WHERE r.documentonotarialid= o.iddocumentonotarial and tl.id=6
              and rownum=1
              ) as es_pep,dd.iddepartamento,dd.idprovincia,dd.iddistrito,
              PF.fechanacimiento,S.idprofesion,s.otraprofesion,
              PJ.idsector,s.cargo as idcargo,
              dd.restodireccion,
               (
                case s.idtipopersona when 1 then pf.telefono when 2 then pj.telefono else '' end
                )as telefono,i.idtipointervencion

                from  sisgen.operacion o 
                inner join sisgen.interviniente i on o.id=i.idoperacion
                INNER JOIN SISGEN.SUJETO S ON S.ID=I.IDPERSONA 
                INNER JOIN SISGEN.SUJETODOCIDENTIFICATIVO SI ON S.ID=SI.IDPERSONA
                INNER JOIN SISGEN.DOCUMENTOIDENTIFICATIVO DI ON SI.IDDOCUMENTOIDENTIFICATIVO=DI.ID
                INNER JOIN SISGEN.tipodocumentoidentificativo tdi ON di.tipodocumentoid=tdi.id
                
                LEFT JOIN SISGEN.PERSONAFISICA PF ON S.IDPERSONAFISICA=PF.ID
                LEFT JOIN SISGEN.ESTADOCIVIL ec on pf.estadocivil=ec.id
                left join sisgen.direccion dd on s.iddireccion=dd.id
                LEFT JOIN SISGEN.PERSONAJURIDICA PJ ON S.IDPERSONAJURIDICA=PJ.ID
                left join sisgen.origenfondos ofs on i.id=ofs.idinterviniente
                left join sisgen.tipointervencion tin on i.idtipointervencion=tin.id
                left join alertaoperacion.documento_laft_notario dln on o.iddocumentonotarial=dln.iddocumentonotarial 
                AND INSTR(',' || dln.valorescontratantes || ',', ',' || TO_CHAR(s.id) || ',') > 0
                where o.iddocumentonotarial=".$iddocumento;
              $sql.=" order by PF.PRIMERAPELLIDO ";
           $stid = oci_parse($db,$sql);
         //  die($sql);
           oci_execute($stid);
           $all=[];
           while($row = oci_fetch_assoc($stid))
           {
            $all[]=$row;
           }
           
            oci_free_statement($stid);
            oci_close($db);   
            return $all;     
        }

public function getListDocumentoPorAlerta($iddocumentox='')
{
  //var_dump($get_data);return;
  $iddocumento = $this->getValorIntSanit("iddocumento");
  $idcontratante = $this->getValorIntSanit("idcontratante");

if($idcontratante=="")
    $idcontratante="690";


  $iddocumento=isset($iddocumento) && intval($iddocumento)>0?$iddocumento:$iddocumentox;




  $all=[];
  $sql="
select n.descripcion as notaria,c.nombre as colegio,
LPAD(dln.codigo, 5, '0') as codigo,dln.id as iddocumentolaft,
dn.numero as numeroinstrumento,dn.numerodekardex,
dn.fechaautorizacion,ti.descripcion as tipoinstrumento
from alertaoperacion.documento_laft_notario dln 
inner join sisgen.DOCUMENTONOTARIAL dn on dln.iddocumentonotarial=dn.id
inner join sisgen.notaria n on dn.idnotaria=n.id
inner join sisgen.colegio c on n.idcolegio=c.id
left join sisgen.tipoinstrumento ti on dn.idinstrumento=ti.id

where dln.id=".$iddocumento;
//  die("=>".$sql);
  $allDocumento=$this->getRow($sql);
  $all[0]=$allDocumento;
$sqlContratante="
    select distinct
    idtipopersona,
    (
      case c.idtipopersona
      when 1 then 'Persona Natural'
      when 2 then 'Persona Jurídica'
      end
    ) as tipopersona
    ,t.abrev as tipodoc,
c.numerodocumento,c.espep,c.iddepartamento,c.idprovincia,c.iddistrito,
c.primerapellido_razonsocial as primerapellido,
c.segundoapellido,c.nombre,to_char(c.fechanacimiento,'dd/mm/YYYY') as fechanacimiento,pr.descripcion as profesion,
c.otraprofesion,s.descripcion as sector,c.otraactividad,tc.descripcion as cargo,
c.domicilio,dep.descripcion as departamento,pro.descripcion as provincia,dis.descripcion as distrito,
c.telefono,tii.descripcion as participacion,p.nombre as pais,
(select 1 from 
    alertaoperacion.documento_laft_notario dln
    WHERE dln.iddocumentonotarial=c.iddocumentonotarial
    AND INSTR(',' || dln.valorescontratantes || ',', ',' || TO_CHAR(c.idsujeto) || ',') > 0
    and rownum=1
) as vinculado

from alertaoperacion.contratante c
inner join sisgen.tipodocumentoidentificativo t on c.idtipodocumento=t.id
left join sisgen.pais p on c.idpais=p.id
left join sisgen.profesion pr on c.idprofesion=pr.id
inner join SISGEN.tipointervencion tii on c.idtipointervencion=tii.id
left join sisgen.sector s on c.idsector=s.id
left join SISGEN.tipocargo tc on c.idcargo=tc.id
left join SISGEN.departamento dep on c.iddepartamento=dep.id
left join SISGEN.provincia pro on c.idprovincia=pro.id
left join SISGEN.distrito dis on c.iddistrito=dis.id
where c.activo=1 and c.id=$idcontratante and c.IDDOCUMENTOLAFT=".$iddocumento;
  
// die($sqlContratante);
  $allContratante=$this->getListAllRows($sqlContratante);
  $allActos=$this->getActosDocumentoPorAlerta($iddocumento);
  $allAlertasActos=$this->getAlertasActos($iddocumento);
  $allAlertasContratantes=$this->getAlertasContratantes($iddocumento);
 


  $all[1]=$allContratante;
  $all[2]=$allActos;
  $all[3]=$allAlertasActos;
  $all[4]=$allAlertasContratantes;

  return $all;
}

//getListDocumentoPorAlerta
private function getActosDocumentoPorAlerta($iddocumentoLaft){

    $all=[];
    if(intval($iddocumentoLaft)>0)
    {
        $sql=" select distinct '' as tipofondo,
        aj.descripcion as acto,
        '' as otroacto,dn.numero as numeroinstrumento,
        tm.descripcion as tipomoneda, cuantia,
        dln.comentario_tipolaft as comentario_laft
        from alertaoperacion.acto_laft_notario al 
        inner join sisgen.actojuridico aj on al.idacto=aj.id
        inner join sisgen.tipomoneda tm on al.idmoneda=tm.id
        inner join alertaoperacion.documento_laft_notario dln on al.iddocumentolaft=dln.id
        inner join sisgen.documentonotarial dn on dln.iddocumentonotarial=dn.id
        where dln.id=".$iddocumentoLaft;

      // die($sql);
      $all=$this->getListAllRows($sql);
    }
    
    return $all;
}


//getListDocumentoPorAlerta
private function getAlertasActos($iddocumentoLaft){

    $all=[];
    if(intval($iddocumentoLaft)>0)
    {
      
        $sql="
                select distinct tbl.id,tbl.descripcion as alerta,grupo,
               '2' as idtipopersona,aj.descripcion as acto
              from ALERTAOPERACION.documento_laft_notario d 
              inner join ALERTAOPERACION.acto_laft_notario  a on d.id=a.iddocumentolaft
             inner join sisgen.actojuridico aj on a.idacto=aj.id
              inner join (  
              select t.id,t.descripcion,g.descripcion as grupo from ocpreporte.tipo_alerta t inner join ocpreporte.grupo_alerta g
              on t.idgrupoalerta=g.id
              where  g.id in (18) 
              ) tbl 
               ON INSTR(',' || a.valoresalerta || ',', ',' || TO_CHAR(tbl.id) || ',') > 0
              WHERE a.activo=1 and d.id=".$iddocumentoLaft;

       //     die($sql);

        $all=$this->getListAllRows($sql);
    }
    
    return $all;
}


private function getAlertasContratantes($iddocumentoLaft){

    $all=[];
    if(intval($iddocumentoLaft)>0)
    {
        $sql="
              select distinct tbl.id,tbl.descripcion as alerta
              ,grupo, '1' as idtipopersona
              from ALERTAOPERACION.documento_laft_notario d 
              inner join (  
              select t.id,t.descripcion,g.descripcion as grupo from ocpreporte.tipo_alerta t inner join ocpreporte.grupo_alerta g
              on t.idgrupoalerta=g.id
              where g.id in (17,19,6) 
              ) tbl 
               ON INSTR(',' || d.valoresalertaporcontratante || ',', ',' || TO_CHAR(tbl.id) || ',') > 0
              WHERE d.id=".$iddocumentoLaft;

      //  die($sql);
      $all=$this->getListAllRows($sql);
    }
    
    return $all;
}

public function getValidarAnexosTipoAlerta(){
    $iddocumentoLaft = $this->getValorIntSanit("iddocumento");
    $all=[];
    if(intval($iddocumentoLaft)>0)
    {
        $sql="
              select  idTipoLaft as tipo_laft from ALERTAOPERACION.documento_laft_notario 
       where iddocumentonotarial=".$iddocumentoLaft."
       
        "
              ;

        
      $all=$this->getRow($sql);
    }
    
    return $all;
}

    public function getActos($iddocumento)
       {      
        $db=$this->connect();
        $iddocumento=isset($iddocumento)?$iddocumento:"0";
        $sql="
            SELECT o.id as idoperacion,
            (
              case ac.id 
              when 115 then o.nombrecontrato
              when 117 then o.nombrecontrato
              when 121 then o.nombrecontrato
              else ac.descripcion end
            ) as descripcion

            ,ac.idgrupoactojuridico,
            ac.id as idactojuridico,aln.otra_alerta as otraalerta
            FROM SISGEN.documentonotarial d 
            inner join sisgen.operacion o on d.id=o.iddocumentonotarial
            left join sisgen.actojuridico ac on o.idactojuridico=ac.id
            left join alertaoperacion.documento_laft_notario dln on d.id=dln.iddocumentonotarial
            left join alertaoperacion.acto_laft_notario aln on dln.id=aln.iddocumentolaft and aln.activo=1 and ac.id=aln.idacto
            where d.id=".$iddocumento;

    //  die($sql);
       $all=$this->getListAllRows($sql);
       return $all;
         
    }


}
?>