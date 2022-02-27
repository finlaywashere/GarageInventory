<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

	$result = authenticate_request(2);
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
			<table id="results" style="width: 80%;">
				<tr id="table_header">
					<th>Name</th>
					<th>Description</th>
					<th>Count</th>
					<th>Notes</th>
					<th>Location</th>
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

var nameH = null;
var desc = null;
var notes = null;
var loc = null;
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
	var json = update_product(pid,nameS,descS,notesS,locS);
	if(!json.success){
		console.log("Failed to save data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+json.reason;
		return;
	}
}

function search(){
	var json = get_product(param.value);
	clearTable(table);
	error.innerHTML = "";
	if(!json.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+json.reason;
		return;
	}
	var entry = document.createElement("tr");
	nameH = createEditableElement(json.product['name'],entry);
	desc = createEditableElement(json.product['description'],entry);
	createElement(json.product['count'],entry);
	notes = createEditableElement(json.product['notes'],entry);
	loc = createEditableElement(json.product['location'],entry);
	table.appendChild(entry);
	pid = param.value;
}

</script>
