<?php 
session_start();
if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("Location:./orders.php");
}
else{
   $conn = new mysqli("localhost", "root", "budget", "project");

    if($conn->connect_error) {
        die("Connect to mysql failed: " . $conn->connect_error);
    }
    echo "test";
    $email = $_SESSION['email'];
    $orderID = $_POST['orderID'];
    $date = date("Y-m-d");
    echo "email: {$email} placed an orderNum {$orderID} at {$date}.";
    $stmt = $conn->prepare("UPDATE orders SET dateOrdered=? WHERE id=?");
    $stmt->bind_param("si",$date,$orderID);
    $stmt->execute();
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO orders(userEmail) VALUES (?)");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->close();
    header("Location:./orders.php");
     
/*
Pass in orderID from previous button (use input hidden with value of the orderID.
Change this order to have the dateOrdered=right now.
Insert a new row into the orders table with same email.
When displaying previous orders show all except the highest valued one (which is the current basket).
Add a total to the order? (not sure how to calculate this?)

*/

}



?>
