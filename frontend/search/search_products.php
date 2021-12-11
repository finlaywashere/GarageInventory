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
			<label>Product Search: </label><input id="search_param" type="text">
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
					<th>Orig ID</th>
					<th>Name</th>
					<th>Desc</th>
					<th>Count</th>
					<th>Location</th>
					<th>Notes</th>
				</tr>
			</table>
		</div>
	</body>
</html>
<script>

var searchButton = document.getElementById("search");
var param = document.getElementById("search_param");
var type = document.getElementById("search_type");
var error = document.getElementById("error");
var table = document.getElementById("results");
searchButton.addEventListener("click",search);

function clearTable(){
	var children = table.querySelectorAll('tr')
	for(let i = 0; i < children.length; i++){
		let found = false;
		if(children[i].childNodes != undefined){
			for(let i1 = 0; i1 < children[i].childNodes.length; i1++){
				var child = children[i].childNodes[i1];
				if(child.nodeName == "TH")
					found = true;
			}
		}
		if(found)
			continue;
		var child = children[i];
		var parent = children[i].parentNode;
		parent.removeChild(child);
	}
}

function search(){
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open("POST", "/inventory/api/public/product/get_products.php", true);
	xmlhttp.addEventListener("load",function() {
		if(xmlhttp.readyState != 4)
			return;
		if (xmlhttp.status==200) {
			var json = JSON.parse(this.responseText);
			if(!json.success){
				console.log("Failed to retrieve data!");
				error.innerHTML = "An error occurred while processing your request. Error: "+json.reason;
				return;
			}
			clearTable();
			var products = json.products;
			for(let i = 0; i < products.length; i++){
				var request2 = new XMLHttpRequest();
				request2.open('POST','/inventory/api/public/product/get_product.php',false);
				request2.addEventListener("load",function() {
					if(request2.readyState != 4)
						return;
					if (request2.status != 200) {
						error.innerHTML = "An error occurred while processing your request. Error Code: "+request2.status;
						console.log("Error occurred! Code: "+request2.status);
						console.log(request2.readyState);
						return;
					}
					var json2 = JSON.parse(request2.responseText);
					if(!json2.success){
						console.log("Failed to retrieve some data!");
						error.innerHTML = "An error occurred while processing your request. Error: "+json2.reason;
						return;
					}
					var entry = document.createElement("tr");
					var id = document.createElement("td");
					id.innerHTML = products[i];
					entry.appendChild(id);
					var oid = document.createElement("td");
					oid.innerHTML = json2.product['original_id'];
					entry.appendChild(oid);
					var name = document.createElement("td");
					name.innerHTML = json2.product['name'];
					entry.appendChild(name);
					var desc = document.createElement("td");
					desc.innerHTML = json2.product['description'];
					entry.appendChild(desc);
					var count = document.createElement("td");
					count.innerHTML = json2.product['count'];
					entry.appendChild(count);
					var location = document.createElement("td");
					location.innerHTML = json2.product['location'];
					entry.appendChild(location);
					var notes = document.createElement("td");
					notes.innerHTML = json2.product['notes'];
					entry.appendChild(notes);
					table.appendChild(entry);
				});
				request2.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				request2.send("product_id="+products[i]);
			}
		}else{
			error.innerHTML = "An error occurred while processing your request. Error Code: "+xmlhttp.status;
		}
	});
	xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlhttp.send("search_type="+type.value+"&search_param="+encodeURIComponent(param.value));
}

</script>
