<?php
    require_once "../../private/authentication.php";

    $result = authenticate_request(0);
    if($result == 0){
        header("Location: /authentication/frontend/login.php?referrer=/authentication/frontend/index.php");
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
			<div class="content">
				<ul style="display: inline-block;">
				<li><h2><a href="search/search_journal.php">Search Journal</a></h2></li>
				<li><h2><a href="search/search_products.php">Search Products</a></h2></li>
				<li><h2><a href="search/search_invoices.php">Search Invoices</a></h2></li>
			</div>
		</body>
</html>
