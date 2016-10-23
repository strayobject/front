#!/bin/bash

bin/composer.phar install

# download letsencrypt client
wget -P cert/ https://github.com/kelunik/acme-client/releases/download/v0.2.10/acme-client.phar
chmod +x acme-client.phar
acme-client.phar auto

crontab -l; echo "0 0 * * * cert/acme-client.phar auto; RC=$?; if [ $RC = 4 ] || [ $RC = 5 ]; then vendor/amphp/aerys --restart -c server.php; fi" ) | crontab -

php -d zend.assertions=-1 vendor/amphp/aerys/bin/aerys -c server.php
