<html>
	<head>
		<title>Order Details</title>
		<link rel="stylesheet" type="text/css" href="./stylesheet.css">
	</head>

	<body>
		<?php
			session_start();
			$liEmail = $liName = $liRole = "";
			if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
				//not logged in
				header("Location:../project/");
			}
			else {
				$liEmail = $_SESSION['email'];
				$liName = $_SESSION['firstName'];
				$liRole = $_SESSION['role'];
				include_once('./config.php');
				$stmt = $conn->prepare("select count(*) from orderDetails405 D where D.orderID=(select max(id) from orders405 O where O.userEmail=?)");
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
							echo "<a href='./orders.php'' class='active'>Order Details</a>";
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
			<h3>Pending Orders</h3>
			<hr>
			<div class="pending-orders">
				<?php
					//get all the order ids that are pending, and put into an array. loop through those and get all the user information.
					//after each user info is displayed, use the orderID to display the orderDetails (product name, quantity etc etc)
					//have a fullfill button on each of these and when pressed, fullfill the order (set date and change quantities ((would require changes to 
					//$stmt = $conn->prepare("select U.firstName, U.lastName, U.email, U.address, U.zipCode, U.state, O.id from orders O join users U on U.email=O.userEmail where O.dateOrdered is not null and O.dateShipped is null");
					//$stmt->execute();
					//$stmt->bind_results($firstName, $lastName, $email, $address, $zipCode, $state, 
					$orderIDs = array();
					//order by could go here for oldest/newest
					$stmt = $conn->prepare("select O.id from orders405 O where O.dateOrdered is not null and O.dateShipped is null");
					$stmt->execute();
					$orderIDtemp = NULL;
					$stmt->bind_result($orderIDtemp);
					while($stmt->fetch()) {
						array_push($orderIDs, $orderIDtemp);
					}
					$stmt->close();
					for($i=0; $i<count($orderIDs); $i++) {
						echo "test";
						echo "<div class='order'>";
							echo "<form action='process_order.php' method='post'>";
								//display user info
								echo "<div class='orderHeader'>";
									$stmt = $conn->prepare("select U.firstName, U.lastName, U.email, U.address, U.zipCode, U.state, U.city, O.dateOrdered from users405 U, orders405 O where U.email=O.userEmail and O.id=?");
									$stmt->bind_param("i",$orderIDs[$i]);
									$stmt->execute();
									$stmt->bind_result($firstName, $lastName, $email, $address, $zipCode, $state, $city, $dateOrdered);
									$stmt->fetch();
									$stmt->close();
									echo "<div class='order-label'>";
										echo "<span></span>";
										echo "<span class='to-upper'>{$lastName}, {$firstName}</span>";
									echo "</div>";
									echo "<div class='order-label'>";
										echo "<span class='to-upper'>{$address}</span>";
										echo "<span class='to-upper'>{$city}, {$state}  {$zipCode}</span>";
									echo "</div>";
									echo "<div class='order-label'>";
										echo "<span>Date Ordered</span>";
										echo "<span class='to-upper'>{$dateOrdered}</span>";
									echo "</div>";
								echo "</div>";
								//display all products on order
								echo "<div class='product'>";
								echo "<span class='product_name'>Name</span>";
								echo "<span class='product_quantity'>Quantity</span>";
								echo "<span class='product_price'>Price</span>";
								echo "</div>";	
								$stmt = $conn->prepare("select P.name, D.quantity, P.price from products405 P join orderDetails405 D on D.productID=P.id where D.orderID=?");
								$stmt->bind_param("i",$orderIDs[$i]);
								$stmt->execute();
								$stmt->bind_result($pName, $pQuantity, $pPrice);
								while($stmt->fetch()) {
								echo "<div class='product'>";
                                echo "<span class='product_name'>{$pName}</span>";
                                echo "<span class='product_quantity'>{$pQuantity}</span>";
                                echo "<span class='product_price'>{$pPrice}</span>";
                                echo "</div>";
								}
							echo "<input type='hidden' name='orderID' value='{$orderIDs[$i]}'>";
							echo "<input type='submit' value='Fullfill Order'>";
							echo "</form>";
						echo "</div>";
					}
					
				?>
			</div>
		</div>
	</body>
</html>
