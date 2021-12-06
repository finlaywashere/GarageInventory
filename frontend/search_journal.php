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
var error = document.getElementById("error");
var table = document.getElementById("results");
searchButton.addEventListener("click",search);

function clearTable(){
	var children = table.querySelectorAll('td')
	for(let i = 0; i < children.length; i++){
		var child = children[i];
		var parent = children[i].parentNode;
		parent.removeChild(child);
	}
}

function search(){
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open("POST", "/inventory/api/public/search_journal.php", true);
	xmlhttp.addEventListener("load",function() {
		if(xmlhttp.readyState != 4)
			return;
		if (xmlhttp.status==200) {
			var json = JSON.parse(this.responseText);
			if(!json.success){
				console.log("Failed to retrieve data!");
				error.innerHTML = "An error occurred while processing your request. Error: "+json.reason;
				return;
			}
			clearTable();
			var journals = json.journals;
			for(let i = 0; i < journals.length; i++){
				var request2 = new XMLHttpRequest();
				request2.open('POST','/inventory/api/public/journal_data.php',true);
				request2.addEventListener("load",function() {
					if(request2.readyState != 4)
						return;
					if (request2.status != 200) {
						error.innerHTML = "An error occurred while processing your request. Error Code: "+request2.status;
						console.log("Error occurred! Code: "+request2.status);
						console.log(request2.readyState);
						return;
					}
					var json2 = JSON.parse(request2.responseText);
					if(!json2.success){
						console.log("Failed to retrieve some data!");
						error.innerHTML = "An error occurred while processing your request. Error: "+json2.reason;
						return;
					}
					var entry = document.createElement("tr");
					var date = document.createElement("td");
					date.innerHTML = json2.journal['date'];
					entry.appendChild(date);
					var type = document.createElement("td");
					type.innerHTML = json2.journal['type'];
					entry.appendChild(type);
					var id = document.createElement("td");
					id.innerHTML = json2.journal['journal_id'];
					entry.appendChild(id);
					var uid = document.createElement("td");
					uid.innerHTML = journals[i];
					entry.appendChild(uid);
					var invoice = document.createElement("td");
					invoice.innerHTML = json2.journal['invoice'];
					entry.appendChild(invoice);
					var text = document.createElement("td");
					text.innerHTML = json2.journal['text'];
					entry.appendChild(text);
					table.appendChild(entry);
				});
				request2.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				request2.send("journal_uid="+journals[i]);
			}
		}else{
			error.innerHTML = "An error occurred while processing your request. Error Code: "+xmlhttp.status;
		}
	});
	xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlhttp.send("search_type="+type.value+"&search_param="+encodeURIComponent(param.value));
}

</script>
