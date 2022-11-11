<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

	$result = authenticate_request("inventory/product");
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
			<label>Product Search: </label><input id="search_param" type="text">
			<label>Type: </label>
			<select id="search_type">
				<option value="1">ID</option>
				<option value="2">Name</option>
				<option value="3">Location</option>
				<option value="4">Description</option>
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
					<th>Description</th>
					<th>Count</th>
					<th>Notes</th>
					<th>Location</th>
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

searchButton.addEventListener("click",searchB);

function searchB(){
	offset = 0;
	search();
}

function search(){
	var products = get_products(type.value,param.value,offset);
	if(!products.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+products.reason;
		return;
	}
	clearTable(table);
	var p = products.products;
	var lastElem = p[p.length-1];
	offsets.push(lastElem);
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
