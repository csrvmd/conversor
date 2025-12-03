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
<a class="mi-boton" href="avanzado.php">Video</a>
<a class="mi-boton" href="TV.php">Compartir</a><br />
<?php
echo "Registro de uso desde tu equipo: {$_SERVER['REMOTE_ADDR']}";
?>
<?php
// Limpia y registra accesos con marca de tiempo y solo los de menos de 1 mes

$reg_file = __DIR__ . '/yourfile.txt';
$nueva_ip = $_SERVER['REMOTE_ADDR'];
$ahora = time();
$nueva_fecha = date('Y-m-d-H-i', $ahora);
$nueva_linea = "$nueva_fecha;$nueva_ip\n";

// Leer el fichero existente si existe
$nuevas_lineas = [];
if (file_exists($reg_file)) {
    $lineas = file($reg_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        // Solo líneas correctas con formato fecha;ip
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})-(\d{2})-(\d{2});([0-9a-fA-F:.]+)$/', $linea, $m)) {
            $fecha_linea = strtotime("{$m[1]}-{$m[2]}-{$m[3]} {$m[4]}:{$m[5]}:00");
            if ($fecha_linea !== false && ($ahora - $fecha_linea) < 30*24*60*60) { // menos de 1 mes (~30 días)
                $nuevas_lineas[] = $linea;
            }
        }
    }
}

// Añade la línea actual (siempre al final)
$nuevas_lineas[] = trim($nueva_linea);
// Vuelve a escribir el fichero depurado
file_put_contents($reg_file, implode("\n", $nuevas_lineas) . "\n");
?>
</div>
<h1>Extraer el audio de un video de internet:</h1>
<p>Para realizar esta operación deberás copiar la URL del video y pegarla 
   dentro de la caja del siguiente formulario. 
   El proceso puede tardar, dependiendo del tamaño del video. 
   Al finalizar el proceso estará disponible para descarga en formato mus.
   Puedes cambiar la extensión a mp2 o mp3 para hacerlo compatible con algunos reproductores.</p>
</div>
<form action="youtube.php" method="POST">
  <fieldset> 
     <legend >Descargar Audio de Internet</legend>
      <input name="url" placeholder= "Copia aqui la URL del video" id="url" type="url"> 
      <input value="Descargar" type= "submit" name="Descargar">
</fieldset>
</form>
<div class="container hyphenation text-justified" lang="es">
<h1>Conversor multimedia:</h1>
<p>Permite la conversión de cualquier archivo multimedia (audio/video) en un archivo de audio. 
   Al pulsar sobre el botón "Examinar" se abrirá nuestro explorador de archivos para su selección. 
   Al finalizar el proceso estará disponible para descarga en formato mus.</p>
</div>

<form enctype="multipart/form-data" action="" method="POST">
   <fieldset> 
      <legend >Conversor de audio/video a audio mp2</legend>
       <input name="subir_archivo" id="subir_archivo" type="file" accept="audio/*,video/*"> 
       <input type="submit" name="Convertir" value="Convertir">

<?php
// Espera que pulsemos botón
if(isset($_POST['Convertir'])){
// Directorio de entrada
$directorio = 'IN/';
// Subimos el archivo a la carpeta IN
$subir_archivo = $directorio.basename($_FILES['subir_archivo']['name']);
if (move_uploaded_file($_FILES['subir_archivo']['tmp_name'], $subir_archivo)) {
      echo "<span style='color:green;'>El archivo: ". basename( $_FILES['subir_archivo']['name']). " se cargó correctamente.";
    } else {
       echo "<span style='color:green;'>Algo ha fallado, inténtalo de nuevo!";
    }
    }
// Ejecutamos script de conversión a mp2 y cambiamos a lextensión a .mus. El archivo queda en la carpeta OUT
exec("/var/www/html/SCRIPTS/toMP2.sh");
?>
   </fieldset>
</form>

<div class="container hyphenation text-justified" lang="es">
<h1>Audios convertidos disponibles:</h1>
<p>Listado por orden de fecha descendente de audios disponibles.
   Se eliminarán transcurridas 24 horas. 
   Pincha sobre el icono <img src="negro.png" style="width:15px;"> de  descarga para guardar en tu equipo.</p>
</div>

<form>
<fieldset> <legend >Archivos disponibles:</legend>
<?php
        // Borra .mus OUT >24h
        foreach (glob('OUT/*') as $f) {
          if(is_file($f) && (time()-filemtime($f))/(60*60) > 24) unlink($f);
        }
        // Listado ordenado por fecha DESC
        $files = glob('OUT/*');
        usort($files, function($a, $b) { return filemtime($b)-filemtime($a); });
        foreach ($files as $path) {
          $fn = basename($path);
          echo "<p>$fn <a href='OUT/$fn' download='$fn'><img src='negro.png' style='width:15px;'></a></p>";
        }

?>
</fieldset>
</form>
</body>
</html>
