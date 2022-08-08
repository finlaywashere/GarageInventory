<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

	$result = authenticate_request(0);
	if($result == 0){
		force_login();
	}
?>
<html>
	<head>
		<title>Internal Inventory Services</title>
		<link rel="stylesheet" type="text/css" href="assets/css/main.css">
		<link rel="stylesheet" type="text/css" href="/frontend/assets/css/main.css">
	</head>
	<body>
		<?php require($_SERVER['DOCUMENT_ROOT']."/frontend/header.php");?>
		<div class="subheader" style="display: inline-block;">
			<label>Invoice ID: </label><input id="search_param" type="number">
			<button id="search">Search</button>
			<p style="color: red;" id="error"></p>
		</div>
		<div class="content">
			<h2>Invoice Information</h2>
			<table id="invoice_table">
				<tr id="table_header">
					<th>Date</th>
					<th>Type</th>
					<th>ID</th>
					<th>Subtotal</th>
					<th>Total</th>
					<th>Customer</th>
					<th>Original ID</th>
					<th>Notes</th>
				</tr>
			</table>
			<br>
			<h2>Entries</h2>
			<table id="results">
				<tr id="table_header">
					<th>Product ID</th>
					<th>Original ID</th>
					<th>Product Name</th>
					<th>Count</th>
					<th>Unit Count</th>
					<th>Unit Price</th>
					<th>Unit Discount</th>
					<th>Notes</th>
					<th>Entry Total</th>
				</tr>
			</table>
			<h2>Payments</h2>
			<table id="payments">
				<tr id="table_header">
					<th>User</th>
					<th>Amount</th>
					<th>Type</th>
					<th>Date</th>
					<th>Identifier</th>
					<th>Notes</th>
				</tr>
			</table>
		</div>
	</body>
</html>
<script src="/assets/js/master.js"></script>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var searchButton = document.getElementById("search");
var param = document.getElementById("search_param");
var error = document.getElementById("error");
var table = document.getElementById("results");
var iTable = document.getElementById("invoice_table");
var pTable = document.getElementById("payments");
searchButton.addEventListener("click",search);

var params = getSearchParameters();
if(params.id != undefined){
	param.value = params.id;
	search();
}

function search(){
	error.innerHTML = "";
	clearTable(table);
	clearTable(iTable);
	clearTable(pTable);
	var invoice = get_invoice(param.value);
	if(!invoice.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+invoice.reason;
		return;
	}
	var customer = get_customer(invoice.invoice['customer']);
	if(!customer.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+customer.reason;
		return;
	}
	var entry = document.createElement("tr");
	createElement(invoice.invoice['date'],entry);
	var iType = invoice.invoice['type'];
	createElement(invoice_type_to_string(iType),entry);
	createElement(invoice.invoice['invoice_id'],entry);
	var subtotal = invoice.invoice['subtotal']/100;
	createElement("$"+subtotal.toFixed(2),entry);
	createElement("$"+(invoice.invoice['total']/100).toFixed(2),entry);
	createElement(customer.customer['name'],entry);
	createElement(invoice.invoice['original_id'],entry);
	createElement(invoice.invoice['notes'],entry);
	iTable.appendChild(entry);
	
	var entries = invoice.invoice['entries'];
	var total = 0;
	for(let i = 0; i < entries.length; i++){
		var entry = entries[i];
		var product = get_product(entry['product']);
		if(!product.success){
			console.log("Failed to retrieve product name!");
			error.innerHTML = "An error occurred while gathering product information! Error: "+product.reason;
			return;
		}
		var tEntry = document.createElement("tr");
		createElement(entry['product'],tEntry);
		createElement(entry['original_id'],tEntry);
		createElement(product.product['name'],tEntry);
		createElement(entry['count'],tEntry);
		createElement(entry['unit_count'],tEntry);
		var price = entry['unit_price'] / 100;
		createElement("$"+price.toFixed(2),tEntry);
		var discount = entry['unit_discount'] / 100;
		createElement("$"+discount.toFixed(2),tEntry);
		createElement(entry['notes'],tEntry);
		var lTotal = (entry['count']/entry['unit_count']*(price-discount));
		createElement("$"+lTotal.toFixed(2), tEntry)
		total += lTotal;
		table.appendChild(tEntry);
	}
	var payments = invoice.invoice['payments'];
	for(let i = 0; i < payments.length; i++){
		var payment = payments[i];
		var tEntry = document.createElement("tr");
		createElement(payment['user'],tEntry);
		createElement("$"+(payment['amount']/100).toFixed(2),tEntry);
		createElement(payment_type_to_string(payment['type']),tEntry);
		createElement(payment['date'],tEntry);
		createElement(payment['identifier'],tEntry);
		createElement(payment['notes'],tEntry);
		pTable.appendChild(tEntry);
	}
	if(total.toFixed(2) != subtotal.toFixed(2)){
		error.innerHTML = "Warning: Subtotal does not equal sum of entries!";
	}
}

</script>
