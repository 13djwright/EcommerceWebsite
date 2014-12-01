<html>
	<head>
		<title>S&W Home</title>
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
			}
		?>
		<h1 id="header">S&W Games and Toys</h1>
		<div id="main">
			<div id="links">
				<?php
					if($liEmail){
						echo "<p>Welcome, {$liName}!</p>";
						echo "<a href='./basket.php'>Basket</a>";
						echo "<a href='./previous_orders.php'>Orders</a>";
						if($liRole == "STAFF" || $liRole == "MANAGER") {
							echo "<a href='./products.php'>Product Details</a>";
						}
						if($liRole == "MANAGER") {
							echo "<a href='./statistics.php'>Statistics</a>";
						}
						echo "<a href='./logout.php'>Logout</a>";
					}
					else {
						echo "<a href='./registration.php'>New User Register</a>";
						echo "<a href='./login.php'>Login</a>";
					}
				?>
			</div>
			<div id="searching">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<input type="text" name="search" placeholder="Search...">
					<input type="submit" value="Submit">
				</form>
			</div>
			<div id="results">
				<?php 
				   include_once('./config.php');
				   if($_SERVER["REQUEST_METHOD"] == "POST") {
						if(!empty($_POST["search"])) {
							$search = test_input($_POST["search"]);
							$param = "%{$_POST[search]}%";
							$stmt = $conn->prepare("SELECT id,name,price,quantity FROM products WHERE name LIKE ?");
							$stmt->bind_param("s",$param);
							$stmt->execute();
							$stmt->bind_result($id,$name,$price,$quantity);
							while ($stmt->fetch()) {
								echo "<div class='product'>";
								echo "<form method='post' action='cart_update.php'>";
								echo "<div class='product_name'>{$name}</div>";
								echo "<div class='product_price'>price: \${$price}</div>";
								echo "<div class='product_quantity'>stock: {$quantity}</div>";
								//change this to a select based on stock. Remember to put a cap on it.
								echo "<select class='quantity_text' name='product_quantity'>";
								for($i = 1; $i <= $quantity && $i <= 10; $i+=1) {
									echo "<option value='{$i}'>{$i}</option>";
								}
								echo "</select>";
								echo "<input type='hidden' value='{$id}' name='product_id'>";
								echo "<input type='submit' class='add_product' value='Add to Basket'>";
								echo "</form>";
								echo "</div>";
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
			</div>
		</div>	
	</body>
</html>
