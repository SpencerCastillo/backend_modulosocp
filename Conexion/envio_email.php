<?php 

function getFecha($strFecha)
{
  if($strFecha!="")
  {
      $arrFecha=explode("-",$strFecha);
      return $arrFecha[2]."/".$arrFecha[1]."/".$arrFecha[0];
  }
  return "";
}

function sendCorreo($_notario,$_correo,$get_data,$html){

return "AA";
$data=json_decode($get_data);
require("class.phpmailer.php");
require("class.smtp.php");
if(!class_exists('DB_Connect') ) 
    require "conexion/DB_Connect.php";

$dbconneconect =new DB_Connect();
$cn=$dbconneconect->connect();


$mail = new PHPMailer();
$xxoficio="0";
if(isset($data->oficio) && $data->oficio!="")
  $xxoficio=$data->oficio;

$xxoficio=trim($xxoficio);
$xxoficio=strtoupper($xxoficio);

$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->Host = "smtp.gmail.com"; // SMTP a utilizar. Por ej. smtp.elserver.com

$mail->Username = "sistemascong@gmail.com"; // Correo completo a utilizar
$mail->Password = "Benjamin123**"; // Contraseña

$mail->SMTPSecure = "tls";
$mail->Port = 587; // Puerto a utilizar
$mail->From = "sistemascong@gmail.com";
$mail->Subject="OFICIO CIRCULAR ".$xxoficio." - NOTARIA ".$_notario;
$mail->FromName = "CNL - SISTEMAS";

$xxemail="";
if(isset($data->email) && $data->email!="")
  $xxemail=$data->email;

if(strpos($xxemail,";")===false)
 $mail->AddAddress($xxemail);
else{
  $aaEmail=explode(";",$xxemail);
  foreach ($aaEmail as  $value) {
    $mail->AddAddress($value);
  }
}

$sql="SELECT RUTA_ARCHIVO FROM OCPREPORTE.CIRCULAR_OCP WHERE ID=".$data->idCircular;
$stmt=oci_parse($cn,$sql);
oci_execute($stmt);
$rowOfic = oci_fetch_assoc($stmt);
$rutaArchivo=$rowOfic["RUTA_ARCHIVO"];

$file=$rutaArchivo;

//$mail->AddAddress("lponce@notarios.org.pe"); 
 //principal
$mail->AddBCC("giancarloramosrivas@gmail.com"); // copia oculta

$mail->IsHTML(true);
$mail->CharSet = 'UTF-8'; // El correo se envía como HTML
$body="<div style='font-size: 14px;font-family: Times New Roman'>".$html."</div>";

//$data = http_build_query($data);
//$file = file_get_contents($url);

/*
$body .= "Cliente :".$_cliente."<br>";
$body .= "N° Documento :".$_dni."<br>";
$body .= "Celular/Teléfono :".$_telefono."<br>";
$body .= "Correo :".$_correo."<br>";
$body .= "Fecha :".getFecha($_fecha)."<br>";
$body .= "Hora :".$_hora."<br>";
$body .= "Servicio :".$servicios."<br>";
*/
/*
$body .= "Atentamente,<br/>";
$body .= "<strong>OCP LA/FT</strong><br/>";*/
//$body .= '<hr style="color: #829AAB;" />';
$mail->Body = $body; // Mensaje a enviar
//$mail->SMTPDebug  = 2;
if($rutaArchivo!="")
  $mail->AddAttachment($file,"oficio".".pdf");

//$mail->AddStringAttachment($file, 'file.pdf', 'base64', 'application/pdf');
//$exito = $mail->Send(); // Envía el correo.


if($exito){
 /*  $sql="INSERT INTO DATAHISTORICA.CORREO_ENVIADO(STATUS,CODIGO_NOTARIO,OFICIO,CORREO,FECHA_REGISTRO) 
  VALUES(1,".$data->ruc.",'".$xxoficio."','".$data->email."',CURRENT_TIMESTAMP)";
*/
  $arr_resp[0]='1';
  $arr_resp[1]='Se envio correctamente los archivos al(los) correo(s):';
}else{
 /* $sql="INSERT INTO DATAHISTORICA.CORREO_ENVIADO(STATUS,CODIGO_NOTARIO,OFICIO,CORREO,FECHA_REGISTRO) 
  VALUES(0,".$data->ruc.",'".$xxoficio."','".$data->email."',CURRENT_TIMESTAMP)";
*/
  $arr_resp[0]='0';
  $arr_resp[1]='No se pudo enviar el correo';
} 
/*
$stmt=oci_parse($cn,$sql);
oci_execute($stmt);
oci_free_statement($stmt);
oci_close($cn);
*/

}
  

?>