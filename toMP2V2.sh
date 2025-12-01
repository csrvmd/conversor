#!/bin/bash

ARCHIVO="$1"
IN="/var/www/html/conversor/IN/"
TEMP="/var/www/html/conversor/TEMP/"
OUT="/var/www/html/conversor/OUT/"

if [ -z "$ARCHIVO" ]; then
  exit 1
fi

if [ ! -f "$IN$ARCHIVO" ]; then
  exit 2
fi

mv "$IN$ARCHIVO" "$TEMP"
cd "$TEMP"
OUTNAME="${ARCHIVO%.*}"
DEST="$OUT$OUTNAME.mus"

# ffmpeg conversion
ffmpeg -y -i "$TEMP$ARCHIVO" -acodec mp2 -ab 256k -ac 2 -ar 48000 "$OUT$OUTNAME.mp2"
FF=$?
rm -f "$TEMP$ARCHIVO"
if [ $FF -ne 0 ]; then
  exit 3
fi

mv "$OUT$OUTNAME.mp2" "$DEST"
exit 0