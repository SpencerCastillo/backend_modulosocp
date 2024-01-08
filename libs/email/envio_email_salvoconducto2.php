<?php 
function generarCodigo($longitud) {
    $key = '';
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
    $max = strlen($pattern)-1;
    for($i=0;$i < $longitud;$i++) $key .= $pattern{mt_rand(0,$max)};
    return $key;
} 
$_correo="giancarloramosrivas@gmail";
$nombres="GIANCALRO";
$apepat="ramos";
$apemat="RIVAS";

$_correo=trim($_correo);
$nombres=trim($nombres);
$apepat=trim($apepat);
$apemat=trim($apemat);

$_numdoc="47386983";

$_numdoc=trim($_numdoc);


$_cliente=$nombres." ".$apepat." ".$apemat;

require("class.phpmailer.php");
require("class.smtp.php");
$mail = new PHPMailer();
$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->Host = "smtp.gmail.com"; // SMTP a utilizar. Por ej. smtp.elserver.com



$mail->Username = "salvoconductonotarial2020@gmail.com"; // Correo completo a utilizar
$mail->Password = "s@lv0c0nduct0"; // Contraseña
$mail->SMTPDebug  = 1;
$mail->SMTPSecure = "tls";
$mail->Port = 587; // Puerto a utilizar
$mail->From = "salvoconductonotarial2020@gmail.com";
$mail->Subject="SALVOCONDUCTO - SERVICIO NOTARIAL";
$mail->FromName = "COLEGIO DE NOTARIOS DE LIMA";
/*
$nom_file="../tempo/salvoconducto_".$_numdoc.".pdf";
$mail->AddAttachment($nom_file);
*/
$file_salvoconducto = file_get_contents("http://localhost:8081/salvoconducto/tempo/salvoconducto_".$_numdoc.".pdf");

$mail->addStringAttachment($file_salvoconducto,"salvoconducto_".$_numdoc.".pdf");

//$mail->addStringAttachment($nom_file,"salvoconducto".$_numdoc."_".generarCodigo("4").".pdf");
$mail->addBCC("giancarloramosrivas@gmail.com"); 
$mail->AddAddress($_correo); //principal
//$mail->AddBCC($str_correo[$i]); // copia oculta

$mail->IsHTML(true);
$mail->CharSet = 'UTF-8'; // El correo se envía como HTML
$body="";

$body .= "<strong>Hola ".$_cliente.", te ha llegado a través de nuestro sistema, el SALVOCONDUCTO - SERVICIOS NOTARIALES. </strong> <br/><br/>";


$body.="<br/>";

$body .= "Atentamente,<br/>";
$body .= "<strong>COLEGIO DE NOTARIOS DE LIMA</strong><br/>";
//$body .= '<hr style="color: #829AAB;" />';
$mail->Body = $body; // Mensaje a enviar
$exito = $mail->Send(); // Envía el correo.

var_dump($exito);
return;
$rsp="";
if($exito)
  $rsp='1';
else
  $rsp='0';


echo $rsp;

?>

