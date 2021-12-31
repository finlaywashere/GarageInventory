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
		<link rel="stylesheet" type="text/css" href="/frontend/assets/css/main.css">
		<link rel="stylesheet" type="text/css" href="../assets/css/main.css">
	</head>
	<body>
		<?php require($_SERVER['DOCUMENT_ROOT']."/frontend/header.php");?>
		<div class="subheader" style="display: inline-block;">
			<label>Name: </label><input id="name" type="text"><br>
			<label>Description: </label><input id="desc" type="text"><br>
			<label>Notes: </label><input id="notes" type="text"><br>
			<label>Location: </label><input id="location" type="text"><br>
			<button id="create">Create</button>
			<p style="color: red;" id="error"></p>
		</div>
	</body>
</html>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var createButton = document.getElementById("create");
var error = document.getElementById("error");

var nameI = document.getElementById("name");
var desc = document.getElementById("desc");
var notes = document.getElementById("notes");
var loc = document.getElementById("location");

createButton.addEventListener("click",create);

function create(){
	if(nameI.value.length == 0) return;
	var result = create_product(nameI.value,desc.value,notes.value,loc.value);
	if(!result.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+result.reason;
		return;
	}
	var id = result.product;
	location.href="/inventory/frontend/product/get_product.php?id="+id;
}

</script>
