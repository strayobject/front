#!/bin/bash

# normally we would not want this to be here
apt-get update && apt-get install cron git zip wget -y


bin/composer.phar install

# download letsencrypt client
if [ ! -f cert/acme-client.phar ];
then
    wget -P cert/ https://github.com/kelunik/acme-client/releases/download/v0.2.10/acme-client.phar
    chmod +x cert/acme-client.phar
fi

cert/acme-client.phar auto

(crontab -l; echo "0 0 * * * /var/www/html/cert/acme-client.phar auto; RC=$?; if [ $RC = 4 ] || [ $RC = 5 ]; then /var/www/html/vendor/amphp/aerys --restart -c server.php; fi" ) | crontab -

php -d zend.assertions=-1 vendor/amphp/aerys/bin/aerys -c server.php
