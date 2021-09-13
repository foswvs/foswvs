# free and open-source wifi vendo software

> preview https://foswvs.github.io/preview/
> backend http://10.0.0.1/a/

 This software may run on any distros, but i recommend using rpi device and flashing `Raspberry Pi OS Lite`.

 Note: Use php version 7 above.

# installation instructions
STEP 1:
 - download from https://www.raspberrypi.org/software/operating-systems/
 - flash `Raspberry Pi OS Lite` on your SDCard; and
 - in /boot directory add empty file named `ssh`
 - connect to `ssh pi@raspberrypi` using the password `raspberry` - don't forget to change the default password of your device.
 
STEP 2:
 - `sudo apt install -y nginx php-fpm php-sqlite3 isc-dhcp-server bind9 git`
 - `visudo` then add `www-data ALL=NOPASSWD: /usr/bin/iptables`
 - `sudo usermod -aG gpio www-data`
 - `git clone https://github.com/foswvs/foswvs.git /home/pi/foswvs`
 - `sudo cp /home/pi/foswvs/foswvs.service /lib/systemd/system/foswvs.service`
 - `sudo systemctl enable foswvs.service`
 - `sudo reboot`
