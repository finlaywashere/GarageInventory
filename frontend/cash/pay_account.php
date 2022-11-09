<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

	$result = authenticate_request("inventory/cash/admin");
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
			<div id="payAccount">
				<label>Cash ID: </label><input id="cid" type="number" min="1"><br>
				<label>Account ID: </label><input id="aid" type="number" min="1"><br>
				<label>Amount: </label><input id="amount" type="number" step="0.05"><br>
				<label>Notes: </label><input id="notes" type="text"><br>
				<button id="pay">Pay Account</button>
				<button id="reset">Reset</button>
				<p style="color: red;" id="error"></p>
				<p style="color: green;" id="success"></p>
			</div>
		</div>
	</body>
</html>
<script src="/assets/js/master.js"></script>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var cid = document.getElementById("cid");
var aid = document.getElementById("aid");
var amount = document.getElementById("amount");
var notes = document.getElementById("notes");

var pay = document.getElementById("pay");
var reset = document.getElementById("reset");
var error = document.getElementById("error");
var success = document.getElementById("success");

pay.addEventListener("click",payF);
reset.addEventListener("click",resetF);

function payF(){
	error.innerHTML = "";
	success.innerHTML = "";
	var result = pay_account(cid.value,aid.value,amount.value*100,strip(notes.value));
	if(!result.success){
		error.innerHTML = "Error: "+result.reason;
	}else{
		success.innerHTML = "Successfully paid account!";
	}
}
function resetF(){
	error.innerHTML = "";
	success.innerHTML = "";
	aid.value = 1;
	cid.value = 1;
	amount.value = 0;
	notes.innerHTML = "";
}

</script>
