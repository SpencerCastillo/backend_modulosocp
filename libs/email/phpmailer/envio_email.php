<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/*
function getFecha_($strFecha)
{
  if($strFecha!="")
  {
      $arrFecha=explode("-",$strFecha);
      return $arrFecha[2]."/".$arrFecha[1]."/".$arrFecha[0];
  }
  return "";
}*/

function sendCorreo($_notario,$documento,$get_data,$html){
  $data=($get_data);

require 'vendor/autoload.php';

if(!class_exists('DB_Connect') ) 
    require "conexion/DB_Connect.php";

$dbconneconect =new DB_Connect();
$cn=$dbconneconect->connect();

$mail = new PHPMailer(true);
$mail->isSMTP();   


 

$xxoficio="0";
if(isset($data->oficio) && $data->oficio!="")
  $xxoficio=$data->oficio;

$xxoficio=trim($xxoficio);
$xxoficio=strtoupper($xxoficio);

$mail->SMTPDebug  = 0;


    $mail->SMTPOptions = array(
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'verify_depth' => 3,
        'allow_self_signed' => true
    ],
);

$mail->Host = "192.168.0.7"; // SMTP a utilizar. Por ej. smtp.elserver.com
$mail->Port = 587; // Puerto a utilizar
$mail->SMTPAuth = true;


/*
$mail->Username = "systemenvios2@gmail.com"; // Correo completo a utilizar
$mail->Password = "Jncarlo***"; // Contraseña
*/


$mail->Username = "alertas@infonotaria.pe"; // Correo completo a utilizar
$mail->Password = "Aocp2022$$"; // Contraseña

$mail->SMTPSecure = "tls";

$mail->setFrom('alertas@infonotaria.pe', 'Alertas - OCP');

$mail->Subject="OFICIO CIRCULAR N° ".$xxoficio." - NOTARIA ".$_notario;


$xxemail="";
if(isset($data->email) && $data->email!=""){
  $xxemail=$data->email;
if(strpos($xxemail,";")===false)
 $mail->addAddress($xxemail);
else{
  $aaEmail=explode(";",$xxemail);
  foreach ($aaEmail as  $value) {
    if($value!="")
      $mail->addAddress($value);
  }
}
}


$sql="SELECT RUTA_ARCHIVO,SUMILLA FROM OCPREPORTE.CIRCULAR_OCP WHERE ID=".$data->idCircular;
$stmt=oci_parse($cn,$sql);
oci_execute($stmt);
$rowOfic = oci_fetch_assoc($stmt);
$rutaArchivo=$rowOfic["RUTA_ARCHIVO"];
$nota=$rowOfic["SUMILLA"];


$file=$rutaArchivo;

//$mail->AddAddress("lponce@notarios.org.pe");

$mail->AddAddress("systemapp38@gmail.com");


 //principal
//$mail->addBCC('sistemascong@gmail.com');
$mail->isHTML(true);
$mail->CharSet = 'UTF-8'; // El correo se envía como HTML

$body="<div style='font-size: 14px;font-family: Times New Roman'>".$html."</div>";

$mail->Body = $body; // Mensaje a enviar

//  $mail->addAttachment($file,"oficio".".pdf");

if($rutaArchivo!="")
  $mail->AddStringAttachment($documento, 'oficio.pdf', 'base64', 'application/pdf');





$exito = $mail->send(); // Envía el correo.

//var_dump($xxemail);

if($exito){
   $sql="INSERT INTO DATAHISTORICA.CORREO_ENVIADO(STATUS,CODIGO_NOTARIO,CORREO,FECHA_REGISTRO,IDOFICIO) 
  VALUES(1,".$data->ruc.",'".$data->email."',CURRENT_TIMESTAMP,'".$data->idCircular."')";

  $arr_resp[0]='1';
  $arr_resp[1]='Se envio correctamente los archivos al(los) correo(s):';
}else{
  $sql="INSERT INTO DATAHISTORICA.CORREO_ENVIADO(STATUS,CODIGO_NOTARIO,CORREO,FECHA_REGISTRO,IDOFICIO) 
  VALUES(0,".$data->ruc.",'".$data->email."',CURRENT_TIMESTAMP,'".$data->idCircular."')";
  $arr_resp[0]='0';
  $arr_resp[1]='No se pudo enviar el correo';
}


$stmt=oci_parse($cn,$sql);
oci_execute($stmt);
oci_free_statement($stmt);
oci_close($cn);

return $exito;
}
  

?>