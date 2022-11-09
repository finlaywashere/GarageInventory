<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

	$result = authenticate_request("inventory/account");
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
			<label>Account ID: </label><input id="search_param" type="number" min="0">
			<label>Start: </label><input id="search_start" type="date">
			<label>End: </label><input id="search_end" type="date">
			<button id="search">Search</button>
			<button id="save">Save</button>
			<p style="color: red;" id="error"></p>
		</div>
		<div class="content">
			<h2>Account Information</h2>
			<table id="results" style="width: 80%;">
				<tr id="table_header">
					<th>Name</th>
					<th>Description</th>
					<th>Balance</th>
					<th>Perms</th>
				</tr>
			</table><br>
			<h2>Account History</h2>
			<table id="table_history" style="width: 80%;">
				<tr id="table_header">
					<th>Type</th>
					<th>Invoice</th>
					<th>Date</th>
					<th>Customer</th>
					<th>Total</th>
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
var start = document.getElementById("search_start");
var end = document.getElementById("search_end");
var error = document.getElementById("error");
var table = document.getElementById("results");
var t_history = document.getElementById("table_history");

var nameH = null;
var desc = null;
var bal = null;
var perms = null;
var aid;

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
	var permsS = perms.innerHTML;
	var json = update_account(aid,nameS,descS,permsS);
	if(!json.success){
		console.log("Failed to save data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+json.reason;
		return;
	}
}

function search(){
	var json = get_account(param.value);
	clearTable(table);
	clearTable(t_history);
	error.innerHTML = "";
	if(!json.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+json.reason;
		return;
	}
	aid = param.value;

	var entry = document.createElement("tr");
	nameH = createEditableElement(json.account['name'],entry);
	desc = createEditableElement(json.account['desc'],entry);
	createElement("$"+(json.account['balance']/100).toFixed(2),entry);
	perms = createEditableElement(json.account['perms'],entry);
	table.appendChild(entry);
	if(start.value == '' || end.value == '')
		return;
	var hist = account_history(param.value,start.value,end.value);
	if(!hist.success){
		console.log("Failed to retrieve history!");
		error.innerHTML = "An error occurred while processing your request. Error: "+hist.reason;
		return;
	}
	if(hist.history.length > 0){
		for(let i = 0; i < hist.history.length; i++){
			var a = hist.history[i];
			var entry = document.createElement("tr");
			if(a.invoice != 0){
				var type = a.invoice['type'];
				createElement(invoice_type_to_string(type),entry);
				createElement("<a href=\"/inventory/frontend/invoice/get_invoice.php?id="+a.invoice['invoice_id']+"\">"+a.invoice['invoice_id']+"</a>",entry);
				createElement(a['date'],entry);
				var cid = a.invoice['customer'];
				createElement("<a href=\"/inventory/frontend/customer/get_customer.php?id="+cid+"\">"+a.invoice.customer_full['name']+"</a>",entry);
				var mul = 1;
				if(type == 2)
					mul = -1;
				createElement("$"+(a['amount']/100 * mul).toFixed(2),entry);
			}else{
				createElement("PAYMENT", entry);
				createElement("N/A",entry);
				createElement(a['date'],entry);
				createElement("N/A",entry);
				createElement("$"+(a['amount']/100).toFixed(2),entry);
			}
			t_history.appendChild(entry);
		}
	}
}

</script>
