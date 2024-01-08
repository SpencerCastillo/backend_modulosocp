<?php 
require_once 'libs/spout/vendor/autoload.php';
        
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;

use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;


class Models_Reports extends DB_Connect {
        
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
    
    /**
     * obtiene un solo registro dado su ID
     * @param int $id identificador unico de registro
     * @return Array array con los registros obtenidos de la base de datos
     */


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

/*LEFT JOIN SISGEN.INTERVINIENTE IX ON  O.ID=IX.IDOPERACION
LEFT JOIN SISGEN.REPRESENTANTE RR ON IX.ID=RR.IDINTERVINIENTE 
LEFT JOIN SISGEN.SUJETO SX ON SX.ID=IX.IDPERSONA OR SX.ID=RR.IDPERSONA*/
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
(SELECT CIRCUIT_ID FROM electnotarial.testimonio_notarial WHERE IDDOCUMENTONOTARIAL=TBL4.IDX
AND  ROWNUM <= 1
)
AS CIRCUIT_ID,

(SELECT ID FROM electnotarial.testimonio_notarial WHERE IDDOCUMENTONOTARIAL=TBL4.IDX
AND  ROWNUM <= 1
)
AS ID_TEST,

'' as EMITIDO


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
       /*
       $stid = oci_parse($db, "
SELECT * FROM (
SELECT ROWNUM as RNUM,tbl.* FROM (
        SELECT  D.ID AS IDX, D.NUMERODEKARDEX,FECHAAUTORIZACION,T.ABREV AS TIPOINSTRUMENTO,
D.NUMERO AS NUMEROINSTRUMENTO,D.SCORING,
N.DESCRIPCION AS NOTARIA,
LISTAGG(A.DESCRIPCION, ',') WITHIN GROUP (ORDER BY A.DESCRIPCION) AS NOMBREACTO ".$sqlSelect."
FROM SISGEN.DOCUMENTONOTARIAL D 
INNER JOIN SISGEN.TIPOINSTRUMENTO T ON D.IDINSTRUMENTO=T.ID
LEFT JOIN SISGEN.OPERACION O ON D.ID=O.IDDOCUMENTONOTARIAL
INNER JOIN OCPREPORTE.NOTARIA N ON D.IDNOTARIA=N.ID
LEFT JOIN SISGEN.ACTOJURIDICO A ON O.IDACTOJURIDICO=A.ID
".$sqlInner."
WHERE D.ID>0  ".$sqlWhere."
 GROUP BY D.ID,NUMERODEKARDEX,FECHAAUTORIZACION,T.ABREV,
D.NUMERO,D.SCORING,N.DESCRIPCION ".$sqlGroup."
ORDER BY IDX,NOTARIA,T.ABREV,D.NUMERO,FECHAAUTORIZACION
)  tbl  ".$sqlPaginator."
) WHERE RNUM>=".$pageInit);

*/       oci_execute($stid);
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
public function cerrarSesion()
{
//    session_start();
//    session_destroy();
    unset($_SESSION["lid"]);
    unset($_SESSION["user"]);
      
    return  array('0' =>'session cerrada');
}

    public function getValidationSession(){
        $data=[];
        if(isset($_SESSION["user"])){
            $data= array(0 =>'1',1=>$_SESSION["user"],'user'=>$_SESSION["user"],'id'=>$_SESSION["lid"],2=>$_SESSION["user"],"rol"=>API::getRol());
        }   else
            $data= array(0 =>'0',1 =>'Invitado','user'=>"Invitado","id"=>0,"rol"=>API::getRol());

        return $data;
    }
    public function getLogin($user,$password)
    { 
        $connect=$this->connect();
        $data=[];
        $user=($user);
        $password=($password);
        
        if($user!="")
            $user=trim($user);
        if($password!="")
            $password=trim($password);

            $usuario=addslashes($user);
            $clave=addslashes($password);
            $sql="SELECT ID,USUARIO,APELLIDOS,NOMBRES,ROL,IDACCESO,IDNOTARIA FROM OCPREPORTE.USUARIO WHERE USUARIO=:ussu AND CLAVE=:pass";
            $stid = oci_parse($connect, $sql);
            oci_bind_by_name($stid, ':ussu', $usuario);
            oci_bind_by_name($stid, ':pass', $clave);
            oci_execute($stid);
            $enSession=false;
            while (($row = oci_fetch_object($stid)) != false) {
                $enSession=true;
                $_SESSION["lid"]=$row->ID;
                $_SESSION["user"]=strtoupper($row->NOMBRES)." ".strtoupper($row->APELLIDOS);
                $_SESSION["lrol"]=$row->ROL;
                $_SESSION["idacceso"]=$row->IDACCESO;
                $_SESSION["idnotaria"]=$row->IDNOTARIA;


            }
 
        if($enSession){
            $xip="";
            //$xip=Utiles::get_client_ip();
            
            $sql="UPDATE  OCPREPORTE.USUARIO SET FECHA_HORA_SESION=CURRENT_TIMESTAMP,IP='".$xip."'
            WHERE ID=".$_SESSION["lid"];
            //CREAR TOKEN DE ACCIONES
              $this->getTokenSeguridad();
            $stid = oci_parse($connect, $sql);
            oci_execute($stid);
            oci_free_statement($stid);
            oci_close($connect);

            $data=array(0 =>'1' ,'error'=>'0','msg'=>'correcto','user'=>$_SESSION["user"],'id'=>$_SESSION["lid"],'rol'=>$_SESSION["lrol"],'idacceso'=>$_SESSION["idacceso"]);
          //  $_SESSION["user"]=$user;
        }else
            $data=array(0 =>'0' ,'error'=>'1','msg'=>'datos incorrectos','user'=>"",'rol'=>0);

           
         return $data;
    }


private function getTokenSeguridad()
{
    include("libs/jwt.php");
    require_once 'conexion/Clvs.php';

    $xuuid=bin2hex(random_bytes(20));
    $time = time();
    $items = array(
             'iat' => $time, // Tiempo que inició el token
             'exp' => $time + (21600), // Tiempo que expirará el token (+1 hora)
             'data' => array( // información del usuario
                      'id' => 1,
                      'name' => 'Jncarlo'
                  ),
             'payload'=>array(
                    'UUID'=>$xuuid
                  )
              );

              $token = JWT::encode($items, KEY_SECRET);
              $_SESSION[NAME_TOKEN]=$token;
              $_SESSION["UUID"]=$xuuid;
              unset($_COOKIE["CK".NAME_TOKEN]);
              setcookie("CK".NAME_TOKEN, $token);
    //          return $token;
}


    public function getRptGenerarsimple($get_data)
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


        $sqlWhere="";
        $sqlInner="";
        $isLimit=true;
        $sqlSelect="";
        $sqlGroup="";
        $sqlLimit="";
        $sqlPaginator="";

        if($bcolegio!="")
            $sqlWhere.=" AND N.IDCOLEGIO=".$bcolegio;
        
        if($bnotaria!="")
            $sqlWhere.=" AND D.IDNOTARIA=".$bnotaria;
        

        if($bacto!="")
            $sqlWhere.=" AND O.IDACTOJURIDICO=".$bacto;
        

        if($btipoInstrumento!="")
          $sqlWhere.=" AND D.IDINSTRUMENTO=".$btipoInstrumento;
          
        if($bnumeroInstrumento>0 )
            $sqlWhere.=" AND D.NUMERO=".$bnumeroInstrumento;
        
        
        if($bnumeroKardex!="")
            $sqlWhere.=" AND D.NUMERODEKARDEX='".$bnumeroKardex."'";


        if($bfechaInicio!="" && $bfechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($bfechaInicio));
            $fin = date("d/m/Y", strtotime($bfechaFin));

            $sqlWhere.=" AND D.FECHAAUTORIZACION BETWEEN '".$inicio."' AND '".$fin."' ";
        }
        
        if($bminPatrimonial!="" || $bmaxPatrimonial!="")
            $isLimit=false;

         if(trim($sqlWhere)!="")
            $isLimit=false;

        if($isLimit==true)
        {
            $sqlPaginator=" WHERE to_char(FECHAAUTORIZACION, 'mm')=2  AND to_char(FECHAAUTORIZACION, 'yyyy')=2021 ";
        }else 
            $sqlPaginator.=" WHERE NUMERODEKARDEX IS NOT NULL "; 
 
       if($bminPatrimonial!="" )
        {
            $sqlPaginator.=" AND MPATRIMONIAL>=".$bminPatrimonial;
        }
        if($bmaxPatrimonial!="" )
        {
            $sqlPaginator.=" AND MPATRIMONIAL<=".$bmaxPatrimonial;
        }

        if($bminPatrimonial!="" || $bmaxPatrimonial!="")
        {
            $sqlSelect.=", SUM(M.CUANTIA) AS MPATRIMONIAL ";
            $sqlInner.=" INNER JOIN SISGEN.MEDIODEPAGO M ON O.ID=M.IDOPERACION "  ;          
            $sqlGroup.=", O.NOMBRECONTRATO ";
        }
        
       
        
       
       
       $db=$this->connect();

//quitar cesados

//    die();
       $stid = oci_parse($db, "
SELECT * FROM (
SELECT ROWNUM as RNUM,tbl.* FROM (
        SELECT  D.ID AS IDX, D.NUMERODEKARDEX,FECHAAUTORIZACION,T.ABREV AS TIPOINSTRUMENTO,
D.NUMERO AS NUMEROINSTRUMENTO,D.SCORING,
N.DESCRIPCION AS NOTARIA,C.NOMBRE AS COLEGIO,
LISTAGG(A.DESCRIPCION, ',' ON OVERFLOW TRUNCATE ) WITHIN GROUP (ORDER BY A.DESCRIPCION) AS NOMBREACTO ".$sqlSelect."
FROM SISGEN.DOCUMENTONOTARIAL D 
INNER JOIN SISGEN.TIPOINSTRUMENTO T ON D.IDINSTRUMENTO=T.ID
LEFT JOIN SISGEN.OPERACION O ON D.ID=O.IDDOCUMENTONOTARIAL
LEFT JOIN SISGEN.ACTOJURIDICO A ON O.IDACTOJURIDICO=A.ID
INNER JOIN SISGEN.NOTARIA N ON D.IDNOTARIA=N.ID
INNER JOIN SISGEN.COLEGIO C ON N.IDCOLEGIO=C.ID
inner join ocpreporte.notariaoficio no on n.id=no.idnotaria

".$sqlInner."
WHERE D.ID>0 and no.activo=1  ".$sqlWhere."
 GROUP BY D.ID,NUMERODEKARDEX,FECHAAUTORIZACION,T.ABREV,
D.NUMERO,D.SCORING,C.NOMBRE,N.DESCRIPCION ".$sqlGroup."
ORDER BY NOTARIA,T.ABREV,FECHAAUTORIZACION,D.NUMERO
)  tbl  ".$sqlPaginator."
) ");

// D.ID,NUMERODEKARDEX,FECHAAUTORIZACION,T.ABREV,D.NUMERO,D.SCORING,N.DESCRIPCION
/*
GROUP BY D.ID,NUMERODEKARDEX,FECHAAUTORIZACION,T.ABREV,D.NUMERO,D.SCORING,N.DESCRIPCION
*/

        $nameRpt="rpt/report".uniqid().".xlsx";
        //$nameRpt="test.xlsx";
        $writer = WriterEntityFactory::createXLSXWriter();

        $writer->openToFile($nameRpt); // write data to a file or to a PHP stream

        /** Create a style with the StyleBuilder */
        $style = (new StyleBuilder())
             ->setFontBold()
//           ->setFontSize(15)
//           ->setFontColor(Color::BLUE)
 //          ->setShouldWrapText()
 //          ->setCellAlignment(CellAlignment::RIGHT)
 //          ->setBackgroundColor(Color::YELLOW)
           ->build();

    //    $row = WriterEntityFactory::createRowFromArray(['Carl', 'is', 'great'], $style);

        $cells_titulo = [
            "COLEGIO",
            "NOTARIA",
            "N° KARDEX",
            "FECHA ESCRITURA",
            "TIPO INSTR.",
            "N° INSTRUMENTO",
            "NOMBRE ACTO",
            "SCORING"
        ];

 //       $singleRow = WriterEntityFactory::createRow($cells_titulo);
        $singleRow = WriterEntityFactory::createRowFromArray($cells_titulo,$style);
        $writer->addRow($singleRow);
        oci_execute($stid);
        $data=[];
        $i=2;
        while (($row = oci_fetch_assoc($stid)) != false) {
            $values = [$row["COLEGIO"],$row["NOTARIA"],$row["NUMERODEKARDEX"],$row["FECHAAUTORIZACION"],$row["TIPOINSTRUMENTO"],$row["NUMEROINSTRUMENTO"],$row["NOMBREACTO"],$row["SCORING"]];
            $rowFromValues = WriterEntityFactory::createRowFromArray($values);
            $writer->addRow($rowFromValues);
        }

        $writer->close();
        oci_free_statement($stid);
        oci_close($db);

        return $nameRpt;

    }




public function getColegios($qBuscar)
{
    $db=$this->connect();
    $sql="SELECT ID,NOMBRE FROM SISGEN.COLEGIO ";
    if($qBuscar!="")
    {
        $sql.=" WHERE UPPER(NOMBRE) LIKE '%".strtoupper($qBuscar)."%'";
    }
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

public function getNotarias($qBuscar,$idColegio)
{
    $sqlBuscar="";
    if($qBuscar!="")
    {
        $sqlBuscar.=" WHERE UPPER(N.DESCRIPCION) LIKE '%".strtoupper($qBuscar)."%'";
    }


    if($idColegio!="")
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

public function getActos($qBuscar,$tipo)
{
    $sqlBuscar="";
    if($qBuscar!="")
    {
        $sqlBuscar.=" WHERE UPPER(A.DESCRIPCION) LIKE '%".strtoupper($qBuscar)."%'";
    }

    if($tipo!="")
    {
        if($sqlBuscar!="")
            $sqlBuscar.=" AND ";
    }

    $db=$this->connect();
    $sql="SELECT * FROM (
    SELECT A.ID,A.DESCRIPCION AS NOMBRE from SISGEN.ACTOJURIDICO A
    ".$sqlBuscar."
    ORDER BY A.DESCRIPCION
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

public function getBanderas($qBuscar)
{
        $sqlBuscar="";
    if($qBuscar!="")
    {
        $sqlBuscar.=" WHERE UPPER(T.BANDERA) LIKE '%".strtoupper($qBuscar)."%'";
    }

    $db=$this->connect();
    $sql="SELECT * FROM (
SELECT T.ID,T.BANDERA AS NOMBRE from SISGEN.TIPOLISTA T
".$sqlBuscar."
ORDER BY T.BANDERA
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

public function getInstrumentos($qBuscar)
{
     $db=$this->connect();
    $sql="SELECT ID,DESCRIPCION AS NOMBRE FROM SISGEN.TIPOINSTRUMENTO
    WHERE ID<>6 AND ID<>7 AND ID<>8

";
    if($qBuscar!="")
    {
        $sql.=" AND UPPER(DESCRIPCION) LIKE '%".strtoupper($qBuscar)."%'";
    }
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

public function getListIps()
{
            $db=$this->connect();
           $result = $db->query('SELECT ip FROM acceso_ip where estado=1');
           $all = $result->fetch_all(MYSQLI_ASSOC);          
            $result->close();
            return $all;
}
  

public function cellColor($cells,$color,$objPHPExcel){
    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => $color
        )
    ));
}

}
?>