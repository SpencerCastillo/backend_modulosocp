<?php 
include('../conexion.php');
 
$id_salvoconducto=$_POST["ids"];
$valor_prefijo=$_POST["valor_prefijo"];

$sql="SELECT * FROM salvoconducto WHERE id=".$id_salvoconducto;
$query=mysql_query($sql) or die(mysql_error());
$row=mysql_fetch_array($query);

$_correo=$row["correo"];
$nombres=$row["nombre"];
$apepat=$row["ape_pat"];
$apemat=$row["ape_mat"];

$_correo=trim($_correo);
$nombres=trim($nombres);
$apepat=trim($apepat);
$apemat=trim($apemat);

$_numdoc=$_POST["numdoc"];

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

$mail->SMTPSecure = "tls";
$mail->Port = 587; // Puerto a utilizar
$mail->From = "salvoconductonotarial2020@gmail.com";
$mail->Subject="SALVOCONDUCTO - SERVICIO NOTARIAL";
$mail->FromName = "COLEGIO DE NOTARIOS DE LIMA";

//$file_salvoconducto = file_get_contents("http://localhost:8081/app/tempo/salvoconducto_".$valor_prefijo.".pdf");

$file_salvoconducto = file_get_contents("http://localhost:8081/salvoconducto/tempo/salvoconducto_".$valor_prefijo.".pdf");

$mail->addStringAttachment($file_salvoconducto,"salvoconducto_".$valor_prefijo.".pdf");

//$mail->addStringAttachment($nom_file,"salvoconducto".$_numdoc."_".generarCodigo("4").".pdf");
//$mail->addBCC("giancarloramosrivas@gmail.com"); 
$mail->AddAddress($_correo); //principal
//$mail->AddBCC($str_correo[$i]); // copia oculta

$mail->IsHTML(true);
$mail->CharSet = 'UTF-8'; // El correo se envía como HTML
$body="";

$body .= "<strong>Hola ". strtoupper($_cliente).", te ha llegado a través de nuestro sistema, el SALVOCONDUCTO - SERVICIOS NOTARIALES. </strong> <br/><br/>";


$body.="<br/>";

$body .= "Atentamente,<br/>";
$body .= "<strong>COLEGIO DE NOTARIOS DE LIMA</strong><br/>";
//$body .= '<hr style="color: #829AAB;" />';
$mail->Body = $body; // Mensaje a enviar
$exito = $mail->Send(); // Envía el correo.

$rsp="";
if($exito)
  $rsp='1';
else
  $rsp='0';


echo $rsp;

?>

