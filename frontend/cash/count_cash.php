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
			<div id="createCustomer">
				<label>ID: </label><input id="id" type="number" min="1"><br>
				<label>Nickels: </label><input id="nickels" type="number" min="0"><br>
				<label>Dimes: </label><input id="dimes" type="number" min="0"><br>
				<label>Quarters: </label><input id="quarters" type="number" min="0"><br>
				<label>Loonies: </label><input id="toonies" type="number" min="0"><br>
				<label>Toonies: </label><input id="loonies" type="number" min="0"><br>
				<label>Fives: </label><input id="fives" type="number" min="0"><br>
				<label>Tens: </label><input id="tens" type="number" min="0"><br>
				<label>Twenties: </label><input id="twenties" type="number" min="0"><br>
				<label>Fifties: </label><input id="fifties" type="number" min="0"><br>
				<label>Hundreds: </label><input id="hundreds" type="number" min="0"><br>
				<button id="count">Count</button>
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

var id = document.getElementById("id");

var nickels = document.getElementById("nickels");
var dimes = document.getElementById("dimes");
var quarters = document.getElementById("quarters");
var loonies = document.getElementById("loonies");
var toonies = document.getElementById("toonies");
var fives = document.getElementById("fives");
var tens = document.getElementById("tens");
var twenties = document.getElementById("twenties");
var fifties = document.getElementById("fifties");
var hundreds = document.getElementById("hundreds");

var count = document.getElementById("count");
var reset = document.getElementById("reset");
var error = document.getElementById("error");
var success = document.getElementById("success");

count.addEventListener("click",countF);
reset.addEventListener("click",resetF);

function countF(){
	error.innerHTML = "";
	success.innerHTML = "";
	var result = count_cash(id.value,nickels.value,dimes.value,quarters.value,loonies.value,toonies.value,fives.value,tens.value,twenties.value,fifties.value,hundreds.value);
	if(!result.success){
		error.innerHTML = "Error: "+result.reason;
	}else{
		success.innerHTML = "Successfully counted cash!";
	}
}
function resetF(){
	error.innerHTML = "";
	success.innerHTML = "";
	id.value = 1;
	nickels.value = 0;
	dimes.value = 0;
	quarters.value = 0;
	loonies.value = 0;
	toonies.value = 0;
	fives.value = 0;
	tens.value = 0;
	twenties.value = 0;
	fifties.value = 0;
	hundreds.value = 0;
}

</script>
