<?php 


require_once 'libs/spout/vendor/autoload.php';
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;

use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;

class Models_Faltantes extends DB_Connect {
        
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
        

        }



    public function getList($get_data)
       {      

       	$info=json_decode($get_data);

       $db=$this->connect();
       
       $sqlWhere="";
       /*
       if($info->fechaInicio!="" && $info->fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($info->fechaInicio));
            $fin = date("d/m/Y", strtotime($info->fechaFin));

            $sqlWhere.=" AND D.FECHAAUTORIZACION BETWEEN '".$inicio."' AND '".$fin."' ";
        }*/


        $listAnios="";
        if(isset($info->listAnios) && $info->listAnios)
        {
            $listAnios=$info->listAnios;
        }

        $isTodos="0";
        $allAnio="";
        if($listAnios!="" && sizeof($listAnios)>0){
            foreach ($listAnios as  $value) {
                if($value=="Todos")
                    $isTodos="1";
                else
                    $allAnio.="'".$value."',";

            }
            $allAnio=substr($allAnio,0,-1);
            if($isTodos=="0")
                $sqlWhere.=" and TO_CHAR(D.FECHAAUTORIZACION,'yyyy') iN (".$allAnio.") ";
        }
        

     if($info->colegio!="")
        {
            $sqlWhere.=" AND N.IDCOLEGIO=".$info->colegio;
            
        }
        if($info->notaria!="")
            $sqlWhere.=" AND D.IDNOTARIA=".$info->notaria;
        
        if($info->tipoInstrumento!="")
        {
            $xidTipoInstr="0";
            switch ($info->tipoInstrumento) {
                case '1': case '6':
                   //$xidTipoInstr="1";
                     $sqlWhere.=" AND (D.IDINSTRUMENTO=1 OR D.IDINSTRUMENTO=6 )";
                    break;
                case '4':  case '7':
                  // $xidTipoInstr="4";
                    $sqlWhere.=" AND (D.IDINSTRUMENTO=4 OR D.IDINSTRUMENTO=7 )";
                    break;
                case '3': case '8':
//                   $xidTipoInstr="3";
                   $sqlWhere.=" AND (D.IDINSTRUMENTO=3 OR D.IDINSTRUMENTO=8 )";
                    break;
                default:
                    $sqlWhere.=" AND (D.IDINSTRUMENTO=".$info->tipoInstrumento.")";
                    break;
            }
           
        }  

          
       $querys=" D.ID>0 ".$sqlWhere." ORDER BY N.CODIGO,TO_CHAR(FECHAAUTORIZACION,'yyyy'),TIPO_INSTRUMENTO,D.NUMERO";
       //AND D.IDNOTARIA=129 ORDER BY N.CODIGO,TO_CHAR(FECHAAUTORIZACION,'yyyy'),D.IDINSTRUMENTO,D.NUMERO
//       die($querys);
        $curs = oci_new_cursor($db);
       $stid = oci_parse($db, "BEGIN OCPREPORTE.P_RPT_REPETIDOS(:querys,:cursbv); END;");
        oci_bind_by_name($stid, ":cursbv", $curs, -1, OCI_B_CURSOR);
        oci_bind_by_name($stid, ":querys", $querys);
        oci_execute($stid);
        oci_execute($curs);
        
        
        
          // Ejecutar el REF CURSOR como un ide de sentencia normal

  
        $nameRpt="rpt/report".uniqid().".xlsx";
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($nameRpt); // write data to a file or to a PHP stream

        $style = (new StyleBuilder())
             ->setFontBold()
           ->build();
        
        $cells_titulo = [
            "COLEGIO",
            "NOTARIA",
            "TIPO INSTR.",
            "N° KARDEX REFERENCIAL",
            "N° INSTR. ANT",
            "INSTR. FALTANTES",
            "N° INSTR. SIG.",
            "CANT. FALTANTES",
            "FECHA INSTR."
        ];

 //       $singleRow = WriterEntityFactory::createRow($cells_titulo);
        $singleRow = WriterEntityFactory::createRowFromArray($cells_titulo,$style);
        $writer->addRow($singleRow);
  
        $data=[];
        $i=2;
        $allNotariosCantidadFaltantes=array();
       while (($row = oci_fetch_array($curs, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {

            $cantFaltantes=intval($row["NUM_INTRUMENTO"])-intval($row["NUM_INSTRUMENTO_ANTERIOR"]);  
            $cantFaltantes-=1;
              if($row["NUM_INSTRUMENTO_ANTERIOR"]=="0")
                $row["NUM_INSTRUMENTO_ANTERIOR"]="";

              
             $values = [$row["COLEGIO"],
             $row["NOTARIA"],
             $row["TIPO_INSTRUMENTO"],
             $row["NUM_KARDEX"],
             $row["NUM_INSTRUMENTO_ANTERIOR"],
             $row["FALTANTES"],
             $row["NUM_INTRUMENTO"],
             $cantFaltantes,
             $row["FECHA_INSTRUMENTO"]
               
         ];
            $rowFromValues = WriterEntityFactory::createRowFromArray($values);
            $writer->addRow($rowFromValues);
            $i++;

              /*
            ALMACENANDO FALTANTES
        */
            /*
            if(!isset($allNotariosCantidadFaltantes[$row["CODIGO_NOTARIA"]][$row["TIPO_INSTRUMENTO"]][$row["ANIO"]]))
            {
                $allNotariosCantidadFaltantes[$row["CODIGO_NOTARIA"]][$row["TIPO_INSTRUMENTO"]][$row["ANIO"]]=0;
            }

            $allNotariosCantidadFaltantes[$row["CODIGO_NOTARIA"]][$row["TIPO_INSTRUMENTO"]][$row["ANIO"]]= $allNotariosCantidadFaltantes[$row["CODIGO_NOTARIA"]][$row["TIPO_INSTRUMENTO"]][$row["ANIO"]]+$cantFaltantes;*/
         
         /*
            FIN ALMACENANDO FALTANTES
         */  

        }

          /*
            ACTUALIZANDO EN BD FALTANTES
        */
         
         /* 
        $sqlFaltantes="delete from  OCPREPORTE.CANTIDAD_INSTRUMENTOS_FALTANTES where TIPO_BD='SG' ";
        $stid = oci_parse($db,$sqlFaltantes);
        oci_execute($stid);
        foreach ($allNotariosCantidadFaltantes as $key => $valueFaltantes) {
            $codigo=$key;
            foreach ($valueFaltantes as $keytipoInstr => $valueAnio) {
                foreach ($valueAnio as $keyanio => $cantidad) {
                $tipoInstrumento="";
                switch ($keytipoInstr) {
                    case 'EP':
                        $tipoInstrumento="1";
                        break;
                    case 'ANC':
                        $tipoInstrumento="2";
                        break;
                    case 'TV':
                        $tipoInstrumento="3";
                        break;
                    case 'GM':
                        $tipoInstrumento="4";
                        break;                    
                    default:
                        $tipoInstrumento="0";
                        break;
                }

                     $sqlFaltantes="INSERT INTO OCPREPORTE.CANTIDAD_INSTRUMENTOS_FALTANTES (CODIGO,IDTIPOINSTRUMENTO,CANTIDAD,TIPO_BD,ANIO)";
                    $sqlFaltantes.=" VALUES ";
                    $sqlFaltantes.=" (".$codigo.",".$tipoInstrumento.",".$cantidad.",'SG','".$keyanio."') ";
                    $stid=oci_parse($db,$sqlFaltantes);
                    oci_execute($stid);
                }
           }
                  
                
        }*/
        /*
            ACTUALIZANDO EN BD FALTANTES
        */

       // var_dump($allNotariosCantidadFaltantes);

        oci_free_statement($stid);
        oci_free_statement($curs);
        oci_close($db);

        $writer->close();
        return $nameRpt;


}


public function getRptGenerarExpRepetido1($get_data)
       {        


        $info=json_decode($get_data);
        
        $sqlWhere="";
        $sqlInner="";
        $isLimit=true;
        $sqlSelect="";
        $sqlGroup="";
        $sqlLimit="";


       
        
        if($info->fechaInicio!="" && $info->fechaFin!="")
        {
            $inicio = date("d/m/Y", strtotime($info->fechaInicio));
            $fin = date("d/m/Y", strtotime($info->fechaFin));

            $sqlWhere.=" AND D.FECHAAUTORIZACION BETWEEN '".$inicio."' AND '".$fin."' ";
        }

        $sqlPaginator="";
     
        
           

         if(trim($sqlWhere)!="")
            $isLimit=false;

        
            $sqlPaginator.=" WHERE NUMERODEKARDEX IS NOT NULL "; 
 
     	
     	  if($info->colegio!="")
        {
            $sqlPaginator.=" AND N.IDCOLEGIO=".$info->colegio;
        
        }
        if($info->notaria!="")
        {
            $sqlPaginator.=" AND D.IDNOTARIA=".$info->notaria;
        }  


        if($info->tipoInstrumento!="")
        {
            $sqlPaginator.=" AND D.IDINSTRUMENTO=".$info->tipoInstrumento;
        } 
        
       
       $db=$this->connect();
       
       $stid = oci_parse($db, "SELECT * FROM (
SELECT * FROM (
SELECT ROWNUM AS RNUM, CANTIDAD,C.NOMBRE AS COLEGIO,N.DESCRIPCION AS NOTARIA,NUMEROINSTRUMENTO,NUMERODEKARDEX,FECHAAUTORIZACION,
T.ABREV AS TIPOINSTRUMENTO
FROM (
SELECT COUNT(*) as cantidad, D.IDNOTARIA,
D.NUMERO AS NUMEROINSTRUMENTO,D.IDINSTRUMENTO,
LISTAGG(D.NUMERODEKARDEX, ', ') WITHIN GROUP (ORDER BY D.NUMERODEKARDEX) AS NUMERODEKARDEX,
LISTAGG(D.FECHAAUTORIZACION, ', ') WITHIN GROUP (ORDER BY D.FECHAAUTORIZACION) AS FECHAAUTORIZACION
FROM SISGEN.DOCUMENTONOTARIAL D  
WHERE D.ID>0 AND D.NUMERO IS NOT NULL  ".$sqlWhere."   
GROUP BY (D.NUMERO,D.IDNOTARIA,D.IDINSTRUMENTO)

HAVING COUNT(*)>1 
) D
INNER JOIN OCPREPORTE.NOTARIA N ON D.IDNOTARIA=N.ID 
INNER JOIN SISGEN.COLEGIO C ON N.IDCOLEGIO=C.ID
INNER JOIN SISGEN.TIPOINSTRUMENTO T ON D.IDINSTRUMENTO=T.ID
".$sqlPaginator."
)

ORDER BY NOTARIA,TIPOINSTRUMENTO,NUMEROINSTRUMENTO,FECHAAUTORIZACION

) TEMP ");


        $nameRpt="rpt/report".uniqid().".xlsx";
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($nameRpt); // write data to a file or to a PHP stream

        $style = (new StyleBuilder())
             ->setFontBold()
           ->build();
        
        $cells_titulo = [
            "COLEGIO",
            "NOTARIA",
            "TIPO INSTR.",
            "N° KARDEX",
            "N° INSTR.",
            "FECHA INSTR.",
            "CANTIDAD REPETIDOS"
        ];

 //       $singleRow = WriterEntityFactory::createRow($cells_titulo);
        $singleRow = WriterEntityFactory::createRowFromArray($cells_titulo,$style);
        $writer->addRow($singleRow);
  
        oci_execute($stid);
        $data=[];
        $i=2;
        while (($row = oci_fetch_assoc($stid)) != false) {

             $values = [$row["COLEGIO"],
             $row["NOTARIA"],
             $row["TIPOINSTRUMENTO"],
             $row["NUMERODEKARDEX"],
             $row["NUMEROINSTRUMENTO"],
             $row["FECHAAUTORIZACION"],
             $row["CANTIDAD"]];
            $rowFromValues = WriterEntityFactory::createRowFromArray($values);
            $writer->addRow($rowFromValues);
            $i++;
        }

        oci_free_statement($stid);
        oci_close($db);

        $writer->close();
        return $nameRpt;

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