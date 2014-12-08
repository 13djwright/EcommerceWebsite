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
		<h1 id="header">S&W Games and Toys</h1>
		<div id="main">
			<div id="links">
				<?php
					if($liEmail){
						$stmt = $conn->prepare("select count(*) from orderDetails D where D.orderID=(select max(id) from orders O where O.userEmail=?)");
						$stmt->bind_param("s", $liEmail);
						$stmt->execute();
						$stmt->bind_result($items_in_basket);
						$stmt->fetch();
						$stmt->close();
						echo "<a href='../project/' class='active'>Home</a>";
						echo "<a href='./basket.php'>Basket ({$items_in_basket})</a>";
						echo "<a href='./previous_orders.php'>Orders</a>";
						if($liRole == "STAFF" || $liRole == "MANAGER") {
							echo "<a href='./products.php'>Product Details</a>";
							echo "<a href='./orders.php'>Orders Details</a>";
						}
						if($liRole == "MANAGER") {
							echo "<a href='./statistics.php'>Statistics</a>";
						}
						echo "<a href='./account.php'>Account Settings</a>";
						echo "<a href='./logout.php'>Logout</a>";
					}
					else {
						echo "<a href='./registration.php'>New User Register</a>";
						echo "<a href='./login.php'>Login</a>";
					}
				?>
			</div>
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
		</div>
	</body>
</html>
