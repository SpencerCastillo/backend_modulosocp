<?php 
require 'vendor/autoload.php'
;

// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();

$texto="";
for ($i=0; $i <100 ; $i++) { 
	$texto.="=>".$i."<br>";
}
$dompdf->loadHtml($texto);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
// Render the HTML as PDF
$contenido=$dompdf->output();
$nombreDelDocumento = "files/hola.pdf";
file_put_contents($nombreDelDocumento, $contenido);

header("Content-type: application/pdf");
header("Content-Disposition: inline; filename=documento.pdf");
readfile($nombreDelDocumento);

/*
$nombreDelDocumento = "files/hola.pdf";
file_put_contents($nombreDelDocumento, $contenido);
*/
 ?>