* installation instructions
 - `git https://github.com/ligrevx/foswvs-php.git foswvs`
 - `cd foswvs`
 - `sudo apt install nginx php-fpm php-sqlite3 isc-dhcp-server`
 - `sudo dpkg -i conf/wiringpi-latest.deb`
 - `sudo cp foswvs.service /lib/systemd/system/foswvs.service`
 - `sudo systemctl enable foswvs.service`
 - `sudo reboot`
