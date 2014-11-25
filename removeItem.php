<?php 
session_start();
if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("./orders.php");
}
else {
    $conn = new mysqli("localhost", "root", "budget", "project");
    if($conn->connect_error) {
        die("Connect to mysql failed: " . $conn->connect_error);
    }
    $orderDetailsID = test_input($_POST['orderDetailsID']);
    echo "Delete this orderDetailsID: {$orderDetailsID}<br>";
    $deletestmt = $conn->prepare('DELETE FROM orderDetails WHERE id=?');
    $deletestmt->bind_param("i",$orderDetailsID);
    $deletestmt->execute();
    $deletestmt->close();
    header("Location:./orders.php");
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
