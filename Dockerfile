FROM php:8.0.2-cli

WORKDIR /usr/src/dynamic-dns-netcup-api/

RUN apt-get update && apt-get install -y \
        iproute2 msmtp  git \
    && echo "sendmail_path = '/usr/bin/msmtp -t'" > $PHP_INI_DIR/conf.d/php-mail.ini \
    && cd /usr/src/dynamic-dns-netcup-api/ && git clone https://github.com/mm28ajos/dynamic-dns-netcup-api ./ \
    && apt-get purge -y git && rm config.php

COPY update-docker.php ./

CMD [ "php", "./update-docker.php", "--quiet" ]
