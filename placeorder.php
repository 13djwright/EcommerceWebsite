<?php 
	session_start();
	if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
		header("Location:../project/");
	}
	else{
		include_once("./config.php"); 
		$email = $_SESSION['email'];
		$orderID = $_POST['orderID'];
		$total = $_POST['total'];
		$date = date("Y-m-d");
		/*
		$stmt = $conn->prepare("select SUM(P.price*D.quantity) from orderDetails D inner join products P on P.id=D.productID where D.orderID=?");
		$stmt->bind_param("i", $orderID);
		$stmt->execute();
		$stmt->bind_result($total);
		$stmt->fetch();
		$stmt->close();
		*/
		
		//FIXME:	Could have problem here when updating if the value will go negative. make this a transaction
		//			and if it rollsback inform the the order could not be proccessed and what not
		$stmt = $conn->prepare("update products P, orderDetails D set P.quantity=P.quantity-D.quantity where P.id=D.productID and D.orderID=?");
		$stmt->bind_param("i", $orderID);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		echo "test";
		$stmt = $conn->prepare("update orderDetails D, products P set D.price_bought_at = case when CURDATE() >= P.promoFrom and CURDATE() <= P.promoTo then round(P.price-(P.price*(P.promoDiscount/100)),2) else P.price end where P.id=D.productID and D.orderID=?");
		$stmt->bind_param("i", $orderID);
		$stmt->execute();
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
