<?php
require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";

	$result = authenticate_request(1);
	if($result == 0){
		header("Location: /authentication/frontend/login.php?referrer=/authentication/frontend/index.php");
		die("Please log in!");
	}
?>
<html>
	<head>
		<title>Internal Inventory Services</title>
		<link rel="stylesheet" type="text/css" href="assets/css/main.css">
		<link rel="stylesheet" type="text/css" href="/frontend/assets/css/main.css">
	</head>
	<body>
		<?php require($_SERVER['DOCUMENT_ROOT']."/frontend/header.php");?>
		<div class="subheader" style="display: inline-block;">
			<label>Journal Search: </label><input id="search_param" type="text">
			<label>Type: </label>
			<select id="search_type">
				<option value="1">Invoice #</option>
				<option value="2">Date</option>
				<option value="3">Contents</option>
				<option value="4">Type</option>
				<option value="5">User</option>
			</select>
			<button id="search">Search</button>
			<p style="color: red;" id="error"></p>
		</div>
		<div class="content">
			<table id="results">
				<tr id="table_header">
					<th>Date</th>
					<th>Type</th>
					<th>ID</th>
					<th>UID</th>
					<th>Invoice</th>
					<th>User</th>
					<th>Text</th>
				</tr>
			</table>
		</div>
	</body>
</html>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var searchButton = document.getElementById("search");
var param = document.getElementById("search_param");
var type = document.getElementById("search_type");
var error = document.getElementById("error");
var table = document.getElementById("results");
searchButton.addEventListener("click",search);

function search(){
	var journal = search_journal(type.value,param.value);
	if(!journal.success){
		console.log("Failed to retrieve data!");
		error.innerHTML = "An error occurred while processing your request. Error: "+journal.reason;
		return;
	}
	clearTable(table);
	var journals = journal.journals;
	for(let i = 0; i < journals.length; i++){
		var journal = get_journal(journals[i]);
		if(!journal.success){
			console.log("Failed to retrieve some data!");
			error.innerHTML = "An error occurred while processing your request. Error: "+journal.reason;
			return;
		}
		var entry = document.createElement("tr");
		createElement(journal.journal['date'],entry);
		createElement(journal_type_to_string(journal.journal['type']),entry);
		createElement(journal.journal['journal_id'],entry);
		createElement(journals[i],entry);
		createElement("<a href=\"/inventory/frontend/invoice/get_invoice.php?id="+journal.journal['invoice']+"\">"+journal.journal['invoice']+"</a>",entry);
		createElement(journal.journal['user'],entry);
		createElement(journal.journal['text'],entry);
		table.appendChild(entry);
	}
}

</script>
