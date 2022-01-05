<!DOCTYPE html>
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
			<label>Product ID: </label><input id="search_param" type="number" min="0"><br>
			<label>Reason: </label><input id="reason" type="text"><br>
			<button id="search">Search</button>
			<button id="save">Save</button>
			<button id="reset">Reset</button>
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
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var reason = document.getElementById("reason");
var searchButton = document.getElementById("search");
var saveButton = document.getElementById("save");
var resetButton = document.getElementById("reset");
var param = document.getElementById("search_param");
var error = document.getElementById("error");
var table = document.getElementById("results");

var count = null;

var oldCount;

var pid;

searchButton.addEventListener("click",search);
saveButton.addEventListener("click",save);
resetButton.addEventListener("click",reset);

var params = getSearchParameters();
if(params.id != undefined){
	param.value = params.id;
	search();
}

function reset(){
	error.innerHTML = "";
	clearTable(table);
	param.value = "";
	reason.value = "";
}

function save(){
	error.innerHTML = "";
	if(count == null) return;
	var newCount = strip(count.innerHTML);
	if(!isWhole(newCount)){
		error.innerHTML = "Count must be a number!";
		return;
	}
	if(confirm("Are you sure you want to adjust inventory for product #"+pid+" from "+oldCount+" to "+newCount)){
		var rText = reason.value;
		var json = adjust_inventory(pid,newCount,rText);
		if(!json.success){
			console.log("Failed to save data!");
			error.innerHTML = "An error occurred while processing your request. Error: "+json.reason;
			return;
		}
	}
}

function search(){
	var value = param.value;
	if(!isWhole(value) || value < 0){
		error.innerHTML = "The product ID must be a whole number greater than 0!";
		return;
	}
	var json = get_product(value);
	clearTable(table);
	error.innerHTML = "";
	if(!json.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+json.reason;
		return;
	}
	var entry = document.createElement("tr");
	createElement(json.product['name'],entry);
	createElement(json.product['description'],entry);
	oldCount = json.product['count'];
	count = createEditableElement(oldCount,entry);
	createElement(json.product['notes'],entry);
	createElement(json.product['location'],entry);
	table.appendChild(entry);
	pid = param.value;
}

</script>
