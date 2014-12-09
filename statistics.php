<html>
	<head>
		<title>Sales Stats</title>
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
				if($liRole != "MANAGER") {
					header("Location: ../project/");
				}
				include_once('./config.php');
				$stmt = $conn->prepare("select count(*) from orderDetails D where D.orderID=(select max(id) from orders O where O.userEmail=?)");
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
						echo "<a href='../project'>Home</a>";
						echo "<a href='./basket.php'>Basket ({$items_in_basket})</a>";
						echo "<a href='./previous_orders.php'>Orders</a>";
						if($liRole == "STAFF" || $liRole == "MANAGER") {
							echo "<a href='./products.php'>Product Details</a>";
							echo "<a href='./orders.php'>Order Details</a>";
						}
						if($liRole == "MANAGER") {
                            echo "<a href='./statistics.php' class='active'>Statistics</a>";
                        	
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
			<div id="statcalc">
				<?php
				//Statistic calculation -- Brandon
					$stmt = $conn->prepare("SELECT P.name,O.dateOrdered,P.price FROM orders O,products P WHERE O.id = P.id");
					$stmt->execute();
					$stmt->bind_result($prodName,$dateOrdered,$price); //coud price become an array of the product prices that result
							// in all the prices for this instance?
					//if so, use loo to iterate through all prices in the table
					$lastWeek = 0;
					$lastMonth = 0;
					$lastYear = 0;
					$total = 0;
					$allProductsTotal = 0;
					$grossprofit = 0;
					$weekRevenue = 0;
					$monthRevenue = 0;
					$annualRevenue = 0;
					if($liRole == 'MANAGER'){

						//render top of table on page
						echo '<br />';
						echo "<span>
							<div class='product_name'>Product Name</div>
							<div class='week'>Past Week</div>
							<div class='month'>Past Month</div>
							<div class='year'>Past Year</div>
							<div class='total'>Total # Sold</div>
							<div class='profit'>Product Revenue</div>
						</span>";
						echo '<br />';
						echo '<br />';
						$currentDate = date("Y-m-d");
						$date_diff = 0;
						

						//loop through each row
						while($stmt->fetch()){
							//echo "<p> " . $currentDate . " " . $dateOrdered . " </p>";
							//echo '<p>in while loop...</p>';
							
							$time_difference = date_diff($dateOrdered,$currentDate);	

							$diff = abs(strtotime($currentDate) - strtotime($dateOrdered));

							$diff_in_days = floor($diff / (60 * 60 * 24));
							//echo $diff_in_days;

							//echo "<p>Date Difference: " . $time_difference/*->format('%R%a days')*/ . "</p>";

							//$date_diff = intval(timeSpan($dateOrdered,$currentDate));
							if($diff_in_days > 7 && $diff_in_days <= 30){
								//last month
								$lastMonth++;
								$lastYear++;
								$monthRevenue += $price;
								$annualRevenue +=$price;

							}
							if($diff_in_days > 30 && $diff_in_days <= 365){
								//last year
								$lastYear++;
								$annualRevenue += $price;

							}
							if($diff_in_days <= 7){
								//last week
								$lastWeek++;
								$lastYear++;
								$lastMonth++;
								$weekRevenue += $price;
								$monthRevenue += $price;
								$annualRevenue += $price;
							}
							$total++;

							$prodProfit = $total * $price;
							$prodProfit = number_format($prodProfit, 2, '.', '');
							
							$grossprofit += $prodProfit;
							$grossprofit = number_format($grossprofit, 2, '.', '');

							//show products sold in past week, month, and year
							//|product name|past week  |past month  |past year    |
							//|product 1   |    ...    | ...        | ...         |
							//|product 2   |    ...    | ...        | ...         |

							echo "<div class='product_name'>$prodName</div>";
							echo "<div class='week'>$lastWeek</div>";
							echo "<div class='month'>$lastMonth</div>";
							echo "<div class='year'>$lastYear</div>";
							echo "<div class='total'>$total</div>";
							echo "<div class='profit'>$prodProfit</div>";

							//total amount sold and total products sold
							$allProductsTotal += $total;

							//render above data in table on page
							$lastWeek = 0;
							$lastMonth = 0;
							$lastYear = 0;
							$total = 0;
							$prodProfit = 0;

							//implement sort for highest/lowest sales
							//most popular
							//least popular
						}
						$weekRevenue = number_format($weekRevenue, 2, '.', '');
						$monthRevenue = number_format($monthRevenue, 2, '.', '');
						$annualRevenue = number_format($annualRevenue, 2, '.', '');
						//render bottom portion of table
						echo "<div class='interval_profit'>Revenue (Week, month, annual)</div>";
						echo "<div class='week'>$weekRevenue</div>";
						echo "<div class='month'>$monthRevenue</div>";
						echo "<div class='year'>$annualRevenue</div>";
						echo "<div class='total'></div>";
						echo "<div class='profit'></div>";
						echo '<br />';
						echo '<br />';
						echo '<br />';
						echo '<br />';
						echo '<br />';
						echo "<div class='all_products'>Total Products Sold: $allProductsTotal</div>";
						echo '<br />';
						echo "<div class='all_products'>Total Revenue: $grossprofit</div>";
						$weekRevenue = 0;
						$monthRevenue = 0;
						$annualRevenue = 0;
					}
					else{
						//if not manager, then redirect to index.php
						header("Location: ../project/");
					}

				
				?>
			</div>
		</div>

<!--TODO:
		Its pretty open here. Maybe just a few various functions and what not. -->
	</body>
</html>
