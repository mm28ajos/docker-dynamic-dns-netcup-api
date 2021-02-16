FROM php:8.0.2-cli

ARG version=v4.1.3

WORKDIR /usr/src/dynamic-dns-netcup-api/

RUN apt-get update && apt-get install -y \
        iproute2 msmtp  git \
    && echo "sendmail_path = '/usr/bin/msmtp -t'" > $PHP_INI_DIR/conf.d/php-mail.ini \
    && git clone -b ${version} https://github.com/mm28ajos/dynamic-dns-netcup-api ./ \
    && apt-get purge -y git && rm -rf .git LICENSE README.md Dockerfile .github

COPY update-docker.php ./

CMD [ "php", "./update-docker.php", "--quiet" ]
