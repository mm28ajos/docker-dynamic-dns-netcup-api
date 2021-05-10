FROM debian:stable-slim

ARG version=v4.3.0
ARG PHP_INI_DIR=/etc/php/7.3/cli

WORKDIR /usr/src/dynamic-dns-netcup-api/

RUN apt-get update && apt-get install -y \
        iproute2 msmtp git php-cli php-curl tzdata \
    && echo "sendmail_path = '/usr/bin/msmtp -t'" > ${PHP_INI_DIR}/conf.d/php-mail.ini \
    && git clone -b ${version} https://github.com/mm28ajos/dynamic-dns-netcup-api ./ \
    && apt-get purge -y git && apt autoremove -y && rm -rf .git LICENSE README.md Dockerfile .github

COPY update-docker.php ./

CMD [ "php", "./update-docker.php", "--quiet"]
