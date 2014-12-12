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
			<div id="searching">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<input type="text" name="search" placeholder="Search..." <?php if(isset($_POST['search'])) { echo "value={$_POST['search']}";} ?>>
					<span>Sort by&nbsp;</span>
					<select name="sort" id="sort">
						<option value="first-name-asc" <?php if($_POST['sort'] == "first-name-asc") { echo 'selected="selected"';} ?>>First Name Asc</option>
						<option value="first-name-desc" <?php if($_POST['sort'] == "first-name-desc") { echo 'selected="selected"';} ?>>First Name Desc</option>
						<option value="last-name-asc"<?php if($_POST['sort'] == "last-name-asc") { echo 'selected="selected"';} ?>>Last Name Asc</option>
						<option value="last-name-desc"<?php if($_POST['sort'] == "last-name-desc") { echo 'selected="selected"';} ?>>Last Name Desc</option>
					</select>
					<input type="submit" value="Submit">
				</form>
			</div>
			<div id="results">
				<?php 
				   if($_SERVER["REQUEST_METHOD"] == "POST") {
						if(!empty($_POST["search"])) {
							$search = test_input($_POST["search"]);
							$sort = test_input($_POST["sort"]);
							$sort_param = "";
							if($sort == "first-name-asc") {
								$sort_param = "firstName asc";
							}
							else if($sort == "first-name-desc") {
								$sort_param = "firstName desc";
							}
							else if($sort == "last-name-asc") {
								$sort_param = "lastName asc";
							}
							else if($sort == "last-name-desc"){
								$sort_param = "lastName desc";
							}
							else {
								$sort_param = "firstName asc";
							}
							$_SESSION['search'] = $search;
							$param = "%{$search}%";
							$stmt = $conn->prepare("SELECT firstName,lastName,role,email FROM users405 WHERE firstName LIKE ? OR lastName LIKE ? ORDER BY {$sort_param}");
							$stmt->bind_param("ss", $param, $param);
							$stmt->execute();
							$stmt->store_result();
							$stmt->bind_result($firstName, $lastName, $role, $email);
							echo "<span>Results: {$stmt->num_rows()}</span>";
							while ($stmt->fetch()) {
								echo "<div class='product'>";
								echo "<form method='post' action='update_user.php'>";
								echo "<div class='product_name'>{$firstName} {$lastName}</div>";
								echo "<div class='product_price' style='width: 200px;'>{$email}</div>";
								echo "<div class='product_quantity' style='width:300px;'>";
								if($role == "CUSTOMER") { 
									echo "<input type='radio' name='role' value='CUSTOMER' checked>Customer";
									echo "<input type='radio' name='role' value='STAFF'>Staff";
									echo "<input type='radio' name='role' value='MANAGER'>Manager";
								}
								else if($role == "STAFF") {
									echo "<input type='radio' name='role' value='CUSTOMER'>Customer";
									echo "<input type='radio' name='role' value='STAFF' checked>Staff";
									echo "<input type='radio' name='role' value='MANAGER'>Manager";
								}
								else if($role == "MANAGER") {
									echo "<input type='radio' name='role' value='CUSTOMER'>Customer";
									echo "<input type='radio' name='role' value='STAFF'>Staff";
									echo "<input type='radio' name='role' value='MANAGER' checked>Manager";
								}
								else {
									echo "<input type='radio' name='role' value='CUSTOMER'>Customer";
									echo "<input type='radio' name='role' value='STAFF'>Staff";
									echo "<input type='radio' name='role' value='MANAGER'>Manager";
								}
								echo "</div>";
								echo "<input type='hidden' value='{$email}' name='email'>";
								echo "<input type='submit' class='add_product' value='Update User'>";
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
