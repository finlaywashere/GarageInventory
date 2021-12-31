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
		<link rel="stylesheet" type="text/css" href="/inventory/frontend/assets/css/main.css">
		<link rel="stylesheet" type="text/css" href="/frontend/assets/css/main.css">
	</head>
	<body>
		<?php require($_SERVER['DOCUMENT_ROOT']."/frontend/header.php");?>
		<div class="subheader" style="display: inline-block;">
			<label>Customer: </label><input id="customer" type="number"><br>
			<label>Type: </label>
			<select id="type">
				<option value="1">Incoming</option>
				<option value="2">Outgoing</option>
				<?php
					$create_sys = authenticate_request(100);
					if($create_sys){
						echo "<option value=\"0\">System</option>";
					}
				?>
			</select><br>
			<label>Notes: </label><input id="notes" type="text"><br>
			<label>Original ID: </label><input id="orig_id" type="text"><br>
			<label>Date: </label><input id="date" type="date"></br>
			<label>Paid Amount: </label><input id="paid" type="number" min="0"><br>
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
					<th>Unit Discount</th>
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
var orig_id = document.getElementById("orig_id");
var date = document.getElementById("date");
var paid = document.getElementById("paid");
var add = document.getElementById("add");
var entries = document.getElementById("results");
add.addEventListener("click",addRow);
createButton.addEventListener("click",create);

function addRow(){
	var r = document.createElement("tr");
	for(var i = 0; i < 7; i++){
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
			map["product"] = strip(columns[0].innerHTML);
			map["orig"] = strip(columns[1].innerHTML);
			map["count"] = strip(columns[2].innerHTML);
			map["unit_count"] = strip(columns[3].innerHTML);
			var price = strip(columns[4].innerHTML);
			if(price.startsWith("$"))
				price = price.substring(1);
			price *= 100;
			map["unit_price"] = Math.floor(price);
			var discount = strip(columns[5].innerHTML);
			if(discount.startsWith("$"))
				discount = discount.substring(1);
			discount *= 100;
			map["unit_discount"] = discount;
			map["notes"] = strip(columns[6].innerHTML);
			var lTotal = map['count'] / map['unit_count'] * (price - discount);
			subtotal += lTotal;
			rows.push(map);
		}
	}
	var map2 = {};
	map2["entries"] = rows;
	map2["customer"] = strip(customer.value);
	map2["notes"] = strip(notes.value);
	map2["type"] = strip(type.value);
	map2["subtotal"] = Math.floor(subtotal);
	map2["total"] = Math.floor(subtotal * 1.13);
	map2["paid"] = Math.floor(paid.value*100);
	map2["orig_id"] = strip(orig_id.value);
	map2["date"] = date.value;

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
