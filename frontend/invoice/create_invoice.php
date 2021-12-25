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
			<label>Customer: </label><input id="customer" type="number">
			<label>Type: </label><input id="type" type="number">
			<label>Notes: </label><input id="notes" type="text">
			<button id="create">Create</button>
			<p style="color: red;" id="error"></p>
		</div>
		<div class="content">
			<button id="add">Add Row</button><br>
			<table id="results">
				<tr id="table_header">
					<th>Product ID</th>
					<th>Original ID</th>
					<th>Count</th>
					<th>Unit Count</th>
					<th>Unit Price</th>
					<th>Notes</th>
				</tr>
			</table>
		</div>
	</body>
</html>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var createButton = document.getElementById("create");
var error = document.getElementById("error");
var customer = document.getElementById("customer");
var type = document.getElementById("type");
var notes = document.getElementById("notes");
var add = document.getElementById("add");
var entries = document.getElementById("results");
add.addEventListener("click",addRow);
createButton.addEventListener("click",create);

function addRow(){
	var r = document.createElement("tr");
	for(var i = 0; i < 6; i++){
		var tmp = document.createElement("td");
		tmp.setAttribute("contenteditable","true");
		tmp.innerHTML = "";
		r.appendChild(tmp);
	}
	entries.appendChild(r);
}

function create(){
	var rows = [];
	var subtotal = 0;
	for(var i = 0; i < entries.childNodes.length; i++){
		var child = entries.childNodes[i];
		if(child.nodeName == "TR"){
			var map = {};
			var columns = child.childNodes;
			map["product"] = columns[0].innerHTML.replace(/(<([^>]+)>)/gi, "");
			map["orig"] = columns[1].innerHTML.replace(/(<([^>]+)>)/gi, "");
			map["count"] = columns[2].innerHTML.replace(/(<([^>]+)>)/gi, "");
			map["unit_count"] = columns[3].innerHTML.replace(/(<([^>]+)>)/gi, "");
			var price = columns[4].innerHTML.replace(/(<([^>]+)>)/gi, "");
			if(price.startsWith("$"))
				price = price.substring(1) * 100;
			map["unit_price"] = Math.floor(price);
			map["notes"] = columns[5].innerHTML.replace(/(<([^>]+)>)/gi, "");
			var lTotal = map['count'] / map['unit_count'] * price;
			subtotal += lTotal;
			rows.push(map);
		}
	}
	var map2 = {};
	map2["entries"] = rows;
	map2["customer"] = customer.value;
	map2["notes"] = notes.value;
	map2["type"] = type.value;
	map2["subtotal"] = Math.floor(subtotal);
	map2["total"] = Math.floor(subtotal * 1.13);
	
	var data = JSON.stringify(map2);
	console.log(data);
	var result = create_invoice(data);
	if(!result.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+result.reason;
		return;
	}
	var id = result.invoice;
	location.href="/inventory/frontend/invoice/get_invoice.php?id="+id;
}

</script>
