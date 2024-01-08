<?php 
include('../conexion.php');
 $http="http://localhost:8081/Salvoconducto/";
$id_salvoconducto=$_POST["ids"];
$valor_prefijo=$_POST["valor_prefijo"];

$sql="SELECT * FROM salvoconducto WHERE id=".$id_salvoconducto;
$query=mysql_query($sql) or die(mysql_error());
$row=mysql_fetch_array($query);

$_correo=$row["correo"];
$nombres=$row["nombre"];
$apepat=$row["ape_pat"];
$apemat=$row["ape_mat"];
$numdoc=$row["num_doc"];

$_correo=trim($_correo);
$nombres=trim($nombres);
$apepat=trim($apepat);
$apemat=trim($apemat);
$_numdoc=trim($numdoc);
$_cliente=$nombres." ".$apepat." ".$apemat;

$xxxemail=$_POST["xxxemail"];
$str_correo=array();
if($xxxemail!=""){
	$_correo="";
	$str_correo=explode(';',$xxxemail);
}

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
//$mail->SMTPDebug = 1;
$mail->From = "salvoconductonotarial2020@gmail.com";
$mail->Subject="SALVOCONDUCTO - SERVICIO NOTARIAL";
$mail->FromName = "COLEGIO DE NOTARIOS DE LIMA";

//SI NO EXISTE CREAMOS UN NUEVO PDF

$dir = 'tempo/'; 
$file=$dir."salvoconducto_".$valor_prefijo.".pdf";
if (!file_exists($file)) {
	$rsp=file_get_contents($http."generarPdf.php?id=".$id_salvoconducto);
	$manage = json_decode($rsp);
	$point=$manage->url;
	$valor_prefijo=$manage->valor_prefijo;
} else
	$point="tempo/salvoconducto_".$valor_prefijo.".pdf";
	

$file_salvoconducto = file_get_contents($http.$point);

$mail->addStringAttachment($file_salvoconducto,"salvoconducto_".$valor_prefijo.".pdf");
if($_correo!="")
	$mail->AddAddress($_correo); //principal

for($i=0;$i<count($str_correo);$i++){
 if($i==0)
	$mail->AddAddress($str_correo[$i]); //principal
 else
	$mail->AddBCC($str_correo[$i]); // copia oculta
}

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
