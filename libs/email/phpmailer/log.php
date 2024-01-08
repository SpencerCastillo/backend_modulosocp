<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();   



    $mail->SMTPOptions = array(
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'verify_depth' => 3,
        'allow_self_signed' => true
    ],
);


                                  //Send using SMTP
    $mail->Host       = '192.168.0.7';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true; 

    $mail->Username = "alertas@infonotaria.pe"; // Correo completo a utilizar
    $mail->Password = "Aocp2022$$"; // Contraseña
    $mail->SMTPDebug  = 0;

    $mail->SMTPSecure = "tls";            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('alertas@infonotaria.pe', 'Alertas - OCP');
    $mail->addBCC('systemenvios2@gmail.com');     //Add a recipient
   
/*
    //Attachments
    $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
*/
    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'ENVIADO';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}