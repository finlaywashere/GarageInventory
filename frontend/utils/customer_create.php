<div id="createCustomer">
	<label>Name: </label><input id="cusName" type="text"><br>
	<label>Email: </label><input id="cusEmail" type="email"><br>
	<label>Phone: </label><input id="cusPhone" type="phone"><br>
	<label>Address: </label><input id="cusAddress" type="address"><br>
	<label>Notes: </label><input id="cusNotes" type="text"><br>
	<label>Type: </label>
	<select id="cusType">
		<option value="1">Normal</option>
		<option value="2">Business</option>
		<?php
			$create_sys = authenticate_request("inventory/admin");
			if($create_sys){
				echo "<option value=\"0\">System</option>";
			}
		?>
	</select><br>
	<button id="cusCreate">Create</button>
	<button id="cusReset">Reset</button>
	<button id="cusClose">Close</button>
	<p style="color: red;" id="cusError"></p>
	<p style="color: green;" id="cusSuccess"></p>
</div>
<script src="/inventory/frontend/assets/js/customer/create_customer.js"></script>
