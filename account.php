<html>
	<head>
		<title>Settings</title>
		<link rel="stylesheet" type="text/css" href="./stylesheet.css">
	</head>

	<body>
		<?php
			session_start();
			$liEmail = $liName = $liRole = "";
			if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
				//not logged in
			}
			else {
				$liEmail = $_SESSION['email'];
				$liName = $_SESSION['firstName'];
				$liRole = $_SESSION['role'];
				include_once('./config.php');
				$stmt = $conn->prepare("select count(*) from orderDetails D where D.orderID=(select max(id) from orders O where O.userEmail=?)");
				$stmt->bind_param("s", $liEmail);
				$stmt->execute();
				$stmt->bind_result($items_in_basket);
				$stmt->fetch();
				$stmt->close();
			}
		?>
		<h1 id="header">S&W Games and Toys</h1>
		<div id="main">
			<div id="links">
				<?php
					if($liEmail){
						echo "<a href='../project/'>Home</a>";
						echo "<a href='./basket.php'>Basket ({$items_in_basket})</a>";
						echo "<a href='./previous_orders.php'>Orders</a>";
						if($liRole == "STAFF" || $liRole == "MANAGER") {
							echo "<a href='./products.php'>Product Details</a>";
							echo "<a href='./orders.php'>Order Details</a>";
						}
						if($liRole == "MANAGER") {
							echo "<a href='./statistics.php'>Statistics</a>";
						}
						echo "<a href='./account.php' class='active'>Account Settings</a>";
						echo "<a href='./logout.php'>Logout</a>";
					}
					else {
						echo "<a href='./registration.php'>New User Register</a>";
						echo "<a href='./login.php'>Login</a>";
					}
				?>
			</div>
		</div>
	</body>
</html>
