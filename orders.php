<html>
<head>
<title>S&W Orders</title>
<link rel="stylesheet" href="./stylesheet.css" type="text/css">
</head>

<body>
<?php
        session_start();
        $liEmail = $liName = $liRole = "";
        if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
            header("Location:../project/");
        }
        else {
            $liEmail = $_SESSION['email'];
            $liName = $_SESSION['firstName'];
            $liRole = $_SESSION['role'];
            $conn = new mysqli("localhost", "root", "budget", "project");
               if($conn->connect_error) {
                    die("Connect to mysql failed: " . $conn->connect_error);
               } 
        }

    ?>
    <h1 id="header">S&W Games and Toys - Orders</h1>
    <div id="main">
    <div id="links">
    <a href="../project/">Home</a>
    <a href="./logout.php">Log Out</a>
    </div>

    <div id="basket">
    <h3>Basket</h3>
    <hr>
    <?php
        $bstmt = $conn->prepare("SELECT MAX(id) FROM orders WHERE userEmail=?");
        $bstmt->bind_param("s",$liEmail);
        $bstmt->execute();
        $bstmt->bind_result($result);
        $bstmt->fetch();
        $bstmt->close();
        if($result) {
            $stmt = $conn->prepare("select D.id, P.name, P.price, D.quantity from orderDetails D inner join products P on P.id=D.productID where D.orderID=?");
            $stmt->bind_param("i",$result);
            $stmt->execute();
            $stmt->bind_result($orderDetailsID,$pname,$pprice,$pquantity);
            while($stmt->fetch()) {
                echo "<div class='product'>";
                echo "<form method='post' action='removeItem.php'>";
                echo "<div class='product_name'>{$pname}</div>";
                echo "<div class='product_price'>{$pprice}</div>";
                echo "<div class='product_quantity'>{$pquantity}</div>";
                echo "<input type='hidden' value='{$orderDetailsID}' name='orderDetailsID'>";
                echo "<input type='submit' class='add_product' value='Delete item'>";
                echo "</form>";
                echo "</div>";
            }
            if($orderDetailsID) {
                echo "<form action='./placeorder.php' method='post'>"; 
                echo "<input type='hidden' name='orderID' value={$result}>";
                echo "<input type='submit' action='' value='Place Order'>";
                echo "</form>";
            }
        }
            $stmt->close();
    ?>

    </div>
    <div id="orders">
    <h3>Orders</h3>
    <hr>
    <?php
    //get all orders from customer w/ their email. show all of them except the max one. 
    //all order ids from user not including most recent one.
    //select O.id, O.dateOrdered, O.dateShipped from orders O where O.id<>(select max(id) from orders O2 where O2.userEmail=?) and O.userEmail=?; 
    
    //ignore --> $stmt = $conn->prepare("SELECT id, dateOrdered, dateShipped from orders where id != (SELECT MAX(id) from orders where userEmail=?) AND userEmail=?");
    $stmt = $conn->prepare("select O.id, O.dateOrdered, O.dateShipped from orders O where O.id<>(select max(id) from orders O2 where O2.userEmail=?) and O.userEmail=?");
    $stmt->bind_param("ss",$liEmail,$liEmail);
    $stmt->execute();
    $stmt->bind_result($orderID,$dateOrdered,$dateShipped);
    while($stmt->fetch()) {
        //get all the products from the order
        echo "<div class='order'>";
        echo "<div class='orderHeader'>";
        echo "<h3>Ordered Date: {$dateOrdered}</h3>";
        if($dateShipped) {
            echo "<h3>Shipped Date: {$dateShipped}</h3>";
        }
        else {
            echo "<h3>Shipped Date: Pending (order being processed)</h3>";
        }
        echo "</div>";
        //here is where the items in the order are output
        $istmt = $conn->prepare("select P.name, P.price, D.quantity from orderDetails D inner join products P on P.id = D.productID where D.orderID = ?");
        echo "Order id: {$orderID}";
        $istmt->bind_param("i",$orderID);
        echo "Test";
        print_r($conn->error_list);
        $istmt->execute();
        echo "Test";
        $istmt->bind_result($pName, $pPrice, $pQuantity);
        while($istmt->fetch()) {
            echo "name: {$pName}\tquantity: {$pQuantity}\tprice: {$pPrice}<br>";
        }
        $istmt->close();
        echo "</div>";
    }
    $stmt->close();
    ?>    
    </div>
    </div>
</body>

</html>
