<!-- this is a template for making new pages -->
<html>
	<head>
		<title>Product Details</title>
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
						echo "<a href='../project/'>Home</a>";
						echo "<a href='./basket.php'>Basket</a>";
						echo "<a href='./previous_orders.php'>Orders</a>";
						if($liRole == "STAFF" || $liRole == "MANAGER") {
                            echo "<a href='./products.php' class='active'>Product Details</a>";
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
			<!--TODO: here we need to have a search bar (like the home page) 
			and then change the functions to update products rather than add 
			them to the cart. It would also be nice to implement a way to save 
			searches so whenever a form is submitted it would show up the last
			search results (so you dont have to search for the same thing).
			This will also allow for a faster way to check if the product was 
			actually updated. Once implemented it would be nice to add the same
			thing to the index page for searching so you results are not lost
			after a search. 
			
			-add search form
			-save searches (so a refresh would not change anything)
			-update table to follow functions
				-add functions for managers and staff on same page
			-->
			</div>
		</div>
	</body>
</html>
