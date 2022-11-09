<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

    $result = authenticate_request("inventory");
    if($result == 0){
    	force_login();
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
			<div class="content" style="display: flex;">
				<div class="block">
					<h3>Search</h3>
					<ul>
						<li><h3><a href="search/search_journal.php">Search Journal</a></h3></li>
						<li><h3><a href="search/search_invoices.php">Search Invoices</a></h3></li>
						<li><h3><a href="search/search_customers.php">Search Customers</a></h3></li>
						<li><h3><a href="search/search_products.php">Search Products</a></h3></li>
						<li><h3><a href="search/search_accounts.php">Search Accounts</a></h3></li>
					</ul>
				</div>
				<div class="block">
					<h3>Create</h3>
					<ul>
						<li><h3><a href="customer/create_customer.php">Create Customer</a></h3></li>
						<li><h3><a href="invoice/create_invoice.php">Create Invoice</a></h3></li>
						<li><h3><a href="product/create_product.php">Create Product</a></h3></li>
					</ul>
				</div>
				<div class="block">
					<h3>Inquire</h3>
					<ul>
						<li><h3><a href="product/get_product.php">Product Information</a></h3></li>
						<li><h3><a href="invoice/get_invoice.php">Invoice Information</a></h3></li>
						<li><h3><a href="customer/get_customer.php">Customer Information</a></h3></li>
					</ul>
				</div>
				<?php
					if(authenticate_request("inventory/cash")){
				?>
				<div class="block">
					<h3>Cash</h3>
					<ul>
						<li><h3><a href="cash/count_cash.php">Count Cash</a></h3></li>
						<li><h3><a href="cash/create_cash.php">Create Cash Location</a></h3></li>
						<li><h3><a href="cash/get_cash.php">Get Cash</a></h3></li>
						<li><h3><a href="cash/pay_account.php">Pay Account</a></h3></li>
					</ul>
				</div>
				<?php
					}
				?>
				<?php
					if(authenticate_request("inventory/admin")){
				?>
						<div class="block">
							<h3>Administrative</h3>
							<ul>
								<li><h3><a href="payment/create_account.php">Create Account</a></h3></li>
								<li><h3><a href="payment/get_account.php">Get Account</a></h3></li>
							</ul>
						</div>
				<?php
					}
				?>
			</div>
		</body>
</html>
