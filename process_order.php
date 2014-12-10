<?php 
	session_start();
	if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
		header("Location:./orders.php");
	}
	else{
		include_once("./config.php"); 
		if(!($_SESSION['role'] == "STAFF" || $_SESSION['role'] == "MANAGER")) {
			header("Location: ../project/");
		}
		$orderID = $_POST['orderID'];
		$date = date("Y-m-d");
		$stmt = $conn->prepare("update orders405 O set O.dateShipped=? where O.id=?");
		$stmt->bind_param("si", $date, $orderID);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		header("Location:./orders.php");
	}
?>
