<!DOCTYPE html>
<html lang="es">
<head>
  
  <link rel="icon" href="icono.png" sizes="64x64" type="image/png" />
  <link href="estilo.css" rel="stylesheet" type="text/css">
  <title>Intercambiador-MA</title>
</head><body>
<div class="container hyphenation text-justified" lang="es">
<div align="left"><img src="canalsur.svg" width="200px">
<a class="mi-boton" href="avanzado.php">Video</a>
<a class="mi-boton" href="index.php">Inicio</a><br />
<?php
echo "Registro de uso desde tu equipo: {$_SERVER['REMOTE_ADDR']}";
?>
</div>
<h1>Intercambio de archivos Radio/Tv:</h1>
<p>Permite el intercambio de cualquier archivo multimedia (audio/video) entre diferentes departamentos. 
   Al pulsar sobre el botóm "Examinar" se abrirá nuestro explorador de archivos para su selección. 
   Los archivos intercambiados estarán disponibles durante un plazo de 24 horas.</p>
</div>

<form enctype="multipart/form-data" action="" method="POST">
   <fieldset> 
      <legend >Intercambiador</legend>
       <input name="subir_archivo" id="subir_archivo" type="file" accept="audio/*,video/*"> 
       <input type="submit" name="Compartir" value="Compartir">

<?php
// Espera que pulsemos botón
if(isset($_POST['Compartir'])){
// Directorio de entrada
$directorio = 'COMPARTIDOS/';
// Subimos el archivo a la carpeta COMPARTIDOS
$subir_archivo = $directorio.basename($_FILES['subir_archivo']['name']);
if (move_uploaded_file($_FILES['subir_archivo']['tmp_name'], $subir_archivo)) {
      echo "<span style='color:green;'>El archivo: ". basename( $_FILES['subir_archivo']['name']). " se cargó correctamente.";
    } else {
       echo "<span style='color:green;'>Algo ha fallado, inténtalo de nuevo!";
    }
    }
?>
   </fieldset>
</form>

<div class="container hyphenation text-justified" lang="es">
<h1>Archivos compartidos:</h1>
<p>Listado por orden alfabético de los archivos multimedia (audio/video) compartidos.
   Recuerda que se eliminarán transcurridas 24 horas. 
   Pincha sobre el icono <img src="negro.png" style="width:15px;"> de  descarga para guardar en tu equipo.</p>
</div>

<form>
<fieldset> <legend >Archivos disponibles:</legend>
<?php
// Borramos archivos con más de 24 horas
$files = glob('COMPARTIDOS/*'); // obtenemos los nombres de los ficheros
foreach($files as $file){
    $lastModifiedTime = filemtime($file);
    $currentTime = time();
    $timeDiff = abs($currentTime - $lastModifiedTime)/(60*60); // En horas
    if(is_file($file) && $timeDiff > 24)
    unlink($file); // Elimino el archivo
}
?>
<?php
// Mostramos los archivos ya convertidos  de la carpeta OUT con icono de descarga
$files = scandir("COMPARTIDOS");
for ($a = 2; $a < count($files); $a++)
{
   ?>
    <p>
        <?php echo $files[$a]; ?>
        <a  href="COMPARTIDOS/<?php echo $files[$a]; ?>" download="<?php echo $files[$a];?>">
            <img src="negro.png" style="width:15px;"> </a>
    </p>
    <?php
}

?>
</fieldset>
</form>

</body>
</html>
