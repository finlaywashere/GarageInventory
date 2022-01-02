<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";

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
					<th>Entry ID</th>
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
		</div>
	</body>
</html>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var searchButton = document.getElementById("search");
var param = document.getElementById("search_param");
var error = document.getElementById("error");
var table = document.getElementById("results");
var iTable = document.getElementById("invoice_table");
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
	createElement("$"+subtotal,entry);
	createElement("$"+(invoice.invoice['total']/100),entry);
	createElement(customer.customer['name'],entry);
	createElement(invoice.invoice['original_id'],entry);
	createElement(invoice.invoice['notes'],entry);
	iTable.appendChild(entry);
	
	var entries = get_invoice_entries(param.value);
	if(!entries.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+entries.reason;
		return;
	}
	var total = 0;
	var invoices = entries.entries;
	for(let i = 0; i < invoices.length; i++){
		var entry = get_invoice_entry(invoices[i]);
		if(!entry.success){
			console.log("Failed to retrieve some data!");
			error.innerHTML = "An error occurred while processing your request. Error: "+entry.reason;
			return;
		}
		var product = get_product(entry.entry['product']);
		if(!product.success){
			console.log("Failed to retrieve product name!");
			error.innerHTML = "An error occurred while gathering product information! Error: "+product.reason;
			return;
		}
		var tEntry = document.createElement("tr");
		createElement(invoices[i],tEntry);
		createElement(entry.entry['product'],tEntry);
		createElement(entry.entry['original_id'],tEntry);
		createElement(product.product['name'],tEntry);
		createElement(entry.entry['count'],tEntry);
		createElement(entry.entry['unit_count'],tEntry);
		var price = entry.entry['unit_price'] / 100;
		createElement("$"+price,tEntry);
		var discount = entry.entry['unit_discount'] / 100;
		createElement("$"+discount,tEntry);
		createElement(entry.entry['notes'],tEntry);
		var lTotal = (entry.entry['count']/entry.entry['unit_count']*(price-discount));
		createElement("$"+lTotal.toFixed(2), tEntry)
		total += lTotal;
		table.appendChild(tEntry);
	}
	if(total.toFixed(2) != subtotal.toFixed(2)){
		error.innerHTML = "Warning: Subtotal does not equal sum of entries!";
	}
}

</script>
