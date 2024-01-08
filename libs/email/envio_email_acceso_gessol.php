<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


function sendCorreo($data,$usuario,$clave){

require 'phpmailer/vendor/autoload.php';

if(!class_exists('DB_Connect') ) 
    require "conexion/DB_Connect.php";

$dbconneconect =new DB_Connect();
$cn=$dbconneconect->connect();

$email=$data["EMAIL"];
$notaria=$data["NOTARIA"];
$id=$data["ID"];


$mail = new PHPMailer();
$mail->IsSMTP();
 

$mail->SMTPDebug  = 0;

$mail->SMTPOptions = array(
                    'ssl' => array(
                      'verify_peer' => false,
                      'verify_peer_name' => false,
                      'allow_self_signed' => true,
                      'cafile' => '[path-to-cert].crt'
                    )
                  );

$mail->Host = "192.168.0.7"; // SMTP a utilizar. Por ej. smtp.elserver.com
$mail->Port = 587; // Puerto a utilizar
$mail->SMTPAuth = true;


$mail->Username = "alertas@infonotaria.pe"; // Correo completo a utilizar
$mail->Password = "Aocp2022$$"; // Contraseña

$mail->SMTPSecure = "tls";

$mail->From = "alertas@infonotaria.pe";
$mail->Subject="ACCESOS GESSOL - OCP";
$mail->FromName = "CNL - OCP";

$xxemail="";
if(isset($email) && $email!="")
  $xxemail=$email;

if(strpos($xxemail,";")===false)
 $mail->AddAddress($xxemail);
else{
  $aaEmail=explode(";",$xxemail);
  foreach ($aaEmail as  $value) {
    $mail->AddAddress($value);
  }
}

$mail->addReplyTo('info@example.com', 'Information');

$html="";

$html.="<p>Estimado(a) Dr(a). <span style='font-weight: bold;'> ".$notaria." </span>, hemos recibido un correo solicitando los accesos a la plataforma del GESSOL con los siguientes datos:</p>";


$html.="
<table>
";

$html.="
<tr>
  <td><em>Nombres y Apellidos</em><td>
  <td style='font-weight: bold;'>".$data["NOMBRES"]."<td>
</tr>
";


$html.="
<tr>
  <td><em>Número de documento</em><td>
  <td style='font-weight: bold;'>".$data["NUMERODOCUMENTO"]."<td>
</tr>
";



$html.="
</table>
";


$html.="
<p>
Los Accesos a la plataforma GESSOL son los siguientes:
Link: http://www.notariadigital.org.pe:4000/Gessol#/LoginName
</p>
";



$html.="
<table>
";

$html.="
<tr>
  <td><em>Usuario : </em><td>
  <td style='font-weight: bold;'>".$usuario."<td>
</tr>
";


$html.="
<tr>
  <td><em>Clave</em><td>
  <td style='font-weight: bold;'>".$clave."<td>
</tr>
";


$html.="
</table>
";
$html.="
<p><em>Los Oficios recibidos con fecha 29 de Noviembre del 2022 se podrán responder desde la plataforma del GESSOL.</em></p>
";

/*
$html.="
<p><em>Este documento fue enviado y guardado, según se registra en el Gestor de Solicitudes (GESSOL).</em></p>
";*/

$html.="
<span style='font-weight: bold;'> 
Atentamente
<br>
ATEC - CNL
</span>

";

 //principal
$mail->AddBCC("systemapp38@gmail.com"); // copia oculta
//$mail->addBCC('sistemascong@gmail.com');
$mail->IsHTML(true);
$mail->CharSet = 'UTF-8'; // El correo se envía como HTML
$body="<div style='font-size: 14px;font-family: Times New Roman'>".$html."</div>";
$mail->Body = $body; // Mensaje a enviar


//$mail->AddStringAttachment($file, 'file.pdf', 'base64', 'application/pdf');


$exito = $mail->Send(); // Envía el correo.


if($exito){
   $sql="UPDATE ELECTNOTARIAL.SOLICITUD_ACCESO_GESSOL
   set CORREOENVIADO=1,FECHACORREOENVIADO=CURRENT_TIMESTAMP
    where id=".$id;

  $arr_resp[0]='1';
  $arr_resp[1]='Se envio correctamente los archivos al(los) correo(s):';
}else{
  $sql="UPDATE ELECTNOTARIAL.SOLICITUD_ACCESO_GESSOL
   set CORREOENVIADO=0
    where id=".$id;
  $arr_resp[0]='0';
  $arr_resp[1]='No se pudo enviar el correo';
}


$stmt=oci_parse($cn,$sql);
oci_execute($stmt);
oci_free_statement($stmt);
oci_close($cn);

return $arr_resp;
}
  

?>