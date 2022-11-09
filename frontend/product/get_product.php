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
		<link rel="stylesheet" type="text/css" href="../assets/css/main.css">
		<link rel="stylesheet" type="text/css" href="/frontend/assets/css/main.css">
	</head>
	<body>
		<?php require($_SERVER['DOCUMENT_ROOT']."/frontend/header.php");?>
		<div class="subheader" style="display: inline-block;">
			<label>Product ID: </label><input id="search_param" type="number" min="0">
			<button id="search">Search</button>
			<button id="save">Save</button>
			<p style="color: red;" id="error"></p>
		</div>
		<div class="content">
			<h2>Product Information</h2>
			<table id="results" style="width: 80%;">
				<tr id="table_header">
					<th>Name</th>
					<th>Description</th>
					<th>Count</th>
					<th>Notes</th>
					<th>Stock Code</th>
					<th>Location</th>
					<th>Average Price</th>
				</tr>
			</table><br>
			<h2>History</h2>
			<table id="table_history" style="width: 80%;">
				<tr id="table_header">
					<th>Type</th>
					<th>Invoice</th>
					<th>Date</th>
					<th>Customer</th>
				</tr>
			</table>
		</div>
	</body>
</html>
<script src="/assets/js/master.js"></script>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var searchButton = document.getElementById("search");
var saveButton = document.getElementById("save");
var param = document.getElementById("search_param");
var error = document.getElementById("error");
var table = document.getElementById("results");
var t_history = document.getElementById("table_history");

var nameH = null;
var desc = null;
var notes = null;
var loc = null;
var code = null;
var pid;

searchButton.addEventListener("click",search);
saveButton.addEventListener("click",save);

var params = getSearchParameters();
if(params.id != undefined){
	param.value = params.id;
	search();
}

function save(){
	if(nameH == null) return;
	var nameS = nameH.innerHTML;
	var descS = desc.innerHTML;
	var notesS = notes.innerHTML;
	var locS = loc.innerHTML;
	var codeS = code.innerHTML;
	var codeI = product_string_to_type(codeS);
	var json = update_product(pid,nameS,descS,notesS,codeI,locS);
	if(!json.success){
		console.log("Failed to save data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+json.reason;
		return;
	}
}

function search(){
	var json = get_product(param.value);
	clearTable(table);
	clearTable(t_history);
	error.innerHTML = "";
	if(!json.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+json.reason;
		return;
	}
	var hist = get_product_history(param.value);
	if(!hist.success){
		console.log("Failed to retrieve history!");
		error.innerHTML = "An error occurred while processing your request. Error: "+json.reason;
		return;
	}
	var entry = document.createElement("tr");
	nameH = createEditableElement(json.product['name'],entry);
	desc = createEditableElement(json.product['description'],entry);
	createElement(json.product['count'],entry);
	notes = createEditableElement(json.product['notes'],entry);
	code = createEditableElement(product_type_to_string(json.product['code']),entry);
	loc = createEditableElement(json.product['location'],entry);
	createElement("$"+(json.product['average_price']/100).toFixed(2),entry);
	table.appendChild(entry);
	if(hist.history.length > 0){
		for(let i = 0; i < hist.history.length; i++){
			var a = hist.history[i];
			var entry = document.createElement("tr");
			createElement(invoice_type_to_string(a['type']),entry);
			createElement("<a href=\"/inventory/frontend/invoice/get_invoice.php?id="+a['invoice_id']+"\">"+a['invoice_id']+"</a>",entry);
			createElement(a['date'],entry);
			var cid = a['customer'];
			createElement("<a href=\"/inventory/frontend/customer/get_customer.php?id="+cid+"\">"+a.customer_full['name']+"</a>",entry);
			t_history.appendChild(entry);
		}
	}
	pid = param.value;
}

</script>
