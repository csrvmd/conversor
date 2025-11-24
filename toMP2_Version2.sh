#!/bin/bash
# Mueve archivos de IN a TEMP, convierte y finalmente sustituye _ por espacio en los nombres .mus resultantes

IN="/var/www/html/IN/"
TEMP="/var/www/html/TEMP/"
OUT="/var/www/html/OUT/"

[ "$(ls -A "$IN")" ] && mv "$IN"* "$TEMP"
cd "$TEMP"
rename 's/ /_/g' *

[ "$(ls -A "$TEMP")" ] && \
for i in "$TEMP"*; do  
    a=$(basename "$i")
    b=${a%.*}
    ffmpeg -y -i "$i" -acodec mp2 -ab 256k -ac 2 -ar 48000 "$OUT$b.mp2"
    rm "$i"
    mv "$OUT$b.mp2" "$OUT$b.mus"
    # Al final, renombra el archivo final .mus sustituyendo _ por espacio
    original="$OUT$b.mus"
    final="${OUT}$(echo "$b" | sed 's/_/ /g').mus"
    if [[ "$original" != "$final" ]]; then
        mv "$original" "$final"
    fi
done