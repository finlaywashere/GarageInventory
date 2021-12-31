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
			<label>Customer Search: </label><input id="search_param" type="text">
			<label>Type: </label>
			<select id="search_type">
				<option value="1">Name</option>
				<option value="2">Phone #</option>
				<option value="3">Email</option>
				<option value="4">ID</option>
			</select>
			<button id="search">Search</button>
			<p style="color: red;" id="error"></p>
		</div>
		<div class="content">
			<table id="results">
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
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var searchButton = document.getElementById("search");
var param = document.getElementById("search_param");
var type = document.getElementById("search_type");
var error = document.getElementById("error");
var table = document.getElementById("results");
searchButton.addEventListener("click",search);

function search(){
	var customers = get_customers(type.value,param.value);
	if(!customers.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+customers.reason;
		return;
	}
	clearTable(table);
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
