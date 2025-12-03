#!/bin/bash
[ "$(ls -A /var/www/html/IN)" ] && 
mv  /var/www/html/IN/* /var/www/html/TEMP/
cd /var/www/html/TEMP/
[ "$(ls -A /var/www/html/TEMP)" ] && 
for i in /var/www/html/TEMP/*; do  
        a=$(basename "$i")
        b=${a%.*}
        ffmpeg -y -i "$i" -acodec mp2 -ab 256k -ac 2 -ar 48000 "/var/www/html/OUT/$b.mp2"; 
	rm  "/var/www/html/TEMP/$a";
	mv "/var/www/html/OUT/$b.mp2" "/var/www/html/OUT/$b.mus";
        done
