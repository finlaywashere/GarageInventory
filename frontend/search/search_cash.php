<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/inventory.php";

	$result = authenticate_request("inventory/account");
	if($result == 0){
		force_login();
	}
?>
<html>
	<head>
		<title>Internal Inventory Services</title>
		<link rel="stylesheet" type="text/css" href="/inventory/frontend/assets/css/main.css">
		<link rel="stylesheet" type="text/css" href="/frontend/assets/css/main.css">
	</head>
	<body>
		<?php require($_SERVER['DOCUMENT_ROOT']."/frontend/header.php");?>
		<div class="subheader">
			<div class="content">
				<h1>Cash Locations</h1>
				<p id="error" style="color: red;"></p>
				<table id="results" class="table">
					<tr id="table_header">
						<th>ID</th>
						<th>Name</th>
						<th>Total</th>
						<th>Last Count</th>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>
<script src="/assets/js/master.js"></script>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var err = document.getElementById("error");

var cash = document.getElementById("results");
var locs = get_cash_locations();
if(!locs.success){
	console.log("Error fetching cash locations!");
	err.innerHTML = "Failed to fetch cash locations!";
}else{
	for(let key in locs.locations){
		var a = locs.locations[key];
		var entry = document.createElement("tr");
		createElement("<a href=\"/inventory/frontend/cash/get_cash.php?id="+key+"\">"+key+"</a>",entry);
		createElement(a.name,entry);
		createElement("$"+(a.total/100).toFixed(2),entry);
		var last_count = a.last_count;
		if(last_count == null){
			createElement("Never counted!", entry);
		}else{
			createElement(last_count.timestamp,entry);
		}
		results.appendChild(entry);
	}
}

</script>
