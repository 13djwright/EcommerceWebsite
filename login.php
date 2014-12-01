<html>
	<head>
		<title>S&W Login</title>
		<link rel="stylesheet" type="text/css" href="./stylesheet.css"/>
	</head>
	<body>
		<?php
		$error = $email = $password = "";
		include_once("./config.php");
		if($_SERVER["REQUEST_METHOD"] == "POST") {

		if(empty($_POST["email"]) || empty($_POST["password"])) {
			$error = "Email and Password required";
		}
		else {
			$email = test_input($_POST["email"]);
			$password = test_input($_POST["password"]);
			$stmt = $conn->prepare("SELECT firstName, email, role FROM users WHERE email=? AND password=?");
			$stmt->bind_param("ss",$email,$password);
			$stmt->execute();
			$stmt->bind_result($firstName, $dbemail, $role);
			$stmt->fetch();
			$stmt->close();
			if($firstName && $dbemail) {
				session_start();
				$_SESSION['firstName'] = $firstName;
				$_SESSION['email'] = $dbemail;
				$_SESSION['role'] = $role;
				header("Location:../project/");
			}
			else {
				$error = "Email and/or Password were incorrect. Please retry.";
			}
		}


		}

		function test_input($data) {
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			return $data;
		}


		?>
		<div class="login">
			<span class="error"><?php echo $error; ?></span>
			<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
				<label>Email: </label><input type="email" name="email"value="<?php echo $email;?>">
				<label>Password: </label><input type="password" name="password">
				<div class="buttons">
					<input type="submit" name="submit" value="Login">
					<a href="../project/">
						<input type="button" value="Cancel">
					</a>
				</div>
			</form>
		</div>
	</body>
</html>
