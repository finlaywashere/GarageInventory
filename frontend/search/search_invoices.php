<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

	$result = authenticate_request("inventory/invoice");
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
			<label>Invoice Search: </label><input id="search_param" type="text">
			<label>Type: </label>
			<select id="search_type">
				<option value="1">Invoice #</option>
				<option value="2">Date</option>
				<option value="3">Customer ID</option>
			</select>
			<button id="search">Search</button>
			<button id="prev">Prev Page</button>
			<button id="next">Next Page</button>
			<p style="color: red;" id="error"></p>
		</div>
		<div class="content">
			<table id="results">
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
		</div>
	</body>
</html>
<script src="/assets/js/master.js"></script>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var searchButton = document.getElementById("search");
var param = document.getElementById("search_param");
var type = document.getElementById("search_type");
var error = document.getElementById("error");
var table = document.getElementById("results");
searchButton.addEventListener("click",search);

var next = document.getElementById("next");
var prev = document.getElementById("prev");

var offset = 0;
var offsets = [0];

next.addEventListener("click",nextpage);
prev.addEventListener("click",prevpage);

function nextpage(){
	var index = offsets.length-1;
	if(offsets[index] === undefined)
		return;
	offset = offsets[index];
	search();
}
function prevpage(){
	var index = offsets.length-3;
	if(index < 0)
		return;
	offset = offsets[index];
	offsets.pop();
	offsets.pop();
	search();
}

function search(){
	error.innerHTML = "";
	var invoicesJ = get_invoices(type.value,param.value,offset);
	if(!invoicesJ.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+invoicesJ.reason;
		return;
	}
	clearTable(table);
	var invoices = invoicesJ.invoices;
	offsets.push(invoices[invoices.length-1]);
	for(let i = 0; i < invoices.length; i++){
		var invoice = get_invoice(invoices[i]);
		if(!invoice.success){
			console.log("Failed to retrieve some data!");
			error.innerHTML = "An error occurred while processing your request. Error: "+invoice.reason;
			return;
		}
		var customer = get_customer(invoice.invoice['customer']);
		if(!customer.success){
			console.log("Failed to retrieve some data!");
			error.innerHTML = "An error occurred while processing your request. Error: "+customer.reason;
			return;
		}
		var entry = document.createElement("tr");
		createElement(invoice.invoice['date'],entry);
		var iType = invoice.invoice['type'];
		createElement(invoice_type_to_string(iType),entry);
		createElement("<a href=\"/inventory/frontend/invoice/get_invoice.php?id="+invoice.invoice['invoice_id']+"\">"+invoice.invoice['invoice_id']+"</a>",entry);
		createElement("$"+invoice.invoice['subtotal']/100,entry);
		createElement("$"+invoice.invoice['total']/100,entry);

		createElement(customer.customer['name'],entry);
		createElement(invoice.invoice['original_id'],entry);
		createElement(invoice.invoice['notes'],entry);
		table.appendChild(entry);
	}
}

</script>
