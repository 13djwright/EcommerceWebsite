<html>
	<head>
		<title>Settings</title>
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
				<?php
					echo "<div id='inline'>";
					echo "<h3>Account Information</h3>";
					if($liRole == "MANAGER") {
						echo "<a href='./users.php'><button>Modify User Roles</button></a>";
					}
					echo "</div>";
					echo "<hr>";
					$stmt = $conn->prepare("select role, firstName, lastName, password, email, address, city, zipCode, state from users405 where email=?");
					$stmt->bind_param("s", $liEmail);
					$stmt->execute();
					$stmt->bind_result($role, $fname, $lname, $pw, $email, $addr, $city, $zip, $state);
					$stmt->fetch();
					$stmt->close();

					if($_SERVER["REQUEST_METHOD"] == "POST") {
						if(!empty($_POST["changeAcc"])) {
							echo "<form method='post' action='./account.php'>";
							echo "<div id='account_info_title'>Account Type:</div> <div id='account_info_field'>
								$role
								</div><br class='clear'>";
							echo "<div id='account_info_title'>First Name:</div> <div id='account_info_field'>
								<input type='text' name='fname' value='{$fname}'>
								</div><br class='clear'>";
							echo "<div id='account_info_title'>Last Name:</div>  <div id='account_info_field'>
								<input type='text' name='lname' value='{$lname}'>
								</div><br class='clear'>";
							echo "<div id='account_info_title'>Email:</div>  <div id='account_info_field'>
								<input type='text' name='email' value='{$email}'>
								</div><br class='clear'>";
							echo "<div id='account_info_title'>Street Address:</div>  <div id='account_info_field'>
								<input type='text' name='address' value='{$addr}'>
								</div><br class='clear'>";
							echo "<div id='account_info_title'>Zip Code:</div>  <div id='account_info_field'>
								<input type='text' name='zip_code' value='{$zip}'>
								</div><br class='clear'>";
							echo "<div id='account_info_title'>City:</div>  <div id='account_info_field'>
								<input type='text' name='city' value='{$city}'>
								</div><br class='clear'>";
							echo "<div id='account_info_title'>State:</div>  <div id='account_info_field'>
								<input type='text' name='state' value='{$state}'>
								</div><br class='clear'>";
							#Update the DB for the changed fields
					
							echo "<input type='submit' value='Confirm Changes' name='confirmAcc' onClick='./account.php'>";
							echo "</form>";

							echo "<form method='post' action='./account.php'>";

							echo "<div id='account_info_title'>Old Password:</div> <div id='account_info_field'><input type='password' name='oldpassword'></div>
							<br class='clear'>";
							echo "<div id='account_info_title'>New Password:</div> <div id='account_info_field'><input type='password' name='newpassword'></div>
							<br class='clear'>";
							echo "<div id='account_info_title'>Re-enter:</div> <div id='account_info_field'><input type='password' name='repassword'></div>
							<br class='clear'>";

							echo "<input type='submit' value='Change Password?' name='PassChange' onClick='./account.php'>";
							echo "</form>";
						}
						else if(!empty($_POST["confirmAcc"])){
							echo "<span id='confirm'>Changes made to your account information.</span>";
							$fname = $_POST['fname'];						
							$lname = $_POST['lname'];
							$email = $_POST['email'];
							$addr = $_POST['address'];
							$zip = $_POST['zip_code'];
							$city = $_POST['city'];
							$state = $_POST['state'];

							$uppstmt = $conn->prepare("UPDATE users405 SET firstName=?, lastName=?, email=?, address=?, city=?, zipCode=?, state=? WHERE email=?");
							$uppstmt->bind_param("sssssiss", $fname, $lname, $email, $addr, $city, $zip, $state, $liEmail);
							$uppstmt->execute();
							$uppstmt->close();


							echo "<div id='account_info_title'>Account Type:</div> <div id='account_info_field'>$role</div><br class='clear'>";
							echo "<div id='account_info_title'>First Name:</div> <div id='account_info_field'>$fname</div><br class='clear'>";
							echo "<div id='account_info_title'>Last Name:</div> <div id='account_info_field'>$lname</div><br class='clear'>";
							echo "<div id='account_info_title'>Email:</div> <div id='account_info_field'>$email</div><br class='clear'>";
							echo "<div id='account_info_title'>Street Address:</div> <div id='account_info_field'>$addr</div><br class='clear'>";
							echo "<div id='account_info_title'>Zip Code:</div> <div id='account_info_field'>$zip</div><br class='clear'>";
							echo "<div id='account_info_title'>City:</div> <div id='account_info_field'>$city</div><br class='clear'>";
							echo "<div id='account_info_title'>State:</div> <div id='account_info_field'>$state</div><br class='clear'>";
							
							echo "<form method='post' action='./account.php'>";
							echo "<input type='submit' value='Change Account Information' name='changeAcc' onClick='./account.php'>";
							echo "</form>";
						}
						else if(!empty($_POST["PassChange"])){
							

							$oldPass = $_POST['oldpassword'];
							$newpass = $_POST['newpassword'];
							$newpassCheck = $_POST['repassword'];

							if($newpass == "" || $newpass == " "){
								$newpass = "wrongPass";
							}
							if($newpassCheck == "" || $newpassCheck == " "){
								$newpassCheck = "wrongPass2";
							}
							if($oldPass == "" || $oldPass == " "){
								$oldPass == "wrongPass";
							}

							$stmt = $conn->prepare("select password from users405 WHERE email=?");
							$stmt->bind_param("s", $liEmail);
							$stmt->execute();
							$stmt->bind_result($currpass);
							$stmt->fetch();
							$stmt->close();

							if($newpass != $newpassCheck){
								echo "<span id='confirm'>New password did not match re-entry of the new password.</span>";
								echo "<form method='post' action='./account.php'>";
								echo "<input type='submit' value='Change Account Information' name='changeAcc' onClick='./account.php'>";
								echo "</form>";
							}
							else if($oldPass != $currpass){
								echo "<span id='confirm'>Incorrect entry for current password.</span>";
								
								echo "<form method='post' action='./account.php'>";
								echo "<input type='submit' value='Change Account Information' name='changeAcc' onClick='./account.php'>";
								echo "</form>";
							}
							else if($newpass == $newpassCheck && $oldPass == $currpass){
								echo "<span id='confirm'>Password has been changed.</span>";
								$uppstmt = $conn->prepare("UPDATE users405 SET password=? WHERE email=?");
								$uppstmt->bind_param("ss", $newpass, $liEmail);
								$uppstmt->execute();
								$uppstmt->close();


								echo "<div id='account_info_title'>Account Type:</div> <div id='account_info_field'>$role</div><br class='clear'>";
								echo "<div id='account_info_title'>First Name:</div> <div id='account_info_field'>$fname</div><br class='clear'>";
								echo "<div id='account_info_title'>Last Name:</div> <div id='account_info_field'>$lname</div><br class='clear'>";
								echo "<div id='account_info_title'>Email:</div> <div id='account_info_field'>$email</div><br class='clear'>";
								echo "<div id='account_info_title'>Street Address:</div> <div id='account_info_field'>$addr</div><br class='clear'>";
								echo "<div id='account_info_title'>Zip Code:</div> <div id='account_info_field'>$zip</div><br class='clear'>";
								echo "<div id='account_info_title'>City:</div> <div id='account_info_field'>$city</div><br class='clear'>";
								echo "<div id='account_info_title'>State:</div> <div id='account_info_field'>$state</div><br class='clear'>";
								
								echo "<form method='post' action='./account.php'>";
								echo "<input type='submit' value='Change Account Information' name='changeAcc' onClick='./account.php'>";
								echo "</form>";
							}

						}
					}
					else{
						#$changes = 'htmlspecialchars($_SERVER["PHP_SELF"])';

						echo "<div id='account_info_title'>Account Type:</div> <div id='account_info_field'>$role</div><br class='clear'>";
						echo "<div id='account_info_title'>First Name:</div> <div id='account_info_field'>$fname</div><br class='clear'>";
						echo "<div id='account_info_title'>Last Name:</div> <div id='account_info_field'>$lname</div><br class='clear'>";
						echo "<div id='account_info_title'>Email:</div> <div id='account_info_field'>$email</div><br class='clear'>";
						echo "<div id='account_info_title'>Street Address:</div> <div id='account_info_field'>$addr</div><br class='clear'>";
						echo "<div id='account_info_title'>Zip Code:</div> <div id='account_info_field'>$zip</div><br class='clear'>";
						echo "<div id='account_info_title'>City:</div> <div id='account_info_field'>$city</div><br class='clear'>";
						echo "<div id='account_info_title'>State:</div> <div id='account_info_field'>$state</div><br class='clear'>";

						echo "<form method='post' action='./account.php'>";
						echo "<input type='submit' value='Change Account Information' name='changeAcc' onClick='./account.php'>";
						echo "</form>";
					}

					
					#implement ability to change.
				?>
				<br />
				
					

				</form>
		</div>
	</body>
</html>
