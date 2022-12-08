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
			<div id="depositDiv">
				<label>Account ID: </label><input id="id" type="number" min="1"><br>
				<label>Amount: </label><input id="amount" type="number"><br>
				<label>Notes: </label><input id="notes" type="text"><br>
				<button id="deposit">Deposit (bank to account)</button>
				<p id="data"></p>
				<p style="color: red;" id="error"></p>
			</div>
		</div>
	</body>
</html>
<script src="/assets/js/master.js"></script>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var id = document.getElementById("id");
var amount = document.getElementById("amount");
var notes = document.getElementById("notes");
var deposit = document.getElementById("deposit");
var data = document.getElementById("data");
var error = document.getElementById("error");

deposit.addEventListener("click",depositF);

function depositF(){
	error.innerHTML = "";
	data.innerHTML = "";
	var result = deposit_account(id.value, amount.value*100, notes.value);
	if(!result.success){
		error.innerHTML = "Error: "+result.reason;
		return;
	}
	data.innerHTML = "Successfully made deposit";
}

</script>
