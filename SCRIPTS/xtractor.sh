
#!/bin/bash
cd /var/www/html/YTDL/
[ "$(ls -A /var/www/html/YTDL)" ] && 
for i in /var/www/html/YTDL/*; do  
        a=$(basename "$i")
        b=${a%.*}
        ffmpeg -y -i "$i" -acodec mp2 -ab 256k -ac 2 -ar 48000 "/var/www/html/OUT/$b.mp2"; 
	rm  "/var/www/html/YTDL/$a";
	mv "/var/www/html/OUT/$b.mp2" "/var/www/html/OUT/$b.mus";
	done
