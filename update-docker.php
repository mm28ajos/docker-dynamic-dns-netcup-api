<?php
// get config
require_once 'config.php';

// get error logging function
require_once 'functions.php';

if (is_numeric(SLEEP_INTERVAL_SEC)) {
	while (true) {
	        require 'update.php';
		sleep(SLEEP_INTERVAL_SEC);
	}
} else {
	outputStderr("SLEEP_INTERVAL_SEC is not a number: ".SLEEP_INTERVAL_SEC);
}
?>
