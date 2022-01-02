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
			<label>Customer: </label><input id="customer" type="number" min="0">
			<button id="cusLookup">Customer Lookup</button>
			<br>
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
			<button id="reset">Reset</button>
			<p style="color: red;" id="error"></p>
			<p style="color: green" id="success"></p>
		</div>
		<div class="content">
			<button id="add">Add Row</button>
			<button id="prodSearch">Product Search</button>
			<br>
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
		<div id="cusLookupDiv" class="floating">
			<h2>Customer ID Lookup</h2>
			<label>Search Type: </label>
			<select id="csLType">
				<option value="1">Name</option>
				<option value="2">Phone #</option>
				<option value="3">Email</option>
				<option value="4">Customer ID</option>
			</select><br>
			<label>Search Parameter: </label><input type="text" id="csLParam"><br>
			<button id="csLSearch">Search</button>
			<button id="csLClose">Close</button>
			<p style="color: red;" id="csLError"></p>
			<div id="csLResults"></div>
		</div>
		<div id="prodLookupDiv" class="floating">
			<h2>Product ID Lookup</h2>
			<label>Search Type: </label>
			<select id="prLType">
				<option value="2">Name</option>
				<option value="4">Description</option>
				<option value="3">Location</option>
				<option value="1">ID</option>
			</select><br>
			<label>Search Parameter: </label><input type="text" id="prLParam"><br>
			<button id="prLSearch">Search</button>
			<button id="prLClose">Close</button>
			<p style="color: red;" id="prLError"></p>
			<div id="prLResults"></div>
		</div>
	</body>
</html>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var createButton = document.getElementById("create");
var resetButton = document.getElementById("reset");
var error = document.getElementById("error");
var success = document.getElementById("success");

var cusLookup = document.getElementById("cusLookup");

var cusLookupDiv = document.getElementById("cusLookupDiv");

var customer = document.getElementById("customer");
var type = document.getElementById("type");
var notes = document.getElementById("notes");
var orig_id = document.getElementById("orig_id");
var date = document.getElementById("date");
var paid = document.getElementById("paid");
var add = document.getElementById("add");
var prodSearch = document.getElementById("prodSearch");
var entries = document.getElementById("results");

add.addEventListener("click",addRow);
createButton.addEventListener("click",create);
resetButton.addEventListener("click",reset);
prodSearch.addEventListener("click",productLookup);
cusLookup.addEventListener("click",customerLookup);

var csLType = document.getElementById("csLType");
var csLParam = document.getElementById("csLParam");
var csLSearch = document.getElementById("csLSearch");
var csLClose = document.getElementById("csLClose");
var csLError = document.getElementById("csLError");
var csLResults = document.getElementById("csLResults");

csLSearch.addEventListener("click",customerSearch);
csLClose.addEventListener("click",customerLookupClose);

var prodLookupDiv = document.getElementById("prodLookupDiv");
var prLType = document.getElementById("prLType");
var prLParam = document.getElementById("prLParam");
var prLSearch = document.getElementById("prLSearch");
var prLClose = document.getElementById("prLClose");
var prLError = document.getElementById("prLError");
var prLResults = document.getElementById("prLResults");

prLSearch.addEventListener("click",productSearch);
prLClose.addEventListener("click",productLookupClose);

function customerSearch(){
	var customers = get_customers(csLType.value,csLParam.value);
	if(!customers.success){
		console.log("Failed to retrieve data!");
		csLError.innerHTML = "An error occurred while processing your request. Error: "+customers.reason;
		return;
	}
	csLResults.innerHTML = "";
	for(let i = 0; i < customers.customers.length; i++){
		if(i == 4){
			var div = document.createElement("div");
			div.className = "oneline";
			var text = document.createElement("p");
			text.innerHTML = "... and "+(customers.customers.length-4)+" more";
			div.appendChild(text);
			csLResults.appendChild(div);
			break;
		}
		var customer = get_customer(customers.customers[i]);
		if(!customer.success){
			console.log("Failed to retrieve some data!");
			csLError.innerHTML = "An error occurred while processing your request. Error: "+customer.reason;
			return;
		}
		var div = document.createElement("div");
		div.className = "oneline";
		var text = document.createElement("p");
		text.innerHTML = "#"+customers.customers[i]+": "+customer.customer['name'];
		div.appendChild(text);
		var button = document.createElement("button");
		button.innerHTML = "Select";
		button.id = customers.customers[i];
		button.addEventListener("click",customerLookupSelect);
		div.appendChild(button);
		csLResults.appendChild(div);
	}
}
function customerLookupSelect(trigger){
	var button = trigger.target;
	var id = button.id;
	customer.value = id;
	cusLookupDiv.style.visibility = "hidden";
}
function customerLookupClose(){
	cusLookupDiv.style.visibility = "hidden";
}

function customerLookup(){
	cusLookupDiv.style.visibility = "visible";
}
function productSearch(){
	var products = get_products(prLType.value,prLParam.value);
	if(!products.success){
		console.log("Failed to retrieve data!");
		prLError.innerHTML = "An error occurred while processing your request. Error: "+products.reason;
		return;
	}
	prLResults.innerHTML = "";
	for(let i = 0; i < products.products.length; i++){
		if(i == 4){
			var div = document.createElement("div");
			div.className = "oneline";
			var text = document.createElement("p");
			text.innerHTML = "... and "+(products.products.length-4)+" more";
			div.appendChild(text);
			prLResults.appendChild(div);
			break;
		}
		var product = get_product(products.products[i]);
		if(!product.success){
			console.log("Failed to retrieve some data!");
			prLError.innerHTML = "An error occurred while processing your request. Error: "+product.reason;
			return;
		}
		var div = document.createElement("div");
		div.className = "oneline";
		var text = document.createElement("p");
		text.innerHTML = "#"+products.products[i]+": "+product.product['name'];
		div.appendChild(text);
		prLResults.appendChild(div);
	}
}
function productLookup(){
	prodLookupDiv.style.visibility = "visible";
}
function productLookupClose(){
	prodLookupDiv.style.visibility = "hidden";
}

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
function reset(){
	error.innerHTML = "";
	success.innerHTML = "";

	customer.value = "";
	type.selectedIndex = 0;
	notes.value = "";
	orig_id.value = "";
	date.value = "";
	paid.value = "";
	clearTable(entries);

	csLType.selectedIndex = 0;
	csLParam.value = "";
	csLResults.innerHTML = "";
	prLType.selectedIndex = 0;
	prLParam.value = "";
	prLResults.innerHTML = "";
}

function create(){
	error.innerHTML = "";
	success.innerHTML = "";
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
		error.value = "An error occurred while processing your request. Error: "+result.reason;
		return;
	}
	var id = result.invoice;
	success.innerHTML = "Successfully created invoice with id <a href=/inventory/frontend/invoice/get_invoice.php?id="+id+">"+id+"</a>";
}

</script>
