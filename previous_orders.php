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
					$stmt = $conn->prepare("select O.id, O.dateOrdered, O.dateShipped, O.total from orders405 O where O.id<>(select max(id) from orders405 O2 where O2.userEmail=?) and O.userEmail=? order by O.id desc");
					$stmt->bind_param("ss",$liEmail,$liEmail);
					$orderIDtemp = $dateOrderedtemp = $dateShipppedtemp = NULL;
					$orderIDs = array();
					$dateOrdereds = array();
					$dateShippeds = array();
					$orderTotals = array();
					$stmt->execute();	
					if(!$stmt->bind_result($orderIDtemp, $dateOrderedtemp, $dateShippedtemp, $orderTotaltemp)) {
						echo "bind result failed";
					}
					while($stmt->fetch()) {
						array_push($orderIDs, $orderIDtemp);
						array_push($dateOrdereds, $dateOrderedtemp);
						array_push($dateShippeds, $dateShippedtemp);
						array_push($orderTotals, $orderTotaltemp);
					}
					$stmt->close();
					$istmt = $conn->prepare("select P.name, D.price_bought_at, D.quantity from orderDetails405 D inner join products405 P on P.id=D.productID where D.orderID=?");
					for($i=0; $i < count($orderIDs); $i++) {
						echo "<div class='order'>";
						echo "<div class='orderHeader'>";
						echo "<div class='order-label'>";
						echo "<span>Ordered Date</span>";
						echo "<span>{$dateOrdereds[$i]}</span>";
						echo "</div>";
						echo "<div class='order-label'>";
						echo "<span>Total</span>";
						echo "<span>\${$orderTotals[$i]}</span>";
						echo "</div>";
						echo "<div class='order-label'>";
						echo "<span>Shipped Date</span>";
						if($dateShippeds[$i]) {
							echo "<span>{$dateShippeds[$i]}</span>";
						}
						else {
							echo "<span>Pending (order being processed)</span>";
						}
						echo "</div>";
						echo "</div>"; //end for div orderHeader
						$istmt->bind_param("i",$orderIDs[$i]);
						$istmt->execute();
						$istmt->bind_result($pName, $pPrice, $pQuantity);
						echo "<div class='previous_orders'>";
						echo "<div class='product'>";
						echo "<span class='product_name'>Name</span>";
						echo "<span class='product_quantity'>Quantity</span>";
						echo "<span class='product_price'>Price</span>";
						echo "</div>";	
						while($istmt->fetch()) {
							echo "<div class='product'>";
							echo "<span class='product_name'>{$pName}</span>";
							echo "<span class='product_quantity'>{$pQuantity}</span>";
							echo "<span class='product_price'>\${$pPrice}</span>";
							echo "</div>";
						}
						echo "</div>";
						echo "</div>";
					}
				?>    
			</div>
		</div>
	</body>

</html>
