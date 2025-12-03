<?php 
// Ponemos la URL del video a descargar
$url = $_POST['url'];
exec("yt-dlp $url 2>&1", $salida);
echo "Operacion realizada correctamente";
?>
<?php
// Ejecutamos script conversión a mp2
echo exec("/var/www/html/SCRIPTS/xtractor.sh");
// Redireccionamos a la página principal
header("Location: " . $_SERVER["HTTP_REFERER"]);





