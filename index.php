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
						echo "<a href='../project/' class='active'>Home</a>";
						echo "<a href='./basket.php'>Basket</a>";
						echo "<a href='./previous_orders.php'>Orders</a>";
						if($liRole == "STAFF" || $liRole == "MANAGER") {
							echo "<a href='./products.php'>Product Details</a>";
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
			<div id="searching">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<input type="text" name="search" placeholder="Search..." <?php if(isset($_POST['search'])) { echo "value={$_POST['search']}";} ?>>
					<span>Sort by&nbsp;</span>
					<select name="sort" id="sort">
						<option value="alphabetical" <?php if($_POST['sort'] == "alphabetical") { echo 'selected="selected"';} ?>>Name</option>
						<option value="price-asc-rank" <?php if($_POST['sort'] == "price-asc-rank") { echo 'selected="selected"';} ?>>Price: Low to High</option>
						<option value="price-desc-rank"<?php if($_POST['sort'] == "price-desc-rank") { echo 'selected="selected"';} ?>>Price: High to Low</option>
					</select>
					<input type="submit" value="Submit">
				</form>
			</div>
			<div id="results">
				<?php 
				   include_once('./config.php');
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
							else {
								$sort_param = "name asc";
							}
							$_SESSION['search'] = $search;
							$param = "%{$search}%";
							$stmt = $conn->prepare("SELECT id,name,price,quantity FROM products WHERE name LIKE ? ORDER BY {$sort_param}");
							$stmt->bind_param("s", $param);
							$stmt->execute();
							$stmt->store_result();
							$stmt->bind_result($id,$name,$price,$quantity);
							echo "<span>Results: {$stmt->num_rows()}</span>";
							while ($stmt->fetch()) {
								echo "<div class='product'>";
								echo "<form method='post' action='cart_update.php' target='hidden_form'>";
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
							$stmt->close();
							echo "<iframe style='display:none' name='hidden_form'></iframe>";
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
