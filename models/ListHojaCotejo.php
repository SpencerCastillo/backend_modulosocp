<?php 

if(!class_exists('DB_Connect') ) 
    include "conexion/DB_Connect.php";


class ListHojaCotejo extends DB_Connect {
        
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


    public function getDocumento($iddocumento,$data)
       {      
        $db=$this->connect();
        $iddocumento=isset($iddocumento)?$iddocumento:"0";
        
        $numeroInstrumento=isset($data->numeroInstrumento)?$data->numeroInstrumento:"";
        $numeroKardex=isset($data->numeroKardex)?$data->numeroKardex:"";
        $idnotaria=isset($data->idnotaria)?$data->idnotaria:"0";
        $fechainstrumento=isset($data->fechainstrumento)?$data->fechainstrumento:"";


        $sql="
            SELECT d.id as iddocumento,upper(n.descripcion) as notaria,ti.descripcion as tipo_instrumento,
            d.idinstrumento,
            d.numero as numero_instrumento,n.telefono1 as telefono,
            di.restodireccion as direccion,
            to_char(d.fechaautorizacion,'dd/mm/YYYY') as fecha_instrumento,
            d.fechaconclusion as fecha_conclusion,
            d.numerodekardex,
            pp.descripcion as provincia,dis.descripcion as distrito,de.descripcion as departamento
            FROM SISGEN.documentonotarial d 
            inner join sisgen.notaria n on d.idnotaria=n.id
            inner join sisgen.tipoinstrumento ti on d.idinstrumento=ti.id
            left join sisgen.direccion di on n.iddireccion=di.id
            left join sisgen.departamento de on di.iddepartamento=de.id
            left join sisgen.provincia pp on di.idprovincia=pp.id
            left join sisgen.distrito dis on di.iddistrito=dis.id
            where d.id>0 ";
            if(intval($iddocumento)>0)
                $sql.=" and d.id=".$iddocumento; 

            if($numeroInstrumento!="")
                $sql.=" and d.numero='".$numeroInstrumento."'"; 
            
            if($numeroKardex!="")
                $sql.=" and d.numerodekardex='".$numeroKardex."'"; 
            
            if(intval($idnotaria)>0)
                $sql.=" and d.idnotaria=".$idnotaria; 

            if($fechainstrumento!=""){
                if(strrpos($fechainstrumento,"-")!==false){
                  $fechainstrumento = date("d/m/Y", strtotime($fechainstrumento));
                }


               // die($fechainstrumento);
                $sql.=" and trunc(d.fechaautorizacion)='".$fechainstrumento."'"; 
            }

                

   // die($sql);
       $stid = oci_parse($db,$sql);
       oci_execute($stid);
       $row = oci_fetch_assoc($stid);
       $all=$row;
        oci_free_statement($stid);
        oci_close($db);   
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
            ac.id as idactojuridico
            FROM SISGEN.documentonotarial d 
            inner join sisgen.operacion o on d.id=o.iddocumentonotarial
            left join sisgen.actojuridico ac on o.idactojuridico=ac.id
            where d.id=".$iddocumento;

       $stid = oci_parse($db,$sql);
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

    public function getContratantesPorOperacion($idOperacion)
       {      
            $db=$this->connect();
            $iddocumento=isset($iddocumento)?$iddocumento:"0";
            $sql="
                SELECT DI.DESCRIPCION AS NUM_DOC,

                PF.NOMBRE,
                PF.PRIMERAPELLIDO,
                PF.SEGUNDOAPELLIDO,
                PJ.RAZONSOCIAL,
                tdi.descripcion as tipo_doc,

                IDTIPOPERSONA,ec.descripcion as estado_civil,
                to_char(fechanacimiento,'dd/mm/YYYY') as fecha_nacimiento,
                pp.descripcion as profesion,s.otraprofesion,cc.descripcion as cargo,pa.nombre as pais,
                dd.restodireccion as direccion,dep.descripcion as departamento,pro.descripcion as provincia,
                dis.descripcion as distrito,
                (
                case s.idtipopersona when 1 then pf.correo when 2 then pj.correo else '' end
                )as correo,
                (
                case s.idtipopersona when 1 then pf.telefono when 2 then pj.telefono else '' end
                )as telefono,
                 (
                case s.idtipopersona when 1 then pf.cliente when 2 then pj.cliente else '' end
                )as contratante,
                sec.descripcion as sector,pj.otraactividad as actividad,ofs.cuantiaorigen,
                ttmm.descripcion as tipo_moneda,
                tin.descripcion as condicion,
                i.id as interviniente,s.id as idsujeto,
                i.fechafirma,i.rolrepresentante as rol
                from  sisgen.operacion o 
                inner join sisgen.interviniente i on o.id=i.idoperacion
                INNER JOIN SISGEN.SUJETO S ON S.ID=I.IDPERSONA 
                INNER JOIN SISGEN.SUJETODOCIDENTIFICATIVO SI ON S.ID=SI.IDPERSONA
                INNER JOIN SISGEN.DOCUMENTOIDENTIFICATIVO DI ON SI.IDDOCUMENTOIDENTIFICATIVO=DI.ID
                INNER JOIN SISGEN.tipodocumentoidentificativo tdi ON di.tipodocumentoid=tdi.id
                
                LEFT JOIN SISGEN.PERSONAFISICA PF ON S.IDPERSONAFISICA=PF.ID
                LEFT JOIN SISGEN.ESTADOCIVIL ec on pf.estadocivil=ec.id
                left join sisgen.profesion pp on s.idprofesion=pp.id
                left join sisgen.tipocargo cc on s.idcargo=cc.id
                left join sisgen.direccion dd on s.iddireccion=dd.id
                left join sisgen.pais pa on dd.idpais=pa.id
                left join sisgen.departamento dep on dd.iddepartamento=dep.id
                left join sisgen.provincia pro on dd.idprovincia=pro.id
                left join sisgen.distrito dis on dd.iddistrito=dis.id
                LEFT JOIN SISGEN.PERSONAJURIDICA PJ ON S.IDPERSONAJURIDICA=PJ.ID
                left join sisgen.sector sec on pj.idsector=sec.id
                left join sisgen.origenfondos ofs on i.id=ofs.idinterviniente
                left join sisgen.tipomoneda ttmm on ofs.tipomoneda=ttmm.id
                left join sisgen.tipointervencion tin on i.idtipointervencion=tin.id
                where o.id=".$idOperacion;

           $stid = oci_parse($db,$sql);
           //die($sql);
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

            public function getRepresentantesPorContratante($idOperacion)
       {      
            $db=$this->connect();
            $idOperacion=isset($idOperacion)?$idOperacion:"0";

            $sql="
                SELECT DI.DESCRIPCION AS NUM_DOC,
                PF.NOMBRE,
                PF.PRIMERAPELLIDO,
                PF.SEGUNDOAPELLIDO,
                PJ.RAZONSOCIAL,
                (
                case s.idtipopersona when 1 then pf.cliente when 2 then pj.cliente else '' end
                )as cliente,
                IDTIPOPERSONA,ec.descripcion as estado_civil,
                to_char(fechanacimiento,'dd/mm/YYYY') as fecha_nacimiento,
                pp.descripcion as profesion,s.otraprofesion,cc.descripcion as cargo,pa.nombre as pais,
                dd.restodireccion as direccion,dep.descripcion as departamento,pro.descripcion as provincia,
                dis.descripcion as distrito,
                (
                case s.idtipopersona when 1 then pf.correo when 2 then pj.correo else '' end
                )as correo,
                (
                case s.idtipopersona when 1 then pf.telefono when 2 then pj.telefono else '' end
                )as telefono,
                 (
                case s.idtipopersona when 1 then pf.cliente when 2 then pj.cliente else '' end
                )as contratante,
                sec.descripcion as sector,pj.otraactividad as actividad,
                tin.descripcion as condicion,
                i.id as interviniente,s.id as idsujeto
                from  sisgen.operacion o 
                inner join sisgen.interviniente i on o.id=i.idoperacion
                inner join sisgen.representante rr on i.id=rr.idinterviniente
                INNER JOIN SISGEN.SUJETO S ON S.id=rr.IDPERSONA 
                INNER JOIN SISGEN.SUJETODOCIDENTIFICATIVO SI ON S.ID=SI.IDPERSONA
                INNER JOIN SISGEN.DOCUMENTOIDENTIFICATIVO DI ON SI.IDDOCUMENTOIDENTIFICATIVO=DI.ID
                LEFT JOIN SISGEN.PERSONAFISICA PF ON S.IDPERSONAFISICA=PF.ID
                LEFT JOIN SISGEN.ESTADOCIVIL ec on pf.estadocivil=ec.id
                left join sisgen.profesion pp on s.idprofesion=pp.id
                left join sisgen.tipocargo cc on s.cargo=cc.id
                left join sisgen.direccion dd on s.iddireccion=dd.id
                left join sisgen.pais pa on dd.idpais=pa.id
                left join sisgen.departamento dep on dd.iddepartamento=dep.id
                left join sisgen.provincia pro on dd.idprovincia=pro.id
                left join sisgen.distrito dis on dd.iddistrito=dis.id
                LEFT JOIN SISGEN.PERSONAJURIDICA PJ ON S.IDPERSONAJURIDICA=PJ.ID
                left join sisgen.sector sec on pj.idsector=sec.id
                left join sisgen.tipointervencion tin on i.idtipointervencion=tin.id
                where o.id=".$idOperacion." ";

           $stid = oci_parse($db,$sql);
           //die($sql);
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


          public function getBanderasPorSujeto($iddocumento)
       {      
            $db=$this->connect();
            $iddocumento=isset($iddocumento)?$iddocumento:"0";
            $sql=" 

                                SELECT                 
               upper(tl.descripcion) as bandera,
                crs.lista AS LISTA,
                r.sujetoid,
                crs.porcentaje
FROM sisgen.relaciones r 
INNER JOIN sisgen.consultamanual cm ON r.consultamanualid = cm.id
INNER JOIN sisgen.lista l ON r.listaid = l.id
INNER JOIN sisgen.tipolista tl ON l.idtipolista = tl.id
INNER JOIN sisgen.consultamanualresult crs ON cm.id = crs.consultaid
WHERE r.documentonotarialid = ".$iddocumento."
AND EXISTS (
    SELECT 1
    FROM TABLE (
        CAST (
            MULTISET (
                SELECT REGEXP_SUBSTR(tl.identificadores, '[^,]+', 1, LEVEL)
                FROM DUAL
                CONNECT BY INSTR(tl.identificadores, ',', 1, LEVEL - 1) > 0
            ) AS sys.odcivarchar2list
        )
    )
    WHERE TO_NUMBER(crs.numlista) = COLUMN_VALUE
)  order by  r.sujetoid ";

//die($sql);

           $stid = oci_parse($db,$sql);
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


          public function getPatrimonial($iddocumento)
       {      
            $db=$this->connect();
            $iddocumento=isset($iddocumento)?$iddocumento:"0";
            $sql="select c.cuantia,tm.descripcion as tipo_moneda,
            (select descripcion from sisgen.mediodepago mp 
            inner join sisgen.formadepago fp on mp.idformadepago=fp.id
            where mp.idoperacion=o.id and rownum<=1 ) as forma_pago,
            (select descripcion from sisgen.momentopago mpp 
            inner join sisgen.mediodepago fp on mpp.id=fp.momentopago
            where fp.idoperacion=o.id and rownum<=1 ) as oportunidad_pago
            from sisgen.operacion o 
            inner join  sisgen.cuantia c on o.id=c.idoperacion 
            inner join sisgen.tipomoneda tm on c.idtipomoneda=tm.id
            where o.id=".$iddocumento;

           $stid = oci_parse($db,$sql);
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

         public function getDetalleMedioPago($idOperacion)
       {      
            $db=$this->connect();
            $idOperacion=isset($idOperacion)?$idOperacion:"0";
            $sql="   SELECT upper(tm.descripcion) as tipo_moneda,
            tpm.descripcion as tipo_medio_pago,
            mm.descripcion as momento_pago,
            mp.fechapago,numerodocumento,cuantia
            ,fdp.descripcion as forma_pago
            FROM sisgen.operacion o 
            inner join sisgen.mediodepago mp on o.id=mp.idoperacion
            left join sisgen.tipomoneda tm on mp.tipomoneda=tm.id
            left join sisgen.tipomediopago tpm on mp.mediopago=tpm.id
            left join sisgen.momentopago mm on mp.momentopago=mm.id
            left join sisgen.formadepago fdp on mp.idformadepago=fdp.id
            where o.id=".$idOperacion;

           $stid = oci_parse($db,$sql);
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

           public function getBienMueble($idOperacion)
       {     
            $db=$this->connect();
            $idOperacion=isset($idOperacion)?$idOperacion:"0";
            $sql="      SELECT sr.descripcion as sede_registral,
            vv.partidaregistral,numeroplaca as placa,ANYO as anio,
          numeroserie,ttp.descripcion as tipo_vehiculo,
          marca,modelo,numeroplaca as placa,combustible,clase,carroceria,
          motor,numerocilindros as cilindros,numerorueda as ruedas
            FROM sisgen.operacion o 
            inner join sisgen.objeto obj on o.id=obj.idoperacion
            inner join sisgen.sujeto ss on obj.idpersona=ss.id
            inner join sisgen.vehiculo vv on ss.idvehiculo=vv.id
            left join SISGEN.sederegistral sr on vv.idsederegistral=sr.id
            left join sisgen.tipovehiculo ttp on vv.tipovehiculo=ttp.id
            where o.id=".$idOperacion;

           $stid = oci_parse($db,$sql);
           oci_execute($stid);
           $all=[];
          $row = oci_fetch_assoc($stid);
          
           
            oci_free_statement($stid);
            oci_close($db);   
            return $row;     
        }

            public function getBienInMueble($idOperacion)
       {     
            $db=$this->connect();
            $idOperacion=isset($idOperacion)?$idOperacion:"0";
            $sql="     
                 SELECT vv.partidaregistral,
            sr.descripcion as sede_registral,
            dep.descripcion as departamento,
            prov.descripcion as provincia,
            dis.descripcion as distrito
            FROM sisgen.operacion o 
            inner join sisgen.objeto obj on o.id=obj.idoperacion
            inner join sisgen.sujeto ss on obj.idpersona=ss.id
            inner join sisgen.inmueble vv on ss.idinmueble=vv.id
            left join SISGEN.sederegistral sr on vv.idsederegistral=sr.id
            left join sisgen.direccion di on ss.iddireccion=di.id
            left join sisgen.departamento dep on di.iddepartamento=dep.id
            left join sisgen.provincia prov on di.idprovincia=prov.id
            left join sisgen.distrito dis on di.iddistrito=dis.id
            where o.id=".$idOperacion;

           $stid = oci_parse($db,$sql);
           oci_execute($stid);
           $all=[];
           $row = oci_fetch_assoc($stid);
            oci_free_statement($stid);
            oci_close($db);   
            return $row;     
        }

    private $allData=array();
    public function runValues($data)
    {
            $this->allData=$data;
    }


     public function getValorFormatString($valor)
    {
         if(isset($this->allData[$valor]))
            return $this->allData[$valor];
        else
            return "";
    }
}
?>