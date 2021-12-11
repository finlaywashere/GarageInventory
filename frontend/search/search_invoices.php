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
				<option value="1">Invoice #</option>
				<option value="2">Date</option>
				<option value="3">Customer ID</option>
			</select>
			<button id="search">Search</button>
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
					<th>Customer ID</th>
					<th>Original ID</th>
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
	xmlhttp.open("POST", "/inventory/api/public/invoice/get_invoices.php", true);
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
			var invoices = json.invoices;
			for(let i = 0; i < invoices.length; i++){
				var request2 = new XMLHttpRequest();
				request2.open('POST','/inventory/api/public/invoice/get_invoice.php',false);
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
					var date = document.createElement("td");
					date.innerHTML = json2.invoice['date'];
					entry.appendChild(date);
					var type = document.createElement("td");
					type.innerHTML = json2.invoice['type'];
					entry.appendChild(type);
					var id = document.createElement("td");
					var idLink = document.createElement("a");
					idLink.href="/inventory/frontend/invoice/get_invoice.php?id="+json2.invoice['invoice_id'];
					idLink.innerHTML = json2.invoice['invoice_id'];
					id.appendChild(idLink);
					entry.appendChild(id);
					var subtotal = document.createElement("td");
					subtotal.innerHTML = json2.invoice['subtotal'];
					entry.appendChild(subtotal);
					var total = document.createElement("td");
					total.innerHTML = json2.invoice['total'];
					entry.appendChild(total);
					var customer = document.createElement("td");
					customer.innerHTML = json2.invoice['customer'];
					entry.appendChild(customer);
					var originalId = document.createElement("td");
					originalId.innerHTML = json2.invoice['original_id'];
					entry.appendChild(originalId);
					var notes = document.createElement("td");
					notes.innerHTML = json2.invoice['notes'];
					entry.appendChild(notes);
					table.appendChild(entry);
				});
				request2.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				request2.send("invoice_id="+invoices[i]);
			}
		}else{
			error.innerHTML = "An error occurred while processing your request. Error Code: "+xmlhttp.status;
		}
	});
	xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlhttp.send("search_type="+type.value+"&search_param="+encodeURIComponent(param.value));
}

</script>
