<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

	$result = authenticate_request(0);
	if($result == 0){
		force_login();
	}
?>
<html>
	<head>
		<title>Internal Inventory Services</title>
		<link rel="stylesheet" type="text/css" href="/frontend/assets/css/main.css">
		<link rel="stylesheet" type="text/css" href="../assets/css/main.css">
	</head>
	<body>
		<?php require($_SERVER['DOCUMENT_ROOT']."/frontend/header.php");?>
		<div class="subheader" style="display: inline-block;">
			<label>Name: </label><input id="name" type="text"><br>
			<label>Description: </label><input id="desc" type="text"><br>
			<label>Notes: </label><input id="notes" type="text"><br>
			<label>Stock Code: </label>
			<select id="type">
				<option value="0">Stock</option>
				<option value="1">Non Stock</option>
				<option value="2">Custom (MP)</option>
				<option value="3">Custom (OO)</option>
				<option value="4">Custom (EV)</option>
				<option value="5">Pseudo</option>
			</select><br>
			<label>Location: </label><input id="loc" type="text"><br>
			<button id="create">Create</button>
			<button id="reset">Reset</button>
			<p style="color: red;" id="error"></p>
			<p style="color: green;" id="success"></p>
		</div>
	</body>
</html>
<script src="/assets/js/master.js"></script>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var createButton = document.getElementById("create");
var resetButton = document.getElementById("reset");
var error = document.getElementById("error");
var success = document.getElementById("success");

var nameI = document.getElementById("name");
var desc = document.getElementById("desc");
var notes = document.getElementById("notes");
var type = document.getElementById("type");
var loc = document.getElementById("loc");

createButton.addEventListener("click",create);
resetButton.addEventListener("click",reset);

function reset(){
	error.innerHTML = "";
	success.innerHTML = "";
	nameI.value = "";
	desc.value = "";
	notes.value = "";
	loc.value = "";
	type.selectedIndex = 0;
}

function create(){
	if(nameI.value.length == 0) return;
	error.innerHTML = "";
	success.innerHTML = "";
	var result = create_product(nameI.value,desc.value,notes.value,type.value,loc.value);
	if(!result.success){
		console.log("Failed to retrieve data!");
		error.value = "An error occurred while processing your request. Error: "+result.reason;
		return;
	}
	var id = result.product;
	success.innerHTML = "Successfully created product with id <a href=/inventory/frontend/product/get_product.php?id="+id+">"+id+"</a>";
}

</script>
