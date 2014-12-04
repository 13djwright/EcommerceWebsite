<html>
	<head>
		<title>S&W Orders</title>
		<link rel="stylesheet" href="./stylesheet.css" type="text/css">
	</head>

	<body>
		<?php
		session_start();
		$liEmail = $liName = $liRole = "";
		if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
			header("Location:../project/");
		}
		else {
			$liEmail = $_SESSION['email'];
			$liName = $_SESSION['firstName'];
			$liRole = $_SESSION['role'];
			include_once("./config.php");
		}

		?>
		<h1 id="header">S&W Games and Toys - Orders</h1>
		<div id="main">
			<div id="links">
				<a href="../project/">Home</a>
				<a href="./basket.php" class='active'>Basket</a>
				<a href="./previous_orders.php">Orders</a>
				<?php
					if($liRole == "STAFF" || $liRole == "MANAGER") {
						echo "<a href='./products.php'>Product Details</a>";
					}
					if($liRole == "MANAGER") {
						echo "<a href='./statistics.php'>Statistics</a>";
					}
				?>
				<a href="./account.php">Account Settings</a>
				<a href="./logout.php">Logout</a>
			</div>

			<div id="basket">
				<h3>Basket</h3>
				<hr>
				<?php
					$bstmt = $conn->prepare("SELECT MAX(id) FROM orders WHERE userEmail=?");
					$bstmt->bind_param("s",$liEmail);
					$bstmt->execute();
					$bstmt->bind_result($result);
					$bstmt->fetch();
					$bstmt->close();
					if($result) {
						$stmt = $conn->prepare("select D.id, P.name, P.price, D.quantity from orderDetails D inner join products P on P.id=D.productID where D.orderID=?");
						$stmt->bind_param("i",$result);
						$stmt->execute();
						$stmt->bind_result($orderDetailsID,$pname,$pprice,$pquantity);
						while($stmt->fetch()) {
							echo "<div class='product'>";
							echo "<form method='post' action='removeItem.php'>";
							echo "<div class='product_name'>{$pname}</div>";
							echo "<div class='product_price'>{$pprice}</div>";
							echo "<div class='product_quantity'>{$pquantity}</div>";
							echo "<input type='hidden' value='{$orderDetailsID}' name='orderDetailsID'>";
							echo "<input type='submit' class='add_product' value='Delete item'>";
							echo "</form>";
							echo "</div>";
						}
						if($orderDetailsID) {
							echo "<form action='./placeorder.php' method='post'>"; 
							echo "<input type='hidden' name='orderID' value={$result}>";
							echo "<input type='submit' action='' value='Place Order'>";
							echo "</form>";
						}
					}
					$stmt->close();
				?>
			</div>
		</div>
	</body>
</html>
