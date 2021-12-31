<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";

	$result = authenticate_request(2);
	if($result == 0){
		header("Location: /authentication/frontend/login.php?referrer=/authentication/frontend/index.php");
		die("Please log in!");
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
			<label>Customer ID: </label><input id="search_param" type="number">
			<button id="search">Search</button>
			<button id="save">Save</button>
			<p style="color: red;" id="error"></p>
		</div>
		<div class="content">
			<table id="results" style="width: 80%;">
				<tr id="table_header">
					<th>Name</th>
					<th>Type</th>
					<th>Email</th>
					<th>Phone #</th>
					<th>Address</th>
					<th>Notes</th>
				</tr>
			</table>
		</div>
	</body>
</html>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var searchButton = document.getElementById("search");
var saveButton = document.getElementById("save");
var param = document.getElementById("search_param");
var error = document.getElementById("error");
var table = document.getElementById("results");

var nameH = null;
var type = null;
var notes = null;
var email = null;
var phone = null;
var address = null;
var cid;

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
	var typeI = string_to_customer_type(type.innerHTML);
	if(typeI === -1){
		error.innerHTML = "Invalid customer type!";
		return;
	}
	var notesS = notes.innerHTML;
	var emailS = email.innerHTML;
	var phoneS = phone.innerHTML;
	var addressS = address.innerHTML;
	
	var json = update_customer(cid,nameS,typeI,emailS,phoneS,addressS,notesS);
	if(!json.success){
		console.log("Failed to save data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+json.reason;
		return;
	}
}

function search(){
	error.innerHTML = "";
	var customer = get_customer(param.value);
	clearTable(table);
	if(!customer.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+customer.reason;
		return;
	}
	var entry = document.createElement("tr");
	nameH = createEditableElement(customer.customer['name'],entry);
	type = createEditableElement(customer_type_to_string(customer.customer['type']),entry);
	email = createEditableElement(customer.customer['email'],entry);
	phone = createEditableElement(customer.customer['phone'],entry);
	address = createEditableElement(customer.customer['address'],entry);
	notes = createEditableElement(customer.customer['notes'],entry);
	table.appendChild(entry);
	cid = param.value;
}

</script>
