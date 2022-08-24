<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

	$result = authenticate_request(100);
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
			<div id="createCash">
				<label>Name: </label><input id="nameS" type="text"><br>
				<button id="create">Create</button>
				<p style="color: red;" id="error"></p>
				<p style="color: green;" id="success"></p>
			</div>
		</div>
	</body>
</html>
<script src="/assets/js/master.js"></script>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var nameS = document.getElementById("nameS");
var create = document.getElementById("create");
var error = document.getElementById("error");
var success = document.getElementById("success");

create.addEventListener("click",createF);

function createF(){
	error.innerHTML = "";
	success.innerHTML = "";
	if(nameS.value != ""){
		var result = create_cash(nameS.value);
		if(!result.success){
			error.innerHTML = "Error: "+result.reason;
			return;
		}
		success.innerHTML = "Successfully created cash location with id "+result.id;
	}else{
		error.innerHTML = "Cash location must have a name!";
	}
}

</script>
