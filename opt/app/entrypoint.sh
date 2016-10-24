#!/bin/bash

# normally we would not want this to be here
apt-get update && apt-get install netcat cron git zip wget -y


bin/composer.phar install


if [ ! -d /root/.acme.sh ];
then
    wget -O -  https://get.acme.sh | sh
fi

/root/.acme.sh/acme.sh --issue --standalone -k 4098 -d test.strayobject.co.uk --post-hook "/var/www/html/vendor/amphp/aerys/bin/aerys --restart -c /var/www/html/server.php"

php -d zend.assertions=-1 vendor/amphp/aerys/bin/aerys -c server.php
