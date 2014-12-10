<html>
	<head>
		<title>Previous Orders</title>
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
				$stmt = $conn->prepare("select count(*) from orderDetails405 D where D.orderID=(select max(id) from orders405 O where O.userEmail=?)");
				$stmt->bind_param("s", $liEmail);
				$stmt->execute();
				$stmt->bind_result($items_in_basket);
				$stmt->fetch();
				$stmt->close();
			}
		?>
		<h1 id="header">S&W Games and Toys - Orders</h1>
		<div id="main">
			<div id="links">
				<a href="../project/">Home</a>
				<a href="./basket.php">Basket <?php echo "({$items_in_basket})";?></a>
				<a href="./previous_orders.php" class='active'>Orders</a>
				<?php
					if($liRole == "STAFF" || $liRole == "MANAGER") {
						echo "<a href='./products.php'>Product Details</a>";
						echo "<a href='./orders.php'>Order Details</a>";
					}
					if($liRole == "MANAGER") {
						echo "<a href='./statistics.php'>Statistics</a>";
					}
				?>
				<a href="./account.php">Account Settings</a>
				<a href="./logout.php">Logout</a>
			</div>

			<div id="orders">
				<h3>Orders</h3>
				<hr>
				<?php
					//get all orders from customer w/ their email. show all of them except the max one. 
					//all order ids from user not including most recent one.
					//select O.id, O.dateOrdered, O.dateShipped from orders O where O.id<>(select max(id) from orders O2 where O2.userEmail=?) and O.userEmail=?; 

					//ignore --> $stmt = $conn->prepare("SELECT id, dateOrdered, dateShipped from orders where id != (SELECT MAX(id) from orders where userEmail=?) AND userEmail=?");
					$stmt = $conn->prepare("select O.id, O.dateOrdered, O.dateShipped from orders405 O where O.id<>(select max(id) from orders405 O2 where O2.userEmail=?) and O.userEmail=?");
					$stmt->bind_param("ss",$liEmail,$liEmail);
					$orderID = $dateOrdered = $dateShippped = NULL;
					if(!$stmt->bind_result($orderID,$dateOrdered,$dateShipped)) {
						echo "bind result failed";
					}
					
					while($stmt->fetch()) {
						//get all the products from the order
						echo "<div class='order'>";
						echo "<div class='orderHeader'>";
						echo "<h3>Ordered Date: {$dateOrdered}</h3>";
						if($dateShipped) {
							echo "<h3>Shipped Date: {$dateShipped}</h3>";
						}
						else {
							echo "<h3>Shipped Date: Pending (order being processed)</h3>";
						}
						echo "</div>";
						//here is where the items in the order are output
						$istmt = $conn->prepare("select P.name, P.price, D.quantity from orderDetails405 D inner join products405 P on P.id=D.productID where D.orderID=?");
						$orderIDTEMP = $orderID;
						echo "Order id: {$orderIDTEMP}";
						$orderIDTEMP = $orderID;
						$istmt->bind_param("i", $orderIDTEMP);
						echo "Test";
						print_r($conn->error_list);
						$istmt->execute();
						echo "Test";
						$istmt->bind_result($pName, $pPrice, $pQuantity);
						while($istmt->fetch()) {
							echo "name: {$pName}\tquantity: {$pQuantity}\tprice: {$pPrice}<br>";
						}
						$istmt->close();
						echo "</div>";
					}
					$stmt->close();
				?>    
			</div>
		</div>
	</body>

</html>
