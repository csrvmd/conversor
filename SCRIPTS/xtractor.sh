#!/bin/bash
YTDL="/var/www/html/conversor/YTDL/"
OUT="/var/www/html/conversor/OUT/"
ERR="/var/www/html/conversor/SCRIPTS/errores.log"

cd "$YTDL"
for i in "$YTDL"*; do
  [ -f "$i" ] || continue
  a=$(basename "$i")
  b="${a%.*}"
  ffmpeg -y -i "$i" -acodec mp2 -ab 256k -ac 2 -ar 48000 "$OUT$b.mp2" >>"$ERR" 2>&1
  FF=$?
  rm -f "$i"
  if [ $FF -ne 0 ]; then
    echo "$(date) - Error ffmpeg $a -> $b.mp2" >> "$ERR"
    continue
  fi
  mv "$OUT$b.mp2" "$OUT$b.mus"
done
