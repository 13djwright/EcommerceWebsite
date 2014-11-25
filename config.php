<?php
$ini_array = parse_ini_file("./mysql.ini");
$servername = $ini_array[servername];
$username = $ini_array[username];
$password = $ini_array[password];
$backup = $ini_array[backup];
$conn = new mysqli($servername, $username, $password, $username);
if ($conn->connect_error) {
        $conn = new mysqli($backup, $username, $password, $username);
        if($conn->connect_error) {
		//neither of the mysql servers are up, redirect maybe?
                die ("Connection to mysql backup failed: " . $conn->connect_error);
        }
}


?>
