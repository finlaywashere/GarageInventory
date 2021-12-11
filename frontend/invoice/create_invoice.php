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
			<table id="results">
				<tr id="table_header">
					<th>Entry ID</th>
					<th>Product ID</th>
					<th>Count</th>
					<th>Unit Price</th>
					<th>Notes</th>
				</tr>
			</table>
		</div>
	</body>
</html>
<script>

var createButton = document.getElementById("search");
var error = document.getElementById("error");
createButton.addEventListener("click",);

function create(){
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open("POST", "/inventory/api/public/invoice/get_invoice_entries.php", true);
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
			var invoices = json.entries;
			for(let i = 0; i < invoices.length; i++){
				var request2 = new XMLHttpRequest();
				request2.open('POST','/inventory/api/public/invoice/get_invoice_entry.php',false);
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
					id.innerHTML = invoices[i];
					entry.appendChild(id);
					var product = document.createElement("td");
					product.innerHTML = json2.entry['product'];
					entry.appendChild(product);
					var count = document.createElement("td");
					count.innerHTML = json2.entry['count'];
					entry.appendChild(count);
					var price = document.createElement("td");
					price.innerHTML = json2.entry['unit_price'];
					entry.appendChild(price);
					var notes = document.createElement("td");
					notes.innerHTML = json2.entry['notes'];
					entry.appendChild(notes);
					table.appendChild(entry);
				});
				request2.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				request2.send("entry_id="+invoices[i]);
			}
		}else{
			error.innerHTML = "An error occurred while processing your request. Error Code: "+xmlhttp.status;
		}
	});
	xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlhttp.send("invoice_id="+param.value);
}

</script>
