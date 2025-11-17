#!/bin/bash
# Uso: bash toMP2.sh filename
ARCHIVO="$1"
IN="/var/www/html/conversor/IN/"
TEMP="/var/www/html/conversor/TEMP/"
OUT="/var/www/html/conversor/OUT/"
ERR="/var/www/html/conversor/SCRIPTS/errores.log"

if [ -z "$ARCHIVO" ]; then
  echo "$(date) - Error: Sin archivo" >> "$ERR"
  exit 1
fi

if [ ! -f "$IN$ARCHIVO" ]; then
  echo "$(date) - Error: Archivo no encontrado $IN$ARCHIVO" >> "$ERR"
  exit 2
fi

mv "$IN$ARCHIVO" "$TEMP"
cd "$TEMP"
OUTNAME="${ARCHIVO%.*}"
DEST="$OUT$OUTNAME.mus"

# ffmpeg conversion
ffmpeg -y -i "$TEMP$ARCHIVO" -acodec mp2 -ab 256k -ac 2 -ar 48000 "$OUT$OUTNAME.mp2" >>"$ERR" 2>&1
FF=$?
rm -f "$TEMP$ARCHIVO"
if [ $FF -ne 0 ]; then
  echo "$(date) - Error ffmpeg $ARCHIVO -> $OUTNAME.mp2" >> "$ERR"
  exit 3
fi

mv "$OUT$OUTNAME.mp2" "$DEST"
exit 0
