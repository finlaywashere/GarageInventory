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
		<link rel="stylesheet" type="text/css" href="/frontend/assets/css/main.css">
		<link rel="stylesheet" type="text/css" href="../assets/css/main.css">
	</head>
	<body>
		<?php require($_SERVER['DOCUMENT_ROOT']."/frontend/header.php");?>
		<div class="subheader" style="display: inline-block;">
			<label>Name: </label><input id="name" type="text"><br>
			<label>Email: </label><input id="email" type="email"><br>
			<label>Phone: </label><input id="phone" type="phone"><br>
			<label>Address: </label><input id="address" type="address"><br>
			<label>Notes: </label><input id="notes" type="text"><br>
			<label>Type: </label>
			<select id="type">
				<option value="1">Normal</option>
				<option value="2">Business</option>
				<?php
					$create_sys = authenticate_request(100);
					if($create_sys){
						echo "<option value=\"0\">System</option>";
					}
				?>
			</select><br>
			<button id="create">Create</button>
			<button id="reset">Reset</button>
			<p style="color: red;" id="error"></p>
			<p style="color: green;" id="success"></p>
		</div>
	</body>
</html>
<script src="/inventory/frontend/assets/js/inventory.js"></script>
<script>

var createButton = document.getElementById("create");
var resetButton = document.getElementById("reset");
var error = document.getElementById("error");
var success = document.getElementById("success");

var nameI = document.getElementById("name");
var email = document.getElementById("email");
var phone = document.getElementById("phone");
var address = document.getElementById("address");
var notes = document.getElementById("notes");
var type = document.getElementById("type");

createButton.addEventListener("click",create);
resetButton.addEventListener("click",reset);

function reset(){
	error.innerHTML = "";
	success.innerHTML = "";
	nameI.value = "";
	email.value = "";
	phone.value = "";
	address.value = "";
	notes.value = "";
	type.selectedIndex = 0;
}

function create(){
	if(nameI.value.length == 0) return;
	error.innerHTML = "";
	success.innerHTML = "";
	var result = create_customer(nameI.value,email.value,phone.value,address.value,notes.value,type.value);
	if(!result.success){
		console.log("Failed to retrieve data!");
		error.value = "An error occurred while processing your request. Error: "+result.reason;
		return;
	}
	var id = result.customer;
	success.innerHTML = "Successfully created customer with id <a href=/inventory/frontend/customer/get_customer.php?id="+id+">"+id+"</a>";
}

</script>
