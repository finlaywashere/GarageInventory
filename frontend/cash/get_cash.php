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
			<div id="getCash">
				<label>ID: </label><input id="id" type="number" min="1"><br>
				<button id="lookup">Lookup</button>
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
var lookup = document.getElementById("lookup");
var data = document.getElementById("data");
var error = document.getElementById("error");

lookup.addEventListener("click",lookupF);

function lookupF(){
	error.innerHTML = "";
	data.innerHTML = "";
	var cash = get_cash(id.value);
	if(!cash.success){
		error.innerHTML = "Error: "+cash.reason;
		return;
	}
	data.innerHTML = "Cash: $"+(cash.cash['total']/100).toFixed(2)+"<br>Last Count: "+cash.cash['last_count']['timestamp'];
}

</script>
