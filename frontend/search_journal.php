<?php
	require_once "../../private/authentication.php";

	$result = authenticate_request(0);
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
		<?php require("../../frontend/header.php");?>
		<div class="subheader" style="display: inline-block;">
			<label>Journal Search: </label><input id="search_param" type="text">
			<label>Type: </label>
			<select id="search_type">
				<option value="1">Invoice #</option>
				<option value="2">Date</option>
				<option value="3">Contents</option>
				<option value="4">Type</option>
			</select>
			<button id="search">Search</button>
		</div>
		<div class="content">
			<table id="results">
				<tr>
					<th>Date</th>
					<th>Type</th>
					<th>ID</th>
					<th>UID</th>
					<th>Invoice</th>
					<th>Text</th>
				</tr>
			</table>
		</div>
	</body>
</html>
<script>

var searchButton = document.getElementById("search");
var param = document.getElementById("search_param");
var type = document.getElementById("search_type"); 
searchButton.addEventListener("click",search);

function search(){
	$.ajax({
		type: "GET",
		url: "../api/public/search_journal.php?search_type="+type.value+"&search_param="+encodeURIComponent(param.value),
		processData: false
	});
}

</script>
