<?php
	$conn = new mysqli("localhost", "root", "budget", "project");
	if($conn->connect_error) {
		//for the multilab if there was a connect error, try to connect to the backup then if this fails just die (or maybe redirect to the raspberry pi with some notification
	}
?>
