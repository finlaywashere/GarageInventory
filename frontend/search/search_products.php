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
			<label>Invoice Search: </label><input id="search_param" type="text">
			<label>Type: </label>
			<select id="search_type">
				<option value="1">ID</option>
				<option value="2">Name</option>
				<option value="3">Location</option>
				<option value="4">Description</option>
			</select>
			<button id="search">Search</button>
			<p style="color: red;" id="error"></p>
		</div>
		<div class="content">
			<table id="results">
				<tr id="table_header">
					<th>ID</th>
					<th>Name</th>
					<th>Description</th>
					<th>Count</th>
					<th>Notes</th>
					<th>Location</th>
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
	var products = get_products(type.value,param.value);
	if(!products.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+products.reason;
		return;
	}
	clearTable(table);
	var p = products.products;
	for(let i = 0; i < p.length; i++){
		var product = get_product(p[i]);
		if(!product.success){
			console.log("Failed to retrieve some data!");
			error.innerHTML = "An error occurred while processing your request. Error: "+product.reason;
			return;
		}
		var entry = document.createElement("tr");
		createElement("<a href=\"/inventory/frontend/product/get_product.php?id="+p[i]+"\">"+p[i]+"</a>",entry);
		createElement(product.product['name'],entry);
		createElement(product.product['description'],entry);
		createElement(product.product['count'],entry);
		createElement(product.product['notes'],entry);
		createElement(product.product['location'],entry);
		table.appendChild(entry);
	}
}

</script>
