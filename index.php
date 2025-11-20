<?php
// Todos los directorios relativos a /var/www/html/conversor/
define('IN_DIR', __DIR__ . '/IN/');
define('OUT_DIR', __DIR__ . '/OUT/');
define('TEMP_DIR', __DIR__ . '/TEMP/');
define('YTDL_DIR', __DIR__ . '/YTDL/');
define('LOG_DIR', __DIR__ . '/logs/');
define('MAX_UPLOAD', 100 * 1024 * 1024); // 100MB

// Detección y registro de IP, fecha, acción
function registrar_log($accion, $archivo = null, $estado = 'OK') {
    $ip = $_SERVER['REMOTE_ADDR'];
    $semana = date('o_W'); // semana ISO: año_semananúmero
    $logfile = LOG_DIR . "registro_$semana.log";
    $fecha = date('Y-m-d H:i:s');
    $line = "$fecha;$ip;$accion;" . ($archivo ? "$archivo;" : "") . "$estado\n";
    file_put_contents($logfile, $line, FILE_APPEND | LOCK_EX);
}

// Limpiar logs antiguos
foreach (glob(LOG_DIR . "registro_*.log") as $file) {
    if (filemtime($file) < time() - 60*60*24*62) unlink($file); // Borra logs >2 meses
}

// Messages control AJAX/POST error
function mostrar_mensaje($txt) {
    echo "<div class='mensaje' id='msg'>$txt</div>";
}

// FILE UPLOAD HANDLING
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Convertir'])) {
    // Upload validation
    if (!isset($_FILES['subir_archivo']) || $_FILES['subir_archivo']['error'] != 0) {
        mostrar_mensaje("No se ha podido subir el archivo. Inténtalo de nuevo.");
        registrar_log('CONVERTIR_UPLOAD', '', 'FALLO_SUBIDA');
    } elseif ($_FILES['subir_archivo']['size'] > MAX_UPLOAD) {
        mostrar_mensaje("El archivo supera el límite de 100MB.");
        registrar_log('CONVERTIR_UPLOAD', $_FILES['subir_archivo']['name'], 'FALLO_TAM');
    } else {
        // MIME strict check
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['subir_archivo']['tmp_name']);
        finfo_close($finfo);
        if (!preg_match('/^(audio|video)\//i', $mime)) {
            mostrar_mensaje("Solo se permiten archivos multimedia (audio/video).");
            registrar_log('CONVERTIR_UPLOAD', $_FILES['subir_archivo']['name'], 'FALLO_MIME');
        } else {
            // Saneamos nombres y subimos
            $nombre = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES['subir_archivo']['name']));
            $destino = IN_DIR . $nombre;
            if (move_uploaded_file($_FILES['subir_archivo']['tmp_name'], $destino)) {
                mostrar_mensaje("Archivo subido correctamente. Iniciando conversión...");
                registrar_log('CONVERTIR_UPLOAD', $nombre);
                // Lanza el script en segundo plano y log
                exec("nohup bash ".__DIR__."/SCRIPTS/toMP2.sh '$nombre' > ".__DIR__."/SCRIPTS/errores.log 2>&1 &");
            } else {
                mostrar_mensaje("Algo ha fallado, inténtalo de nuevo.");
                registrar_log('CONVERTIR_UPLOAD', $nombre, 'FALLO_MOVE');
            }
        }
    }
}

// VIDEO EXTRACT sin comprobación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Descargar'])) {
    $url = trim($_POST['url']);
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        mostrar_mensaje("La URL del vídeo no es válida.");
        registrar_log('DESCARGA_URL', '', 'FALLO_URL');
    } else {
        mostrar_mensaje("Descarga en curso. Esto puede tardar unos minutos.");
        registrar_log('DESCARGA_URL', $url, 'INICIO');
        // Ejecuta el youtube.php en segundo plano
        exec("nohup php ".__DIR__."/youtube.php '$url' > ".__DIR__."/SCRIPTS/errores.log 2>&1 &");
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Conversor multimedia - Extraer y convertir audio</title>
  <link rel="shortcut icon" href="icono.png" type="image/png">
  <link rel="stylesheet" href="estilo.css">
  <script>
    // Mensajes desaparecerán tras unos segundos
    window.onload = function() {
      let msg = document.getElementById('msg');
      if (msg) setTimeout(() => {msg.style.display='none';}, 4000);
    };
  </script>
</head>
<body>
<div class="container">
  <img src="canalsur.svg" style="height:40px;width:200px;float:left;">
  <div style="margin-left:220px;">
    <p>IP actual: <?= $_SERVER['REMOTE_ADDR']; ?></p>
    <h1>Extraer y convertir el audio de videos de internet:</h1>
    <div class="text-justified">Para realizar esta operación deberás copiar la URL del video y pegarla dentro de la caja del siguiente formulario.
    El proceso puede tardar, dependiendo del tamaño del video. Al finalizar el proceso estará disponible para descarga en formato mus.</div>
    <form action="index.php" method="POST">
      <fieldset>
         <legend >Descargar Audio de Internet</legend>
          <input name="url" placeholder= "Copia aqui la URL del video" id="url" type="url" required>
          <input value="Descargar" type= "submit" name="Descargar">
      </fieldset>
    </form>
    <h1>Conversor multimedia:</h1>
    <div class="text-justified">Permite la conversión de cualquier archivo multimedia (audio/video) en un archivo de audio.
      Al pulsar sobre el botón "Examinar" se abrirá nuestro explorador de archivos para su selección.
      Al finalizar el proceso estará disponible para descarga en formato mus.</div>
    <form enctype="multipart/form-data" action="index.php" method="POST">
      <fieldset>
        <legend>Conversor de audio/video a audio mp2</legend>
        <input name="subir_archivo" id="subir_archivo" type="file" accept="audio/*,video/*" required>
        <input type="submit" name="Convertir" value="Convertir">
      </fieldset>
    </form>
    <h1>Audios convertidos disponibles:</h1>
    <div class="text-justified">Listado por orden de descarga de los audios disponible. Los archivos se eliminarán transcurridas 24 horas. Pincha sobre la flecha del archivo que quieras descargar en tu equipo.</div>
    <form>
      <fieldset>
        <legend>Archivos disponibles:</legend>
        <?php
        // Borra .mus OUT >24h
        foreach (glob(OUT_DIR . "*.mus") as $f) {
          if(is_file($f) && (time()-filemtime($f))/(60*60) > 24) unlink($f);
        }
        // Listado ordenado por fecha DESC
        $files = glob(OUT_DIR."*.mus");
        usort($files, function($a, $b) { return filemtime($b)-filemtime($a); });
        foreach ($files as $path) {
          $fn = basename($path);
          echo "<p>$fn <a href='OUT/$fn' download='$fn'><img src='negro.png' style='width:15px;'></a></p>";
        }
        ?>
      </fieldset>
    </form>
  </div>
</div>
</body>
</html>
