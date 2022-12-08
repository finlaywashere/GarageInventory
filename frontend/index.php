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
			<div class="container">
				<div class="row text-center">
					<div class="col">
						<h3>Search</h3>
						<h3><a class="btn btn-secondary" href="search/search_journal.php">Search Journal</a></h3>
						<h3><a class="btn btn-secondary" href="search/search_invoices.php">Search Invoices</a></h3>
						<h3><a class="btn btn-secondary" href="search/search_customers.php">Search Customers</a></h3>
						<h3><a class="btn btn-secondary" href="search/search_products.php">Search Products</a></h3>
						<h3><a class="btn btn-secondary" href="search/search_accounts.php">Search Accounts</a></h3>
					</div>
					<div class="col">
						<h3>Create</h3>
						<h3><a class="btn btn-secondary" href="customer/create_customer.php">Create Customer</a></h3>
						<h3><a class="btn btn-secondary" href="invoice/create_invoice.php">Create Invoice</a></h3>
						<h3><a class="btn btn-secondary" href="product/create_product.php">Create Product</a></h3>
					</div>
					<div class="col">
						<h3>Inquire</h3>
							<h3><a class="btn btn-secondary" href="product/get_product.php">Product Information</a></h3>
							<h3><a class="btn btn-secondary" href="invoice/get_invoice.php">Invoice Information</a></h3>
							<h3><a class="btn btn-secondary" href="customer/get_customer.php">Customer Information</a></h3>
					</div>
					<?php
						if(authenticate_request("inventory/cash")){
					?>
					<div class="col">
						<h3>Cash</h3>
						<h3><a class="btn btn-secondary" href="cash/count_cash.php">Count Cash</a></h3>
						<h3><a class="btn btn-secondary" href="cash/create_cash.php">Create Cash Location</a></h3>
						<h3><a class="btn btn-secondary" href="cash/get_cash.php">Get Cash</a></h3>
						<h3><a class="btn btn-secondary" href="cash/pay_account.php">Pay Account</a></h3>
					</div>
					<?php
						}
					?>
					<?php
						if(authenticate_request("inventory/admin")){
					?>
					<div class="col">
						<h3>Administrative</h3>
						<h3><a class="btn btn-secondary" href="payment/create_account.php">Create Account</a></h3>
						<h3><a class="btn btn-secondary" href="payment/get_account.php">Get Account</a></h3>
						<h3><a class="btn btn-secondary" href="payment/deposit_account.php">Bank Deposits</a></h3>
					</div>
					<?php
						}
					?>
				</div>
			</div>
		</body>
</html>
