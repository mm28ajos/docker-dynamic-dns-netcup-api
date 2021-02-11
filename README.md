# Dynamic DNS client for netcup DNS API
*This project is not affiliated with the company netcup GmbH. Although it is developed by an employee, it is not an official client by netcup GmbH and was developed in my free time.*
*netcup is a registered trademark of netcup GmbH, Karlsruhe, Germany.*

**A simple dynamic DNS client written in PHP for use with the netcup DNS API.**

## Requirements
* Be a netcup customer: https://www.netcup.de – or for international customers: https://www.netcup.eu
  * You don't have to be a domain reseller to use the necessary functions for this client – every customer with a domain may use it.
* netcup API key and API password, which can be created within your CCP at https://ccp.netcup.net
* A domain :wink:

## Features
### Implemented
* All necessary API functions for DNS actions implemented (REST API)
* Determines correct public IP addresses (IPv4 and IPv6). Using local adapter for determining IPv6 address and local FritzBox or public API for determining the IPv4 address.
* Caching the IP provided to netcup DNS to avoid unnecessary API calls
* Updating of a single/multiple specific subdomain, domain root, or multiple subdomains
* configure hosts for IPv4 and IPv6 separately
* Creation of DNS record, if it doesn't already exist
* If configured, lowers TTL to 300 seconds for the domain on each run, if necessary

## Getting started
### Docker Compose
Create a docker compose file, see an example below. Note, the network_mode must be host. Add the configuration files for the update script and msmtprc, if mail notification should be used, as volumes. Refer to exmaple configuration below.

```
version: '2.2'
services:
  dynamic-dns-netcup-updater:
    image: mm28ajos/dynamic-dns-netcup-api:latest
    volumes:
      - /path/config.php:/usr/src/dynamic-dns-netcup-api/config.php
      - /path/msmtprc.conf:/root/.msmtprc
    network_mode: host
    restart: unless-stopped
```
### Configuration

Mount the scrip configuration to **/usr/src/dynamic-dns-netcup-api/config.php**, refer to example below.

```
<?php
// Enter your netcup customer number here
define('CUSTOMERNR', '12345');

//Enter your API-Key and -Password here - you can generate them in your CCP at https://ccp.netcup.net
define('APIKEY', 'abcdefghijklmnopqrstuvwxyz');
define('APIPASSWORD', 'abcdefghijklmnopqrstuvwxyz');

// Enter Domain which should be used for dynamic DNS
define('DOMAIN', 'mydomain.com');

//Enter subdomain(s) to be used for dynamic DNS IPv4, alternatively '@' for domain root or '*' for wildcard. If the record doesn't exist, the script will create it.
define('HOST_IPv4', 'server.example.com,server1.example.com');

//Enter subdomain(s) to be used for dynamic DNS IPv6, alternatively '@' for domain root or '*' for wildcard. If the record doesn't exist, the script will create it.
define('HOST_IPv6', 'server.example.com,server1.example.com');

//Activate IPv4 update
define('USE_IPV4', true);

//Should the script try to get the public IPv4 from your FritzBox?
define('USE_FRITZBOX', false);

//IP of the Fritz Box. You can use default fritz.box
define('FRITZBOX_IP', 'fritz.box');

//If set to true, the script will check for your public IPv6 address too and add it as an AAAA-Record / change an existing AAAA-Record for the host.
//Activate this only if you have IPv6 connectivity, or you *WILL* get errors.
define('USE_IPV6', false);

//Required if using IPv6: The interface to get the IPv6 address from
define('IPV6_INTERFACE', 'eth0');

//Shall only IPv6 addresses be set in the AAAA record which have a static EUI-64-Identifier (no privacy extensions)?
define('NO_IPV6_PRIVACY_EXTENSIONS', true);

//If set to true, this will change TTL to 300 seconds on every run if necessary.
define('CHANGE_TTL', true);

// Use netcup DNS REST-API
define('APIURL', 'https://ccp.netcup.net/run/webservice/servers/endpoint.php?JSON');

// Send an email on errors and warnings. Requires the 'sendmail_path' to be set in php.ini.
define('SEND_MAIL', false);

// Recipient mail address for error and warnings
define('MAIL_RECIPIENT', 'user@domain.tld');

// Define the interval to wait before checking for a new IP again
define('SLEEP_INTERVAL_SEC', 5);
?>
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
