<html>
<head>
<title>S&W Register new User</title>
<link rel="stylesheet" type="text/css" href="./stylesheet.css"/>
</head>
<body>
<?php
//TODO: check that all data is here check primary key is not taken insert the data into database

$addressErr = $firstNameErr = $lastNameErr = $emailErr = $passwordErr = $repasswordErr = $zipCodeErr = $stateErr = "";
include_once("./config.php");
$address = $firstName = $lastName = $email = $password = $repassword = $zipCode = $state = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
//check that each variable is not empty
if (empty($_POST["firstName"])) {
    $firstNameErr = "First Name is required";
}else {
    $firstName = test_input($_POST["firstName"]);
    if (!preg_match("/^[a-zA-z]*$/",$firstName)) {
        $firstNameErr = "Only letters allowed";
    }
}
if (empty($_POST["lastName"])) {
    $lastNameErr = "Last Name is required";
}else {
    $lastName = test_input($_POST["lastName"]);
    if (!preg_match("/^[a-zA-z]*$/",$lastName)) {
        $lastNameErr = "Only letters allowed";
    }

}
if (empty($_POST["email"])) {
    $emailErr = "Email is required";
}else {
    $email = test_input($_POST["email"]);
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    } else {
    }
}
if (empty($_POST["password"])) {
    $passwordErr = "Password is required";
}else {
    if (empty($_POST["repassword"])) {
        $repasswordErr = "Re-entering password is required";
    }else {
        $password = test_input($_POST["password"]);
        $repassword = test_input($_POST["repassword"]);
        //$hash = password_hash($password, PASSWORD_DEFAULT);
        if($password !== $repassword) {
            $passwordErr = $repasswordErr = "Passwords do not match";
        }
    }

}
if (empty($_POST["zipCode"])) {
    $zipCodeErr = "Zipcode is required";
}else {
    $zipCode = test_input($_POST["zipCode"]);
    if(!preg_match("/^[0-9]{5}$/",$zipCode)) {
        $zipCodeErr = "Invalid Zipcode";
    }
}
if (empty($_POST["state"])) {
    $stateErr = "State initials are required";
}else {
    $state = strtoupper(test_input($_POST["state"]));
    if(!preg_match("/^[A-Z]{2}$/",$state)) {
        $stateErr = "Not valid state initials";
    }
}
if (empty($_POST["address"])) {
    $addressErr = "Address is required";
}else {
    $address = test_input($_POST["address"]);
}

//Check to make sure there are no errors, 
if(
$firstNameErr === "" && 
$lastNameErr === "" && 
$emailErr === "" && 
$passwordErr === "" && 
$repasswordErr === "" && 
$addressErr === "" && 
$zipCodeErr === "" && 
$stateErr === "") {
        
    $stmt = $conn->prepare("SELECT email FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();
    if(!$result) {
        //new email so take all of the data and insert into users table
        $stmt = $conn->prepare("INSERT INTO users(firstName, lastName, email, password, address, zipCode, state) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssis", $firstName, $lastName, $email, $password, $address, $zipCode, $state);
        $stmt->execute();
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO orders(userEmail) VALUES (?)");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $stmt->close();
        echo ("<script language='javascript'>window.alert('Registration Successful. You will no be directed back to the homepage.');
        window.location='../project/';
        </script>"); 
    }
    else{
        $emailErr = "Email address already used.";
    }
}


}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}



?>

<p><span class="error">* required field</span></p>
<div class="register">
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
<label>First name: </label><input type="text" name="firstName" value="<?php echo $firstName;?>">
<span class="error">* <?php echo $firstNameErr;?></span>
<br>
<label>Last name:</label> <input type="text" name="lastName" value="<?php echo $lastName;?>">
<span class="error">* <?php echo $lastNameErr;?></span>
<br>
<label>Email:</label> <input type="text" name="email" value="<?php echo $email;?>">
<span class="error">* <?php echo $emailErr;?></span>
<br>
<label>Password:</label> <input type="password" name="password">
<span class="error">* <?php echo $passwordErr;?></span>
<br>
<label>Re-enter:</label> <input type="password" name="repassword">
<span class="error">* <?php echo $repasswordErr;?></span>
<br>
<label>Address:</label> <input type="text" name="address" value="<?php echo $address;?>">
<span class="error">* <?php echo $addressErr;?></span>
<br>
<label>Zipcode:</label> <input type="text" name="zipCode" value="<?php echo $zipCode;?>">
<span class="error">* <?php echo $zipCodeErr;?></span>
<br>
<label>State Abrv:</label> <input type="text" name="state" value="<?php echo $state;?>">
<span class="error">* <?php echo $stateErr;?></span>
<br>
<div class="buttons">
<input type="submit" value="Submit">
<a href="../project/">
<input type="button" value="Cancel">
</a>
</div>
</form>
</div>
</body>
</html>
