<?php
// Ponemos la URL del video a descargar
$url = $_POST['url'];
exec("yt-dlp --ignore-config --no-mtime --cache-dir /var/www/html/cacheytdl --no-check-certificate -f best -o /var/www/html/VIDEO/'%(title)s.%(ext)s' $url 2>&1", $salida);
echo "Operacion realizada correctamente";
?>
<?php
// Redireccionamos a la página principal
header("Location: avanzado.php");
