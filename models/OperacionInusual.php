<?php 

if(!class_exists('DB_Connect') ) 
    include "conexion/DB_Connect.php";


class Models_OperacionInusual extends DB_Connect {
        
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



public function getListOperacionAll()
{ 
  $iddocumento = $this->getValorIntSanit("iddocumento");
  $idtipoLaft = $this->getValorIntSanit("idtipoLaft");
  $idColegio = $this->getValorIntSanit("idColegio");
  $idNotaria = $this->getValorIntSanit("idNotaria");
  
  $idEstadoOcp = $this->getValorStringSanit("idEstadoOcp");
  $idTipoDoc = $this->getValorIntSanit("idTipoDoc");
  $numdocumento = $this->getValorIntSanit("numdocumento");
  $nombrecontratante = $this->getValorStringSanit("nombrecontratante");

$bfechaInicio = $this->getValorStringSanit("fechaInicio");
$bfechaFin = $this->getValorStringSanit("fechaFin");

  $pageIndex = $this->getValorIntSanit("pageIndex");
  $pageSize = $this->getValorIntSanit("pageSize");

  $sql="
 select cc.id as idcontratante,
 l.id as iddocumentolaft,
c.nombre as colegio,
n.descripcion as notaria,
ti.abrev as tipodoc,
cc.numerodocumento,
ac.descripcion as acto,
trim(cc.primerapellido_razonsocial||' '||cc.segundoapellido||' '||cc.nombre)
as contratante,

cc.rol,
(
select descripcion from ocpreporte.tipo_alerta 
                    where INSTR(',' || l.valoresalertaporcontratante || ',', ',' || id || ',') > 0
FETCH FIRST 1 ROWS ONLY) as alertacontratante,
(
select descripcion from ocpreporte.tipo_alerta 
 where INSTR(',' || aln.valoresalerta || ',', ',' || id || ',') > 0
FETCH FIRST 1 ROWS ONLY) as alertaacto,
to_char(l.fecharegistro,'dd/mm/YYYY')as fecharegistro,
to_char(l.fecharegistro,'hh24:mi:ss') as horaregistro,
d.numerodekardex,
to_char(d.fechaautorizacion,'dd/mm/YYYY')as fechaautorizacion,
t.abrev as tipo_instrumento,
d.numero as numerodeinstrumento,
(case   aln.idmoneda   
when 1 then (aln.cuantia*0.26)   
when 2 then aln.cuantia   
when 3 then (aln.cuantia*1.06)   else 0 end
) AS PATRIMONIAL_DOLARES,
(case   cc.idmoneda   
when 1 then (cc.monto*0.26)   
when 2 then cc.monto   
when 3 then (cc.monto*1.06)   else 0 end
) AS cuantia_contratante_DOLARES,
l.valoresalertaporcontratante,
(0) as scoring_cliente,
LENGTH(l.valoresalertaporcontratante) -
LENGTH(REPLACE(l.valoresalertaporcontratante, ',', '')) + 1 AS cantidad_alertas_contratante,
'1' as cantidad_kardex,
NVL((
    select ea.descripcion from ocpreporte.comentario_alerta ca 
    inner join ocpreporte.estado_alerta ea on ca.idestadoocp=ea.id 
     where ca.idalerta=cc.id and ca.idtipoalerta=$idtipoLaft ORDER BY ca.id desc FETCH NEXT 1 ROWS ONLY  
),'PENDIENTE') as estado_ocp,
l.iddocumentonotarial

from ALERTAOPERACION.documento_laft_notario l 
inner join sisgen.documentonotarial d on l.iddocumentonotarial=d.id
left join ALERTAOPERACION.acto_laft_notario aln on l.id=aln.iddocumentolaft
left join sisgen.actojuridico ac on aln.idacto=ac.id
inner join ALERTAOPERACION.contratante cc on d.id=cc.iddocumentonotarial
inner join sisgen.tipodocumentoidentificativo ti on cc.idtipodocumento=ti.id
inner join sisgen.notaria n on d.idnotaria=n.id
inner join sisgen.colegio c on n.idcolegio=c.id
INNER JOIN SISGEN.TIPOINSTRUMENTO t ON d.IDINSTRUMENTO=T.ID
where enviado=1 and cc.activo=1  AND l.idtipoLaft=2 
  ";
  

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
    if($numdocumento!="")
         $sql.=" AND cc.numerodocumento='".$numdocumento."'";

    if(intval($idTipoDoc)>0)
         $sql.=" AND cc.idtipodocumento='".$idTipoDoc."'";
     

    if($nombrecontratante!="")
         $sql.=" AND trim(cc.primerapellido_razonsocial||' '||cc.segundoapellido||' '||cc.nombre) LIKE '".$nombrecontratante."%'";

    if($idEstadoOcp!="")
            {
              $sql.=" AND (";
              $sql.="
                ( select ca.idestadoocp from ocpreporte.comentario_alerta ca 
                where ca.idalerta=cc.id and ca.idtipoalerta=2   ORDER BY ca.id desc 
                FETCH NEXT 1 ROWS ONLY
                ) in ($idEstadoOcp)
              ";
              $sql.=" ) ";
          
            }
$sql.=" order by l.id desc
  ";

        $pageInit=0;
          if($pageIndex!="" && $pageIndex>0)
            $pageInit=((int)($pageIndex-1)*(int)$pageSize);
        
        if($pageSize!="")
            $sql.=" OFFSET ".($pageInit>0?$pageInit:"0")." ROWS FETCH NEXT ".$pageSize." ROWS ONLY ";
        else
            $sql.=" FECTH NEXT 25 ROWS ONLY";

 //die("=>".$sql);
  $all=$this->getListAllRows($sql);
  return $all;
}



public function getCountOperacionAll()
{ 
  $iddocumento = $this->getValorIntSanit("iddocumento");
  $idtipoLaft = $this->getValorIntSanit("idtipoLaft");
  $idColegio = $this->getValorIntSanit("idColegio");
  $idNotaria = $this->getValorIntSanit("idNotaria");
  
  $idEstadoOcp = $this->getValorStringSanit("idEstadoOcp");
  $idTipoDoc = $this->getValorIntSanit("idTipoDoc");
  $numdocumento = $this->getValorIntSanit("numdocumento");
  $nombrecontratante = $this->getValorStringSanit("nombrecontratante");

$bfechaInicio = $this->getValorStringSanit("fechaInicio");
$bfechaFin = $this->getValorStringSanit("fechaFin");

  $pageIndex = $this->getValorIntSanit("pageIndex");
  $pageSize = $this->getValorIntSanit("pageSize");

  $sql="
 select count(1) as cantidad

from ALERTAOPERACION.documento_laft_notario l 
inner join sisgen.documentonotarial d on l.iddocumentonotarial=d.id
left join ALERTAOPERACION.acto_laft_notario aln on l.id=aln.iddocumentolaft
left join sisgen.actojuridico ac on aln.idacto=ac.id
inner join ALERTAOPERACION.contratante cc on d.id=cc.iddocumentonotarial
inner join sisgen.tipodocumentoidentificativo ti on cc.idtipodocumento=ti.id
inner join sisgen.notaria n on d.idnotaria=n.id
inner join sisgen.colegio c on n.idcolegio=c.id
INNER JOIN SISGEN.TIPOINSTRUMENTO t ON d.IDINSTRUMENTO=T.ID
where enviado=1 and cc.activo=1  AND l.idtipoLaft=2 
  ";
  

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
    if($numdocumento!="")
         $sql.=" AND cc.numerodocumento='".$numdocumento."'";

    if(intval($idTipoDoc)>0)
         $sql.=" AND cc.idtipodocumento='".$idTipoDoc."'";
     

    if($nombrecontratante!="")
         $sql.=" AND trim(cc.primerapellido_razonsocial||' '||cc.segundoapellido||' '||cc.nombre) LIKE '".$nombrecontratante."%'";

    if($idEstadoOcp!="")
            {
              $sql.=" AND (";
              $sql.="
                ( select ca.idestadoocp from ocpreporte.comentario_alerta ca 
                where ca.idalerta=cc.id and ca.idtipoalerta=2   ORDER BY ca.id desc 
                FETCH NEXT 1 ROWS ONLY
                ) in ($idEstadoOcp)
              ";
              $sql.=" ) ";
          
            }

    //      die($sql);
  
  $total=$this->getAllTotal($sql);
  return $total;
}


public function getReporte()
{

 $iddocumento = $this->getValorIntSanit("iddocumento");
  $idtipoLaft = $this->getValorIntSanit("idtipoLaft");
  $idColegio = $this->getValorIntSanit("idColegio");
  $idNotaria = $this->getValorIntSanit("idNotaria");
  
  $idEstadoOcp = $this->getValorIntSanit("idEstadoOcp");
  $idTipoDoc = $this->getValorIntSanit("idTipoDoc");
  $numdocumento = $this->getValorIntSanit("numdocumento");
  $nombrecontratante = $this->getValorStringSanit("nombrecontratante");

$bfechaInicio = $this->getValorStringSanit("fechaInicio");
$bfechaFin = $this->getValorStringSanit("fechaFin");

  $sql="
 select cc.id as idcontratante,
 l.id as iddocumentolaft,
c.nombre as colegio,
n.descripcion as notaria,
ti.abrev as tipodoc,
cc.numerodocumento,
ac.descripcion as acto,
trim(cc.primerapellido_razonsocial||' '||cc.segundoapellido||' '||cc.nombre)
as contratante,

cc.rol,
(
select descripcion from ocpreporte.tipo_alerta 
                    where INSTR(',' || l.valoresalertaporcontratante || ',', ',' || id || ',') > 0
FETCH FIRST 1 ROWS ONLY) as alertacontratante,
(
select descripcion from ocpreporte.tipo_alerta 
 where INSTR(',' || aln.valoresalerta || ',', ',' || id || ',') > 0
FETCH FIRST 1 ROWS ONLY) as alertaacto,
to_char(l.fecharegistro,'dd/mm/YYYY')as fecharegistro,
to_char(l.fecharegistro,'hh24:mi:ss') as horaregistro,
d.numerodekardex,
to_char(d.fechaautorizacion,'dd/mm/YYYY')as fechaautorizacion,
t.abrev as tipo_instrumento,
d.numero as numerodeinstrumento,
(case   aln.idmoneda   
when 1 then (aln.cuantia*0.26)   
when 2 then aln.cuantia   
when 3 then (aln.cuantia*1.06)   else 0 end
) AS PATRIMONIAL_DOLARES,
(case   cc.idmoneda   
when 1 then (cc.monto*0.26)   
when 2 then cc.monto   
when 3 then (cc.monto*1.06)   else 0 end
) AS cuantia_contratante_DOLARES,
l.valoresalertaporcontratante,
(
select nivel_calificacion from OCPREPORTE.rpt_scoring_factor_cliente
where numdoc=cc.numerodocumento  FETCH NEXT 1 ROWS ONLY) as scoring_cliente,
LENGTH(l.valoresalertaporcontratante) -
LENGTH(REPLACE(l.valoresalertaporcontratante, ',', '')) + 1 AS cantidad_alertas_contratante,
'1' as cantidad_kardex,
NVL((
    select ea.descripcion from ocpreporte.comentario_alerta ca 
    inner join ocpreporte.estado_alerta ea on ca.idestadoocp=ea.id 
     where ca.idalerta=cc.id and ca.idtipoalerta=1 ORDER BY ca.id desc FETCH NEXT 1 ROWS ONLY  
),'PENDIENTE') as estado_ocp

from ALERTAOPERACION.documento_laft_notario l 
inner join sisgen.documentonotarial d on l.iddocumentonotarial=d.id
left join ALERTAOPERACION.acto_laft_notario aln on l.id=aln.iddocumentolaft
left join sisgen.actojuridico ac on aln.idacto=ac.id
inner join ALERTAOPERACION.contratante cc on d.id=cc.iddocumentonotarial
inner join sisgen.tipodocumentoidentificativo ti on cc.idtipodocumento=ti.id
inner join sisgen.notaria n on d.idnotaria=n.id
inner join sisgen.colegio c on n.idcolegio=c.id
INNER JOIN SISGEN.TIPOINSTRUMENTO t ON d.IDINSTRUMENTO=T.ID
where enviado=1 and cc.activo=1 and cc.vinculado=1 AND l.idtipoLaft=1 
  ";
  

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
    if($numdocumento!="")
         $sql.=" AND cc.numerodocumento='".$numdocumento."'";

    if(intval($idTipoDoc)>0)
         $sql.=" AND cc.idtipodocumento='".$idTipoDoc."'";
     

    if($nombrecontratante!="")
         $sql.=" AND trim(cc.primerapellido_razonsocial||' '||cc.segundoapellido||' '||cc.nombre) LIKE '".$nombrecontratante."%'";

    if($idEstadoOcp>0)
            {
              $sql.=" AND (";
              if($idEstadoOcp==1)
              {

                  $sql.=" 
                ( select count(1) from ocpreporte.comentario_alerta ca 
                where ca.idalerta=cc.id and ca.idtipoalerta=1 
                )=0  OR ";
              }
              $sql.="
                ( select ca.idestadoocp from ocpreporte.comentario_alerta ca 
                where ca.idalerta=cc.id and ca.idtipoalerta=1   ORDER BY ca.id desc 
                FETCH NEXT 1 ROWS ONLY
                )=$idEstadoOcp
              ";
              $sql.=" ) ";
          
            }
$sql.=" order by l.id desc
  ";

//  die("=>".$sql);
  $all=$this->getListAllRows($sql);


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
           $objPHPExcel->getActiveSheet()->getStyle("I1")->getFont()->setBold(true);
           $objPHPExcel->getActiveSheet()->getStyle("J1")->getFont()->setBold(true);
           $objPHPExcel->getActiveSheet()->getStyle("K1")->getFont()->setBold(true);
           $objPHPExcel->getActiveSheet()->getStyle("L1")->getFont()->setBold(true);
           $objPHPExcel->getActiveSheet()->getStyle("M1")->getFont()->setBold(true);
           $objPHPExcel->getActiveSheet()->getStyle("N1")->getFont()->setBold(true);
           $objPHPExcel->getActiveSheet()->getStyle("O1")->getFont()->setBold(true);
           $objPHPExcel->getActiveSheet()->getStyle("P1")->getFont()->setBold(true);
           $objPHPExcel->getActiveSheet()->getStyle("Q1")->getFont()->setBold(true);
           $objPHPExcel->getActiveSheet()->getStyle("R1")->getFont()->setBold(true);
           $objPHPExcel->getActiveSheet()->getStyle("S1")->getFont()->setBold(true);
           


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);

$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);



    $objPHPExcel->getActiveSheet()->SetCellValue('A1',"COLEGIO");
    $objPHPExcel->getActiveSheet()->SetCellValue('B1',"NOTARIA");
    $objPHPExcel->getActiveSheet()->SetCellValue('C1',"TIPO DE DOC.");
    $objPHPExcel->getActiveSheet()->SetCellValue('D1',"N° DOCUMENTO");
    $objPHPExcel->getActiveSheet()->SetCellValue('E1',"CONTRATANTE");
    $objPHPExcel->getActiveSheet()->SetCellValue('F1',"ROL");
    $objPHPExcel->getActiveSheet()->SetCellValue('G1',"DESCRIPCIÓN ALERTA");
    $objPHPExcel->getActiveSheet()->SetCellValue('H1',"FECHA GENERACIÓN");
    $objPHPExcel->getActiveSheet()->SetCellValue('I1',"ACTO");
    $objPHPExcel->getActiveSheet()->SetCellValue('J1',"KARDEX");
    
    $objPHPExcel->getActiveSheet()->SetCellValue('K1',"FECHA INSTRUMENTO");
    $objPHPExcel->getActiveSheet()->SetCellValue('L1',"N° INSTR.");
    $objPHPExcel->getActiveSheet()->SetCellValue('M1',"PATRIMONIAL");
    $objPHPExcel->getActiveSheet()->SetCellValue('N1',"MONTO CONTRATANTE");
    
    $objPHPExcel->getActiveSheet()->SetCellValue('O1',"SCORING CLIENTE");
    $objPHPExcel->getActiveSheet()->SetCellValue('P1',"CANT. ALERTA");
    $objPHPExcel->getActiveSheet()->SetCellValue('Q1',"CANT. KARDEX");
    $objPHPExcel->getActiveSheet()->SetCellValue('R1',"ESTADO ALERTA");








        $i=2;
          foreach ($all as $row) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i,$row["colegio"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i,$row["notaria"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i,$row["tipodoc"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i,$row["numerodocumento"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i,$row["contratante"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i,$row["rol"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$i,$row["alertacontratante"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('H'.$i,$row["fecharegistro"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('I'.$i,$row["acto"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('J'.$i,$row["numerodekardex"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('K'.$i,$row["fechaautorizacion"]);

            $objPHPExcel->getActiveSheet()->SetCellValue('L'.$i,$row["numerodeinstrumento"]);

            $objPHPExcel->getActiveSheet()->SetCellValue('M'.$i,$row["patrimonial_dolares"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('N'.$i,$row["cuantia_contratante_dolares"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('O'.$i,$row["scoring_cliente"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('P'.$i,$row["cantidad_alertas_contratante"]);
            
             $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$i,1);
             $objPHPExcel->getActiveSheet()->SetCellValue('R'.$i,$row["estado_ocp"]);

            $i++;
        }
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $nameRpt="report".uniqid();
        $objWriter->save('rpt/'.$nameRpt.'.xlsx');

        $id=uniqid();

        return "rpt/".$nameRpt.".xlsx";


  return $all;
}


}
