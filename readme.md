# free and open-source wifi vendo software

> backend `http://10.0.0.1/a` password: `admin`

 This software may run on any distros, but i recommend using rpi device and flashing `Raspberry Pi OS Lite`.
 Note: Use php version 7 above.

# installation instructions
 - `sudo apt install nginx php-fpm php-sqlite3 isc-dhcp-server`
 - `visudo` then add `www-data ALL=NOPASSWD: /usr/bin/iptables`
 - `sudo usermod -aG gpio www-data`
 - `git https://github.com/ligrevx/foswvs-php.git /home/pi/foswvs`
 - `cd /home/pi/foswvs`
 - `sudo cp foswvs.service /lib/systemd/system/foswvs.service`
 - `sudo systemctl enable foswvs.service`
 - `sudo reboot`
