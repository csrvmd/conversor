<?php
// Recibimos la URL como argv[1]
if ($argc !== 2) exit("No URL provided\n");
$url = escapeshellarg($argv[1]);
$ytdl_path = __DIR__ . '/YTDL/';
$cache_dir = __DIR__ . '/cacheyotubedl';

if (!is_dir($ytdl_path)) mkdir($ytdl_path, 0770, true);
if (!is_dir($cache_dir)) mkdir($cache_dir, 0770, true);

// yt-dlp: descarga extrayendo audio y nombres restrictivos
$cmd = "yt-dlp --nocheck-certificate -o $ytdl_path/%(title)s.%(ext)s --cache-dir $cache_dir --restrict-filenames -x $url 2>&1";
exec($cmd, $output, $status);

// Log de errores
$logfile = __DIR__ . "/SCRIPTS/errores.log";
$logstring = "[".date('Y-m-d H:i:s')."] yt-dlp $url STATUS:$status \n".implode("\n",$output)."\n";
file_put_contents($logfile, $logstring, FILE_APPEND);

// Si OK, ejecuta conversión
if ($status === 0) {
    exec("bash ".__DIR__."/SCRIPTS/xtractor.sh >> $logfile 2>&1");
}
exit;
?>
