<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/inventory/api/private/authentication.php";

	$result = authenticate_request(0);
	if($result == 0){
		header("Location: /authentication/frontend/login.php?referrer=/authentication/frontend/index.php");
		die("Please log in!");
	}
?>
<html>
	<head>
		<title>Internal Inventory Services</title>
		<link rel="stylesheet" type="text/css" href="../assets/css/main.css">
		<link rel="stylesheet" type="text/css" href="/frontend/assets/css/main.css">
	</head>
	<body>
		<?php require($_SERVER['DOCUMENT_ROOT']."/frontend/header.php");?>
		<div class="subheader" style="display: inline-block;">
			<label>Product ID: </label><input id="search_param" type="number">
			<button id="search">Search</button>
			<button id="save">Save</button>
			<p style="color: red;" id="error"></p>
		</div>
		<div class="content">
			<table id="results" style="width: 80%;">
				<tr id="table_header">
					<th>Original ID</th>
					<th>Name</th>
					<th>Description</th>
					<th>Count</th>
					<th>Notes</th>
					<th>Location</th>
				</tr>
			</table>
		</div>
	</body>
</html>
<script>

var searchButton = document.getElementById("search");
var saveButton = document.getElementById("save");
var param = document.getElementById("search_param");
var error = document.getElementById("error");
var table = document.getElementById("results");

var id = null;
var count;
var name;
var desc;
var notes;
var loc;

searchButton.addEventListener("click",search);
saveButton.addEventListener("click",save);

// From https://stackoverflow.com/questions/5448545/how-to-retrieve-get-parameters-from-javascript/
function getSearchParameters() {
	var prmstr = window.location.search.substr(1);
	return prmstr != null && prmstr != "" ? transformToAssocArray(prmstr) : {};
}

function transformToAssocArray( prmstr ) {
	var params = {};
	var prmarr = prmstr.split("&");
	for ( var i = 0; i < prmarr.length; i++) {
		var tmparr = prmarr[i].split("=");
		params[tmparr[0]] = tmparr[1];
	}
	return params;
}
var params = getSearchParameters();
if(params.id != undefined){
	param.value = params.id;
	search();
}

function save(){
	if(id == null) return;
	//TODO: Implement changing this stuff
}

function clearTable(){
	var children = table.querySelectorAll('tr')
	for(let i = 0; i < children.length; i++){
		let found = false;
		if(children[i].childNodes != undefined){
			for(let i1 = 0; i1 < children[i].childNodes.length; i1++){
				var child = children[i].childNodes[i1];
				if(child.nodeName == "TH")
					found = true;
			}
		}
		if(found)
			continue;
		var child = children[i];
		var parent = children[i].parentNode;
		parent.removeChild(child);
	}
}

function search(){
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open("POST", "/inventory/api/public/product/get_product.php", true);
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
			var entry = document.createElement("tr");
			id = document.createElement("td");
			id.innerHTML = json.product['original_id'];
			id.setAttribute("contenteditable","true");
			entry.appendChild(id);
			name = document.createElement("td");
			name.innerHTML = json.product['name'];
			name.setAttribute("contenteditable","true");
			entry.appendChild(name);
			desc = document.createElement("td");
			desc.innerHTML = json.product['description'];
			desc.setAttribute("contenteditable","true");
			entry.appendChild(desc);
			count = document.createElement("td");
			count.innerHTML = json.product['count'];
			count.setAttribute("contenteditable","true");
			entry.appendChild(count);
			notes = document.createElement("td");
			notes.innerHTML = json.product['notes'];
			notes.setAttribute("contenteditable","true");
			entry.appendChild(notes);
			loc = document.createElement("td");
			loc.innerHTML = json.product['location'];
			loc.setAttribute("contenteditable","true");
			entry.appendChild(loc);
			table.appendChild(entry);
		}else{
			error.innerHTML = "An error occurred while processing your request. Error Code: "+xmlhttp.status;
		}
	});
	xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlhttp.send("product_id="+param.value);
}

</script>
