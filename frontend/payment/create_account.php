<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

	$result = authenticate_request("inventory/account/admin");
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
			<div id="createAccount">
				<label>Name: </label><input id="name" type="text"><br>
				<label>Desc: </label><input id="desc" type="text"><br>
				<label>Perms: </label><input id="perms" type="number" min="0"><br>
				<button id="create">Create</button><br>
				<button id="reset">Reset</button>
				<p id="success" style="color: green;"></p>
				<p id="error" style="color: red;"></p>
			</div>
		</div>
	</body>
</html>
<script src="/assets/js/master.js"></script>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var nameI = document.getElementById("name");
var desc = document.getElementById("desc");
var perms = document.getElementById("perms");

var create = document.getElementById("create");
var reset = document.getElementById("reset");

var success = document.getElementById("success");
var error = document.getElementById("error");

create.addEventListener("click",createAccount);
reset.addEventListener("click",resetFields);

function createAccount(){
	if(nameI.value == "")
		return;
	var result = create_account(nameI.value,perms.value,desc.value);
	if(!result.success){
		console.log("Failed to create account!");
		error.innerHTML = "Failed to create account! Reason: "+result.reason;
		return;
	}
	success.innerHTML = "Successfully created account "+result.account
}
function resetFields(){
	name.value = "";
	desc.value = "";
	perms.value = 0;
	success.innerHTML = "";
	error.innerHTML = "";
}

</script>
