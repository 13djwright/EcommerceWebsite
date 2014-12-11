<html>
	<head>
		<title>S&W Register new User</title>
		<link rel="stylesheet" type="text/css" href="./stylesheet.css"/>
	</head>
	<body>
		<?php
			
			include_once('./config.php');
			session_start();
			$liEmail = $liName = $liRole = "";
			if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
				//not logged in
			}
			else {
				$liEmail = $_SESSION['email'];
				$liName = $_SESSION['firstName'];
				$liRole = $_SESSION['role'];
			}
			if(!($liRole == "MANAGER" || $liRole == "STAFF")) {
				header("Location: ../project/");
			}
			if($_SERVER['REQUEST_METHOD'] == "POST") {
				$productAdded = $productName = $productPrice = $productQuantity = $promoFrom = $promoTo = $promoDiscount = NULL;
				$productNameErr = $productPriceErr = $productQuantityErr = $promoFromErr = $promoToErr = $promoDiscountErr = NULL;
				
				if(empty($_POST["productName"])) {
					$productNameErr = "Product name is required";
				}
				else {
					$productName = test_input($_POST["productName"]);
					if (!preg_match("/^[a-zA-z()!'?]*$/",$productName)) {
						$firstNameErr = "Characters entered are not allowed";
					}
				}
				
				if(empty($_POST["productPrice"])) {
					$productPriceErr = "Product price is required";
				}
				else {
					$productPrice = test_input($_POST["productPrice"]);
					if(!preg_match("/^\d+\.\d\d$/", $productPrice)) {
						$productPriceErr = "Only decimal numbers allowed.";
					}
				}
			
				if(empty($_POST["productQuantity"])) {
					$productQuantityErr = "Product quantity is required";
				}
				else {
					$productQuantity = test_input($_POST["productQuantity"]);
					if(!preg_match("/^\d+$/",$productQuantity)) {
						$productQuantityErr = "Only numbers allowed.";
					}
				}
				
				if(!empty($_POST["promoFrom"]) && !empty($_POST['promoTo'])) {
				//both are filled out
					$promoFrom = test_input($_POST["promoFrom"]);
					$promoTo = test_input($_POST["promoTo"]);
					$promoDiscount = test_input($_POST["promoDiscount"]);
					if(empty($_POST['promoDiscount'])) {
						//no promo, error
						$promoDiscountErr = "Discount needed if inputting dates.";
					}
					else {
						//no errors all is good.
						$promoFrom = test_input($_POST["promoFrom"]);
						$promoTo = test_input($_POST["promoTo"]);
						$promoDiscount = test_input($_POST["promoDiscount"]);
						if(!preg_match("/^[0-1][0-9]\/[0-3][0-9]/[1-2][0-9]{3}$/", $promoFrom)) {
							$promoFromErr = "Only valid dates allowed";
						}
						if(!preg_match("/^[0-1][0-9]\/[0-3][0-9]/[1-2][0-9]{3}$/", $promoTo)) {
							$promoToErr = "Only valid dates allowed";
						}
						if($promoTo > $promoFrom && ($promoFrom == NULL && $promoTo == NULL)) {
							$promoFromErr = "From must be before To";
						}
						if(!preg_match("/^\d+$/", $promoDiscount)) {
							$promoDiscountErr = "Discount must be an integer";
						}
					}
				}
				else if(!empty($_POST["promoFrom"]) || !empty($_POST['promoTo'])){
				//only one is filled out, error
					$promoFrom = test_input($_POST["promoFrom"]);
					$promoTo = test_input($_POST["promoTo"]);
					$promoDiscount = test_input($_POST["promoDiscount"]);
					$promoFromErr = "Both dates required for a promotion";
				}
				else if(empty($_POST["promoFrom"]) && empty($_POST['promoTo']) && !empty($_POST["promoDiscount"]) ){
					$promoDiscount = test_input($_POST["promoDiscount"]);
					$promoFromErr = "Dates required if entering discount";
				}
				$promoDiscount = $promoDiscount ?: 0;
				//if no errors, we are okay to insert
				if( $productNameErr == NULL &&
					$productPriceErr == NULL && 
					$productQuantityErr == NULL &&
					$promoFromErr == NULL &&
					$promoToErr == NULL &&
					$promoDiscountErr == NULL) {
					$stmt = $conn->prepare("insert into products405(name,price,quantity,promoFrom,promoTo,promoDiscount) values (?,?,?,?,?,?)");
					$stmt->bind_param("sdissi",$productName, $productPrice, $productQuantity, $promoFrom, $promoTo, $promoDiscount);
					$stmt->execute();
					$stmt->fetch();
					$stmt->close();
					$productAdded = "Product added successfully.";
				}
				else {
					$productAdded = "";
				}
			}
			

			function test_input($data) {
			   $data = trim($data);
			   $data = stripslashes($data);
			   $data = htmlspecialchars($data);
			   return $data;
			}
		?>
		<h1 id='header'>S&W Games and Toys</h1>
		<div id="main">
			<div id="links">
				<?php
					if($liEmail){
						$stmt = $conn->prepare("select count(*) from orderDetails405 D where D.orderID=(select max(id) from orders405 O where O.userEmail=?)");
						$stmt->bind_param("s", $liEmail);
						$stmt->execute();
						$stmt->bind_result($items_in_basket);
						$stmt->fetch();
						$stmt->close();
						echo "<a href='../project/'>Home</a>";
						echo "<a href='./basket.php'>Basket ({$items_in_basket})</a>";
						echo "<a href='./previous_orders.php'>Orders</a>";
						if($liRole == "STAFF" || $liRole == "MANAGER") {
							echo "<a href='./products.php' class='active'>Product Details</a>";
							echo "<a href='./orders.php'>Order Details</a>";
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
			<h3>Add a Product</h3>
			<hr>
			<div class="register">
				<span><?php echo $productAdded;?></span>
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
					<label>Product Name: </label><input type="text" name="productName" value="<?php echo $productName;?>">
					<span class="error">* <?php echo $productNameErr;?></span>
					<br>
					<label>Price:</label> <input type="number" min="0" max="200" step="0.01" name="productPrice" value="<?php echo $productPrice;?>">
					<span class="error">* <?php echo $productPriceErr;?></span>
					<br>
					<label>Quantity:</label> <input type="number" min="0" step="1" name="productQuantity" value="<?php echo $productQuantity;?>">
					<span class="error">* <?php echo $productQuantityErr;?></span>
					<br>
					<label>Promo From:</label> <input type="date" name="promoFrom" value="<?php echo $promoFrom;?>">
					<span class="error"><?php echo $promoFromErr;?></span>
					<br>
					<label>Promo To:</label> <input type="date" name="promoTo" value="<?php echo $promoTo;?>">
					<span class="error"><?php echo $promoToErr;?></span>
					<br>
					<label>Promo Discount:</label> <input type="number" min="0" max="100" step="1" name="promoDiscount" value="<?php echo $promoDiscount;?>">
					<span class="error"><?php echo $promoDiscountErr;?></span>
					<br>
					<div class="buttons">
						<input type="submit" value="Add">
						<a href="./products.php">
							<input type="button" value="Cancel">
						</a>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>
