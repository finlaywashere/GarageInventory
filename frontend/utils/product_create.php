<div id="createProduct">
	<label>Name: </label><input id="prodName" type="text"><br>
	<label>Description: </label><input id="prodDesc" type="text"><br>
	<label>Notes: </label><input id="prodNotes" type="text"><br>
	<label>Stock Code: </label>
	<select id="prodType">
		<option value="0">Stock</option>
		<option value="1">Non Stock</option>
		<option value="2">Custom (MP)</option>
		<option value="3">Custom (OO)</option>
		<option value="4">Custom (EV)</option>
		<option value="5">Pseudo</option>
	</select><br>
	<label>Location: </label><input id="prodLoc" type="text"><br>
	<button id="prodCreate">Create</button>
	<button id="prodReset">Reset</button>
	<button id="prodClose">Close</button>
	<p style="color: red;" id="prodError"></p>
	<p style="color: green;" id="prodSuccess"></p>
</div>
<script src="/inventory/frontend/assets/js/product/create_product.js"></script>
