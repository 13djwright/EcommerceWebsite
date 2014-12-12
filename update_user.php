<?php
	session_start();
	if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
		header("Location:../project/");
	}
	else{ 
			include_once("./config.php"); 
			$liEmail = $_SESSION['email'];
			$liRole = $_SESSION['role'];
			if(!($liRole == "MANAGER")) {
				header("Location: ../project/");
			}
			$email = test_input($_POST["email"]);
			$role = test_input($_POST["role"]);
			$error = "";
			if(!empty($role) && !empty($email)) {
				if(!preg_match("^(CUSTOMER|STAFF|MANAGER)^", $role)) {
					$error = "Error: role input is incorrect.";
				}
				else {
					$stmt = $conn->prepare("update users405 set role=? where email=?");
					$stmt->bind_param("ss", $role, $email);
					$stmt->execute();
					$stmt->fetch();
					$stmt->close();
				}
			}
			else {
				$error = "Error: email or role is empty.";
			}
			header("Location:./users.php");
		}


	function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
	}
?>
