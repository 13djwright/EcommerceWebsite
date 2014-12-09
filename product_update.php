<?php
	session_start();
	if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
		header("Location:../project/");
	}
	else{ 
		include_once("./config.php"); 
		$email = $_SESSION['email'];
		if(!($_SESSION['role'] == "MANAGER" || $_SESSION['role'] == "STAFF")) {
			header("Location:../project/");
		}
		
		$productID = $_POST["product_id"];
		$productQuantity = $_POST["product_quantity"];
		$productName = $_POST['product_name'];
		$productPrice = $_POST['product_price'];
		$promoFrom = $_POST['promo_from'];
		$promoTo = $_POST['promo_to'];
		$promoDiscount = $_POST['promo_discount'];
		$promoFrom = $promoFrom ?: NULL;
		$promoTo = $promoTo ?: NULL;
		echo $productPrice . "\t" . $productQuantity . "\t" . $productName . "\t" . $promoFrom . "\t" . $promoTo;
		$stmt = $conn->prepare("update products P set P.price = ?, P.quantity = ?, P.name = ?, P.promoFrom = ?, P.promoTo = ?, P.promoDiscount = ? where P.id=?");
		if(!$stmt) {
			echo "prep fail";
		}
		if(!$stmt->bind_param("disssii", $productPrice, $productQuantity, $productName, $promoFrom, $promoTo,$promoDiscount, $productID)) {
			echo "bind fail";
		}
		if(!$stmt->execute()) {
			echo "ex fail";
		}
		$stmt->close();
		header("Location:./products.php");
	}
?>
