<?php
	session_start();
	if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
		header("Location:../project/");
	}
	else{ 
		include_once("./config.php"); 
		$email = $_SESSION['email'];
		$productID = test_input($_POST["product_id"]);
		$productQuantity = test_input($_POST["product_quantity"]);
		$orderIDstmt = $conn->prepare("SELECT max(id) FROM orders where userEmail=?");
		$orderIDstmt->bind_param("s",$email);
		$orderIDstmt->execute();
		$orderIDstmt->bind_result($orderID);
		$orderIDstmt->fetch();
		$orderIDstmt->close();
		/*whenever a user creates an account, they will have an order ready, and once the order is actually ordered
		  the user is given a new order to add to.
		  insert into the order details table all of the data needed.*/
		$stmt = $conn->prepare("SELECT id FROM orderDetails where orderID=? AND productID=?");
		$stmt->bind_param("ii",$orderID, $productID);
		$stmt->bind_result($result);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		if($result) {
			$uppstmt = $conn->prepare("UPDATE orderDetails D SET D.quantity=? WHERE D.id=?");
			$uppstmt->bind_param("ii",$productQuantity,$result);
			$uppstmt->execute();
			$uppstmt->close();
		} else {
			$addpstmt = $conn->prepare("INSERT INTO orderDetails(orderID, productID, quantity) VALUES (?,?,?)");
			$addpstmt->bind_param("iii", $orderID, $productID, $productQuantity);
			$addpstmt->execute();
			$addpstmt->close();
		}
		header("Location:./basket.php");
	}



	function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
	}
?>
