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
					<th>Product Name</th>
					<th>Count</th>
					<th>Unit Price</th>
					<th>Notes</th>
				</tr>
			</table>
		</div>
	</body>
</html>
<script>

var searchButton = document.getElementById("search");
var param = document.getElementById("search_param");
var error = document.getElementById("error");
var table = document.getElementById("results");
searchButton.addEventListener("click",search);

// From https://stackoverflow.com/questions/5448545/how-to-retrieve-get-parameters-from-javascript/
function getSearchParameters() {
	var prmstr = window.location.search.substr(1);
	return prmstr != null && prmstr != "" ? transformToAssocArray(prmstr) : {};
}

function transformToAssocArray( prmstr ) {
	var params = {};
	var prmarr = prmstr.split("&");
	for ( var i = 0; i < prmarr.length; i++) {
		var tmparr = prmarr[i].split("=");
		params[tmparr[0]] = tmparr[1];
	}
	return params;
}
var params = getSearchParameters();
if(params.id != undefined){
	param.value = params.id;
	search();
}

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
					var xhr = new XMLHttpRequest();
					xhr.open("POST","/inventory/api/public/product/get_product.php",true);
					xhr.addEventListener("load",function(){
						var json3 = JSON.parse(xhr.responseText);
						if(!json3.success){
							console.log("Failed to retrieve product name!");
							error.innerHTML = "An error occurred while gathering product information! Error: "+json3.reason;
							return;
						}
						var entry = document.createElement("tr");
						var id = document.createElement("td");
						id.innerHTML = invoices[i];
						entry.appendChild(id);
						var product = document.createElement("td");
						product.innerHTML = json2.entry['product'];
						entry.appendChild(product);
						var pName = document.createElement("td");
						pName.innerHTML = json3.product['name'];
						entry.appendChild(pName);
						var count = document.createElement("td");
	                    count.innerHTML = json2.entry['count'];
	                    entry.appendChild(count);
	                    var price = document.createElement("td");
	                    price.innerHTML = "$"+json2.entry['unit_price'] / 100;
	                    entry.appendChild(price);
	                    var notes = document.createElement("td");
	                    notes.innerHTML = json2.entry['notes'];
	                    entry.appendChild(notes);
	                    table.appendChild(entry);
						
					});
					xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					xhr.send("product_id="+json2.entry['product']);
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
