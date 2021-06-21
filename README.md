# Dockernized dynamic DNS client for netcup DNS API

![Docker Pulls](https://img.shields.io/docker/pulls/mm28ajos/docker-dynamic-dns-netcup-api.svg
![Docker Build](https://github.com/mm28ajos/docker-dynamic-dns-netcup-api/actions/workflows/build-images.yml/badge.svg) 

**A dockernized dynamic DNS client written in PHP for use with the netcup DNS API.** This project is a fork of https://github.com/stecklars/dynamic-dns-netcup-api, refer to https://github.com/mm28ajos/dynamic-dns-netcup-api.

## Features
* Determines public IP addresses (IPv4 and IPv6) without external third party look ups.
    * using local adapter for IPv6
    * using local FritzBox for IPv4. Note, using external service for determining the IPv4 addresses is possible if no fritz box is available or as a fallback
* setting to choose to only consider ipv6 addresses without privacy extensions for ipv6 (SLAAC)
* Caching the IP provided to netcup DNS to avoid unnecessary API calls
* Updating of a specific or multiple subdomains or domain root
* E-Mail alert in case updating/getting new IP addresses runs in warnings/errors
* configure hosts for updating IPv4 and IPv6 separately
* Creation of DNS record, if it does not already exist for the subdomain given
* If configured, lowers TTL to 300 seconds for the domain on each run if necessary
* Restart docker containers on IP address change (requires docker socket to be exposed to container)

## Requirements
* Be a netcup customer: https://www.netcup.de – or for international customers: https://www.netcup.eu
* You don't have to be a domain reseller to use the necessary functions for this client – every customer with a domain may use it.
* netcup API key and API password, which can be created within your CCP at https://ccp.netcup.net
* A domain :wink:

## Getting started
### Docker Compose
Create a docker compose file, see an example below. Note, the network_mode must be host in order to allow for retrieval of the ipv6 address from local adapter. For debuggig, you may override the default command, refer to example below. Additionaly, note, the docker socket must be added as volume to the container if you want to restart other containers in case the ipv6 address has changed. Add the configuration files for the update script and msmtprc, if mail notification should be used, as volumes. Refer to exmaple configuration below. Set time zone by TZ environment variable.

Alternativly, use environment variables for the script settings, see next section.

```
# only settings from config files
version: '2.2'
services:
  dynamic-dns-netcup-updater:
    image: mm28ajos/docker-dynamic-dns-netcup-api:latest
    volumes:
      - /path/config.ini:/usr/src/dynamic-dns-netcup-api/config.ini
      - /path/msmtprc.conf:/root/.msmtprc
      # required if you want to restart containers if ip address cahnged
      - /var/run/docker.sock:/var/run/docker.sock
    network_mode: host
    # if you want the container to be verbose, override the default command with this one below
    command: php ./update-docker.php

    environment:
      - TZ=Europe/Berlin
    restart: unless-stopped
```

```
# all settings via environment variables
version: '2.2'
services:
  dynamic-dns-netcup-updater:
    image: mm28ajos/docker-dynamic-dns-netcup-api:latest
    volumes:
      - /path/msmtprc.conf:/root/.msmtprc
    network_mode: host
    environment:
      - TZ=Europe/Berlin
      - CUSTOMERNR = 12345
      - APIKEY = abcdefghijklmnopqrstuvwxyz
      - APIPASSWORD = abcdefghijklmnopqrstuvwxyz
      - DOMAIN = mydomain.com
      - USE_IPV4 = true
      - HOST_IPv4 = sub.subdomainA, server1.subdomainC
      - USE_FRITZBOX = false
      - FRITZBOX_IP = fritz.box
      - USE_IPV6 = false
      - HOST_IPv6 = sub.subdomainB, server1.subdomainB
      - IPV6_INTERFACE = eth0
      - NO_IPV6_PRIVACY_EXTENSIONS = true
      - CHANGE_TTL = true
      - SEND_MAIL = false
      - MAIL_RECIPIENT = user@domain.tld
      - SLEEP_INTERVAL_SEC = 5
      - RESTART_CONTAINERS = true
      - CONTAINERS = containerA, ContainerB
    restart: unless-stopped
```
### Configuration

Mount the scrip configuration to **/usr/src/dynamic-dns-netcup-api/config.ini**, refer to example below or set any of the settings as environment variable in docker-compose. Note, environment settings override settings on the mounted php ini-file.

```
; Enter your netcup customer number here
CUSTOMERNR = 12345

; Enter your API-Key and -Password here - you can generate them in your CCP at https://ccp.netcup.net
APIKEY = abcdefghijklmnopqrstuvwxyz
APIPASSWORD = abcdefghijklmnopqrstuvwxyz

; Enter Domain which should be used for dynamic DNS
DOMAIN = mydomain.com


; Activate IPv4 update
USE_IPV4 = true

; Required if USE_IPV4 = true. Enter subdomain(s) to be used for dynamic DNS IPv4 (seperated by comma), alternatively '@' for domain root or '*' for wildcard. If the record doesn't exist, the script will create it.
HOST_IPv4 = sub.subdomainA, server1.subdomainC

; Should the script try to get the public IPv4 from your FritzBox?
USE_FRITZBOX = false

; Required if USE_FRITZBOX = true. IP of the Fritz Box. You can use default fritz.box
; FRITZBOX_IP = fritz.box


; If set to true, the script will check for your public IPv6 address too and add it as an AAAA-Record / change an existing AAAA-Record for the host.
; Activate this only if you have IPv6 connectivity, or you *WILL* get errors.
USE_IPV6 = false

; Required if USE_IPV6 = true. Enter subdomain(s) to be used for dynamic DNS IPv6 (seperated by comma), alternatively '@' for domain root or '*' for wildcard. If the record doesn't exist, the script will create it.
; HOST_IPv6 = sub.subdomainB, server1.subdomainB

; Required if USE_IPV6 = true. The interface to get the IPv6 address from
; IPV6_INTERFACE = eth0

; Required if USE_IPV6 = true. Shall only IPv6 addresses be set in the AAAA record which have a static EUI-64-Identifier (no privacy extensions)?
; NO_IPV6_PRIVACY_EXTENSIONS = true


; If set to true, this will change TTL to 300 seconds on every run if necessary.
CHANGE_TTL = true

; Send an email on errors and warnings. Requires the 'sendmail_path' to be set in php.ini
SEND_MAIL = false

; Required if SEND_MAIL = true. Recipient mail address for error and warnings
; MAIL_RECIPIENT = user@domain.tld

; Define the interval to wait before checking for a new IP again
SLEEP_INTERVAL_SEC = 5

; If true, restarts all docker containers defined by setting "CONTAINERS" if IP has changed. Note, requires docker socket to be exposed to host.
RESTART_CONTAINERS = false

; Required if RESTART_CONTAINERS = true. Name all docker containers to restart if ip address changed.
; CONTAINERS = containerA,containerB
```

Mount the mail configuration, if required, to **/root/.msmtprc**, refer to example below.

```
# Set defaults.
defaults

# Enable or disable TLS/SSL encryption.
auth on
tls on
tls_starttls off
tls_certcheck on

# Set up a default account's settings.
account default
add_missing_from_header on
host "smtp.domain.tld"
port 465
domain "domain.tld"
maildomain "domain.tld"
user username
password "password"
from "user@domain.tld"
```
