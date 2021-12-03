<?php
    require_once "../../private/authentication.php";

    $result = authenticate_request(0);
    if($result == 0){
        header("Location: login.php?referrer=/authentication/frontend/index.php");
        die("Please log in!");
    }
?>

<html>
	<head>
		<title>Internal Inventory Services</title>
		<link rel="stylesheet" type="text/css" href="assets/css/main.css">
		<link rel="stylesheet" type="text/css" href="/frontend/assets/css/main.css">
	</head>
		<body>
			<?php require("../../frontend/header.php");?>
			<?php require("private/stock.php");?>
		</body>
</html>
