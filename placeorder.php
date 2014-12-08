<?php 
	session_start();
	if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
		header("Location:./orders.php");
	}
	else{
		include_once("./config.php"); 
		$email = $_SESSION['email'];
		$orderID = $_POST['orderID'];
		$date = date("Y-m-d");
		$stmt = $conn->prepare("select SUM(P.price*D.quantity) from orderDetails D inner join products P on P.id=D.productID where D.orderID=?");
		$stmt->bind_param("i", $orderID);
		$stmt->execute();
		$stmt->bind_result($total);
		$stmt->fetch();
		$stmt->close();
		echo "email: {$email} placed an orderNum {$orderID} at {$date}.";
		$stmt = $conn->prepare("UPDATE orders SET dateOrdered=?, total=? WHERE id=?");
		$stmt->bind_param("sdi",$date,$total,$orderID);
		$stmt->execute();
		$stmt->close();
		$stmt = $conn->prepare("INSERT INTO orders(userEmail) VALUES (?)");
		$stmt->bind_param("s",$email);
		$stmt->execute();
		$stmt->close();
		header("Location:./previous_orders.php");
	}
?>
