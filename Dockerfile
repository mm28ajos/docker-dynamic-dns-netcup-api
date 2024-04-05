FROM debian:stable-slim

ARG version=v4.5.0

WORKDIR /usr/src/dynamic-dns-netcup-api/

RUN apt-get update && apt-get upgrade -y && apt-get install -y \
        iproute2 msmtp git php-cli php-curl tzdata curl \
    && PHPVERSION=$(php -v | head -n 1 | cut -c5-7) \
    && echo "sendmail_path = '/usr/bin/msmtp -t'" > /etc/php/${PHPVERSION}/cli/conf.d/php-mail.ini \
    && git clone -b ${version} https://github.com/mm28ajos/dynamic-dns-netcup-api ./ \
    && apt-get purge -y git && apt autoremove -y && rm -rf .git LICENSE README.md Dockerfile .github

RUN curl -fsSL https://get.docker.com -o get-docker.sh && \
    sh get-docker.sh && rm get-docker.sh && apt purge -y docker-ce docker-ce-rootless-extras curl && apt autoremove -y

COPY update-docker.php ./

CMD [ "php", "./update-docker.php", "--quiet"]
