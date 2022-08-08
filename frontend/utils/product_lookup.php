<div id="prodLookupDiv" class="floating">
	<h2>Product ID Lookup</h2>
	<label>Search Type: </label>
	<select id="prLType">
		<option value="2">Name</option>
		<option value="4">Description</option>
		<option value="3">Location</option>
		<option value="1">ID</option>
	</select><br>
	<label>Search Parameter: </label><input type="text" id="prLParam"><br>
	<button id="prLSearch">Search</button>
	<button id="prLClose">Close</button>
	<p style="color: red;" id="prLError"></p>
	<div id="prLResults"></div>
</div>
<div id="prodCreateDiv" class="floating" style="padding-top: 10px;">
	<h2>Create Product</h2>
	<?php
		require $_SERVER['DOCUMENT_ROOT']."/inventory/frontend/utils/product_create.php";
	?>
</div>
<script src="/inventory/frontend/assets/js/utils/product_lookup.js"></script>
