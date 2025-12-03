<!DOCTYPE html>
<html lang="es">
<head>
  
  <link rel="icon" href="icono.png" sizes="64x64" type="image/png" />
  <link href="estilo.css" rel="stylesheet" type="text/css">
  <title>Conversor-MA</title>
</head>
<body>
<div class="container hyphenation text-justified" lang="es">
<div align="left"><img src="canalsur.svg" width="200px">
<a class="mi-boton" href="TV.php">Compartir</a>
<a class="mi-boton" href="index.php">Inicio</a>
<br />
<?php 
echo "Registro de uso desde tu equipo: {$_SERVER['REMOTE_ADDR']}";
?>
</div>
<h1>Extraer mejor video de internet:</h1>
<p>Para realizar esta operación deberás copiar la URL del video y pegarla
   dentro de la caja del siguiente formulario.
   El proceso puede tardar, dependiendo del tamaño del video.
   Al finalizar el proceso estará disponible para descarga en el mejor formato disponible.
</p>
</div>
<form action="mejorvideo.php" method="POST">
  <fieldset>
     <legend >Descargar Audio de Internet</legend>
      <input name="url" placeholder= "Copia aqui la URL del video" id="url" type="url">
      <input value="Descargar" type= "submit" name="Descargar">
</fieldset>
</form>
<div class="container hyphenation text-justified" lang="es">
<h1>Videos Descargados:</h1>
<p>Listado por orden alfabético de los videos descargados.
   Recuerda que se eliminarán transcurridas 24 horas.
   Pincha sobre el icono <img src="negro.png" style="width:15px;"> de  descarga para guardar en tu equipo.</p>
</div>
<form>
<fieldset> <legend >Archivos disponibles:</legend>
<?php
// Borramos archivos con más de 24 horas
$files = glob('VIDEO/*'); // obtenemos los nombres de los ficheros
foreach($files as $file){
    $lastModifiedTime = filemtime($file);
    $currentTime = time();
    $timeDiff = abs($currentTime - $lastModifiedTime)/(60*60); // En horas
    if(is_file($file) && $timeDiff > 24)
    unlink($file); // Elimino el archivo
}
?>
<?php
// Mostramos los archivos ya convertidos  de la carpeta VIDEO con icono de descarga
$files = scandir("VIDEO");
for ($a = 2; $a < count($files); $a++)
{
   ?>
    <p>
        <?php echo $files[$a]; ?>
        <a  href="VIDEO/<?php echo $files[$a]; ?>" download="<?php echo $files[$a];?>">
            <img src="negro.png" style="width:15px;"> </a>
    </p>
    <?php
}

?>
</fieldset>
</form>

</body>
</html>

