<?php
 
class DB_Connect {
 
 private static $itemsListTipoInstr=array(
            1=>"EP",
            2=>"ANC",
            3=>"TV",
            4=>"GM",
            5=>"T",
            6=>"EP",
            7=>"GM",
            8=>"TBM",
            "E"=>"EP",
            "ESCRITURA PUBLICA"=>"EP",
            "ESCRITURAS PUBLICAS"=>"EP",
            "ESCRITURAS"=>"EP",
            "ESCRITURA PÚBLICA"=>"EP",
            "ESCRITURAS PUBLICA"=>"EP",
            "ESCRITURA PUBLICAS"=>"EP",
            "ASUNTOS NO CONTENCIOSOS"=>"ANC",
            "ASUNTO NO CONTENCIOSO"=>"ANC",
            "NO CONTENCIOSO"=>"ANC",
            "NO CONTENCIOSOS"=>"ANC",
            "ASUNTOS NO CONTENCIOSO"=>"ANC",
            "C"=>"ANC",
            "EXPEDIENTES"=>"ANC",
            "EXPEDIENTE"=>"ANC",
            "N"=>"ANC",
            "NC"=>"ANC",
            "TRANSFERENCIA VEHICULAR"=>"TV",
            "TRANSFERENCIAS VEHICULAR"=>"TV",
            "TRANSFERENCIAS VEHICULARES"=>"TV",
            "TRANFERENCIA VEHICULAR"=>"TV",
            "VEHICULAR"=>"TV",
            "REGISTRO DE VEHICULARES"=>"TV",
            "G"=>"GM",
            "GARANTIAS MOBILIARIAS"=>"GM",
            "GARANTIA MOBILIARIA"=>"GM",
            "GARANTIAS MOBILIARIA"=>"GM",
            "TESTAMENTOS"=>"T"
        );

    // constructor
    function __construct() 
    {
         
    }
 
    // destructor
    function __destruct() 
    {
        // $this->close();
    }
 
    // Connecting to database
    public function connect() 
    {
        require_once 'Config.conf';
       // $db_host = '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=CNL-GRAMOS2)(PORT=1521))(CONNECT_DATA=(SID=orcl)))';

        $conn = oci_connect('system', 'Cnl$12345',"192.168.0.9/orcl",'AL32UTF8');
        if (!$conn) {
            $e = oci_error();
        }
      /*  try{
                //conexión a base de datos
                $con = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
                $con -> set_charset("utf8");
                    
              }catch (mysqli_sql_exception $e)
                   {
                    my_status_header(500);
                    exit;
        }   */

        // return database handler
        return $conn;
    }

    public static function connect_db() 
    {
        require_once 'Config.php';
//        $conn = oci_connect('system', '12345','localhost/Test','AL32UTF8');
        
        $conn = oci_connect('system', 'Cnl$12345','192.168.0.9/orcl','AL32UTF8');
        if (!$conn) {
            $e = oci_error();
        }
     
        return $conn;
    }
 
    // Closing database connection
    public function close() 
    {
        // mysql_close();
    }

     private function getTipoInstrumento()
    {
        $db=$this->connect_db();
        $stid=oci_parse($db,"SELECT NUM_TIPO,TIPO FROM DATAHISTORICA.tipo_instrumento_");
        oci_execute($stid);
        $data=[];
        while($row=oci_fetch_assoc($stid))
        {
            $data[]=$row;
        }
        oci_free_statement($stid);
        oci_close($db);
        return $data;
    }
    
     public function getTipoInstrumentoById($id)
    {
        $data_instr=$this->getTipoInstrumento();
        $sdetalle="";
        foreach ($data_instr as $row) {
            if($row["NUM_TIPO"]==$id)
               $sdetalle.="'".$row["TIPO"]."',";
        }
        $sdetalle=substr($sdetalle,0,-1);
        return $sdetalle;
   }


     private function getNotariosPeticion()
    {
        $db=$this->connect_db();
        $stid=oci_parse($db,"SELECT P.ID,P.IDNOTARIA,N.CODIGO FROM DATAHISTORICA.NOTARIAPETICION P INNER JOIN
SISGEN.NOTARIA N ON P.IDNOTARIA=N.ID
WHERE ACTIVO=1");
        oci_execute($stid);
        $data=[];
        while($row=oci_fetch_assoc($stid))
        {
            $data[]=$row;
        }
        oci_free_statement($stid);
        oci_close($db);
        return $data;
    }

      private function getNotariosPeticionOficio()
    {
        $db=$this->connect_db();
        $stid=oci_parse($db,"
                SELECT O.ID,O.IDNOTARIA,N.CODIGO FROM 
                OCPREPORTE.NOTARIAOFICIO O INNER JOIN
                SISGEN.NOTARIA N ON O.IDNOTARIA=N.ID
                WHERE ACTIVO=1

            ");
        oci_execute($stid);
        $data=[];
        while($row=oci_fetch_assoc($stid))
        {
            $data[]=$row;
        }
        oci_free_statement($stid);
        oci_close($db);
        return $data;
    }
    
     public function getNotariosPeticionAll()
    {
        $data_not=$this->getNotariosPeticionOficio();
        $sdetalle_ids="";
        $sdetalle_rucs="";
        foreach ($data_not as $row) {
               $sdetalle_ids.="'".$row["IDNOTARIA"]."',";
               $sdetalle_rucs.="".$row["CODIGO"].",";
        }
        $sdetalle_ids=substr($sdetalle_ids,0,-1);
        $sdetalle_rucs=substr($sdetalle_rucs,0,-1);
        if($sdetalle_ids=="")
            $sdetalle_ids="0";
        
        if($sdetalle_rucs=="")
            $sdetalle_rucs="0";
        
        $items=array('IDNOTARIA' =>$sdetalle_ids,'RUC'=>$sdetalle_rucs);
        return $items;
   }
   

public static function getTipoInstrumentoParaReporte($idInstr)
{
    $idInstr=trim($idInstr);
    $strInstr="";
    if($idInstr!="" && isset(self::$itemsListTipoInstr[$idInstr])){
        $strInstr=self::$itemsListTipoInstr[$idInstr];
        return $strInstr;
    }
    return $idInstr;
}

  public function getValorNumerico($valor)
    {
        if(intval($valor)>0){
            return isset($valor)?$valor:"0";
        }
        else
            return "0";
    }

      public function getValorIntSanit($valor)
    {
        
        if($valor!=""){
            $reg=filter_input(INPUT_GET, $valor, FILTER_SANITIZE_NUMBER_INT);
            return isset($reg)?$reg:"0";
        }
        else
            return "0";
    }


    public function getValorStringSanit($valor)
    {
        
        if($valor!=""){
            $reg=filter_input(INPUT_GET, $valor, FILTER_SANITIZE_STRING);
            return isset($reg)?trim($reg):"";
        }
        else
            return "0";
    }



    public function getValorString($valor)
    {
         if($valor!=""){
            $valor = strip_tags($valor);
            return isset($valor)?trim($valor):"";
        }
        else
            return "";
    }

    public function getValidar($parametros)
    {
        if(sizeof($parametros)>0)
        {
            foreach ($parametros as $key => $value) {
               if($key=="tipo")
                    {
                            switch ($value) {
                                           case 'string':
                                                    $reg=filter_input(INPUT_GET, $value["parametro"], FILTER_SANITIZE_STRING);
                                               break;
                                           
                                           case 'number':
                                               $reg=filter_input(INPUT_GET, $value["parametro"], FILTER_SANITIZE_NUMBER_INT);
                                               break;
                                           
                                           default:
                                               # code...
                                               break;
                                       }           
                    }
            }
        }
    }
   
    public function getListAllRows($sql)
    {
        if($sql!=""){
            $db=$this->connect();
            $stid = oci_parse($db,$sql);
            oci_execute($stid);
            $all=array();
        //    die($sql);
            while (($row = oci_fetch_assoc($stid)) != false) {

//                var_dump($row->key);
              //  var_dump($row);
                foreach ($row as $key => $value ) {
//                    var_dump($key);
                    $row[strtolower($key)]=$row[$key];
                }
                $all[]=$row;
            }
            oci_free_statement($stid);
            oci_close($db);
            return $all;
         }
         return [];
    }

 
    public function getRow($sql)
    {
        if($sql!=""){
            $db=$this->connect();
            $stid = oci_parse($db,$sql);
            oci_execute($stid);
            $row = oci_fetch_assoc($stid);

            if($row!=false){
            foreach ($row as $key => $value ) {
                    $row[strtolower($key)]=$row[$key];
            }
            oci_free_statement($stid);
            oci_close($db);
            }
            
            return $row;
         }
         return [];
    }


        public function getAllTotal($sql)
    {
        if($sql!=""){
            $db=$this->connect();
           // die($sql);
            $stid = oci_parse($db,$sql);
            oci_execute($stid);
            $cantidad=0;
            $row = oci_fetch_assoc($stid);
            if(isset($row["CANTIDAD"]) && intval($row["CANTIDAD"])>0)
                 $cantidad=intval($row["CANTIDAD"]);
            else 
              $cantidad=0;
                 oci_free_statement($stid);
                 oci_close($db);
          
            return $cantidad;
         }
    }
 
}
 
?>