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
		<div class="subheader" style="display: inline-block;">
			<div class="content">
				<h1>Accounts</h1>
				<p id="error" style="color: red;"></p>
				<table id="results">
					<tr id="table_header">
						<th>ID</th>
						<th>Name</th>
						<th>Desc</th>
						<th>Balance</th>
						<th>Perms</th>
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

var acc = document.getElementById("results");
var accounts = get_accounts();
if(!accounts.success){
	console.log("Error fetching accounts!");
	err.innerHTML = "Failed to fetch accounts!";
}else{
	for(let key in accounts.accounts){
		var a = accounts.accounts[key];
		var entry = document.createElement("tr");
		createElement(key,entry);
		createElement(a.name,entry);
		createElement(a.desc,entry);
		createElement("$"+(a.balance/100).toFixed(2),entry);
		createElement(a.perms,entry);
		results.appendChild(entry);
	}
}

</script>
