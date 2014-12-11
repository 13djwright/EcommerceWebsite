<html>
	<head>
		<title>S&W Home</title>
		<link rel="stylesheet" type="text/css" href="./stylesheet.css">
	</head>

	<body>
		<?php
			include_once("./config.php");
			session_start();
			$liEmail = $liName = $liRole = "";
			if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
				//not logged in
				header("Location: ../project/");
			}
			else {
				$liEmail = $_SESSION['email'];
				$liName = $_SESSION['firstName'];
				$liRole = $_SESSION['role'];
				if(!($liRole == "MANAGER" || $liRole == "STAFF")) {
					header("Location: ../project/");
				}
			}
			if($_SERVER["REQUEST_METHOD"] == "POST") {
				$productID = $_POST["product_id"];
				if(!is_null($productID)) {
					$stmt = $conn->prepare("delete from products405 where id=?");
					$stmt->bind_param("i",$productID);
					$stmt->execute();
					$stmt->fetch();
					$stmt->close();
				}
			}
		?>
		<h1 id="header">S&W Games and Toys</h1>
		<div id="main">
			<div id="links">
				<?php
				   include_once('./config.php');
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
			<h3>Delete Product</h3>
			<hr>
			<div id="searching">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<input type="text" name="search" placeholder="Search..." <?php if(isset($_POST['search'])) { echo "value={$_POST['search']}";} ?>>
					<span>Sort by&nbsp;</span>
					<select name="sort" id="sort">
						<option value="alphabetical" <?php if($_POST['sort'] == "alphabetical") { echo 'selected="selected"';} ?>>Name</option>
						<option value="price-asc-rank" <?php if($_POST['sort'] == "price-asc-rank") { echo 'selected="selected"';} ?>>Price: Low to High</option>
						<option value="price-desc-rank"<?php if($_POST['sort'] == "price-desc-rank") { echo 'selected="selected"';} ?>>Price: High to Low</option>
						<option value="quan-asc-rank" <?php if($_POST['sort'] == "quan-asc-rank") { echo 'selected="selected"';} ?>>Quantity: Low to High</option>
						<option value="quan-desc-rank"<?php if($_POST['sort'] == "quan-desc-rank") { echo 'selected="selected"';} ?>>Quantity: High to Low</option>
					</select>
					<input type="submit" value="Submit">
					<a href='./products.php'>
						<input type='button' value='Cancel'>
					</a>
				</form>
			</div>
			<div id="results">
				<?php 
				   if($_SERVER["REQUEST_METHOD"] == "POST") {
						if(!empty($_POST["search"])) {
							$search = test_input($_POST["search"]);
							$sort = test_input($_POST["sort"]);
							$sort_param = "";
							if($sort == "name") {
								$sort_param = "name asc";
							}
							else if($sort == "price-asc-rank") {
								$sort_param = "price asc";
							}
							else if($sort == "price-desc-rank") {
								$sort_param = "price desc";
							}
							else if($sort == "quan-asc-rank") {
								$sort_param = "quantity asc";
							}
							else if($sort == "quan-desc-rank") {
								$sort_param = "quantity desc";
							}
							else {
								$sort_param = "name asc";
							}
							$_SESSION['search'] = $search;
							$param = "%{$search}%";
							$stmt = $conn->prepare("SELECT id,name,price,quantity,promoFrom,promoTo,promoDiscount FROM products405 WHERE name LIKE ? ORDER BY {$sort_param}");
							$stmt->bind_param("s", $param);
							$stmt->execute();
							$stmt->store_result();
							$stmt->bind_result($id,$name,$price,$quantity, $promoFrom, $promoTo, $promoDiscount);
							echo "<span>Results: {$stmt->num_rows()}</span>";
							$alert_message = "\"You must be logged in to add items to your cart.\"";
							while ($stmt->fetch()) {
								echo "<div class='product'>";
								echo '<form method="post" action=' . htmlspecialchars($_SERVER["PHP_SELF"]) . '>';
								echo "<div class='product_name'>{$name}</div>";
								$today = date("Y-m-d");
								if($promoTo && $promoFrom) {
									if($today >= $promoFrom && $today <= $promoTo) {
										$discountPrice = round($price - ($price*($promoDiscount/100)),2);
										echo "<div class='product_price'>Price: <del>\${$price}</del> <ins>{$discountPrice} ({$promoDiscount}% off)</ins></div>";
									}
								}
								else {
									echo "<div class='product_price'>Price: \${$price}</div>";
								}
								echo "<div class='product_quantity'>";
								if($quantity > 0) {
									echo "stock: {$quantity}";
								}
								else {
									echo "stock: Out of Stock";
								}
								echo "</div>";
								echo "<input type='hidden' value='{$id}' name='product_id'>";
								echo "<input type='submit' class='add_product' value='Delete'>";
								echo "</form>";
								echo "</div>";
							}
							$stmt->close();
						}
				   }

					function test_input($data) {
						$data = trim($data);
						$data = stripslashes($data);
						$data = htmlspecialchars($data);
						return $data;
					}
				?>
			</div>
		</div>	
	</body>
</html>
