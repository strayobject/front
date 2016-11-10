#!/bin/bash

#TODO: move certificate fetching to it's own container
if [ $DEV ]; then
    rm -r var/cache/*
    php -d zend.assertions=1 vendor/amphp/aerys/bin/aerys -d -c server.php
else
    # normally we would not want this to be here
    apt-get update && apt-get install netcat cron git zip wget -y

    bin/composer.phar install

    if [ ! -d /root/.acme.sh ];
    then
        wget -O -  https://get.acme.sh | sh
    fi
    # todo renewal will fail because port is used
    /root/.acme.sh/acme.sh --issue --standalone --httpport 8080 -d strayobject.co.uk \
    --fullchainpath "/var/www/html/cert/fullchain.cer" --keypath "/var/www/html/cert/strayobject.co.uk.key" \
    --renew-hook "/var/www/html/vendor/amphp/aerys/bin/aerys --restart -c /var/www/html/server.php"

    php -d zend.assertions=-1 vendor/amphp/aerys/bin/aerys -c server.php
fi
