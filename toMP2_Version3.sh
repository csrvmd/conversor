#!/bin/bash
IN="/var/www/html/IN/"
TEMP="/var/www/html/TEMP/"
OUT="/var/www/html/OUT/"

[ "$(ls -A "$IN" 2>/dev/null)" ] && mv "$IN"* "$TEMP"
cd "$TEMP" || exit 1
rename 's/ /_/g' *

[ "$(ls -A "$TEMP" 2>/dev/null)" ] && \
for i in "$TEMP"*; do  
    [ -f "$i" ] || continue
    a=$(basename "$i")
    b=${a%.*}
    ffmpeg -y -i "$i" -acodec mp2 -ab 256k -ac 2 -ar 48000 "$OUT$b.mp2"
    rm "$i"
    mv "$OUT$b.mp2" "$OUT$b.mus"
done

# --- SUSTITUIR GUIONES BAJOS POR ESPACIOS EN LOS .mus FINALIZADOS ---
cd "$OUT" || exit 1
for musfile in *.mus; do
    [ -f "$musfile" ] || continue
    # Obtiene nuevo nombre con espacios en vez de guiones bajos
    nombre_con_espacios="$(echo "$musfile" | sed 's/_/ /g')"
    # Solo renombra si el nombre cambia
    if [ "$musfile" != "$nombre_con_espacios" ]; then
        mv -- "$musfile" "$nombre_con_espacios"
    fi
done