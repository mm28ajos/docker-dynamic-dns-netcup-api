<?php
/**
 * Load's all available environment variables and overrides settings from config file.
 */
function getEnvironmentVariables()
{
	if ($customnr = getenv('CUSTOMERNR')) {
		$config_array['CUSTOMERNR'] = $customnr;
	}

	if ($apikey = getenv('APIKEY')) {
		$config_array['APIKEY'] = $apikey;
	}

	if ($apipassword = getenv('APIPASSWORD')) {
		$config_array['APIPASSWORD'] = $apipassword;
	}

	if ($domain = getenv('DOMAIN')) {
		$config_array['DOMAIN'] = $domain;
	}

	if ($hostipv4 = getenv('HOST_IPv4')) {
		$config_array['HOST_IPv4'] = $hostipv4;
	}

	if ($hostipv6 = getenv('HOST_IPv6')) {
		$config_array['HOST_IPv6'] = $hostipv6;
	}

	if ($useipv4 = getenv('USE_IPV4')) {
		$config_array['USE_IPV4'] = $useipv4;
	}

	if ($usefb = getenv('USE_FRITZBOX')) {
		$config_array['USE_FRITZBOX'] = $usefb;
	}

	if ($fbip = getenv('FRITZBOX_IP')) {
		$config_array['FRITZBOX_IP'] = $fbip;
	}

	if ($useipv6 = getenv('USE_IPV6')) {
		$config_array['USE_IPV6'] = $useipv6;
	}

	if ($ipv6interface = getenv('IPV6_INTERFACE')) {
		$config_array['IPV6_INTERFACE'] = $ipv6interface;
	}

	if ($ipv6priv = getenv('NO_IPV6_PRIVACY_EXTENSIONS')) {
		$config_array['NO_IPV6_PRIVACY_EXTENSIONS'] = $ipv6priv;
	}

	if ($changettl = getenv('CHANGE_TTL')) {
		$config_array['CHANGE_TTL'] = $changettl;
	}

	if ($apiurl = getenv('APIURL')) {
		$config_array['APIURL'] = $apiurl;
	}

	if ($sendmail = getenv('SEND_MAIL')) {
		$config_array['SEND_MAIL'] = $sendmail;
	}

	if ($mailrec = getenv('MAIL_RECIPIENT')) {
		$config_array['MAIL_RECIPIENT'] = $mailrec;
	}

	if ($sleepinsec = getenv('SLEEP_INTERVAL_SEC')) {
		$config_array['SLEEP_INTERVAL_SEC'] = $sleepinsec;
	}
}

// get error logging function and load config
require_once 'functions.php';

// load and override config set by environment variables
getEnvironmentVariables();

// check for updates every SLEEP_INTERVAL_SEC, see config
if (is_numeric($config_array['SLEEP_INTERVAL_SEC'])) {
	while (true) {
	        require 'update.php';
		sleep($config_array['SLEEP_INTERVAL_SEC']);
	}
} else {
	outputStderr("SLEEP_INTERVAL_SEC is not a number: ".$config_array['SLEEP_INTERVAL_SEC']);
}
?>
