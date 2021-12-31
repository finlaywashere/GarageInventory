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
		<link rel="stylesheet" type="text/css" href="/frontend/assets/css/main.css">
		<link rel="stylesheet" type="text/css" href="assets/css/main.css">
	</head>
		<body>
			<?php require("../../frontend/header.php");?>
			<div class="content">
				<ul>
					<li><h3><a href="search/search_journal.php">Search Journal</a></h3></li>
					<li><h3><a href="search/search_invoices.php">Search Invoices</a></h3></li>
					<li><h3><a href="search/search_customers.php">Search Customers</a></h3></li>
					<li><h3><a href="search/search_products.php">Search Products</a></h3></li>
				</ul>
				<br>
				<ul>
					<li><h3><a href="customer/create_customer.php">Create Customer</a></h3></li>
					<li><h3><a href="invoice/create_invoice.php">Create Invoice</a></h3></li>
					<li><h3><a href="product/create_product.php">Create Product</a></h3></li>
				</ul>
				<br>
				<ul>
					<li><h3><a href="product/get_product.php">Product Information</a></h3></li>
					<li><h3><a href="invoice/get_invoice.php">Invoice Information</a></h3></li>
					<li><h3><a href="customer/get_customer.php">Customer Information</a></h3></li>
				</ul>
			</div>
		</body>
</html>
