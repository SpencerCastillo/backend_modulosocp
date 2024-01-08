<?php 

header("Access-Control-Allow-Origin:*");

function getFecha($strFecha)
{
  if($strFecha!="")
  {
      $arrFecha=explode("-",$strFecha);
      return $arrFecha[2]."/".$arrFecha[1]."/".$arrFecha[0];
  }
  return "";
}

function getCadena_decode($strCadena)
{
  if($strCadena!=""){
      $strCadena=trim($strCadena);
      $cadena_result=str_replace("*_*"," ",$strCadena);
      return $cadena_result;
  }
  return "";
}

//function sendCorreo($_cliente,$_dni,$_telefono,$_correo,$_fecha,$_hora,$servicios){

require("class.phpmailer.php");
require("class.smtp.php");


$_cliente=getCadena_decode($_POST["nombre"]);
$_dni=getCadena_decode($_POST["numdoc"]);
$_telefono=getCadena_decode($_POST["celular"]);
$_correo=getCadena_decode($_POST["correo"]);
$_fecha=getCadena_decode($_POST["fecha"]);
$_hora=getCadena_decode($_POST["horaatencion"]);
$servicios=getCadena_decode($_POST["servicio"]);



$mail = new PHPMailer();
$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->Host = "smtp.gmail.com"; // SMTP a utilizar. Por ej. smtp.elserver.com


$mail->Username = "salvoconductonotarial2020@gmail.com"; // Correo completo a utilizar
$mail->Password = "s@lv0c0nduct0"; // Contraseña



$mail->SMTPSecure = "tls";
$mail->Port = 587; // Puerto a utilizar
$mail->From = "informes@a.com";
$mail->Subject="CITA NOTARIAL";
$mail->FromName = "Notaria 01";

//$mail->AddAddress($_correo);
$mail->AddAddress($_correo); //principal

$mail->IsHTML(true);
$mail->CharSet = 'UTF-8'; // El correo se envía como HTML
$body="";

$body .= "Hola, te ha llegado a través de nuestro sistema la confirmación de su reserva de Cita Notarial <br/><br/>";

$body .= "<div style='font-weight:bold;'>";

$body .= "Cliente :".$_cliente."<br>";

$body .= "N° Documento :".$_dni."<br>";

$body .= "Celular/Teléfono :".$_telefono."<br>";

$body .= "Correo :".$_correo."<br>";

$body .= "Fecha :".getFecha($_fecha)."<br>";

$body .= "Hora :".$_hora."<br>";

$body .= "Servicio :".$servicios."<br>";


$body .= "</div>";

$body.="<br/>";

$body .= "Atentamente,<br/>";
$body .= "<strong>Notaría 01</strong><br/>";
//$body .= '<hr style="color: #829AAB;" />';
$mail->Body = $body; // Mensaje a enviar
$exito = $mail->Send(); // Envía el correo.

if($exito){
  $arr_resp[0]='1';
  $arr_resp[1]='Se envio correctamente los archivos al(los) correo(s):';
}else{
  $arr_resp[0]='0';
  $arr_resp[1]='No se pudo enviar el correo';
}

echo json_encode($arr_resp);
?>

