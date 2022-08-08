<div id="cusLookupDiv" class="floating">
	<h2>Customer ID Lookup</h2>
	<label>Search Type: </label>
	<select id="csLType">
		<option value="1">Name</option>
		<option value="2">Phone #</option>
		<option value="3">Email</option>
		<option value="4">Customer ID</option>
	</select><br>
	<label>Search Parameter: </label><input type="text" id="csLParam"><br>
	<button id="csLSearch">Search</button>
	<button id="csLClose">Close</button>
	<p style="color: red;" id="csLError"></p>
	<div id="csLResults"></div>
</div>
<div id="cusCreateDiv" class="floating" style="padding-top: 10px;">
	<h2>Create Customer</h2>
	<?php require $_SERVER['DOCUMENT_ROOT']."/inventory/frontend/utils/customer_create.php";?>
</div>
<script src="/inventory/frontend/assets/js/utils/customer_lookup.js"></script>
