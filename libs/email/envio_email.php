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

function sendCorreo_ant($_notario,$_correo,$get_data,$html){
  $data=json_decode($get_data);
require("class.phpmailer.php");
require("class.smtp.php");
if(!class_exists('DB_Connect') ) 
    require "conexion/DB_Connect.php";

$dbconneconect =new DB_Connect();
$cn=$dbconneconect->connect();


$mail = new PHPMailer();
$mail->IsSMTP();
 

$xxoficio="0";
if(isset($data->oficio) && $data->oficio!="")
  $xxoficio=$data->oficio;

$xxoficio=trim($xxoficio);
$xxoficio=strtoupper($xxoficio);

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


/*
$mail->Username = "systemenvios2@gmail.com"; // Correo completo a utilizar
$mail->Password = "Jncarlo***"; // Contraseña
*/

/*
$mail->Username = "systemenvios1@gmail.com"; // Correo completo a utilizar
$mail->Password = "Qwerty123$"; // Contraseña
*/
/*
$mail->Username = "sistemascong@gmail.com"; // Correo completo a utilizar
$mail->Password = "Benjamin123**"; // Contraseña
*/
$mail->Username = "alertas@infonotaria.pe"; // Correo completo a utilizar
$mail->Password = "Aocp2022$$"; // Contraseña

$mail->SMTPSecure = "tls";

$mail->From = "alertas@infonotaria.pe";
$mail->Subject="OFICIO CIRCULAR N° ".$xxoficio." - NOTARIA ".$_notario;
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

//$mail->AddAddress("lponcepld@gmail.com");


 //principal
//$mail->AddBCC("giancarloramosrivas@gmail.com"); // copia oculta
//$mail->addBCC('sistemascong@gmail.com');
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
if($rutaArchivo!="")
  $mail->AddAttachment($file,"oficio".".pdf");

//$mail->AddStringAttachment($file, 'file.pdf', 'base64', 'application/pdf');


$exito = $mail->Send(); // Envía el correo.


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

return $mail;
}
  

?>