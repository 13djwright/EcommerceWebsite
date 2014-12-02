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
			}
		?>
		<h1 id="header">S&W Games and Toys - Orders</h1>
		<div id="main">
			<div id="links">
				<a href="../project/">Home</a>
				<a href="./basket.php">Basket</a>
				<a href="./previous_orders.php" class='active'>Orders</a>
				<?php
					if($liRole == "STAFF" || $liRole == "MANAGER") {
						echo "<a href='./products.php'>Product Details</a>";
					}
					if($liRole == "MANAGER") {
						echo "<a href='./statistics.php'>Statistics</a>";
					}
				?>
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
					$stmt = $conn->prepare("select O.id, O.dateOrdered, O.dateShipped from orders O where O.id<>(select max(id) from orders O2 where O2.userEmail=?) and O.userEmail=?");
					$stmt->bind_param("ss",$liEmail,$liEmail);
					if(!$stmt->execute()) {
						echo "Failed.";	
					}
					else {
						echo "not failed";
					}
					//$stmt->bind_result($orderID,$dateOrdered,$dateShipped);
					echo "test";
					$result = $stmt->fetch_result();
					echo "res" . $result;
					while($data = $result->fetch_assoc()) {
						$stat[] = $data;
						echo "data";
					}
					echo "test";
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
						$istmt = $conn->prepare("select P.name, P.price, D.quantity from orderDetails D inner join products P on P.id=D.productID where D.orderID=?");
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
