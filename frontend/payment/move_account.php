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
			<div id="moveDiv">
				<label>Src ID: </label><input id="src" type="number" min="1">
				<button id="aLookup">Lookup Account</button><br>
				<label>Dst ID: </label><input id="dst" type="number" min="1"><br>
				<label>Amount: </label><input id="amount" type="number"><br>
				<label>Notes: </label><input id="notes" type="text"><br>
				<button id="move">Move</button>
				<p id="data"></p>
				<p style="color: red;" id="error"></p>
			</div>
		</div>
		<?php
		require_once "../utils/account_lookup.php";
		?>
	</body>
</html>
<script src="/assets/js/master.js"></script>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var lookup = document.getElementById("aLookup");

accLookupSetup(null, lookup);

var src = document.getElementById("src");
var dst = document.getElementById("dst");
var amount = document.getElementById("amount");
var notes = document.getElementById("notes");
var move = document.getElementById("move");
var data = document.getElementById("data");
var error = document.getElementById("error");

move.addEventListener("click",moveF);

function moveF(){
	error.innerHTML = "";
	data.innerHTML = "";
	var result = move_account(src.value, dst.value, amount.value*100, notes.value);
	if(!result.success){
		error.innerHTML = "Error: "+result.reason;
		return;
	}
	data.innerHTML = "Successfully moved money between accounts!";
}

</script>
