<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

	$result = authenticate_request("inventory/customer");
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
			<label>Customer Search: </label><input id="search_param" type="text">
			<label>Type: </label>
			<select id="search_type">
				<option value="1">Name</option>
				<option value="2">Phone #</option>
				<option value="3">Email</option>
				<option value="4">ID</option>
			</select>
			<button id="search">Search</button>
			<button id="prev">Prev Page</button>
			<button id="next">Next Page</button>
			<p style="color: red;" id="error"></p>
		</div>
		<div class="content">
			<table id="results" class="table">
				<tr id="table_header">
					<th>ID</th>
					<th>Name</th>
					<th>Phone</th>
					<th>Email</th>
					<th>Address</th>
					<th>Type</th>
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
	var customers = get_customers(type.value,param.value,offset);
	if(!customers.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+customers.reason;
		return;
	}
	clearTable(table);
	offsets.push(customers.customers[customers.customers.length-1]);
	for(let i = 0; i < customers.customers.length; i++){
		var customer = get_customer(customers.customers[i]);
		if(!customer.success){
			console.log("Failed to retrieve some data!");
			error.innerHTML = "An error occurred while processing your request. Error: "+customer.reason;
			return;
		}
		var entry = document.createElement("tr");
		createElement("<a href=\"/inventory/frontend/customer/get_customer.php?id="+customers.customers[i]+"\">"+customers.customers[i]+"</a>",entry);
		createElement(customer.customer['name'],entry);
		createElement(customer.customer['phone'],entry);
		createElement(customer.customer['email'],entry);
		createElement(customer.customer['address'],entry);
		createElement(customer_type_to_string(customer.customer['type']),entry);
		table.appendChild(entry);
	}
}

</script>
