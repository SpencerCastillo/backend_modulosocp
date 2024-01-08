<?php 

require 'libs/PHPExcel/Classes/PHPExcel/IOFactory.php';
class Models_Colaborador extends DB_Connect {
        
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

  $fechaInicio = $this->getValorStringSanit("fechaInicio");
  $fechaFin = $this->getValorStringSanit("fechaFin");
  $colaborador = $this->getValorStringSanit("colaborador");
  $numerodocumento = $this->getValorStringSanit("numerodocumento");
  
  $idnotaria = $this->getValorStringSanit("idnotaria");
  $idcolegio = $this->getValorStringSanit("idcolegio");
  $idestado = $this->getValorIntSanit("idestado");
  $pageIndex = $this->getValorIntSanit("pageIndex");
  $pageSize = $this->getValorIntSanit("pageSize");

  $sql="
 select cn.id,cn.cargo, c.nombre as colegio,
  n.descripcion as notaria,
  'TRABAJADORES' as clase,ti.abrev as tipodoc,
  cn.numdoc,cn.nombre,
  to_char(cn.fecha_registro,'dd/mm/YYYY') as fecha_registro,
  TO_CHAR(cn.hora_registro, 'HH24:MI:SS') AS hora_registro,
   NVL((
    select ea.descripcion from ocpreporte.comentario_alerta ca 
    inner join ocpreporte.estado_alerta ea on ca.idestadoocp=ea.id 
     where ca.idalerta=cn.id and ca.idtipoalerta=4 ORDER BY ca.id desc FETCH NEXT 1 ROWS ONLY  
  ),'PENDIENTE') as estado_ocp


  from alertaoperacion.colaboradornotaria cn
  inner join sisgen.notaria n on cn.idnotaria=n.id
  inner join sisgen.colegio c on n.idcolegio=c.id
  inner join sisgen.tipodocumentoidentificativo ti on cn.idtipodoc=ti.id
  where cn.id>=0 ";
     if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.=" AND ( trunc(cn.fecha_registro) BETWEEN '".$inicio."' AND '".$fin."' ) ";
        }

      if($numerodocumento!="")
         $sql.=" AND cn.numdoc='".$numerodocumento."'";

      if($colaborador!="")
         $sql.=" AND cn.nombre LIKE '".$colaborador."%'";

      if($idnotaria!="")
         $sql.=" AND cn.idnotaria=".$idnotaria;

      if($idcolegio!="")
           $sql.=" AND c.id =".$idcolegio;

       if($idestado>0)
            {
              $sql.=" AND (";
              if($idestado==1)
              {

                  $sql.=" 
                ( select count(1) from ocpreporte.comentario_alerta ca 
                where ca.idalerta=cn.id and ca.idtipoalerta=4 
                )=0  OR ";
              }
              $sql.="
                ( select ca.idestadoocp from ocpreporte.comentario_alerta ca 
                where ca.idalerta=cn.id and ca.idtipoalerta=4   ORDER BY ca.id desc 
                FETCH NEXT 1 ROWS ONLY
                )=$idestado
              ";
              $sql.=" ) ";
          
            }
      
       
       

  $sql.=" ORDER BY cn.id desc ";
//  die("=>".$sql);
  $all=$this->getListAllRows($sql);
  return $all;
}


public function getColaboradorById()
{
  $id = $this->getValorIntSanit("id");
  $allData=[];
  $all=[];
  $allAlerta=[];
  $alerta="";

  $sql="
     select c.nombre as colegio,
  n.descripcion as notaria,
  'TRABAJADORES' as clase,ti.abrev as tipodoc,
  cn.numdoc,cn.nombre,cn.cargo,
  to_char(cn.fecha_registro,'dd/mm/YYYY') as fecha_registro,
  TO_CHAR(cn.hora_registro, 'HH24:MI:SS') AS hora_registro,
  alerta,
  NVL((
    select ea.descripcion from ocpreporte.comentario_alerta ca 
    inner join ocpreporte.estado_alerta ea on ca.idestadoocp=ea.id 
     where ca.idalerta=cn.id and ca.idtipoalerta=4 ORDER BY ca.id desc FETCH NEXT 1 ROWS ONLY  
  ),'PENDIENTE') as estado_ocp
  from alertaoperacion.colaboradornotaria cn
  inner join sisgen.notaria n on cn.idnotaria=n.id
  inner join sisgen.colegio c on n.idcolegio=c.id
  inner join sisgen.tipodocumentoidentificativo ti on cn.idtipodoc=ti.id
  where cn.id=".$id;

  //die($sql);
if($this->getRow($sql)!=null)
  $all=$this->getRow($sql);

if(isset($all["alerta"]))
  $alerta=$all["alerta"];

if($alerta!=""){
  $sqlAlerta="
     select codigo,descripcion from ocpreporte.tipo_alerta where idgrupoalerta=2
  and codigo in (".$alerta.")";

  $allAlerta=$this->getListAllRows($sqlAlerta);
}

  $allData[0]=$all;
  $allData[1]=$allAlerta;
  return $allData;
}



public function getEstadoAlerta()
{
  $idtipoLaft = $this->getValorStringSanit("idtipoLaft");
  //die("=> ".$idtipoLaft);
  $sql=" select id,descripcion from ocpreporte.estado_alerta
  where activo=1";
  switch ($idtipoLaft) {
    case '2':
        $sql.=" AND oi=1";
      break;
  }




  $all=$this->getListAllRows($sql);
  return $all;
}
public function generarRpt()
{

  $fechaInicio = $this->getValorStringSanit("fechaInicio");
  $fechaFin = $this->getValorStringSanit("fechaFin");
  $colaborador = $this->getValorStringSanit("colaborador");
  $numerodocumento = $this->getValorIntSanit("numdoc");
  $idnotaria = $this->getValorStringSanit("idnotaria");
  $idcolegio = $this->getValorStringSanit("idcolegio");
  $idestado = $this->getValorIntSanit("idestado");

  $sql="
 select cn.id,cn.cargo, c.nombre as colegio,
  n.descripcion as notaria,
  'TRABAJADORES' as clase,ti.abrev as tipodoc,
  cn.numdoc,cn.nombre,
  to_char(cn.fecha_registro,'dd/mm/YYYY') as fecha_registro,
  TO_CHAR(cn.hora_registro, 'HH24:MI:SS') AS hora_registro,
   NVL((
    select ea.descripcion from ocpreporte.comentario_alerta ca 
    inner join ocpreporte.estado_alerta ea on ca.idestadoocp=ea.id 
     where ca.idalerta=cn.id and ca.idtipoalerta=4 ORDER BY ca.id desc FETCH NEXT 1 ROWS ONLY  
  ),'PENDIENTE') as estado_ocp

  from alertaoperacion.colaboradornotaria cn
  inner join sisgen.notaria n on cn.idnotaria=n.id
  inner join sisgen.colegio c on n.idcolegio=c.id
  inner join sisgen.tipodocumentoidentificativo ti on cn.idtipodoc=ti.id
  where cn.id>=0 ";
     if($fechaInicio!="" && $fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($fechaInicio));
            $fin = date("d/m/Y", strtotime($fechaFin));

            $sql.=" AND ( trunc(cn.fecha_registro) BETWEEN '".$inicio."' AND '".$fin."' ) ";
        }

      if($numerodocumento!="")
         $sql.=" AND cn.numdoc='".$numerodocumento."'";

      if($colaborador!="")
         $sql.=" AND cn.nombre LIKE '".$colaborador."%'";

      if($idnotaria!="")
         $sql.=" AND cn.idnotaria=".$idnotaria;

      if($idcolegio!="")
           $sql.=" AND c.id =".$idcolegio;


       if($idestado>0)
            {
              $sql.=" AND (";
              if($idestado==1)
              {

                  $sql.=" 
                ( select count(1) from ocpreporte.comentario_alerta ca 
                where ca.idalerta=cn.id and ca.idtipoalerta=4 
                )=0  OR ";
              }
              $sql.="
                ( select ca.idestadoocp from ocpreporte.comentario_alerta ca 
                where ca.idalerta=cn.id and ca.idtipoalerta=4   ORDER BY ca.id desc 
                FETCH NEXT 1 ROWS ONLY
                )=$idestado
              ";
              $sql.=" ) ";
          
            }
      

      
       
       

  $sql.=" ORDER BY cn.id desc ";
  //die("=>".$sql);
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



$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);



    $objPHPExcel->getActiveSheet()->SetCellValue('A1',"COLEGIO");
    $objPHPExcel->getActiveSheet()->SetCellValue('B1',"NOTARIA");
    $objPHPExcel->getActiveSheet()->SetCellValue('C1',"COLABORADOR");
    $objPHPExcel->getActiveSheet()->SetCellValue('D1',"CARGO");
    $objPHPExcel->getActiveSheet()->SetCellValue('E1',"TIPO DE DOC.");
    $objPHPExcel->getActiveSheet()->SetCellValue('F1',"N° DOCUMENTO");
    $objPHPExcel->getActiveSheet()->SetCellValue('G1',"FECHA REGISTRO");
    $objPHPExcel->getActiveSheet()->SetCellValue('H1',"HORA REGISTRO");
    $objPHPExcel->getActiveSheet()->SetCellValue('I1',"ESTADO OCP");
 

        $i=2;
          foreach ($all as $row) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i,$row["colegio"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i,$row["notaria"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i,$row["nombre"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i,$row["cargo"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i,$row["tipodoc"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i,$row["numdoc"]);
            
            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$i,$row["fecha_registro"]);
            $objPHPExcel->getActiveSheet()->SetCellValue('H'.$i,$row["hora_registro"]);
            

            $objPHPExcel->getActiveSheet()->SetCellValue('I'.$i,$row["estado_ocp"]);

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
?>